<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Jwks\KeySetCache;

class Proofs
{
    /** Clock skew tolerated on both the proof token and the status list, in seconds. */
    private const CLOCK_TOLERANCE_SECONDS = 5;

    /** Ceiling on a fetched status list, mirroring @proof-holdings/delegation-verifier. */
    private const MAX_STATUS_LIST_BYTES = 524288;

    private const STATUS_LIST_TIMEOUT_SECONDS = 5.0;

    /** The one `iss` an offline verification accepts. */
    private const PROOF_TOKEN_ISSUER = 'proof.holdings';

    /** The draft's 2-bit slot values (draft-ietf-oauth-status-list). */
    private const SLOT_VALID = 0b00;
    private const SLOT_REVOKED = 0b01;
    private const SLOT_SUSPENDED = 0b10;

    /** The last status list read, reusable until the issuer's own `ttl`/`exp` runs out. */
    private ?array $statusListCache = null;

    /**
     * The issuer's key set, held between verifications under the key-rotation rules.
     *
     * Not tidiness: the public JWKS endpoint is IP-rate-limited, and one verification reads the
     * key set twice — once for the proof, once for the status list signed with the same key.
     * Refetching per call would double a verifier's traffic against that limit and, past it,
     * start failing its GOOD tokens.
     */
    private KeySetCache $keySet;

    /**
     * @param int|float $maxJwksAge How old (seconds) the key set may grow, while it cannot be
     *     refreshed, before offline verification answers `jwks_stale`: above 0, at most 25 days.
     */
    public function __construct(
        private readonly HttpClient $http,
        int|float $maxJwksAge = KeySetCache::DEFAULT_MAX_JWKS_AGE_SECONDS,
    ) {
        $this->keySet = new KeySetCache(
            $http,
            $http->baseUrl . '/.well-known/jwks.json',
            KeySetCache::validateMaxJwksAge($maxJwksAge),
        );
    }

    /**
     * Validate a proof token online (checks revocation status).
     *
     * Pass $identifier to also bind the proof to the identifier you expect it to be about:
     * a proof about anything else answers valid:false with reason "identifier_mismatch",
     * and a proof of an encrypted decision — whose hash is over ciphertext — answers
     * "identifier_unverifiable". For a proof about a verified identifier any format is
     * accepted and canonicalized as it was at issuance; a proof of an approval decision binds
     * only on the exact recipient value it was issued for.
     */
    public function validate(string $proofToken, ?string $identifier = null): array
    {
        $body = ['proof_token' => $proofToken];
        if ($identifier !== null) {
            $body['identifier'] = $identifier;
        }
        return $this->http->post('/api/v1/proofs/validate', $body);
    }

    /**
     * Revoke a proof by public handle (`ph_ctl_*`/`ph_dlg_*`) or verification id.
     *
     * Revoking a PROOF does not withdraw the asset, consent or delegation it is about — those
     * have their own surfaces.
     */
    public function revoke(string $id, ?string $reason = null): array
    {
        $body = [];
        if ($reason !== null) {
            $body['reason'] = $reason;
        }
        return $this->http->post('/api/v1/proofs/' . rawurlencode($id) . '/revoke', $body);
    }

    /** Get the status of a proof by public handle (`ph_ctl_*`/`ph_dlg_*`) or verification id. A live proof reads `active`. */
    public function status(string $id): array
    {
        return $this->http->get('/api/v1/proofs/' . rawurlencode($id) . '/status');
    }

    public function listRevoked(): array
    {
        return $this->http->get('/api/v1/proofs/revoked');
    }

