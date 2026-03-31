<?php

declare(strict_types=1);

namespace ProofHoldings\Crypto;

/**
 * HITL End-to-End Encryption — Crypto Module.
 *
 * Implements the envelope spec from docs/ENCRYPTION_ENVELOPE.md.
 * Uses PHP openssl_* functions and hash_pbkdf2.
 *
 * Two modes:
 * - Encrypt-only: agent has public key, encrypts messages for the approver
 * - Encrypt+decrypt: approver has password, decrypts messages
 */
class HitlCrypto
{
    private const int ENVELOPE_VERSION = 1;
    private const string MESSAGE_ALG = 'RSA-OAEP-256+AES-256-GCM';
    private const string PRIVATE_KEY_ALG = 'PBKDF2-SHA256+AES-256-GCM';
    private const int AES_KEY_BYTES = 32;
    private const int AES_IV_BYTES = 12;
    private const int AES_TAG_BYTES = 16;
    private const int RSA_EK_BYTES = 512;
    private const int PBKDF2_ITERATIONS = 600_000;
    private const int PBKDF2_SALT_BYTES = 16;
    private const int PBKDF2_KEY_BYTES = 32;

    // -----------------------------------------------------------------------
    // Base64url helpers
    // -----------------------------------------------------------------------

    private static function toBase64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function fromBase64url(string $s): string
    {
        return base64_decode(strtr($s, '-_', '+/') . str_repeat('=', (4 - strlen($s) % 4) % 4), true) ?: '';
    }

    // -----------------------------------------------------------------------
    // Key Identifier
    // -----------------------------------------------------------------------

    /** Compute kid = lowercase_hex(SHA-256(DER-encoded SPKI public key bytes)). */
    public static function computeKid(string $publicKeyPem): string
    {
        $res = openssl_pkey_get_public($publicKeyPem);
        if ($res === false) {
            throw new \RuntimeException('Invalid public key PEM');
        }
        $details = openssl_pkey_get_details($res);
        if ($details === false) {
            throw new \RuntimeException('Failed to get public key details');
        }
        // $details['key'] is PEM, we need DER — extract from PEM
        $pem = $details['key'];
        $lines = array_filter(explode("\n", $pem), fn($l) => !str_starts_with($l, '-----'));
        $der = base64_decode(implode('', $lines));
        return hash('sha256', $der);
    }

    // -----------------------------------------------------------------------
    // RSA Key Generation
    // -----------------------------------------------------------------------

    /** Generate an RSA-4096 key pair. Returns [publicKeyPem, privateKeyPem]. */
    public static function generateKeyPair(): array
    {
        $config = [
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
        $res = openssl_pkey_new($config);
        if ($res === false) {
            throw new \RuntimeException('Failed to generate RSA key pair');
        }
        openssl_pkey_export($res, $privateKeyPem);
        $details = openssl_pkey_get_details($res);
        return [$details['key'], $privateKeyPem];
    }

    // -----------------------------------------------------------------------
    // PBKDF2
    // -----------------------------------------------------------------------

    /** Derive a 256-bit key from password + salt using PBKDF2-SHA256 at 600k iterations. */
    public static function deriveKey(string $password, string $salt): string
    {
        return hash_pbkdf2('sha256', $password, $salt, self::PBKDF2_ITERATIONS, self::PBKDF2_KEY_BYTES, true);
    }

    // -----------------------------------------------------------------------
    // AES-256-GCM
    // -----------------------------------------------------------------------

    private static function aesGcmEncrypt(string $key, string $iv, string $plaintext, string $aad): array
    {
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag, $aad, self::AES_TAG_BYTES);
        if ($ciphertext === false) {
            throw new \RuntimeException('AES-GCM encryption failed');
        }
        return [$ciphertext, $tag];
    }

