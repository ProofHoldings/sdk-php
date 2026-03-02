<?php

/**
 * Type definitions for the proof.holdings API.
 *
 * AUTO-GENERATED — DO NOT EDIT
 * Source: npm run sdk:generate-types (scripts/generate-sdk-types.ts)
 * Generated from OpenAPI spec via src/openapi/registry.ts
 */

declare(strict_types=1);

namespace ProofHoldings\Types;

// ============================================================================
// Common
// ============================================================================

class APIInfo
{
    public function __construct(
        public readonly string $name,
        public readonly string $version,
        public readonly string $description,
        public readonly array $links,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            version: $data['version'],
            description: $data['description'],
            links: $data['links'],
        );
    }
}

/**
 * Field-level validation error detail
 */
class ErrorDetail
{
    public function __construct(
        public readonly string $field,
        public readonly string $message,
        public readonly string $code,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            field: $data['field'],
            message: $data['message'],
            code: $data['code'],
        );
    }
}

/**
 * Standard error response format
 */
class ErrorResponse
{
    public function __construct(
        public readonly array $error,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            error: $data['error'],
        );
    }
}

/**
 * Detailed health check response
 */
class HealthResponse
{
    public function __construct(
        public readonly string $status,
        public readonly mixed $timestamp,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            timestamp: $data['timestamp'],
        );
    }
}

/**
 * Liveness probe response — always returns ok if the process is alive
 */
class LivenessResponse
{
    public function __construct(
        public readonly string $status,
        public readonly string $timestamp,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            timestamp: $data['timestamp'],
        );
    }
}

class IdParam
{
    public function __construct(
        public readonly string $id,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
        );
    }
}

/**
 * Pagination info in list responses
 */
class PaginationResponse
{
    public function __construct(
        public readonly bool $hasMore,
        public readonly ?string $nextCursor = null,
        public readonly ?int $total = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            hasMore: $data['hasMore'],
            nextCursor: $data['nextCursor'] ?? null,
            total: $data['total'] ?? null,
        );
    }
}

/**
 * Generic success response
 */
class SuccessResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $message = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'],
            message: $data['message'] ?? null,
        );
    }
}

// ============================================================================
// Verification Requests
// ============================================================================

/**
 * Context for custom action types
 */
class ActionContext
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $description = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
        );
    }
}

class CancelRequestResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $status,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'],
            status: $data['status'],
        );
    }
}

class ReferenceIdParam
{
    public function __construct(
        public readonly string $referenceId,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            referenceId: $data['referenceId'],
        );
    }
}

/**
 * Asset to be verified in a multi-asset request
 */
class RequestedAsset
{
    public function __construct(
        public readonly string $type,
        public readonly ?string $identifier = null,
        public readonly mixed $channel = null,
        public readonly ?array $allowed_channels = null,
        public readonly ?bool $required = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            identifier: $data['identifier'] ?? null,
            channel: $data['channel'] ?? null,
            allowed_channels: $data['allowed_channels'] ?? null,
            required: $data['required'] ?? null,
        );
    }
}

// ============================================================================
// Domains
// ============================================================================

/**
 * Request body for adding a domain
 */
class AddDomainRequest
{
    public function __construct(
        public readonly string $domain,
        public readonly ?string $project_id = null,
        public readonly ?bool $for_email_sending = null,
        public readonly ?string $verification_method = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            domain: $data['domain'],
            project_id: $data['project_id'] ?? null,
            for_email_sending: $data['for_email_sending'] ?? null,
            verification_method: $data['verification_method'] ?? null,
        );
    }
}

/**
 * Request body for connecting a Cloudflare API token
 */
class ConnectCloudflareRequest
{
    public function __construct(
        public readonly string $api_token,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            api_token: $data['api_token'],
        );
    }
}

/**
 * Request body for connecting GoDaddy API credentials
 */
class ConnectGoDaddyRequest
{
    public function __construct(
        public readonly string $api_key,
        public readonly string $api_secret,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            api_key: $data['api_key'],
            api_secret: $data['api_secret'],
        );
    }
}

/**
 * Request body for connecting a generic DNS provider
 */
class ConnectProviderRequest
{
    public function __construct(
        public readonly array $credentials,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            credentials: $data['credentials'],
        );
    }
}

/**
 * Domain record
 */
class Domain
{
    public function __construct(
        public readonly string $id,
        public readonly string $domain,
        public readonly string $status,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly ?string $verification_method = null,
        public readonly ?string $provider = null,
        public readonly ?bool $email_sending_enabled = null,
        public readonly ?string $project_id = null,
        public readonly ?string $verified_at = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            domain: $data['domain'],
            status: $data['status'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
            verification_method: $data['verification_method'] ?? null,
            provider: $data['provider'] ?? null,
            email_sending_enabled: $data['email_sending_enabled'] ?? null,
            project_id: $data['project_id'] ?? null,
            verified_at: $data['verified_at'] ?? null,
        );
    }
}

/**
 * Domain verification check result
 */
class DomainCheckResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $domain,
        public readonly string $status,
        public readonly bool $verified,
        public readonly ?string $verified_at,
        public readonly int $check_count,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            domain: $data['domain'],
            status: $data['status'],
            verified: $data['verified'],
            verified_at: $data['verified_at'] ?? null,
            check_count: $data['check_count'],
        );
    }
}