    /**
     * Verify a proof token without calling the Proof API with your key.
     *
     * Two public documents are read, both cacheable and neither revealing which proof you are
     * checking: the JWKS (for the signature) and the issuer's Token Status List (for revocation).
     *
     * By default the proof's status-list slot IS checked, so a revoked or suspended proof answers
     * `valid: false` with `reason` `revoked`/`suspended`. A list that cannot be read —
     * unreachable, failing its own checks, or not covering this slot — answers
     * `status_unavailable`: the refusal to guess, not a claim that the proof is withdrawn. A
     * token carrying no slot at all stays valid with `revocation_checked: false`, which is
     * exactly what the issuer's own `POST /proofs/validate` answers for it.
     *
     * Pass $checkRevocation = false to check the signature alone; the result then reports
     * `revocation_checked: false`.
     *
     * The key set follows the issuer's key-rotation rules (`sdks/shared/fixtures/jwks_rotation/`):
     * a key the issuer revoked answers `key_revoked` with a `pull_hint` naming where a re-issued
     * token can be fetched; a key set that could not be refreshed for longer than `maxJwksAge`
     * answers `jwks_stale` with its `cache_age_seconds`; a key of another family, a token signed
     * outside its key's window or living longer than the key allows answer `wrong_key_purpose`,
     * `outside_key_window` and `token_lifetime_exceeded`; a `kid` the issuer does not publish
     * answers `unknown_key`. A key set that could not be used at all answers `status_unavailable`.
     *
     * Requires the firebase/php-jwt package:
     *   composer require "firebase/php-jwt:^7.0"
     *
     * @return array{valid: bool, payload?: array, error?: string, reason?: string, revocation_checked: bool, pull_hint?: array{issuer_origin: string, handle: ?string}, cache_age_seconds?: ?int}
     */
    public function verifyOffline(string $token, bool $checkRevocation = true): array
    {
        if (!class_exists(\Firebase\JWT\JWT::class)) {
            throw new \RuntimeException(
                'Offline verification requires firebase/php-jwt. Install with: composer require "firebase/php-jwt:^7.0"'
            );
        }

        $unverified = $this->decodeSegment($token, 1);
        if ($unverified === null) {
            return [
                'valid' => false,
                'error' => 'token payload is not valid base64url JSON',
                'reason' => 'invalid',
                'revocation_checked' => false,
            ];
        }
        // The family only selects which key may answer; the signature still has to match that key.
        $family = ($unverified->token_type ?? null) === 'delegation' ? 'delegation' : 'proof';

        try {
            $checked = $this->verifyAgainstKeySet($token, $family);
        } catch (\Exception $e) {
            // \Exception, not \Throwable: an \Error is a programming bug, and reporting one as a
            // verdict would both hide it and put an internal message into a field the caller may
            // render.
            $checked = ['ok' => false, 'reason' => 'invalid', 'message' => $e->getMessage()];
        }
        if (!$checked['ok']) {
            $failure = [
                'valid' => false,
                'error' => $checked['message'],
                'reason' => $checked['reason'],
                'revocation_checked' => false,
            ];
            if ($checked['reason'] === 'key_revoked') {
                $handle = $family === 'delegation' ? ($unverified->sub ?? null) : ($unverified->proof_id ?? null);
                $failure['pull_hint'] = [
                    'issuer_origin' => $this->origin($this->http->baseUrl),
                    'handle' => is_string($handle) ? $handle : null,
                ];
            }
            if ($checked['reason'] === 'jwks_stale') {
                $failure['cache_age_seconds'] = $checked['cacheAgeSeconds'];
            }
            return $failure;
        }
        $payloadArray = (array) $checked['payload'];

        // The issuer pin. Without it any key in the fetched key set verifies any token that
        // carries it — "issuer-agnostic" means a trust policy supplied by the caller, never the
        // absence of one.
        if (($payloadArray['iss'] ?? null) !== self::PROOF_TOKEN_ISSUER) {
            return [
                'valid' => false,
                'error' => sprintf('unexpected issuer "%s"', (string) ($payloadArray['iss'] ?? '')),
                'reason' => 'invalid',
                'revocation_checked' => false,
            ];
        }

        $verified = [
            'valid' => true,
            'payload' => [
                'iss' => $payloadArray['iss'] ?? null,
                'sub' => $payloadArray['sub'] ?? null,
                'iat' => $payloadArray['iat'] ?? null,
                'exp' => $payloadArray['exp'] ?? null,
                'type' => $payloadArray['type'] ?? null,
                'channel' => $payloadArray['channel'] ?? null,
                'identifier_hash' => $payloadArray['identifier_hash'] ?? null,
                'verified_at' => $payloadArray['verified_at'] ?? null,
                'user_id' => $payloadArray['user_id'] ?? null,
                'decision' => $payloadArray['decision'] ?? null,
            ],
            'revocation_checked' => false,
        ];

        if (!$checkRevocation) {
            return $verified;
        }

        $claim = $this->readStatusListClaim($payloadArray);
        if ($claim === null) {
            return $verified;
        }

        [$state, $message] = $this->checkStatusList($claim);
        if ($state === 'valid') {
            return ['valid' => true, 'payload' => $verified['payload'], 'revocation_checked' => true];
        }
        if ($state === 'revoked' || $state === 'suspended') {
            return [
                'valid' => false,
                'payload' => $verified['payload'],
                'error' => $message,
                'reason' => $state,
                'revocation_checked' => true,
            ];
        }

        return [
            'valid' => false,
            'payload' => $verified['payload'],
            'error' => $message,
            'reason' => 'status_unavailable',
            'revocation_checked' => false,
        ];
    }

