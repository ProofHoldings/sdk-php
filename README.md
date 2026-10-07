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
use Proof\Proof;

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
$response = $proof->proofs->revoke('ver_abc123', 'User requested');

// Get revocation list
$revoked = $proof->proofs->listRevoked();

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

### Signing-key rotation

`verifyOffline` fetches and caches the issuer's key set (JWKS) itself and follows its rotation
rules. The cached set is reused for 10 minutes; a `kid` it does not know triggers one refetch, at
most once every 30 seconds.

- **`key_revoked`** — the issuer revoked the key that signed the token. The result carries
  `pull_hint: ['issuer_origin' => ..., 'handle' => ...]`: where to fetch the re-signed token
  (`handle` is the proof's `proof_id`, or a delegation's `sub`). The handle is read from the token
  itself, whose signature a revoked key no longer vouches for: URL-encode it before building a
  request from it. A revocation takes effect only after two key-set fetches at least 5 minutes
  apart both carry it, and is then kept for a year.
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

## Error Handling

```php
use Proof\Proof;
use Proof\Exceptions\NotFoundException;
use Proof\Exceptions\RateLimitException;
use Proof\Exceptions\ProofException;

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

## Requirements

- PHP >= 8.1
- guzzlehttp/guzzle >= 7.5
- firebase/php-jwt ^7.0 (optional, for offline verification — every 6.x release carries a security advisory)