/**
 * Domain verification initiation response
 */
class DomainVerificationResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $domain,
        public readonly string $status,
        public readonly string $verification_method,
        public readonly array $dns_record,
        public readonly array $http_file,
        public readonly array $provider,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            domain: $data['domain'],
            status: $data['status'],
            verification_method: $data['verification_method'],
            dns_record: $data['dns_record'],
            http_file: $data['http_file'],
            provider: $data['provider'],
        );
    }
}

class ProviderParam
{
    public function __construct(
        public readonly string $id,
        public readonly string $provider,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            provider: $data['provider'],
        );
    }
}

/**
 * Request body for setting up email sending on a domain
 */
class SetupEmailSendingRequest
{
    public function __construct(
        public readonly ?string $from_email = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            from_email: $data['from_email'] ?? null,
        );
    }
}

/**
 * Request body for starting B2B domain verification
 */
class StartDomainVerificationRequest
{
    public function __construct(
        public readonly string $domain,
        public readonly ?string $customer_id = null,
        public readonly ?string $verification_method = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            domain: $data['domain'],
            customer_id: $data['customer_id'] ?? null,
            verification_method: $data['verification_method'] ?? null,
        );
    }
}

/**
 * Request body for starting email-based domain verification
 */
class StartEmailVerificationRequest
{
    public function __construct(
        public readonly ?string $email_prefix = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email_prefix: $data['email_prefix'] ?? null,
        );
    }
}

/**
 * Request body for starting user domain verification
 */
class StartUserDomainVerificationRequest
{
    public function __construct(
        public readonly string $domain,
        public readonly ?string $channel = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            domain: $data['domain'],
            channel: $data['channel'] ?? null,
        );
    }
}

// ============================================================================
// Emails
// ============================================================================

/**
 * Request body for adding a new email
 */
class AddEmailRequest
{
    public function __construct(
        public readonly string $email,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
        );
    }
}

/**
 * User email address
 */
class UserEmail
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly bool $is_primary,
        public readonly string $created_at,
        public readonly ?string $verified_at = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            email: $data['email'],
            is_primary: $data['is_primary'],
            created_at: $data['created_at'],
            verified_at: $data['verified_at'] ?? null,
        );
    }
}

/**
 * OTP code submission
 */
class VerifyCodeRequest
{
    public function __construct(
        public readonly string $code,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
        );
    }
}

// ============================================================================
// Phones
// ============================================================================

/**
 * Request body for adding a new phone
 */
class AddPhoneRequest
{
    public function __construct(
        public readonly string $channel,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            channel: $data['channel'],
        );
    }
}

/**
 * User phone number
 */
class UserPhone
{
    public function __construct(
        public readonly string $id,
        public readonly string $phone,
        public readonly bool $is_primary,
        public readonly string $created_at,
        public readonly ?string $verified_at = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            phone: $data['phone'],
            is_primary: $data['is_primary'],
            created_at: $data['created_at'],
            verified_at: $data['verified_at'] ?? null,
        );
    }
}

// ============================================================================
// Assets
// ============================================================================

/**
 * Query parameters for listing assets
 */
class ListAssetsQuery
{
    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $status = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'] ?? null,
            status: $data['status'] ?? null,
        );
    }
}

class AssetIndexParam
{
    public function __construct(
        public readonly string $id,
        public readonly string $assetIndex,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            assetIndex: $data['assetIndex'],
        );
    }
}

/**
 * User verified asset
 */
class UserAsset
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $channel,
        public readonly string $identifier,
        public readonly string $status,
        public readonly string $created_at,
        public readonly ?string $verified_at = null,
        public readonly ?string $expires_at = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            type: $data['type'],
            channel: $data['channel'],
            identifier: $data['identifier'],
            status: $data['status'],
            created_at: $data['created_at'],
            verified_at: $data['verified_at'] ?? null,
            expires_at: $data['expires_at'] ?? null,
        );
    }
}

// ============================================================================
// Auth
// ============================================================================

class AuthSessionIdParam
{
    public function __construct(
        public readonly string $sessionId,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            sessionId: $data['sessionId'],
        );
    }
}

/**
 * Active session entry
 */
class AuthSessionListItem
{
    public function __construct(
        public readonly string $id,
        public readonly string $channel,
        public readonly string $created_at,
        public readonly string $expires_at,
        public readonly bool $is_current,
        public readonly ?string $user_agent = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            channel: $data['channel'],
            created_at: $data['created_at'],
            expires_at: $data['expires_at'],
            is_current: $data['is_current'],
            user_agent: $data['user_agent'] ?? null,
        );
    }
}

/**
 * Auth session response
 */
class AuthSessionResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $channel,
        public readonly string $status,
        public readonly string $expires_at,
        public readonly ?string $deep_link = null,
        public readonly ?string $qr_code = null,
        public readonly ?string $instructions = null,
        public readonly ?string $verified_at = null,
        public readonly ?string $user_id = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            type: $data['type'],
            channel: $data['channel'],
            status: $data['status'],
            expires_at: $data['expires_at'],
            deep_link: $data['deep_link'] ?? null,
            qr_code: $data['qr_code'] ?? null,
            instructions: $data['instructions'] ?? null,
            verified_at: $data['verified_at'] ?? null,
            user_id: $data['user_id'] ?? null,
        );
    }
}

/**
 * Request body for creating a new auth session (login)
 */