    /**
     * Make the next offline verification refetch the key set, and drop the cached status list.
     *
     * The refetch ignores the key set's 10-minute reuse window and the 30-second cooldowns, and a
     * successful one resets the set's age — the remedy a `jwks_stale` answer names. What the
     * verifier knows is kept: key confirmations and tombstones survive (`clearTombstones()` is
     * the separate, deliberate reset). The status list is verified against that same key set, so
     * a list kept across a rotation would be one signed by a key the caller just asked to stop
     * trusting.
     *
     * Named to match `refreshJWKS()` / `refresh_jwks()` / `RefreshJWKS()` in the other three SDKs.
     */
    public function refreshJWKS(): void
    {
        $this->keySet->forceRefresh();
        $this->statusListCache = null;
    }

    /**
     * Forget every revoked-key tombstone this client has applied or is waiting to confirm.
     *
     * They are held in memory only — a new process forgets them too — and otherwise kept until a
     * year after the revocation, whatever later key sets say.
     */
    public function clearTombstones(): void
    {
        $this->keySet->clearTombstones();
    }

    /** One segment of a compact JWS as a JSON object, or null when it is not one. */
    private function decodeSegment(string $token, int $index): ?\stdClass
    {
        $segments = explode('.', $token);
        if (count($segments) !== 3) {
            return null;
        }
        $raw = $this->b64urlDecode($segments[$index]);
        $decoded = $raw === false ? null : json_decode($raw, false);

        return $decoded instanceof \stdClass ? $decoded : null;
    }

