# proof-holdings/sdk - PHP

Official PHP SDK for the [proof.holdings](https://proof.holdings) verification API.

## Installation

```bash
composer require proof-holdings/sdk
```

For offline JWT verification:

```bash
composer require firebase/php-jwt
```

## Quick Start

```php
use ProofHoldings\ProofHoldings;

$proof = new ProofHoldings('pk_live_...');

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

// Offline verification (requires firebase/php-jwt)
$result = $proof->proofs->verifyOffline('eyJhbGciOi...');
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

## Error Handling

```php
use ProofHoldings\ProofHoldings;
use ProofHoldings\Exceptions\NotFoundException;
use ProofHoldings\Exceptions\RateLimitException;
use ProofHoldings\Exceptions\ProofHoldingsException;

$proof = new ProofHoldings('pk_live_...');

try {
    $v = $proof->verifications->retrieve('nonexistent');
} catch (NotFoundException $e) {
    echo "Not found: {$e->errorCode}\n";
} catch (RateLimitException $e) {
    echo "Rate limited, try again later\n";
} catch (ProofHoldingsException $e) {
    echo "API error {$e->statusCode}: {$e->errorCode} - {$e->getMessage()}\n";
}
```

## Configuration

```php
$proof = new ProofHoldings(
    apiKey: 'pk_live_...',
    baseUrl: 'https://api.proof.holdings',  // Default
    timeout: 30.0,                           // Seconds, default 30
    maxRetries: 2,                           // Default 2
);
```

## Requirements

- PHP >= 8.1
- guzzlehttp/guzzle >= 7.5
- firebase/php-jwt >= 6.0 (optional, for offline verification)