class CreateAuthSessionRequest
{
    public function __construct(
        public readonly string $channel,
        public readonly ?string $phone_number = null,
        public readonly ?string $email = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            channel: $data['channel'],
            phone_number: $data['phone_number'] ?? null,
            email: $data['email'] ?? null,
        );
    }
}

/**
 * Request body for exchanging a one-time token for JWT
 */
class ExchangeTokenRequest
{
    public function __construct(
        public readonly string $token,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            token: $data['token'],
        );
    }
}

/**
 * Token exchange response
 */
class ExchangeTokenResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $jwt,
        public readonly string $expiresAt,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'],
            jwt: $data['jwt'],
            expiresAt: $data['expiresAt'],
        );
    }
}

/**
 * List of active sessions
 */
class ListSessionsResponse
{
    public function __construct(
        public readonly array $sessions,
        public readonly int $total,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            sessions: $data['sessions'],
            total: $data['total'],
        );
    }
}

/**
 * Logout all devices response
 */
class LogoutAllResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly int $invalidated,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'],
            message: $data['message'],
            invalidated: $data['invalidated'],
        );
    }
}

/**
 * Authenticated user profile
 */
class AuthUser
{
    public function __construct(
        public readonly string $id,
        public readonly string $created_at,
        public readonly ?array $phone_numbers = null,
        public readonly ?array $emails = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            created_at: $data['created_at'],
            phone_numbers: $data['phone_numbers'] ?? null,
            emails: $data['emails'] ?? null,
        );
    }
}

/**
 * Request body for submitting an auth OTP code
 */
class SubmitAuthCodeRequest
{
    public function __construct(
        public readonly string $code,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
        );
    }
}

class TokenParam
{
    public function __construct(
        public readonly string $token,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            token: $data['token'],
        );
    }
}

/**
 * Transfer token generation response
 */
class TransferTokenResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $token,
        public readonly string $url,
        public readonly int $expiresIn,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'],
            token: $data['token'],
            url: $data['url'],
            expiresIn: $data['expiresIn'],
        );
    }
}

// ============================================================================
// Billing
// ============================================================================

/**
 * Request body for creating a Stripe checkout session
 */
class CheckoutRequest
{
    public function __construct(
        public readonly string $plan,
        public readonly string $success_url,
        public readonly string $cancel_url,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            plan: $data['plan'],
            success_url: $data['success_url'],
            cancel_url: $data['cancel_url'],
        );
    }
}

/**
 * Checkout session creation response
 */
class CheckoutResponse
{
    public function __construct(
        public readonly string $url,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'],
        );
    }
}

/**
 * Request body for creating a Stripe billing portal session
 */
class PortalRequest
{
    public function __construct(
        public readonly string $return_url,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            return_url: $data['return_url'],
        );
    }
}

/**
 * Billing portal session creation response
 */
class PortalResponse
{
    public function __construct(
        public readonly string $url,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'],
        );
    }
}

// ============================================================================
// Settings
// ============================================================================

/**
 * Branding settings
 */
class BrandingSettings
{
    public function __construct(
        public readonly ?string $business_name = null,
        public readonly ?string $logo_url = null,
        public readonly ?string $primary_color = null,
        public readonly ?string $support_email = null,
        public readonly ?string $email_theme = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            business_name: $data['business_name'] ?? null,
            logo_url: $data['logo_url'] ?? null,
            primary_color: $data['primary_color'] ?? null,
            support_email: $data['support_email'] ?? null,
            email_theme: $data['email_theme'] ?? null,
        );
    }
}

/**
 * Request body for updating user settings
 */
class UpdateSettingsRequest
{
    public function __construct(
        public readonly ?BrandingSettings $branding = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            branding: isset($data['branding']) ? BrandingSettings::fromArray($data['branding']) : null,
        );
    }
}

/**
 * Usage query parameters
 */
class UsageQueryParams
{
    public function __construct(
        public readonly ?string $period = null,
        public readonly ?int $months = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            period: $data['period'] ?? null,
            months: $data['months'] ?? null,
        );
    }
}

// ============================================================================
// Verifications
// ============================================================================

/**
 * Challenge details for verification
 */
class Challenge
{
    public function __construct(
        public readonly string $code,
        public readonly string $instruction,
        public readonly string $expires_at,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
            instruction: $data['instruction'],
            expires_at: $data['expires_at'],
        );
    }
}

/**
 * DNS provider credentials for auto-DNS verification
 */
class DNSProviderCredentials
{
    public function __construct(
        public readonly string $provider,
        public readonly ?string $api_token = null,
        public readonly ?string $api_key = null,
        public readonly ?string $api_secret = null,
        public readonly ?string $account_id = null,
        public readonly ?string $username = null,
        public readonly ?string $access_key_id = null,
        public readonly ?string $secret_access_key = null,
        public readonly ?string $region = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            provider: $data['provider'],
            api_token: $data['api_token'] ?? null,
            api_key: $data['api_key'] ?? null,
            api_secret: $data['api_secret'] ?? null,
            account_id: $data['account_id'] ?? null,
            username: $data['username'] ?? null,
            access_key_id: $data['access_key_id'] ?? null,
            secret_access_key: $data['secret_access_key'] ?? null,
            region: $data['region'] ?? null,
        );
    }
}

/**
 * Response after resending verification email
 */
class ResendVerificationResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly string $expires_at,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'],
            message: $data['message'],
            expires_at: $data['expires_at'],
        );
    }
}

/**
 * Request body for submitting a challenge code
 */
class SubmitChallengeRequest
{
    public function __construct(
        public readonly string $challenge_code,
        public readonly ?string $source_identifier = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            challenge_code: $data['challenge_code'],
            source_identifier: $data['source_identifier'] ?? null,
        );
    }
}

/**
 * Request body for submitting a verification code
 */
class SubmitVerificationCodeRequest
{
    public function __construct(
        public readonly string $code,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
        );
    }
}

class VerificationRequestListQuery
{
    public function __construct(
        public readonly ?int $page = null,
        public readonly ?int $limit = null,
        public readonly ?string $status = null,
        public readonly ?string $reference_id = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            page: $data['page'] ?? null,
            limit: $data['limit'] ?? null,
            status: $data['status'] ?? null,
            reference_id: $data['reference_id'] ?? null,
        );
    }
}

/**
 * Request body for creating a new verification
 */
class CreateVerificationRequest
{
    public function __construct(
        public readonly string $type,
        public readonly string $channel,
        public readonly string $identifier,
        public readonly ?string $external_user_id = null,
        public readonly ?array $client_metadata = null,
        public readonly mixed $dns_provider = null,
        public readonly ?string $email_prefix = null,
        public readonly ?string $bot_token = null,
        public readonly ?array $waba_credentials = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            channel: $data['channel'],
            identifier: $data['identifier'],
            external_user_id: $data['external_user_id'] ?? null,
            client_metadata: $data['client_metadata'] ?? null,
            dns_provider: $data['dns_provider'] ?? null,
            email_prefix: $data['email_prefix'] ?? null,
            bot_token: $data['bot_token'] ?? null,
            waba_credentials: $data['waba_credentials'] ?? null,
        );
    }
}

/**
 * Request body for creating a multi-asset verification request
 */
class CreateVerificationRequestBody
{
    public function __construct(
        public readonly array $assets,
        public readonly ?string $reference_id = null,
        public readonly mixed $project_id = null,
        public readonly mixed $public_profile_id = null,
        public readonly mixed $action_type = null,
        public readonly ?ActionContext $action_context = null,
        public readonly ?string $validity_requirement = null,
        public readonly ?bool $partial_ok = null,
        public readonly ?string $redirect_url = null,
        public readonly ?string $callback_url = null,
        public readonly ?int $expires_in = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            assets: $data['assets'],
            reference_id: $data['reference_id'] ?? null,
            project_id: $data['project_id'] ?? null,
            public_profile_id: $data['public_profile_id'] ?? null,
            action_type: $data['action_type'] ?? null,
            action_context: isset($data['action_context']) ? ActionContext::fromArray($data['action_context']) : null,
            validity_requirement: $data['validity_requirement'] ?? null,
            partial_ok: $data['partial_ok'] ?? null,
            redirect_url: $data['redirect_url'] ?? null,
            callback_url: $data['callback_url'] ?? null,
            expires_in: $data['expires_in'] ?? null,
        );
    }
}

class TestVerifyResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $channel,
        public readonly string $status,
        public readonly string $identifier,
        public readonly string $verified_at,
        public readonly string $proof_token,
        public readonly string $proof_expires_at,
        public readonly string $test_mode,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            type: $data['type'],
            channel: $data['channel'],
            status: $data['status'],
            identifier: $data['identifier'],
            verified_at: $data['verified_at'],
            proof_token: $data['proof_token'],
            proof_expires_at: $data['proof_expires_at'],
            test_mode: $data['test_mode'],
        );
    }
}

/**
 * Verification object
 */
class Verification
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $channel,
        public readonly string $identifier,
        public readonly string $status,
        public readonly int $attempts,
        public readonly int $max_attempts,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly ?Challenge $challenge = null,
        public readonly ?Proof $proof = null,
        public readonly ?string $external_user_id = null,
        public readonly ?string $verified_at = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            type: $data['type'],
            channel: $data['channel'],
            identifier: $data['identifier'],
            status: $data['status'],
            attempts: $data['attempts'],
            max_attempts: $data['max_attempts'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
            challenge: isset($data['challenge']) ? Challenge::fromArray($data['challenge']) : null,
            proof: isset($data['proof']) ? Proof::fromArray($data['proof']) : null,
            external_user_id: $data['external_user_id'] ?? null,
            verified_at: $data['verified_at'] ?? null,
        );
    }
}

class VerificationListQuery
{
    public function __construct(
        public readonly ?int $page = null,
        public readonly ?int $limit = null,
        public readonly ?string $status = null,
        public readonly ?string $type = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            page: $data['page'] ?? null,
            limit: $data['limit'] ?? null,
            status: $data['status'] ?? null,
            type: $data['type'] ?? null,
        );
    }
}

/**
 * Paginated list of verifications
 */
class VerificationListResponse
{
    public function __construct(
        public readonly array $data,
        public readonly PaginationResponse $pagination,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            data: $data['data'],
            pagination: PaginationResponse::fromArray($data['pagination']),
        );
    }
}

/**
 * Multi-asset verification request
 */