    /**
     * The signature against the issuer's key set of `$family`, then the window and lifetime of the
     * key that made it, then the token's own time claims — so a forgery reads as a bad signature,
     * never as a policy violation of a key it did not come from, and a policy violation outranks
     * an expired token.
     *
     * @return array{ok: true, payload: \stdClass}|array{ok: false, reason: string, message: string, cacheAgeSeconds?: ?int}
     */
    private function verifyAgainstKeySet(string $token, string $family): array
    {
        $header = $this->decodeSegment($token, 0);
        if ($header === null) {
            return ['ok' => false, 'reason' => 'invalid', 'message' => 'token header is not valid base64url JSON'];
        }
        $alg = $header->alg ?? null;
        // Chosen from the allow-list, never trusted from the header: `none` and HMAC die here.
        if (!is_string($alg) || !in_array($alg, KeySetCache::SUPPORTED_ALGORITHMS, true)) {
            return ['ok' => false, 'reason' => 'invalid', 'message' => sprintf('token algorithm %s is not accepted', json_encode($alg))];
        }
        $kid = is_string($header->kid ?? null) ? $header->kid : null;

        $resolution = $this->keySet->resolve($kid, $alg, $family);
        switch ($resolution['kind']) {
            case 'unavailable':
                return ['ok' => false, 'reason' => 'status_unavailable', 'message' => $resolution['message']];
            case 'stale':
                return [
                    'ok' => false,
                    'reason' => 'jwks_stale',
                    'message' => 'the issuer key set could not be refreshed and the cached copy is older than '
                        . 'maxJwksAge; call refreshJWKS() once the issuer is reachable',
                    'cacheAgeSeconds' => $resolution['cacheAgeSeconds'],
                ];
            case 'revoked':
                return ['ok' => false, 'reason' => 'key_revoked', 'message' => sprintf('the issuer revoked signing key "%s"', $resolution['kid'])];
            case 'wrong_purpose':
                return ['ok' => false, 'reason' => 'wrong_key_purpose', 'message' => sprintf('signing key "%s" is not a %s key', $resolution['kid'], $family)];
            case 'keys':
                break;
            default:
                return ['ok' => false, 'reason' => 'unknown_key', 'message' => 'no key in the issuer key set matches this token'];
        }

        $matched = null;
        $payload = null;
        $claimFailure = null;
        foreach ($resolution['candidates'] as $candidate) {
            try {
                $payload = $this->decodeWithTolerance($token, $candidate['key']);
            } catch (\Firebase\JWT\ExpiredException $e) {
                // php-jwt checks the signature before the time claims: this key made it.
                $payload = $e->getPayload();
                $claimFailure = ['expired', $e->getMessage()];
            } catch (\Firebase\JWT\BeforeValidException $e) {
                $payload = $e->getPayload();
                $claimFailure = ['invalid', $e->getMessage()];
            } catch (\Firebase\JWT\SignatureInvalidException | \DomainException) {
                // Not this key; a token matching none of them is a bad signature.
                continue;
            } catch (\Exception $e) {
                // A malformed token, not a key that failed to match it.
                return ['ok' => false, 'reason' => 'invalid', 'message' => $e->getMessage()];
            }
            $matched = $candidate;
            break;
        }
        if ($matched === null) {
            return ['ok' => false, 'reason' => 'invalid', 'message' => 'signature verification failed'];
        }

        $violation = \ProofHoldings\Jwks\KeySet::checkKeyPolicy(
            $matched['record'],
            $payload->iat ?? null,
            $payload->exp ?? null,
            $resolution['registryLive'],
        );
        if ($violation === 'outside_key_window') {
            return ['ok' => false, 'reason' => $violation, 'message' => "token iat lies outside the signing key's window"];
        }
        if ($violation === 'token_lifetime_exceeded') {
            return ['ok' => false, 'reason' => $violation, 'message' => "token exp lies beyond iat plus the signing key's max_token_lifetime"];
        }
        if ($claimFailure !== null) {
            return ['ok' => false, 'reason' => $claimFailure[0], 'message' => $claimFailure[1]];
        }
        return ['ok' => true, 'payload' => $payload];
    }

    /**
     * Decode with the same 5-second clock tolerance the other three SDKs apply, against the clock
     * the key set ages on, without leaving the library's GLOBAL leeway or timestamp changed for the
     * rest of the host application.
     */
    private function decodeWithTolerance(string $token, \Firebase\JWT\Key $key): \stdClass
    {
        $previousLeeway = \Firebase\JWT\JWT::$leeway;
        $previousTimestamp = \Firebase\JWT\JWT::$timestamp;
        try {
            \Firebase\JWT\JWT::$leeway = max($previousLeeway, self::CLOCK_TOLERANCE_SECONDS);
            \Firebase\JWT\JWT::$timestamp = $this->nowSeconds();
            return \Firebase\JWT\JWT::decode($token, $key);
        } finally {
            \Firebase\JWT\JWT::$leeway = $previousLeeway;
            \Firebase\JWT\JWT::$timestamp = $previousTimestamp;
        }
    }

    /** "Now" for every claim check, read from the same clocks the key set ages on. */
    private function nowSeconds(): int
    {
        return (int) floor(KeySetCache::clock()->wallMs() / 1000);
    }

