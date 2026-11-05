# proof-holdings/sdk - PHP

Official PHP SDK for the [proof.holdings](https://proof.holdings) verification API.

## Installation

```bash
composer require proof-holdings/sdk
```

For offline JWT verification:

```bash
composer require "firebase/php-jwt:^7.0"
```

## Quick Start

```php
use ProofHoldings\Proof;

$proof = new Proof('pk_live_...');

// Create a phone verification
$v = $proof->verifications->create([
    'type' => 'phone',
    'channel' => 'whatsapp',
    'identifier' => '+1234567890',
]);
echo "Verification {$v['id']} created: {$v['status']}\n";

// Wait for user to complete verification
$result = $proof->verifications->waitForCompletion($v['id']);
echo "Result: {$result['status']}\n";
```

## Resources

### Verifications

```php
// Create
$v = $proof->verifications->create([
    'type' => 'domain',
    'channel' => 'dns',
    'identifier' => 'example.com',
]);

// Retrieve
$v = $proof->verifications->retrieve('ver_abc123');

// List with filters
$page = $proof->verifications->list(['status' => 'verified', 'type' => 'phone', 'limit' => 10]);

// Trigger DNS/HTTP check
$v = $proof->verifications->verify('ver_abc123');

// Submit OTP code
$v = $proof->verifications->submit('ver_abc123', 'ABC123');

// Poll until complete
$v = $proof->verifications->waitForCompletion('ver_abc123', interval: 2.0, timeout: 300.0);
```

### Verification Requests (Multi-Asset)

```php
$req = $proof->verificationRequests->create([
    'assets' => [
        ['type' => 'phone', 'required' => true],
        ['type' => 'email', 'identifier' => 'user@example.com'],
    ],
    'reference_id' => 'user_123',
    'callback_url' => 'https://yourapp.com/webhook',
    'expires_in' => 86400,
]);
echo "Send user to: {$req['verification_url']}\n";

// Poll until complete
$result = $proof->verificationRequests->waitForCompletion($req['id']);
```

### Proofs

```php
// Validate online (checks revocation)
$result = $proof->proofs->validate('eyJhbGciOi...');

// Revoke
$response = $proof->proofs->revoke('ver_abc123', 'user_request');

// Get revocation list
$revoked = $proof->proofs->listRevoked();

// After a signing-key rotation: list what was re-signed, page by page, then pull the current tokens
$since = new DateTimeImmutable('2026-10-01T00:00:00Z');
for ($page = 1, $totalPages = 1; $page <= $totalPages; $page++) {
    $reissued = $proof->proofs->listReissued($since, ['page' => $page, 'limit' => 100]);
    $totalPages = $reissued['pagination']['total_pages'];
    foreach ($reissued['data'] as $item) {
        $pulled = str_starts_with($item['proof_id'], 'ph_dlg_')
            ? $proof->proofs->delegationToken($item['proof_id']) // public, no key needed
            : $proof->proofs->token($item['proof_id']);
    }
}

// Offline verification (requires firebase/php-jwt) — no API call with your key. Two public
// documents are read: the JWKS for the signature, and the issuer's status list for revocation,
// which is checked by default.
$result = $proof->proofs->verifyOffline('eyJhbGciOi...');
if (!$result['valid']) {
    // 'revoked' | 'suspended' | 'expired' | 'invalid' | 'status_unavailable', or one of the
    // key-rotation reasons below
    echo $result['reason'];
}

// 'status_unavailable' means the revocation status could NOT be read — an unreachable list, one
// that failed its own checks, or a slot the list does not cover. It is a refusal to guess, not a
// claim that the proof is withdrawn. A token minted without a status-list slot stays valid and
// reports revocation_checked = false, exactly as the issuer's own validate answers for it.

// Signature only, for an air-gapped check against a warm key cache:
$signatureOnly = $proof->proofs->verifyOffline('eyJhbGciOi...', false);
var_dump($signatureOnly['revocation_checked']); // false — never assume it was checked

// The key set and the status list are held between verifications. refreshJWKS() makes the next
// verification refetch the key set and drops the cached status list.
$proof->proofs->refreshJWKS();
```

### Sessions (Phone-First Flow)

```php
$session = $proof->sessions->create(['channel' => 'telegram']);
echo "Deep link: {$session['deep_link']}\n";

$result = $proof->sessions->waitForCompletion($session['id']);
```

### Webhook Deliveries

```php
$deliveries = $proof->webhookDeliveries->list(['status' => 'failed']);
$result = $proof->webhookDeliveries->retry('del_abc123');
```

### Webhook Endpoints

```php
// The signing secret is returned ONCE — here and by rotateSecret(). Store it now.
$endpoint = $proof->webhookEndpoints->create([
    'url' => 'https://example.com/webhooks/proof',
    'scope' => 'all',
    'events' => ['verification.completed'],
]);
$secret = $endpoint['secret'];

$endpoints = $proof->webhookEndpoints->list();          // ['data' => [...]], no secrets
$proof->webhookEndpoints->update($endpoint['id'], ['status' => 'disabled']);
$rotated = $proof->webhookEndpoints->rotateSecret($endpoint['id']); // $rotated['secret']
$proof->webhookEndpoints->delete($endpoint['id']);

// A sub-account's callback signing secret (returned once, by the rotation)
$callback = $proof->profiles->rotateCallbackSecret('prof_abc123'); // ['profile_id' => ..., 'secret' => ...]
```

## Key rotation


`verifyOffline` fetches and caches the issuer's key set (JWKS) itself and follows its rotation
rules. The cached set is reused for 10 minutes; a `kid` it does not know triggers one refetch, at
most once every 30 seconds.

**Retiring the legacy keys.** The keys Proof signed with before this rotation scheme — the legacy
keys — are retired once. Proof re-signs the live tokens they signed under a current key, and revokes
the legacy keys no earlier than 30 days after that re-signing. From then on an old copy that names
its key answers `key_revoked`, and one with no `kid` does not verify at all. A token that was
already superseded, revoked or expired when they were re-signed, or that changed while it was read,
or that the re-signing refused or could not match to its record, stays on the legacy key and stops
verifying when they are revoked. Replace every copy you handed out or published with the re-signed
token (see `key_revoked` below).

- **`key_revoked`** — the issuer revoked the key that signed the token, after a suspected compromise
  or at the retirement of the legacy keys. The result carries
  `pull_hint: ['issuer_origin' => ..., 'handle' => ...]`: where to fetch the re-signed token
  (`handle` is the proof's `proof_id`, or a delegation's `sub`). The handle is read from the token
  itself, whose signature a revoked key no longer vouches for: URL-encode it before building a
  request from it — `proofs->token($handle)` for a `ph_ctl_` handle and
  `proofs->delegationToken($handle)` for a `ph_dlg_` one do that for you. `proofs->token()` serves
  only a proof your own account owns — any other answers 404 — so a re-signed token of someone
  else's proof comes from whoever holds it; `proofs->delegationToken()` is public. An API key is
  refused the token of one of the account's own verified assets (403 `owner_session_required`), and
  `proofs->listReissued()` does not list those for a key: read it signed in, in the dashboard. A
  revocation takes effect only after two key-set fetches at least 5 minutes apart both carry it, and
  is then kept for a year.
- **`jwks_stale`** — the key set could not be refreshed and the cached copy is older than
  `maxJwksAge` (seconds; default 24 hours, at most 25 days; `new Proof(..., maxJwksAge: ...)`). The
  result carries `cache_age_seconds`. Call `refreshJWKS()` once the issuer is reachable.
- **`wrong_key_purpose`**, **`outside_key_window`**, **`token_lifetime_exceeded`** — the key belongs
  to another token family, the token was signed outside the key's validity window, or it lives
  longer than the key allows. **`unknown_key`** — no published key matches the token's `kid`.

Revoked-key tombstones are kept in memory only — they are not persisted, so every new process
(each PHP-FPM request, unless the `Proof` instance lives longer, as under Octane or Swoole) learns
them again from the issuer. `clearTombstones()` forgets them on purpose. `refreshJWKS()` makes the
next verification refetch the key set (and drops the cached status list); it does not forget
tombstones.

**What a rotation changes.** `iat` is when the token was signed; `verified_at` is when the control
it attests was verified. A token re-signed after a key revocation keeps every claim but `iat` — the
same `jti`, `exp` and `verified_at`, under a new `kid`; a proof token from before `jti` existed also
gains its row's `jti` and `proof_id`. Judge a proof's age by `verified_at`.

**A generic JOSE or JWT library gives signature-only assurance.** Checking the signature against
the JWKS is none of the checks above — a key's purpose, its window and lifetime limit, tombstones,
the status list. Use `verifyOffline` or `proofs->validate()`.

**The current `jti` generation is an online check.** Once a proof is verified again,
`proofs->validate()` (`POST /proofs/validate`) answers `superseded` for the earlier token. Offline
verification still accepts that token until its own `exp`: it carries the proof's status-list slot,
shared with the current token, so revoking or suspending the proof stops both.

**Refresh does not revoke.** A token re-issued within its generation — a renewal — leaves the
earlier copy valid until its own `exp`; only a revoked key ends it sooner. To withdraw a token,
revoke the proof.

**Minimum version.** These checks are in `proof-holdings/sdk` 1.0.0 and later. `proofs->token()`,
`proofs->listReissued()` and `proofs->delegationToken()` are in 1.4.0 and later.

## Error Handling

```php
use ProofHoldings\Proof;
use ProofHoldings\Exceptions\NotFoundException;
use ProofHoldings\Exceptions\RateLimitException;
use ProofHoldings\Exceptions\ProofException;

$proof = new Proof('pk_live_...');

try {
    $v = $proof->verifications->retrieve('nonexistent');
} catch (NotFoundException $e) {
    echo "Not found: {$e->errorCode}\n";
} catch (RateLimitException $e) {
    echo "Rate limited, try again later\n";
} catch (ProofException $e) {
    echo "API error {$e->statusCode}: {$e->errorCode} - {$e->getMessage()}\n";
}
```

## Configuration

```php
$proof = new Proof(
    apiKey: 'pk_live_...',
    baseUrl: 'https://api.proof.holdings',  // Default
    timeout: 30.0,                           // Seconds, default 30
    maxRetries: 2,                           // Default 2
    maxJwksAge: 86400,                       // Offline verification: stale key-set bound, seconds
);
```

## Credentials

Sign in once with `npx @proof-holdings/mcp-server login` and construct the client with no key:

```php
$proof = new Proof();                        // or new Proof(baseUrl: '...')
```

When the key argument is omitted it is resolved in this order:

1. `PROOF_API_KEY`, when set and non-empty.
2. The entry for this client's base URL in `~/.proof-holdings/credentials`, the file the login
   command writes. The file is keyed by base URL, so a key saved against staging is never sent
   to production.
3. Otherwise construction throws `InvalidArgumentException`, naming the three roads: pass the
   key, set `PROOF_API_KEY`, or run `npx @proof-holdings/mcp-server login`.

The base URL is the `baseUrl` argument, else a non-empty `PROOF_BASE_URL`, else
`https://api.proof.holdings`. An explicit key is used as given, and an explicit empty key
(`new Proof('')`) is an error — neither reads the environment or the file.

## Requirements

- PHP >= 8.3 (the SDK uses typed class constants)
- guzzlehttp/guzzle >= 7.15.2
- firebase/php-jwt ^7.0 (optional, for offline verification — every 6.x release carries a security advisory)