class VerificationRequest
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly array $assets,
        public readonly string $action_type,
        public readonly bool $partial_ok,
        public readonly string $expires_at,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly ?string $reference_id = null,
        public readonly ?string $redirect_url = null,
        public readonly ?string $completed_at = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            status: $data['status'],
            assets: $data['assets'],
            action_type: $data['action_type'],
            partial_ok: $data['partial_ok'],
            expires_at: $data['expires_at'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
            reference_id: $data['reference_id'] ?? null,
            redirect_url: $data['redirect_url'] ?? null,
            completed_at: $data['completed_at'] ?? null,
        );
    }
}

class VerificationRequestListResponse
{
    public function __construct(
        public readonly array $data,
        public readonly array $pagination,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            data: $data['data'],
            pagination: $data['pagination'],
        );
    }
}

// ============================================================================
// Profiles
// ============================================================================

/**
 * Request body for claiming a username
 */
class ClaimUsernameRequest
{
    public function __construct(
        public readonly string $username,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'],
        );
    }
}

/**
 * Custom link on a public profile
 */
class CustomLink
{
    public function __construct(
        public readonly string $title,
        public readonly string $url,
        public readonly ?string $icon = null,
        public readonly ?bool $is_visible = null,
        public readonly ?int $display_order = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            url: $data['url'],
            icon: $data['icon'] ?? null,
            is_visible: $data['is_visible'] ?? null,
            display_order: $data['display_order'] ?? null,
        );
    }
}

class ProfileIdParam
{
    public function __construct(
        public readonly string $profileId,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            profileId: $data['profileId'],
        );
    }
}

/**
 * Proof display configuration for a profile
 */
class ProfileProofEntry
{
    public function __construct(
        public readonly string $asset_id,
        public readonly ?bool $is_visible = null,
        public readonly ?string $mask_level = null,
        public readonly ?int $display_order = null,
        public readonly ?string $label = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            asset_id: $data['asset_id'],
            is_visible: $data['is_visible'] ?? null,
            mask_level: $data['mask_level'] ?? null,
            display_order: $data['display_order'] ?? null,
            label: $data['label'] ?? null,
        );
    }
}

/**
 * Profile theme customization
 */
class ProfileTheme
{
    public function __construct(
        public readonly ?string $primary_color = null,
        public readonly ?string $background_type = null,
        public readonly ?string $background_color = null,
        public readonly ?string $gradient_start = null,
        public readonly ?string $gradient_end = null,
        public readonly ?string $font_style = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            primary_color: $data['primary_color'] ?? null,
            background_type: $data['background_type'] ?? null,
            background_color: $data['background_color'] ?? null,
            gradient_start: $data['gradient_start'] ?? null,
            gradient_end: $data['gradient_end'] ?? null,
            font_style: $data['font_style'] ?? null,
        );
    }
}

/**
 * Request body for creating a new profile
 */
class CreateProfileRequest
{
    public function __construct(
        public readonly ?string $display_name = null,
        public readonly ?string $bio = null,
        public readonly ?string $avatar_url = null,
        public readonly ?string $business_name = null,
        public readonly ?bool $is_business = null,
        public readonly ?bool $is_public = null,
        public readonly ?bool $is_primary = null,
        public readonly ?ProfileTheme $theme = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            display_name: $data['display_name'] ?? null,
            bio: $data['bio'] ?? null,
            avatar_url: $data['avatar_url'] ?? null,
            business_name: $data['business_name'] ?? null,
            is_business: $data['is_business'] ?? null,
            is_public: $data['is_public'] ?? null,
            is_primary: $data['is_primary'] ?? null,
            theme: isset($data['theme']) ? ProfileTheme::fromArray($data['theme']) : null,
        );
    }
}

/**
 * Request body for updating which proofs are displayed on a profile
 */
class UpdateProfileProofsRequest
{
    public function __construct(
        public readonly array $proofs,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            proofs: $data['proofs'],
        );
    }
}

/**
 * Request body for updating a profile
 */
class UpdateProfileRequest
{
    public function __construct(
        public readonly ?string $display_name = null,
        public readonly ?string $bio = null,
        public readonly ?string $avatar_url = null,
        public readonly ?string $business_name = null,
        public readonly ?bool $is_business = null,
        public readonly ?bool $is_public = null,
        public readonly ?bool $is_primary = null,
        public readonly ?bool $show_verification_dates = null,
        public readonly ?bool $show_proof_channels = null,
        public readonly ?ProfileTheme $theme = null,
        public readonly ?array $custom_links = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            display_name: $data['display_name'] ?? null,
            bio: $data['bio'] ?? null,
            avatar_url: $data['avatar_url'] ?? null,
            business_name: $data['business_name'] ?? null,
            is_business: $data['is_business'] ?? null,
            is_public: $data['is_public'] ?? null,
            is_primary: $data['is_primary'] ?? null,
            show_verification_dates: $data['show_verification_dates'] ?? null,
            show_proof_channels: $data['show_proof_channels'] ?? null,
            theme: isset($data['theme']) ? ProfileTheme::fromArray($data['theme']) : null,
            custom_links: $data['custom_links'] ?? null,
        );
    }
}

class UsernameParam
{
    public function __construct(
        public readonly string $username,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            username: $data['username'],
        );
    }
}

/**
 * Public profile
 */