    /** The token's own slot reference, or null when it carries none. */
    private function readStatusListClaim(array $claims): ?array
    {
        $status = $claims['status'] ?? null;
        $status = is_object($status) ? (array) $status : $status;
        if (!is_array($status)) {
            return null;
        }

        $statusList = $status['status_list'] ?? null;
        $statusList = is_object($statusList) ? (array) $statusList : $statusList;
        if (!is_array($statusList)) {
            return null;
        }

        $idx = $statusList['idx'] ?? null;
        $uri = $statusList['uri'] ?? null;
        if (!is_int($idx) || $idx < 0 || !is_string($uri) || $uri === '') {
            return null;
        }

        return ['idx' => $idx, 'uri' => $uri];
    }

    /**
     * Resolve one token's slot against the issuer's signed status list.
     *
     * @return array{0: string, 1: string} the outcome name and, for a negative one, why
     */
    private function checkStatusList(array $claim): array
    {
        $uri = $claim['uri'];
        $parts = parse_url($uri);
        if (!is_array($parts) || !isset($parts['scheme'], $parts['host'])) {
            return ['unavailable', 'status list uri is not a valid absolute URL'];
        }

        // The uri arrives INSIDE the artifact being verified, so fetching whatever it names would
        // hand a forged token both the status authority and a request from your network. Only the
        // origin this client is configured against is honoured.
        $configuredOrigin = $this->origin($this->http->baseUrl);
        if ($this->origin($uri) !== $configuredOrigin) {
            return ['unavailable', sprintf(
                'status list uri origin %s is not the configured issuer origin %s',
                $this->origin($uri),
                $configuredOrigin
            )];
        }

        $loaded = $this->loadStatusList($uri);
        if (is_string($loaded)) {
            return ['unavailable', $loaded];
        }

        [$data, $bits] = $loaded;
        $slot = $this->readStatusSlot($data, $claim['idx'], $bits);
        if ($slot === null) {
            return ['unavailable', sprintf('status list has no slot %d', $claim['idx'])];
        }

        return match ($slot) {
            self::SLOT_REVOKED => ['revoked', 'This proof has been revoked'],
            self::SLOT_SUSPENDED => ['suspended', 'This proof is currently suspended'],
            self::SLOT_VALID => ['valid', ''],
            // 0b11 is reserved for application-specific use by the draft; the issuer packs it when
            // it could not resolve a delegation's control proof. It is an absence of an answer.
            default => ['unavailable', sprintf('status list slot carries unknown status %d', $slot)],
        };
    }

    /**
     * The origin of a url, normalized the way `URL.origin` does it in the JS SDK: scheme and host
     * lowercased, a port dropped when it is the scheme's default. The four SDKs must answer the
     * same question here, or a status-list uri accepted by one is refused by another.
     */
    private function origin(string $url): string
    {
        $parts = parse_url($url);
        if (!is_array($parts) || !isset($parts['scheme'], $parts['host'])) {
            return '';
        }
        $scheme = strtolower($parts['scheme']);
        $host = strtolower($parts['host']);
        $defaults = ['http' => 80, 'https' => 443];
        $port = $parts['port'] ?? null;
        $suffix = ($port === null || ($defaults[$scheme] ?? null) === $port) ? '' : ':' . $port;

        return $scheme . '://' . $host . $suffix;
    }