    private static function aesGcmDecrypt(string $key, string $iv, string $ciphertext, string $tag, string $aad): string
    {
        $plaintext = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag, $aad);
        if ($plaintext === false) {
            throw new \RuntimeException('Decryption failed');
        }
        return $plaintext;
    }

    // -----------------------------------------------------------------------
    // RSA-OAEP-SHA256 wrap/unwrap
    // -----------------------------------------------------------------------

    private static function rsaOaepWrap(string $publicKeyPem, string $plaintext): string
    {
        $res = openssl_pkey_get_public($publicKeyPem);
        if ($res === false) {
            throw new \RuntimeException('Invalid public key');
        }
        $encrypted = '';
        $ok = openssl_public_encrypt($plaintext, $encrypted, $res, OPENSSL_PKCS1_OAEP_PADDING);
        if (!$ok) {
            throw new \RuntimeException('RSA-OAEP encryption failed');
        }
        return $encrypted;
    }

    private static function rsaOaepUnwrap(string $privateKeyPem, string $ciphertext): string
    {
        $res = openssl_pkey_get_private($privateKeyPem);
        if ($res === false) {
            throw new \RuntimeException('Decryption failed');
        }
        $decrypted = '';
        $ok = openssl_private_decrypt($ciphertext, $decrypted, $res, OPENSSL_PKCS1_OAEP_PADDING);
        if (!$ok) {
            throw new \RuntimeException('Decryption failed');
        }
        return $decrypted;
    }

    // -----------------------------------------------------------------------
    // Envelope Validation
    // -----------------------------------------------------------------------

    public static function validateMessageEnvelope(array $envelope): bool
    {
        if (($envelope['v'] ?? null) !== 1) return false;
        if (($envelope['alg'] ?? null) !== self::MESSAGE_ALG) return false;
        if (strlen(self::fromBase64url($envelope['ek'] ?? '')) !== self::RSA_EK_BYTES) return false;
        if (strlen(self::fromBase64url($envelope['iv'] ?? '')) !== self::AES_IV_BYTES) return false;
        if (strlen(self::fromBase64url($envelope['tag'] ?? '')) !== self::AES_TAG_BYTES) return false;
        if (empty($envelope['ct'])) return false;
        if (!preg_match('/^[a-f0-9]{64}$/', $envelope['kid'] ?? '')) return false;
        $allowed = ['v', 'alg', 'ek', 'iv', 'ct', 'tag', 'kid'];
        if (count(array_diff(array_keys($envelope), $allowed)) > 0) return false;
        if (count($envelope) !== 7) return false;
        return true;
    }

    public static function validatePrivateKeyEnvelope(array $envelope): bool
    {
        if (($envelope['v'] ?? null) !== 1) return false;
        if (($envelope['alg'] ?? null) !== self::PRIVATE_KEY_ALG) return false;
        if (strlen(self::fromBase64url($envelope['iv'] ?? '')) !== self::AES_IV_BYTES) return false;
        if (strlen(self::fromBase64url($envelope['tag'] ?? '')) !== self::AES_TAG_BYTES) return false;
        if (strlen(self::fromBase64url($envelope['salt'] ?? '')) !== self::PBKDF2_SALT_BYTES) return false;
        if (empty($envelope['ct'])) return false;
        if (!preg_match('/^[a-f0-9]{64}$/', $envelope['kid'] ?? '')) return false;
        $allowed = ['v', 'alg', 'iv', 'ct', 'tag', 'salt', 'kid'];
        if (count(array_diff(array_keys($envelope), $allowed)) > 0) return false;
        if (count($envelope) !== 7) return false;
        return true;
    }

    // -----------------------------------------------------------------------
    // Message Encryption (Section 4.1)
    // -----------------------------------------------------------------------

    /** Encrypt a confirmation message. AAD = "{hitlId}". */
    public static function encryptMessage(
        string $plaintext,
        string $publicKeyPem,
        string $hitlId,
        ?string $aesKey = null,
        ?string $iv = null,
    ): array {
        $aesKey ??= random_bytes(self::AES_KEY_BYTES);
        $iv ??= random_bytes(self::AES_IV_BYTES);
        $aad = $hitlId;

        [$ct, $tag] = self::aesGcmEncrypt($aesKey, $iv, $plaintext, $aad);
        $ek = self::rsaOaepWrap($publicKeyPem, $aesKey);
        $kid = self::computeKid($publicKeyPem);

        return [
            'v' => self::ENVELOPE_VERSION,
            'alg' => self::MESSAGE_ALG,
            'ek' => self::toBase64url($ek),
            'iv' => self::toBase64url($iv),
            'ct' => self::toBase64url($ct),
            'tag' => self::toBase64url($tag),
            'kid' => $kid,
        ];
    }

    /** Decrypt a confirmation message envelope. */
    public static function decryptMessage(
        array $envelope,
        string $privateKeyPem,
        string $hitlId,
    ): string {
        if (!self::validateMessageEnvelope($envelope)) {
            throw new \RuntimeException('Decryption failed');
        }
        $ek = self::fromBase64url($envelope['ek']);
        $iv = self::fromBase64url($envelope['iv']);
        $ct = self::fromBase64url($envelope['ct']);
        $tag = self::fromBase64url($envelope['tag']);
        $aad = $hitlId;

        $aesKey = self::rsaOaepUnwrap($privateKeyPem, $ek);
        return self::aesGcmDecrypt($aesKey, $iv, $ct, $tag, $aad);
    }

    // -----------------------------------------------------------------------
    // Private Key Encryption (Section 4.2)
    // -----------------------------------------------------------------------

    /** Encrypt an RSA private key with a user password for storage. */
    public static function encryptPrivateKey(
        string $privateKeyPem,
        string $password,
        string $publicKeyPem,
        string $hitlId,
        string $userId,
    ): array {
        $salt = random_bytes(self::PBKDF2_SALT_BYTES);
        $derivedKey = self::deriveKey($password, $salt);
        $iv = random_bytes(self::AES_IV_BYTES);
        $aad = "{$hitlId}:{$userId}";

        // Export private key as DER (PKCS#8)
        $res = openssl_pkey_get_private($privateKeyPem);
        openssl_pkey_export($res, $pkcs8Pem);
        // Convert PEM to DER
        $lines = array_filter(explode("\n", $pkcs8Pem), fn($l) => !str_starts_with($l, '-----'));
        $der = base64_decode(implode('', $lines));

        [$ct, $tag] = self::aesGcmEncrypt($derivedKey, $iv, $der, $aad);
        $kid = self::computeKid($publicKeyPem);

        return [
            'v' => self::ENVELOPE_VERSION,
            'alg' => self::PRIVATE_KEY_ALG,
            'iv' => self::toBase64url($iv),
            'ct' => self::toBase64url($ct),
            'tag' => self::toBase64url($tag),
            'salt' => self::toBase64url($salt),
            'kid' => $kid,
        ];
    }

    /** Decrypt an RSA private key using the user's password. Returns PEM string. */
    public static function decryptPrivateKey(
        array $envelope,
        string $password,
        string $hitlId,
        string $userId,
    ): string {
        if (!self::validatePrivateKeyEnvelope($envelope)) {
            throw new \RuntimeException('Decryption failed');
        }
        $salt = self::fromBase64url($envelope['salt']);
        $derivedKey = self::deriveKey($password, $salt);
        $iv = self::fromBase64url($envelope['iv']);
        $ct = self::fromBase64url($envelope['ct']);
        $tag = self::fromBase64url($envelope['tag']);
        $aad = "{$hitlId}:{$userId}";

        $der = self::aesGcmDecrypt($derivedKey, $iv, $ct, $tag, $aad);

        // Convert DER back to PEM
        $b64 = chunk_split(base64_encode($der), 64, "\n");
        return "-----BEGIN PRIVATE KEY-----\n{$b64}-----END PRIVATE KEY-----\n";
    }

    // -----------------------------------------------------------------------
    // Convenience
    // -----------------------------------------------------------------------

    /** Generate RSA-4096 and encrypt private key with password. */
    public static function generateEncryptionKeyPair(
        string $password,
        string $hitlId,
        string $userId,
    ): array {
        [$publicKeyPem, $privateKeyPem] = self::generateKeyPair();
        $encrypted = self::encryptPrivateKey($privateKeyPem, $password, $publicKeyPem, $hitlId, $userId);
        $kid = self::computeKid($publicKeyPem);
        return [
            'public_key_pem' => $publicKeyPem,
            'encrypted_private_key' => $encrypted,
            'kdf_salt' => $encrypted['salt'],
            'kid' => $kid,
        ];
    }
}