class PublicProfile
{
    public function __construct(
        public readonly string $id,
        public readonly bool $is_public,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly ?string $display_name = null,
        public readonly ?string $bio = null,
        public readonly ?string $avatar_url = null,
        public readonly ?string $business_name = null,
        public readonly ?bool $is_business = null,
        public readonly ?bool $is_primary = null,
        public readonly ?string $username = null,
        public readonly ?ProfileTheme $theme = null,
        public readonly ?array $custom_links = null,
        public readonly ?array $proofs = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            is_public: $data['is_public'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
            display_name: $data['display_name'] ?? null,
            bio: $data['bio'] ?? null,
            avatar_url: $data['avatar_url'] ?? null,
            business_name: $data['business_name'] ?? null,
            is_business: $data['is_business'] ?? null,
            is_primary: $data['is_primary'] ?? null,
            username: $data['username'] ?? null,
            theme: isset($data['theme']) ? ProfileTheme::fromArray($data['theme']) : null,
            custom_links: $data['custom_links'] ?? null,
            proofs: $data['proofs'] ?? null,
        );
    }
}

// ============================================================================
// API Keys
// ============================================================================

/**
 * Request body for creating an API key
 */
class CreateAPIKeyRequest
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $environment = null,
        public readonly ?array $scopes = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            environment: $data['environment'] ?? null,
            scopes: $data['scopes'] ?? null,
        );
    }
}

/**
 * API key (key value only shown on creation)
 */
class APIKeyResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $key_prefix,
        public readonly string $environment,
        public readonly array $scopes,
        public readonly string $created_at,
        public readonly ?string $last_used_at = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            key_prefix: $data['key_prefix'],
            environment: $data['environment'],
            scopes: $data['scopes'],
            created_at: $data['created_at'],
            last_used_at: $data['last_used_at'] ?? null,
        );
    }
}

// ============================================================================
// DNS Credentials
// ============================================================================

/**
 * Request body for creating stored DNS provider credentials
 */
class CreateProviderCredentialRequest
{
    public function __construct(
        public readonly string $provider,
        public readonly array $credentials,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            provider: $data['provider'],
            credentials: $data['credentials'],
        );
    }
}

// ============================================================================
// Sessions
// ============================================================================

/**
 * Request body for creating a phone verification session
 */
class CreateSessionRequest
{
    public function __construct(
        public readonly string $phone,
        public readonly ?string $channel = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            phone: $data['phone'],
            channel: $data['channel'] ?? null,
        );
    }
}

/**
 * Phone verification session
 */
class Session
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $created_at,
        public readonly string $expires_at,
        public readonly ?Challenge $challenge = null,
        public readonly ?Proof $proof = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            status: $data['status'],
            created_at: $data['created_at'],
            expires_at: $data['expires_at'],
            challenge: isset($data['challenge']) ? Challenge::fromArray($data['challenge']) : null,
            proof: isset($data['proof']) ? Proof::fromArray($data['proof']) : null,
        );
    }
}

class SessionIdParam
{
    public function __construct(
        public readonly string $id,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
        );
    }
}

// ============================================================================
// Account
// ============================================================================

/**
 * Request body for final account deletion
 */
class DeleteAccountRequest
{
    public function __construct(
        public readonly string $session_id,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            session_id: $data['session_id'],
        );
    }
}

/**
 * Request body for initiating account deletion
 */
class InitiateAccountDeletionRequest
{
    public function __construct(
        public readonly ?string $channel = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            channel: $data['channel'] ?? null,
        );
    }
}

// ============================================================================
// Verified Users
// ============================================================================

class ExternalUserIdParam
{
    public function __construct(
        public readonly string $externalUserId,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            externalUserId: $data['externalUserId'],
        );
    }
}

/**
 * Detailed verification info for a specific external user
 */
class VerifiedUserDetail
{
    public function __construct(
        public readonly string $external_user_id,
        public readonly int $verification_count,
        public readonly array $types_verified,
        public readonly array $verifications,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            external_user_id: $data['external_user_id'],
            verification_count: $data['verification_count'],
            types_verified: $data['types_verified'],
            verifications: $data['verifications'],
        );
    }
}

/**
 * Aggregated verification summary for an external user
 */
class VerifiedUserSummary
{
    public function __construct(
        public readonly string $external_user_id,
        public readonly int $verification_count,
        public readonly array $types_verified,
        public readonly array $verifications,
        public readonly ?string $first_verified_at = null,
        public readonly ?string $last_verified_at = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            external_user_id: $data['external_user_id'],
            verification_count: $data['verification_count'],
            types_verified: $data['types_verified'],
            verifications: $data['verifications'],
            first_verified_at: $data['first_verified_at'] ?? null,
            last_verified_at: $data['last_verified_at'] ?? null,
        );
    }
}

/**
 * Paginated list of verified users
 */
class VerifiedUserListResponse
{
    public function __construct(
        public readonly array $data,
        public readonly array $pagination,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            data: $data['data'],
            pagination: $data['pagination'],
        );
    }
}

// ============================================================================
// Proofs
// ============================================================================

/**
 * JSON Web Key
 */
class JWK
{
    public function __construct(
        public readonly string $kty,
        public readonly string $n,
        public readonly string $e,
        public readonly string $use,
        public readonly string $alg,
        public readonly string $kid,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            kty: $data['kty'],
            n: $data['n'],
            e: $data['e'],
            use: $data['use'],
            alg: $data['alg'],
            kid: $data['kid'],
        );
    }
}

/**
 * JSON Web Key Set for offline proof verification
 */