    /**
     * The decompressed list and its bit width, or a message saying why it is unavailable.
     *
     * @return array{0: string, 1: int}|string
     */
    private function loadStatusList(string $uri): array|string
    {
        $cached = $this->statusListCache;
        if ($cached !== null && $cached['uri'] === $uri && $cached['expires_at'] > $this->nowSeconds()) {
            return [$cached['data'], $cached['bits']];
        }

        try {
            $response = $this->http->fetchPublic(
                $uri,
                self::STATUS_LIST_TIMEOUT_SECONDS,
                ['accept' => 'application/statuslist+jwt, application/jwt, text/plain'],
                self::MAX_STATUS_LIST_BYTES,
            );
        } catch (\Exception $e) {
            return 'status list fetch failed: ' . $e->getMessage();
        }

        if ($response['status'] !== 200) {
            return 'status list fetch failed with HTTP ' . $response['status'];
        }
        if ($response['declared_length'] !== null && $response['declared_length'] > self::MAX_STATUS_LIST_BYTES) {
            return 'status list response is too large';
        }

        $listToken = trim($response['body']);
        if (strlen($listToken) > self::MAX_STATUS_LIST_BYTES) {
            return 'status list response is too large';
        }

        $segments = explode('.', $listToken);
        if (count($segments) !== 3) {
            return 'status list is not a compact JWS';
        }

        $header = json_decode((string) $this->b64urlDecode($segments[0]), true);
        // The draft mandates this `typ`, and it is the one header that separates a status list
        // from every other JWT the issuer signs with the SAME key.
        if (!is_array($header) || ($header['typ'] ?? null) !== 'statuslist+jwt') {
            return 'status list must carry typ "statuslist+jwt"';
        }

        // Held to its own key family (`status-list`) and that key's window.
        try {
            $checked = $this->verifyAgainstKeySet($listToken, 'status-list');
        } catch (\Exception $e) {
            return 'status list did not verify: ' . $e->getMessage();
        }
        if (!$checked['ok']) {
            return 'status list did not verify: ' . $checked['message'];
        }
        $payload = (array) $checked['payload'];

        // A freshness channel with no expiry is not one: an exp-less list would be honoured
        // forever, so its absence reads as "cannot answer" rather than fail-open. `iss` is
        // deliberately NOT required — the issuer does not put one in the list.
        if (!isset($payload['exp']) || !is_int($payload['exp'])) {
            return 'status list carries no exp';
        }
        // The list must say it is the list we asked for, or a valid list from the same issuer
        // could be replayed in place of another one.
        if (($payload['sub'] ?? null) !== $uri) {
            return 'status list `sub` does not match the uri it was fetched from';
        }

        $statusList = $payload['status_list'] ?? null;
        $statusList = is_object($statusList) ? (array) $statusList : $statusList;
        if (!is_array($statusList) || !is_string($statusList['lst'] ?? null)) {
            return 'status list payload has no `lst`';
        }
        $bits = is_int($statusList['bits'] ?? null) ? $statusList['bits'] : 2;

        $compressed = $this->b64urlDecode($statusList['lst']);
        if ($compressed === false) {
            return 'status list `lst` is not valid base64url';
        }
        $data = @gzuncompress($compressed);
        if ($data === false) {
            return 'status list payload could not be decompressed';
        }

        // Honour the shorter of the issuer's published `ttl` and the token's own `exp`.
        $expiresAt = $payload['exp'];
        $ttl = $payload['ttl'] ?? null;
        if (is_int($ttl) && $ttl > 0) {
            $expiresAt = min($expiresAt, $this->nowSeconds() + $ttl);
        }
        $this->statusListCache = ['uri' => $uri, 'data' => $data, 'bits' => $bits, 'expires_at' => $expiresAt];

        return [$data, $bits];
    }

    /**
     * Read one slot: lowest index in the least significant bits, $bits statuses per byte — the
     * draft's byte order, pinned across the four SDKs and the issuer by
     * sdks/shared/fixtures/status_list_slots.json.
     *
     * Null means the array does not reach that index — "no answer", never "valid".
     */
    private function readStatusSlot(string $data, int $index, int $bits): ?int
    {
        if ($index < 0 || !in_array($bits, [1, 2, 4, 8], true)) {
            return null;
        }
        $perByte = intdiv(8, $bits);
        $byteIndex = intdiv($index, $perByte);
        if ($byteIndex >= strlen($data)) {
            return null;
        }

        return (ord($data[$byteIndex]) >> (($index % $perByte) * $bits)) & ((1 << $bits) - 1);
    }

    private function b64urlDecode(string $value): string|false
    {
        return base64_decode(strtr($value, '-_', '+/'), true);
    }
}
