<?php

declare(strict_types=1);

namespace ProofHoldings\Resources;

use ProofHoldings\HttpClient;
use ProofHoldings\Streaming;
use ProofHoldings\Types\ApprovalLinkResponse;
use ProofHoldings\Types\Confirmation;
use ProofHoldings\Types\ConfirmationListResponse;

class Confirmations
{
    private const array TERMINAL_STATES = ['approved', 'denied', 'expired', 'cancelled'];

    public function __construct(private readonly HttpClient $http) {}

    /**
     * Stream confirmation status via SSE with polling fallback.
     * Endpoint: GET /api/v1/confirmations/:id/events
     *
     * @return \Generator<int, array{event: string, id: ?string, data: mixed}>
     */
    public function stream(string $id, ?callable $shouldCancel = null): \Generator
    {
        return Streaming::streamEndpoint(
            $this->http,
            '/api/v1/confirmations/' . rawurlencode($id) . '/events',
            fn () => (array) $this->http->get('/api/v1/confirmations/' . rawurlencode($id)),
            self::TERMINAL_STATES,
            "Confirmation {$id}",
            $shouldCancel,
        );
    }

    public function create(array $params): Confirmation
    {
        $data = $this->http->post('/api/v1/confirmations', $params);
        return Confirmation::fromArray($data);
    }

    public function retrieve(string $id): Confirmation
    {
        $data = $this->http->get('/api/v1/confirmations/' . rawurlencode($id));
        return Confirmation::fromArray($data);
    }

    /**
     * List confirmations. Filters: status, hitl_id, profile_id (one sub-account's confirmations),
     * start_date, end_date, page, limit.
     */
    public function list(array $params = []): ConfirmationListResponse
    {
        $data = $this->http->get('/api/v1/confirmations', $params);
        return ConfirmationListResponse::fromArray($data);
    }

    /**
     * Check the decider's own signature on a confirmation's decision, locally (no request is made).
     *
     * The proof token is Proof's signed word about the decision; this checks the approver's, against
     * `$publicKey` — the key YOU hold (SPKI or PKCS#1 PEM): for an owner decision, your HITL config's
     * own public key; for an approver, the key you recorded from `hitl->getApproverKeys()` when they
     * enrolled. The key the response carries must be that key; `signed_input` must be
     * `decision.<id>.<action>.<nonce>.<sha256 hex of response.text, or ->` for this confirmation's id,
     * its action and its comment, and `status` must be the signed action's (`approved` for `approve`,
     * `denied` for `deny`) — so a valid signature copied from another decision, or a status that
     * contradicts it, does not pass; and `signature` must be base64 (either alphabet; padding and
     * line breaks optional) of an RSASSA-PKCS1-v1_5 / SHA-256 signature that verifies under your key. Compare the confirmation's id
     * with the id you asked for yourself. The key is required because a key the server sent is no
     * evidence against the server. Never throws for anything inside the confirmation; the shared rule
     * is `sdks/shared/fixtures/decision_signature/README.md`.
     *
     * Returns, checked in this order: `unsigned` (no decision yet, one recorded without signed
     * evidence — older, or made by the system — or evidence fields that are not strings),
     * `key_mismatch` (`$publicKey` is not the key the decision carries), `input_mismatch`
     * (`signed_input` does not name this confirmation, its action and its comment, or `status` is not
     * the signed action's), `signature_invalid` (the signature is not base64, or does not
     * verify under your key), `verified`.
     *
     * @param Confirmation|array<string, mixed> $confirmation what `retrieve()` returns, or the API's array
     */
    public function verifyDecisionSignature(Confirmation|array $confirmation, string $publicKey): string
    {
        if ($confirmation instanceof Confirmation) {
            [$id, $status, $r] = [$confirmation->id, $confirmation->status, $confirmation->response];
        } else {
            [$id, $status, $r] = [$confirmation['id'] ?? null, $confirmation['status'] ?? null, $confirmation['response'] ?? null];
        }
        if (!is_array($r) || array_is_list($r) && $r !== []) {
            return 'unsigned';
        }
        $carried = $r['public_key'] ?? null;
        $signedInput = $r['signed_input'] ?? null;
        $signature = $r['signature'] ?? null;
        if (!is_string($carried) || !is_string($signedInput) || !is_string($signature)) {
            return 'unsigned';
        }
        $carriedBare = preg_replace('/[ \t\r\n]/', '', $carried);
        if ($carriedBare === '' || $signedInput === '' || $signature === '') {
            return 'unsigned';
        }
        $callerBare = preg_replace('/[ \t\r\n]/', '', $publicKey);
        if ($callerBare === '' || $callerBare !== $carriedBare) {
            return 'key_mismatch';
        }

        $action = $r['action'] ?? null;
        $text = $r['text'] ?? null;
        if (!is_string($id) || !is_string($action) || ($text !== null && !is_string($text))) {
            return 'input_mismatch';
        }
        $signedStatus = ['approve' => 'approved', 'deny' => 'denied'][$action] ?? null;
        if ($signedStatus === null || $status !== $signedStatus) {
            return 'input_mismatch';
        }
        $nonce = explode('.', $signedInput)[3] ?? '';
        $digest = $text !== null && $text !== '' ? hash('sha256', $text) : '-';
        if ($signedInput !== "decision.{$id}.{$action}.{$nonce}.{$digest}") {
            return 'input_mismatch';
        }

        $b64 = preg_replace('/={1,2}$/', '', strtr(preg_replace('/[ \t\r\n]/', '', $signature), '-_', '+/'));
        if (preg_match('#^[A-Za-z0-9+/]*$#', $b64) !== 1 || strlen($b64) % 4 === 1) {
            return 'signature_invalid';
        }
        $raw = base64_decode($b64, true);
        if ($raw === false) {
            return 'signature_invalid';
        }
        $key = self::rsaPublicKeyFromPem($publicKey);
        if ($key === null) {
            return 'signature_invalid';
        }
        return openssl_verify($signedInput, $raw, $key, OPENSSL_ALGO_SHA256) === 1 ? 'verified' : 'signature_invalid';
    }