class JWKS
{
    public function __construct(
        public readonly array $keys,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            keys: $data['keys'],
        );
    }
}

/**
 * Proof token after successful verification
 */
class Proof
{
    public function __construct(
        public readonly string $token,
        public readonly string $expires_at,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            token: $data['token'],
            expires_at: $data['expires_at'],
        );
    }
}

/**
 * Request body for revoking a proof
 */
class RevokeProofRequest
{
    public function __construct(
        public readonly mixed $reason = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            reason: $data['reason'] ?? null,
        );
    }
}

class RevokeProofResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $revoked_at,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'],
            revoked_at: $data['revoked_at'],
        );
    }
}

/**
 * Entry in the revocation list
 */
class RevokedProof
{
    public function __construct(
        public readonly string $proof_id,
        public readonly string $revoked_at,
        public readonly string $reason,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            proof_id: $data['proof_id'],
            revoked_at: $data['revoked_at'],
            reason: $data['reason'],
        );
    }
}

/**
 * Signed list of revoked proofs
 */
class RevocationList
{
    public function __construct(
        public readonly array $revoked,
        public readonly string $generated_at,
        public readonly string $signature,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            revoked: $data['revoked'],
            generated_at: $data['generated_at'],
            signature: $data['signature'],
        );
    }
}

/**
 * Request body for validating a proof token
 */
class ValidateProofRequest
{
    public function __construct(
        public readonly string $proof_token,
        public readonly ?string $identifier = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            proof_token: $data['proof_token'],
            identifier: $data['identifier'] ?? null,
        );
    }
}

/**
 * Proof status check result
 */
class ProofStatusResponse
{
    public function __construct(
        public readonly string $proof_id,
        public readonly string $status,
        public readonly bool $is_valid,
        public readonly bool $is_revoked,
        public readonly ?string $revoked_at,
        public readonly ?string $revoked_reason,
        public readonly ?string $expires_at,
        public readonly bool $is_expired,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            proof_id: $data['proof_id'],
            status: $data['status'],
            is_valid: $data['is_valid'],
            is_revoked: $data['is_revoked'],
            revoked_at: $data['revoked_at'] ?? null,
            revoked_reason: $data['revoked_reason'] ?? null,
            expires_at: $data['expires_at'] ?? null,
            is_expired: $data['is_expired'],
        );
    }
}

/**
 * Proof validation result
 */
class ValidateProofResponse
{
    public function __construct(
        public readonly bool $valid,
        public readonly ?array $proof = null,
        public readonly ?string $error = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            valid: $data['valid'],
            proof: $data['proof'] ?? null,
            error: $data['error'] ?? null,
        );
    }
}

// ============================================================================
// Templates
// ============================================================================

class ChannelTypeParam
{
    public function __construct(
        public readonly string $id,
        public readonly string $channel,
        public readonly string $type,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            channel: $data['channel'],
            type: $data['type'],
        );
    }
}

/**
 * Message template
 */
class Template
{
    public function __construct(
        public readonly string $channel,
        public readonly string $message_type,
        public readonly string $body,
        public readonly ?string $id = null,
        public readonly ?string $subject = null,
        public readonly ?string $button_text = null,
        public readonly ?string $button_url_template = null,
        public readonly ?bool $is_active = null,
        public readonly ?int $version = null,
        public readonly ?string $updated_at = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            channel: $data['channel'],
            message_type: $data['message_type'],
            body: $data['body'],
            id: $data['id'] ?? null,
            subject: $data['subject'] ?? null,
            button_text: $data['button_text'] ?? null,
            button_url_template: $data['button_url_template'] ?? null,
            is_active: $data['is_active'] ?? null,
            version: $data['version'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }
}

/**
 * Template content fields
 */
class TemplateContent
{
    public function __construct(
        public readonly string $body,
        public readonly ?string $subject = null,
        public readonly ?string $button_text = null,
        public readonly ?string $button_url_template = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            body: $data['body'],
            subject: $data['subject'] ?? null,
            button_text: $data['button_text'] ?? null,
            button_url_template: $data['button_url_template'] ?? null,
        );
    }
}

class TemplatePathParam
{
    public function __construct(
        public readonly string $channel,
        public readonly string $messageType,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            channel: $data['channel'],
            messageType: $data['messageType'],
        );
    }
}

/**
 * Request body for previewing a template
 */
class TemplatePreviewRequest
{
    public function __construct(
        public readonly string $channel,
        public readonly string $message_type,
        public readonly string $body,
        public readonly ?string $subject = null,
        public readonly ?string $button_text = null,
        public readonly ?string $button_url_template = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            channel: $data['channel'],
            message_type: $data['message_type'],
            body: $data['body'],
            subject: $data['subject'] ?? null,
            button_text: $data['button_text'] ?? null,
            button_url_template: $data['button_url_template'] ?? null,
        );
    }
}

/**
 * Request body for rendering a template with variables
 */
class TemplateRenderRequest
{
    public function __construct(
        public readonly string $channel,
        public readonly string $message_type,
        public readonly ?array $variables = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            channel: $data['channel'],
            message_type: $data['message_type'],
            variables: $data['variables'] ?? null,
        );
    }
}

/**
 * Available template variable
 */
class TemplateVariable
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'],
        );
    }
}

// ============================================================================
// Projects
// ============================================================================

/**
 * Project branding settings
 */
class ProjectBranding
{
    public function __construct(
        public readonly ?string $business_name = null,
        public readonly ?string $logo_url = null,
        public readonly ?string $primary_color = null,
        public readonly ?string $support_email = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            business_name: $data['business_name'] ?? null,
            logo_url: $data['logo_url'] ?? null,
            primary_color: $data['primary_color'] ?? null,
            support_email: $data['support_email'] ?? null,
        );
    }
}

/**
 * Request body for creating a project
 */
class CreateProjectRequest
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?ProjectBranding $branding = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            description: $data['description'] ?? null,
            branding: isset($data['branding']) ? ProjectBranding::fromArray($data['branding']) : null,
        );
    }
}

/**
 * Project callback URLs
 */
class ProjectCallbacks
{
    public function __construct(
        public readonly ?string $success_url = null,
        public readonly ?string $failure_url = null,
        public readonly ?string $cancel_url = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success_url: $data['success_url'] ?? null,
            failure_url: $data['failure_url'] ?? null,
            cancel_url: $data['cancel_url'] ?? null,
        );
    }
}

/**
 * Project
 */
class Project
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly ?string $description = null,
        public readonly ?ProjectBranding $branding = null,
        public readonly ?ProjectCallbacks $callbacks = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
            description: $data['description'] ?? null,
            branding: isset($data['branding']) ? ProjectBranding::fromArray($data['branding']) : null,
            callbacks: isset($data['callbacks']) ? ProjectCallbacks::fromArray($data['callbacks']) : null,
        );
    }
}

/**
 * Request body for updating a project
 */
class UpdateProjectRequest
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?ProjectBranding $branding = null,
        public readonly ?ProjectCallbacks $callbacks = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            branding: isset($data['branding']) ? ProjectBranding::fromArray($data['branding']) : null,
            callbacks: isset($data['callbacks']) ? ProjectCallbacks::fromArray($data['callbacks']) : null,
        );
    }
}

// ============================================================================
// Verify Flow
// ============================================================================

/**
 * Request body for starting asset verification in a verification request
 */
class StartAssetVerificationRequest
{
    public function __construct(
        public readonly string $channel,
        public readonly ?string $identifier = null,
        public readonly ?string $bot_token = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            channel: $data['channel'],
            identifier: $data['identifier'] ?? null,
            bot_token: $data['bot_token'] ?? null,
        );
    }
}

/**
 * Request body for creating a demo verification request
 */
class TryItRequest
{
    public function __construct(
        public readonly string $type,
        public readonly string $identifier,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            identifier: $data['identifier'],
        );
    }
}

// ============================================================================
// 2FA
// ============================================================================

/**
 * Request body for starting 2FA verification
 */
class Start2FARequest
{
    public function __construct(
        public readonly string $action_type,
        public readonly string $channel,
        public readonly ?string $email_id = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            action_type: $data['action_type'],
            channel: $data['channel'],
            email_id: $data['email_id'] ?? null,
        );
    }
}

// ============================================================================
// Webhook Deliveries
// ============================================================================

/**
 * Webhook delivery statistics
 */
class WebhookDeliveryStats
{
    public function __construct(
        public readonly int $total,
        public readonly int $delivered,
        public readonly int $failed,
        public readonly int $pending,
        public readonly float $success_rate,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            total: $data['total'],
            delivered: $data['delivered'],
            failed: $data['failed'],
            pending: $data['pending'],
            success_rate: $data['success_rate'],
        );
    }
}

/**
 * Webhook delivery record
 */
class WebhookDelivery
{
    public function __construct(
        public readonly string $id,
        public readonly string $event_type,
        public readonly string $url,
        public readonly string $status,
        public readonly int $attempts,
        public readonly int $max_attempts,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly ?string $verification_request_id = null,
        public readonly ?string $last_attempt_at = null,
        public readonly ?string $next_retry_at = null,
        public readonly ?int $response_status = null,
        public readonly ?string $response_body = null,
        public readonly ?string $error_message = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            event_type: $data['event_type'],
            url: $data['url'],
            status: $data['status'],
            attempts: $data['attempts'],
            max_attempts: $data['max_attempts'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
            verification_request_id: $data['verification_request_id'] ?? null,
            last_attempt_at: $data['last_attempt_at'] ?? null,
            next_retry_at: $data['next_retry_at'] ?? null,
            response_status: $data['response_status'] ?? null,
            response_body: $data['response_body'] ?? null,
            error_message: $data['error_message'] ?? null,
        );
    }
}

class RetryDeliveryResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly WebhookDelivery $delivery,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'],
            delivery: WebhookDelivery::fromArray($data['delivery']),
        );
    }
}

class WebhookDeliveryListQuery
{
    public function __construct(
        public readonly ?int $page = null,
        public readonly ?int $limit = null,
        public readonly ?string $status = null,
        public readonly ?string $event_type = null,
        public readonly mixed $verification_request_id = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            page: $data['page'] ?? null,
            limit: $data['limit'] ?? null,
            status: $data['status'] ?? null,
            event_type: $data['event_type'] ?? null,
            verification_request_id: $data['verification_request_id'] ?? null,
        );
    }
}

class WebhookDeliveryListResponse
{
    public function __construct(
        public readonly array $data,
        public readonly array $pagination,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            data: $data['data'],
            pagination: $data['pagination'],
        );
    }
}