    /** The rsaEncryption AlgorithmIdentifier (1.2.840.113549.1.1.1, NULL parameters) an RSA SPKI carries. */
    private const string RSA_ENCRYPTION = "\x30\x0d\x06\x09\x2a\x86\x48\x86\xf7\x0d\x01\x01\x01\x05\x00";

    /**
     * The RSA key in the first PEM block of `$text`, by the shared rule rather than by OpenSSL's PEM reader
     * (which would also read a certificate, or a block after the first): label `PUBLIC KEY` (SPKI) or
     * `RSA PUBLIC KEY` (PKCS#1), a base64 body, and a first DER element that is an rsaEncryption key
     * re-encoding to itself, with a 4096- to 16384-bit modulus and an odd public exponent from 3 to 2^31 - 1.
     * Null for anything else.
     */
    private static function rsaPublicKeyFromPem(string $text): ?\OpenSSLAsymmetricKey
    {
        if (preg_match('#-----BEGIN ([^\n-]*)-----([\s\S]*?)-----END \1-----#', $text, $block) !== 1) {
            return null;
        }
        $body = preg_replace('/[ \t\r\n]/', '', $block[2]);
        if (!in_array($block[1], ['PUBLIC KEY', 'RSA PUBLIC KEY'], true)
            || preg_match('#^[A-Za-z0-9+/]*={0,2}$#D', $body) !== 1 || strlen($body) % 4 !== 0) {
            return null;
        }
        $element = self::firstDerElement((string) base64_decode($body, true));
        if ($element === null) {
            return null;
        }
        // OpenSSL is handed SPKI only; a PKCS#1 key is wrapped in the SPKI an RSA key re-encodes to.
        if ($block[1] === 'RSA PUBLIC KEY') {
            $bits = "\x03" . self::derLength(strlen($element) + 1) . "\x00" . $element;
            $spki = "\x30" . self::derLength(strlen(self::RSA_ENCRYPTION . $bits)) . self::RSA_ENCRYPTION . $bits;
        } else {
            $spki = $element;
        }
        $key = @openssl_pkey_get_public("-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($spki), 64, "\n") . "-----END PUBLIC KEY-----\n");
        $details = $key === false ? false : openssl_pkey_get_details($key);
        if ($details === false || ($details['type'] ?? null) !== OPENSSL_KEYTYPE_RSA) {
            return null;
        }
        $reencoded = base64_decode(preg_replace('/-----[^-]+-----|\s/', '', $details['key']), true);
        $header = 2 + (ord($spki[1]) & 0x80 ? ord($spki[1]) & 0x7f : 0);
        if ($reencoded !== $spki || substr($spki, $header, strlen(self::RSA_ENCRYPTION)) !== self::RSA_ENCRYPTION) {
            return null;
        }
        // A key every SDK verifies with: a 4096- to 16384-bit modulus (OpenSSL refuses larger, Go would not) and an
        // odd exponent from 3 to 2^31 - 1 (Go's crypto/rsa refuses larger).
        $bits = (int) ($details['bits'] ?? 0);
        $e = ltrim((string) ($details['rsa']['e'] ?? ''), "\x00");
        $exponent = strlen($e) <= 4 ? (int) hexdec(bin2hex($e)) : 0;
        return $bits >= 4096 && $bits <= 16384 && $exponent >= 3 && $exponent <= 2147483647 && $exponent % 2 === 1
            ? $key : null;
    }

    /** The first DER element (a SEQUENCE) of `$der`, whatever follows it ignored, as OpenSSL reads a key. */
    private static function firstDerElement(string $der): ?string
    {
        if (strlen($der) < 2 || $der[0] !== "\x30") {
            return null;
        }
        $length = ord($der[1]);
        $start = 2;
        if ($length & 0x80) {
            $n = $length & 0x7f;
            if ($n < 1 || $n > 4 || strlen($der) < 2 + $n) {
                return null;
            }
            $length = 0;
            for ($i = 0; $i < $n; $i++) {
                $length = $length * 256 + ord($der[2 + $i]);
            }
            $start = 2 + $n;
        }
        return $start + $length <= strlen($der) ? substr($der, 0, $start + $length) : null;
    }

    private static function derLength(int $length): string
    {
        if ($length < 0x80) {
            return chr($length);
        }
        $bytes = ltrim(pack('N', $length), "\x00");
        return chr(0x80 | strlen($bytes)) . $bytes;
    }

    /**
     * Get the HMAC approval link for a pending confirmation — the HITL config owner's link,
     * decided with the config's own key. It is not an approver's link: an approver's goes only
     * to their own channel, and `POST /api/v1/confirmations/{id}/resend` ("Send again") is what
     * sends it there again. Every call returns the same owner link until "Send again" reaches
     * the owner; "Send again" replaces it, and the next call returns a new one.
     */
    public function approvalLink(string $id): ApprovalLinkResponse
    {
        $data = $this->http->post('/api/v1/confirmations/' . rawurlencode($id) . '/approval-link');
        return ApprovalLinkResponse::fromArray($data);
    }
}
