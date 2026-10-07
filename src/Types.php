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
        public readonly ?int $page = null,
        public readonly ?int $limit = null,
        public readonly ?int $total = null,
        public readonly ?int $total_pages = null,
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
            total: $data['total'] ?? null,
            total_pages: $data['total_pages'] ?? null,
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
// Other
// ============================================================================

class AccountSessionIdParam
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
 * One-call account snapshot for the dashboard home and AI agents
 */
class AccountSummary
{
    public function __construct(
        public readonly string $as_of,
        public readonly float $cache_age_seconds,
        public readonly array $quota,
        public readonly array $request_health,
        public readonly array $top_channels,
        public readonly array $channels_with_zero_completions,
        public readonly float $active_api_keys,
        public readonly ?array $next_action,
        public readonly ?array $hitl_channels = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            as_of: $data['as_of'],
            cache_age_seconds: $data['cache_age_seconds'],
            quota: $data['quota'],
            request_health: $data['request_health'],
            top_channels: $data['top_channels'],
            channels_with_zero_completions: $data['channels_with_zero_completions'],
            active_api_keys: $data['active_api_keys'],
            next_action: $data['next_action'] ?? null,
            hitl_channels: $data['hitl_channels'] ?? null,
        );
    }
}

/**
 * Request body for declaring a channel for a Circle member
 */
class AddCircleMemberChannelBody
{
    public function __construct(
        public readonly string $channel,
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
            channel: $data['channel'],
            identifier: $data['identifier'],
        );
    }
}

/**
 * Per-API-key usage query parameters
 */
class ApiKeyUsageQueryParams
{
    public function __construct(
        public readonly ?string $environment = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            environment: $data['environment'] ?? null,
        );
    }
}

/**
 * Regenerated approval link for a pending confirmation
 */
class ApprovalLinkResponse
{
    public function __construct(
        public readonly string $approval_url,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            approval_url: $data['approval_url'],
        );
    }
}

/**
 * Account-session response. Same wire format whether the request created a new account or pivoted to login — the `type` field distinguishes the outcome.
 */
class AccountSessionResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $channel,
        public readonly string $status,
        public readonly string $expires_at,
        public readonly ?string $deep_link = null,
        public readonly ?string $qr_code = null,
        public readonly ?string $qr_text = null,
        public readonly ?string $sms_code = null,
        public readonly ?string $sms_message = null,
        public readonly ?string $email = null,
        public readonly ?bool $email_sent = null,
        public readonly ?string $email_error = null,
        public readonly ?string $instructions = null,
        public readonly ?string $verified_at = null,
        public readonly ?string $user_id = null,
        public readonly ?string $created_at = null,
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
            qr_text: $data['qr_text'] ?? null,
            sms_code: $data['sms_code'] ?? null,
            sms_message: $data['sms_message'] ?? null,
            email: $data['email'] ?? null,
            email_sent: $data['email_sent'] ?? null,
            email_error: $data['email_error'] ?? null,
            instructions: $data['instructions'] ?? null,
            verified_at: $data['verified_at'] ?? null,
            user_id: $data['user_id'] ?? null,
            created_at: $data['created_at'] ?? null,
        );
    }
}

/**
 * Chat ID discovery poll result
 */
class ChatIdDiscoveryPollResponse
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $chat_id = null,
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
            chat_id: $data['chat_id'] ?? null,
        );
    }
}

/**
 * Chat ID discovery token and Telegram deeplink
 */
class ChatIdDiscoveryResponse
{
    public function __construct(
        public readonly string $token,
        public readonly string $deep_link,
        public readonly string $qr_code,
        public readonly string $qr_text,
        public readonly int $expires_in,
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
            deep_link: $data['deep_link'],
            qr_code: $data['qr_code'],
            qr_text: $data['qr_text'],
            expires_in: $data['expires_in'],
        );
    }
}

/**
 * A Circle member to create (name + optional declared channels)
 */
class CircleMemberInput
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $telegram_chat_id = null,
        public readonly ?string $whatsapp_phone = null,
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
            telegram_chat_id: $data['telegram_chat_id'] ?? null,
            whatsapp_phone: $data['whatsapp_phone'] ?? null,
        );
    }
}

/**
 * Request body for creating an account-bootstrap session. The server transparently pivots to a login session when the submitted identity already belongs to an active User, so the agent polls identically regardless of outcome.
 */
class CreateAccountRequest
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
 * Request body for adding a member to a Circle
 */
class CreateCircleMemberBody
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $whatsapp_phone = null,
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
            whatsapp_phone: $data['whatsapp_phone'] ?? null,
        );
    }
}

/**
 * Typed delegate identifier
 */
class DelegateIdentifier
{
    public function __construct(
        public readonly string $type,
        public readonly string $value,
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
            value: $data['value'],
        );
    }
}

/**
 * Create a delegation from a proven domain to an artifact
 */
class CreateDelegationRequest
{
    public function __construct(
        public readonly string $control_proof,
        public readonly DelegateIdentifier $delegate,
        public readonly array $scope,
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
            control_proof: $data['control_proof'],
            delegate: DelegateIdentifier::fromArray($data['delegate']),
            scope: $data['scope'],
            expires_in: $data['expires_in'] ?? null,
        );
    }
}

/**
 * Query parameters for listing HITL configurations
 */
class HitlListQuery
{
    public function __construct(
        public readonly ?int $page = null,
        public readonly ?int $limit = null,
        public readonly ?string $status = null,
        public readonly ?string $environment = null,
        public readonly ?string $profile_id = null,
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
            environment: $data['environment'] ?? null,
            profile_id: $data['profile_id'] ?? null,
        );
    }
}

/**
 * Create a cross-channel identity challenge (Proof of Me)
 */
class IdentityChallengeCreate
{
    public function __construct(
        public readonly string $circle_id,
        public readonly string $member_id,
        public readonly ?string $suspicious_channel = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            circle_id: $data['circle_id'],
            member_id: $data['member_id'],
            suspicious_channel: $data['suspicious_channel'] ?? null,
        );
    }
}

/**
 * An identity challenge (Proof of Me)
 */
class IdentityChallengeResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly ?string $channel = null,
        public readonly ?string $confirm_channel = null,
        public readonly ?bool $single_channel_warning = null,
        public readonly ?string $circle_id = null,
        public readonly ?bool $is_drill = null,
        public readonly ?string $proof_id = null,
        public readonly ?string $proof_token = null,
        public readonly ?string $created_at = null,
        public readonly ?string $resolved_at = null,
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
            status: $data['status'],
            channel: $data['channel'] ?? null,
            confirm_channel: $data['confirm_channel'] ?? null,
            single_channel_warning: $data['single_channel_warning'] ?? null,
            circle_id: $data['circle_id'] ?? null,
            is_drill: $data['is_drill'] ?? null,
            proof_id: $data['proof_id'] ?? null,
            proof_token: $data['proof_token'] ?? null,
            created_at: $data['created_at'] ?? null,
            resolved_at: $data['resolved_at'] ?? null,
            expires_at: $data['expires_at'] ?? null,
        );
    }
}

/**
 * Identity challenges, newest first
 */
class IdentityChallengeListResponse
{
    /**
     * @param list<IdentityChallengeResponse> $data
     */
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
            data: array_map(static fn (array $item) => IdentityChallengeResponse::fromArray($item), $data['data']),
            pagination: $data['pagination'],
        );
    }
}

/**
 * Subscribe to Lookalike Watch. `brand_tokens` are the names watched for confusable newly-published artifacts; `owned_namespaces` suppress alerts on artifacts you publish yourself; `notify_email` must be an address already proven on the account, because a subscription is a standing outbound channel rather than a one-shot send. Every bound is published on the fields themselves — restating a cap in prose is the second declaration this block exists to avoid.
 */
class LookalikeWatchSubscribe
{
    public function __construct(
        public readonly array $brand_tokens,
        public readonly ?array $owned_namespaces = null,
        public readonly ?string $notify_email = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            brand_tokens: $data['brand_tokens'],
            owned_namespaces: $data['owned_namespaces'] ?? null,
            notify_email: $data['notify_email'] ?? null,
        );
    }
}

/**
 * HITL master-key KDF salt for the authenticated user
 */
class MasterKeyResponse
{
    public function __construct(
        public readonly ?string $master_kdf_salt,
        public readonly ?string $backup_kdf_salt,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            master_kdf_salt: $data['master_kdf_salt'] ?? null,
            backup_kdf_salt: $data['backup_kdf_salt'] ?? null,
        );
    }
}

class McpConnectInfo
{
    public function __construct(
        public readonly string $message,
        public readonly string $install_command,
        public readonly string $install_caveat,
        public readonly array $client_config,
        public readonly string $docs_url,
        public readonly ?string $remote_url = null,
        public readonly ?array $remote_config = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            message: $data['message'],
            install_command: $data['install_command'],
            install_caveat: $data['install_caveat'],
            client_config: $data['client_config'],
            docs_url: $data['docs_url'],
            remote_url: $data['remote_url'] ?? null,
            remote_config: $data['remote_config'] ?? null,
        );
    }
}

/**
 * Universal asset search query parameters
 */
class MeAssetSearchQueryParams
{
    public function __construct(
        public readonly string $value,
        public readonly ?string $type = null,
        public readonly ?string $environment = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            value: $data['value'],
            type: $data['type'] ?? null,
            environment: $data['environment'] ?? null,
        );
    }
}

/**
 * Exact-match asset dossier. `*_masked` fields are always masked; the owner's JWT session additionally receives unmasked `identifier`/`recipient` twins — never returned to API-key callers (owner-vs-not boundary, SEC-001). Authorizations/confirmations require hitl:read for API-key callers; related records follow the provable verification_id -> authorization_id chain only.
 */
class MeAssetSearchResponse
{
    public function __construct(
        public readonly array $data,
        public readonly float $total,
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
            total: $data['total'],
        );
    }
}

/**
 * Account search query parameters
 */
class MeSearchQueryParams
{
    public function __construct(
        public readonly string $q,
        public readonly ?string $type = null,
        public readonly ?string $environment = null,
        public readonly ?int $limit = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            q: $data['q'],
            type: $data['type'] ?? null,
            environment: $data['environment'] ?? null,
            limit: $data['limit'] ?? null,
        );
    }
}

/**
 * Account search results grouped by resource type. `masked_identifier` is always masked; the owner's JWT session additionally receives the unmasked `identifier` twin — never returned to API-key callers (owner-vs-not boundary, SEC-001). The hitl/confirmations buckets require the matching read scope.
 */
class MeSearchResponse
{
    public function __construct(
        public readonly array $data,
        public readonly float $total,
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
            total: $data['total'],
        );
    }
}

/**
 * Request body for creating a multi-channel verification group
 */
class CreateMultiChannelVerificationRequest
{
    public function __construct(
        public readonly string $type,
        public readonly string $identifier,
        public readonly array $channels,
        public readonly ?string $external_user_id = null,
        public readonly ?array $client_metadata = null,
        public readonly ?int $proof_expiry_days = null,
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
            channels: $data['channels'],
            external_user_id: $data['external_user_id'] ?? null,
            client_metadata: $data['client_metadata'] ?? null,
            proof_expiry_days: $data['proof_expiry_days'] ?? null,
        );
    }
}

class OAuthAuthorizationServerMetadata
{
    public function __construct(
        public readonly string $issuer,
        public readonly string $authorization_endpoint,
        public readonly string $token_endpoint,
        public readonly string $registration_endpoint,
        public readonly string $revocation_endpoint,
        public readonly array $response_types_supported,
        public readonly array $grant_types_supported,
        public readonly array $code_challenge_methods_supported,
        public readonly array $token_endpoint_auth_methods_supported,
        public readonly array $scopes_supported,
        public readonly string $service_documentation,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            issuer: $data['issuer'],
            authorization_endpoint: $data['authorization_endpoint'],
            token_endpoint: $data['token_endpoint'],
            registration_endpoint: $data['registration_endpoint'],
            revocation_endpoint: $data['revocation_endpoint'],
            response_types_supported: $data['response_types_supported'],
            grant_types_supported: $data['grant_types_supported'],
            code_challenge_methods_supported: $data['code_challenge_methods_supported'],
            token_endpoint_auth_methods_supported: $data['token_endpoint_auth_methods_supported'],
            scopes_supported: $data['scopes_supported'],
            service_documentation: $data['service_documentation'],
        );
    }
}

class OAuthProtectedResourceMetadata
{
    public function __construct(
        public readonly string $resource,
        public readonly array $authorization_servers,
        public readonly array $scopes_supported,
        public readonly array $bearer_methods_supported,
        public readonly string $resource_name,
        public readonly string $resource_documentation,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            resource: $data['resource'],
            authorization_servers: $data['authorization_servers'],
            scopes_supported: $data['scopes_supported'],
            bearer_methods_supported: $data['bearer_methods_supported'],
            resource_name: $data['resource_name'],
            resource_documentation: $data['resource_documentation'],
        );
    }
}

/**
 * Per-API-key usage: verification counts by type/channel/status + per-collection totals (requests, confirmations, authorizations) + attribution metadata. Counts only — no recipient identifiers (SEC-001). Data is returned for revoked keys (audit).
 */
class ApiKeyUsageResponse
{
    public function __construct(
        public readonly array $api_key,
        public readonly array $verifications,
        public readonly array $verification_requests,
        public readonly array $confirmations,
        public readonly array $authorizations,
        public readonly ?string $attribution_cutoff_at,
        public readonly ?float $attributed_fraction,
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
            verifications: $data['verifications'],
            verification_requests: $data['verification_requests'],
            confirmations: $data['confirmations'],
            authorizations: $data['authorizations'],
            attribution_cutoff_at: $data['attribution_cutoff_at'] ?? null,
            attributed_fraction: $data['attributed_fraction'] ?? null,
        );
    }
}

class AvailableAssetsQuery
{
    public function __construct(
        public readonly mixed $profile_id = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            profile_id: $data['profile_id'] ?? null,
        );
    }
}

/**
 * A Circle member (channel presence and a masked enrollment phone only, never an identifier)
 */
class CircleMember
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $status,
        public readonly bool $has_telegram,
        public readonly bool $has_whatsapp,
        public readonly bool $mesh_opted_in,
        public readonly bool $mesh_opt_out,
        public readonly ?string $enrolled_phone_hint,
        public readonly ?string $enrolled_at,
        public readonly ?string $last_verified_at,
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
            status: $data['status'],
            has_telegram: $data['has_telegram'],
            has_whatsapp: $data['has_whatsapp'],
            mesh_opted_in: $data['mesh_opted_in'],
            mesh_opt_out: $data['mesh_opt_out'],
            enrolled_phone_hint: $data['enrolled_phone_hint'] ?? null,
            enrolled_at: $data['enrolled_at'] ?? null,
            last_verified_at: $data['last_verified_at'] ?? null,
        );
    }
}

/**
 * A Circle — a named group of trusted contacts
 */
class Circle
{
    /**
     * @param list<CircleMember> $members
     */
    public function __construct(
        public readonly string $id,
        public readonly ?string $user_id,
        public readonly ?string $name,
        public readonly ?string $owner_name,
        public readonly ?string $profile_id,
        public readonly string $environment,
        public readonly bool $proof_me_enabled,
        public readonly string $status,
        public readonly int $member_count,
        public readonly array $members,
        public readonly string $created_at,
        public readonly string $updated_at,
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
            user_id: $data['user_id'] ?? null,
            name: $data['name'] ?? null,
            owner_name: $data['owner_name'] ?? null,
            profile_id: $data['profile_id'] ?? null,
            environment: $data['environment'],
            proof_me_enabled: $data['proof_me_enabled'],
            status: $data['status'],
            member_count: $data['member_count'],
            members: array_map(static fn (array $item) => CircleMember::fromArray($item), $data['members']),
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
        );
    }
}

/**
 * Paginated list of Circles
 */
class CircleListResponse
{
    /**
     * @param list<Circle> $data
     */
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
            data: array_map(static fn (array $item) => Circle::fromArray($item), $data['data']),
            pagination: $data['pagination'],
        );
    }
}

/**
 * A member channel (metadata only — never the identifier, SEC-PM-006)
 */
class CircleMemberChannel
{
    public function __construct(
        public readonly string $id,
        public readonly string $channel,
        public readonly string $status,
        public readonly ?string $verified_at,
        public readonly string $created_at,
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
            status: $data['status'],
            verified_at: $data['verified_at'] ?? null,
            created_at: $data['created_at'],
        );
    }
}

/**
 * The channels of a Circle member
 */
class CircleMemberChannelListResponse
{
    /**
     * @param list<CircleMemberChannel> $channels
     */
    public function __construct(
        public readonly array $channels,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            channels: array_map(static fn (array $item) => CircleMemberChannel::fromArray($item), $data['channels']),
        );
    }
}

/**
 * Result of manually triggering a member drill (Proof of Me)
 */
class CircleMemberDrillResponse
{
    public function __construct(
        public readonly mixed $id,
        public readonly string $status,
        public readonly ?string $confirm_channel = null,
        public readonly ?bool $single_channel_warning = null,
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
            confirm_channel: $data['confirm_channel'] ?? null,
            single_channel_warning: $data['single_channel_warning'] ?? null,
        );
    }
}

/**
 * Request body for inviting (enrolling) a Circle member
 */
class CircleMemberInviteBody
{
    public function __construct(
        public readonly mixed $member_id,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            member_id: $data['member_id'],
        );
    }
}

/**
 * A single-use member enrollment link (both channels + QR)
 */
class CircleMemberInviteResponse
{
    public function __construct(
        public readonly string $token,
        public readonly string $member_id,
        public readonly string $telegram_deep_link,
        public readonly string $whatsapp_deep_link,
        public readonly string $qr_code,
        public readonly string $qr_text,
        public readonly int $expires_in,
        public readonly string $suggested_share_text,
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
            member_id: $data['member_id'],
            telegram_deep_link: $data['telegram_deep_link'],
            whatsapp_deep_link: $data['whatsapp_deep_link'],
            qr_code: $data['qr_code'],
            qr_text: $data['qr_text'],
            expires_in: $data['expires_in'],
            suggested_share_text: $data['suggested_share_text'],
        );
    }
}

/**
 * The members of a Circle
 */
class CircleMemberListResponse
{
    /**
     * @param list<CircleMember> $members
     */
    public function __construct(
        public readonly array $members,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            members: array_map(static fn (array $item) => CircleMember::fromArray($item), $data['members']),
        );
    }
}

/**
 * A HITL confirmation request
 */
class Confirmation
{
    public function __construct(
        public readonly string $id,
        public readonly mixed $hitl_id,
        public readonly string $message,
        public readonly string $status,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly ?array $response = null,
        public readonly ?string $timeout_at = null,
        public readonly ?string $proof_token = null,
        public readonly ?string $resolved_at = null,
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
            hitl_id: $data['hitl_id'],
            message: $data['message'],
            status: $data['status'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
            response: $data['response'] ?? null,
            timeout_at: $data['timeout_at'] ?? null,
            proof_token: $data['proof_token'] ?? null,
            resolved_at: $data['resolved_at'] ?? null,
        );
    }
}

/**
 * Query parameters for listing confirmations
 */
class ConfirmationListQuery
{
    public function __construct(
        public readonly ?int $page = null,
        public readonly ?int $limit = null,
        public readonly ?string $status = null,
        public readonly ?string $hitl_id = null,
        public readonly ?string $start_date = null,
        public readonly ?string $end_date = null,
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
            hitl_id: $data['hitl_id'] ?? null,
            start_date: $data['start_date'] ?? null,
            end_date: $data['end_date'] ?? null,
        );
    }
}

/**
 * Paginated list of confirmations
 */
class ConfirmationListResponse
{
    /**
     * @param list<Confirmation> $data
     */
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
            data: array_map(static fn (array $item) => Confirmation::fromArray($item), $data['data']),
            pagination: $data['pagination'],
        );
    }
}

/**
 * Body of `confirmation.approved` / `confirmation.denied`. A timeout is `confirmation.denied` with `response.channel` `system` and `response.text` `timeout`.
 */
class ConfirmationWebhookPayload
{
    public function __construct(
        public readonly string $event,
        public readonly string $confirmation_id,
        public readonly mixed $hitl_id,
        public readonly string $status,
        public readonly string $message,
        public readonly bool $encrypted,
        public readonly ?array $response = null,
        public readonly ?string $proof_token = null,
        public readonly ?string $resolved_at = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            event: $data['event'],
            confirmation_id: $data['confirmation_id'],
            hitl_id: $data['hitl_id'],
            status: $data['status'],
            message: $data['message'],
            encrypted: $data['encrypted'],
            response: $data['response'] ?? null,
            proof_token: $data['proof_token'] ?? null,
            resolved_at: $data['resolved_at'] ?? null,
        );
    }
}

/**
 * Request body for creating a Circle
 */
class CreateCircleBody
{
    /**
     * @param list<CircleMemberInput>|null $members
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $owner_name = null,
        public readonly mixed $profile_id = null,
        public readonly ?bool $proof_me_enabled = null,
        public readonly ?array $members = null,
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
            owner_name: $data['owner_name'] ?? null,
            profile_id: $data['profile_id'] ?? null,
            proof_me_enabled: $data['proof_me_enabled'] ?? null,
            members: isset($data['members']) ? array_map(static fn (array $item) => CircleMemberInput::fromArray($item), $data['members']) : null,
        );
    }
}

/**
 * Request body for creating a confirmation
 */
class CreateConfirmationBody
{
    public function __construct(
        public readonly mixed $hitl_id,
        public readonly string $message,
        public readonly ?int $proof_expiry_days = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            hitl_id: $data['hitl_id'],
            message: $data['message'],
            proof_expiry_days: $data['proof_expiry_days'] ?? null,
        );
    }
}

/**
 * Request body for creating a HITL configuration
 */
class CreateHitlBody
{
    public function __construct(
        public readonly string $name,
        public readonly ?array $channels = null,
        public readonly mixed $circle_id = null,
        public readonly ?float $timeout_seconds = null,
        public readonly mixed $profile_id = null,
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
            channels: $data['channels'] ?? null,
            circle_id: $data['circle_id'] ?? null,
            timeout_seconds: $data['timeout_seconds'] ?? null,
            profile_id: $data['profile_id'] ?? null,
        );
    }
}

/**
 * A delegation — link 2 of the proof chain: domain → artifact. NO endpoint returns this shape: every delegation response carries the derived `effective_status`/`is_valid` too, so read `DelegationListItem` (list, revoke) or `DelegationDetail` (create, get). Published as a back-compat type only.
 */
class Delegation
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $delegation_id,
        public readonly string $principal,
        public readonly DelegateIdentifier $delegate,
        public readonly array $scope,
        public readonly string $control_proof,
        public readonly string $status,
        public readonly ?string $revoked_at,
        public readonly ?string $revoked_reason,
        public readonly string $environment,
        public readonly string $issued_at,
        public readonly string $expires_at,
        public readonly string $created_at,
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
            delegation_id: $data['delegation_id'] ?? null,
            principal: $data['principal'],
            delegate: DelegateIdentifier::fromArray($data['delegate']),
            scope: $data['scope'],
            control_proof: $data['control_proof'],
            status: $data['status'],
            revoked_at: $data['revoked_at'] ?? null,
            revoked_reason: $data['revoked_reason'] ?? null,
            environment: $data['environment'],
            issued_at: $data['issued_at'],
            expires_at: $data['expires_at'],
            created_at: $data['created_at'],
        );
    }
}

/**
 * A delegation with its derived effective status
 */
class DelegationDetail
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $delegation_id,
        public readonly string $principal,
        public readonly DelegateIdentifier $delegate,
        public readonly array $scope,
        public readonly string $control_proof,
        public readonly string $status,
        public readonly ?string $revoked_at,
        public readonly ?string $revoked_reason,
        public readonly string $environment,
        public readonly string $issued_at,
        public readonly string $expires_at,
        public readonly string $created_at,
        public readonly ?string $token,
        public readonly string $effective_status,
        public readonly bool $is_valid,
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
            delegation_id: $data['delegation_id'] ?? null,
            principal: $data['principal'],
            delegate: DelegateIdentifier::fromArray($data['delegate']),
            scope: $data['scope'],
            control_proof: $data['control_proof'],
            status: $data['status'],
            revoked_at: $data['revoked_at'] ?? null,
            revoked_reason: $data['revoked_reason'] ?? null,
            environment: $data['environment'],
            issued_at: $data['issued_at'],
            expires_at: $data['expires_at'],
            created_at: $data['created_at'],
            token: $data['token'] ?? null,
            effective_status: $data['effective_status'],
            is_valid: $data['is_valid'],
        );
    }
}

/**
 * A delegation without its token, carrying stored status plus derived liveness — what the owner listing reports, and what `POST /delegations/{id}/revoke` answers with
 */
class DelegationListItem
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $delegation_id,
        public readonly string $principal,
        public readonly DelegateIdentifier $delegate,
        public readonly array $scope,
        public readonly string $control_proof,
        public readonly string $status,
        public readonly ?string $revoked_at,
        public readonly ?string $revoked_reason,
        public readonly string $environment,
        public readonly string $issued_at,
        public readonly string $expires_at,
        public readonly string $created_at,
        public readonly string $effective_status,
        public readonly bool $is_valid,
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
            delegation_id: $data['delegation_id'] ?? null,
            principal: $data['principal'],
            delegate: DelegateIdentifier::fromArray($data['delegate']),
            scope: $data['scope'],
            control_proof: $data['control_proof'],
            status: $data['status'],
            revoked_at: $data['revoked_at'] ?? null,
            revoked_reason: $data['revoked_reason'] ?? null,
            environment: $data['environment'],
            issued_at: $data['issued_at'],
            expires_at: $data['expires_at'],
            created_at: $data['created_at'],
            effective_status: $data['effective_status'],
            is_valid: $data['is_valid'],
        );
    }
}

/**
 * The caller's own delegations (no public enumeration exists)
 */
class DelegationListResponse
{
    /**
     * @param list<DelegationListItem> $data
     */
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
            data: array_map(static fn (array $item) => DelegationListItem::fromArray($item), $data['data']),
            pagination: $data['pagination'],
        );
    }
}

/**
 * A delegation including its publishable signed token. NO endpoint returns this shape — `POST /delegations` and `GET /delegations/{id}` answer with `DelegationDetail`, which adds the derived `effective_status`/`is_valid`. Published as a back-compat type only.
 */
class DelegationWithToken
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $delegation_id,
        public readonly string $principal,
        public readonly DelegateIdentifier $delegate,
        public readonly array $scope,
        public readonly string $control_proof,
        public readonly string $status,
        public readonly ?string $revoked_at,
        public readonly ?string $revoked_reason,
        public readonly string $environment,
        public readonly string $issued_at,
        public readonly string $expires_at,
        public readonly string $created_at,
        public readonly ?string $token,
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
            delegation_id: $data['delegation_id'] ?? null,
            principal: $data['principal'],
            delegate: DelegateIdentifier::fromArray($data['delegate']),
            scope: $data['scope'],
            control_proof: $data['control_proof'],
            status: $data['status'],
            revoked_at: $data['revoked_at'] ?? null,
            revoked_reason: $data['revoked_reason'] ?? null,
            environment: $data['environment'],
            issued_at: $data['issued_at'],
            expires_at: $data['expires_at'],
            created_at: $data['created_at'],
            token: $data['token'] ?? null,
        );
    }
}

/**
 * HITL configuration with messaging channels and timeout
 */
class Hitl
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $user_id,
        public readonly array $channels,
        public readonly int $timeout_seconds,
        public readonly string $status,
        public readonly ?array $keypair,
        public readonly string $environment,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly ?string $name = null,
        public readonly ?string $profile_id = null,
        public readonly ?string $circle_id = null,
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
            user_id: $data['user_id'] ?? null,
            channels: $data['channels'],
            timeout_seconds: $data['timeout_seconds'],
            status: $data['status'],
            keypair: $data['keypair'] ?? null,
            environment: $data['environment'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
            name: $data['name'] ?? null,
            profile_id: $data['profile_id'] ?? null,
            circle_id: $data['circle_id'] ?? null,
        );
    }
}

/**
 * Response from requesting authorization consent for HITL channels
 */
class HitlAuthorizationResponse
{
    public function __construct(
        public readonly string $hitl_id,
        public readonly array $authorizations,
        public readonly string $message,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            hitl_id: $data['hitl_id'],
            authorizations: $data['authorizations'],
            message: $data['message'],
        );
    }
}

/**
 * Finalized HITL configuration (pending → active) with optional authorization dispatch results
 */
class HitlFinalizeResponse
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $user_id,
        public readonly array $channels,
        public readonly int $timeout_seconds,
        public readonly string $status,
        public readonly ?array $keypair,
        public readonly string $environment,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly ?string $name = null,
        public readonly ?string $profile_id = null,
        public readonly ?string $circle_id = null,
        public readonly ?array $authorizations = null,
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
            user_id: $data['user_id'] ?? null,
            channels: $data['channels'],
            timeout_seconds: $data['timeout_seconds'],
            status: $data['status'],
            keypair: $data['keypair'] ?? null,
            environment: $data['environment'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
            name: $data['name'] ?? null,
            profile_id: $data['profile_id'] ?? null,
            circle_id: $data['circle_id'] ?? null,
            authorizations: $data['authorizations'] ?? null,
        );
    }
}

/**
 * Paginated list of HITL configurations
 */
class HitlListResponse
{
    /**
     * @param list<Hitl> $data
     */
    public function __construct(
        public readonly array $data,
        public readonly array $pagination,
        public readonly array $limits,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            data: array_map(static fn (array $item) => Hitl::fromArray($item), $data['data']),
            pagination: $data['pagination'],
            limits: $data['limits'],
        );
    }
}

/**
 * Everything needed to embed the verification badge for a username (proof.holdings/badge/{username})
 */
class ProfileBadgeResponse
{
    public function __construct(
        public readonly string $username,
        public readonly string $subject,
        public readonly int $verified_count,
        public readonly string $state,
        public readonly string $checked_at,
        public readonly string $profile_url,
        public readonly array $badge_urls,
        public readonly array $snippets,
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
            subject: $data['subject'],
            verified_count: $data['verified_count'],
            state: $data['state'],
            checked_at: $data['checked_at'],
            profile_url: $data['profile_url'],
            badge_urls: $data['badge_urls'],
            snippets: $data['snippets'],
        );
    }
}

/**
 * Body for a profile-scoped template write. Distinct from the account-level `TemplateContent`: it also accepts `is_active`, which every read of the template returns — declaring `TemplateContent` here published a field callers could read and had no documented way to set.
 */
class ProfileTemplateContent
{
    public function __construct(
        public readonly string $body,
        public readonly ?string $subject = null,
        public readonly ?string $button_text = null,
        public readonly ?string $button_url_template = null,
        public readonly ?bool $is_active = null,
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
            is_active: $data['is_active'] ?? null,
        );
    }
}

/**
 * The client-generated RSA keypair for a HITL config. `public_key`, `encrypted_private_key` and `kdf_salt` are required; the `wrapped_dek`/`dek_iv`/`dek_tag` trio carries the v2 master-key envelope and the `backup_*` trio the recovery wrapper. The server never receives the master password, so it cannot decrypt any of it.
 */
class PutHitlKeysBody
{
    public function __construct(
        public readonly string $public_key,
        public readonly string $encrypted_private_key,
        public readonly string $kdf_salt,
        public readonly ?string $wrapped_dek = null,
        public readonly ?string $dek_iv = null,
        public readonly ?string $dek_tag = null,
        public readonly ?string $backup_wrapped_dek = null,
        public readonly ?string $backup_dek_iv = null,
        public readonly ?string $backup_dek_tag = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            public_key: $data['public_key'],
            encrypted_private_key: $data['encrypted_private_key'],
            kdf_salt: $data['kdf_salt'],
            wrapped_dek: $data['wrapped_dek'] ?? null,
            dek_iv: $data['dek_iv'] ?? null,
            dek_tag: $data['dek_tag'] ?? null,
            backup_wrapped_dek: $data['backup_wrapped_dek'] ?? null,
            backup_dek_iv: $data['backup_dek_iv'] ?? null,
            backup_dek_tag: $data['backup_dek_tag'] ?? null,
        );
    }
}

/**
 * Completion-rate history query parameters
 */
class RequestHealthHistoryQueryParams
{
    public function __construct(
        public readonly ?int $days = null,
        public readonly ?string $interval = null,
        public readonly ?string $environment = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            days: $data['days'] ?? null,
            interval: $data['interval'] ?? null,
            environment: $data['environment'] ?? null,
        );
    }
}

/**
 * Per-bucket completion-rate trend: requests bucketed by created_at (UTC day/week), reusing /me/summary's stale-expiry bucketing and completed/(completed+expired+cancelled) rate. Dense (zero-filled), chronological. Counts + rate only — no recipient identifiers (SEC-001). Demo traffic excluded (SEC-003).
 */
class RequestHealthHistoryResponse
{
    public function __construct(
        public readonly string $interval,
        public readonly float $days,
        public readonly string $as_of,
        public readonly array $points,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            interval: $data['interval'],
            days: $data['days'],
            as_of: $data['as_of'],
            points: $data['points'],
        );
    }
}

/**
 * Approval link metadata returned by GET /resolve. For resolved confirmations, includes history fields.
 */
class ResolveLinkMetadata
{
    public function __construct(
        public readonly string $confirmation_id,
        public readonly string $hitl_id,
        public readonly string $status,
        public readonly bool $encrypted,
        public readonly string $timeout_at,
        public readonly string $created_at,
        public readonly ?string $config_name,
        public readonly ?bool $config_archived = null,
        public readonly ?string $business_name = null,
        public readonly ?string $logo_url = null,
        public readonly ?array $response = null,
        public readonly ?string $resolved_at = null,
        public readonly ?string $masked_actor = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            confirmation_id: $data['confirmation_id'],
            hitl_id: $data['hitl_id'],
            status: $data['status'],
            encrypted: $data['encrypted'],
            timeout_at: $data['timeout_at'],
            created_at: $data['created_at'],
            config_name: $data['config_name'] ?? null,
            config_archived: $data['config_archived'] ?? null,
            business_name: $data['business_name'] ?? null,
            logo_url: $data['logo_url'] ?? null,
            response: $data['response'] ?? null,
            resolved_at: $data['resolved_at'] ?? null,
            masked_actor: $data['masked_actor'] ?? null,
        );
    }
}

/**
 * HITL master-key KDF salt to set or change
 */
class SetMasterKeyBody
{
    public function __construct(
        public readonly string $master_kdf_salt,
        public readonly ?string $backup_kdf_salt = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            master_kdf_salt: $data['master_kdf_salt'],
            backup_kdf_salt: $data['backup_kdf_salt'] ?? null,
        );
    }
}

/**
 * Account summary query parameters
 */
class SummaryQueryParams
{
    public function __construct(
        public readonly ?string $environment = null,
        public readonly ?string $profile_id = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            environment: $data['environment'] ?? null,
            profile_id: $data['profile_id'] ?? null,
        );
    }
}

/**
 * Result of a suspend/unsuspend transition (m-proof-status-list)
 */
class SuspendProofResponse
{
    public function __construct(
        public readonly string $proof_id,
        public readonly string $status,
        public readonly ?string $suspended_at,
        public readonly string $status_list_uri,
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
            suspended_at: $data['suspended_at'] ?? null,
            status_list_uri: $data['status_list_uri'],
        );
    }
}

/**
 * A message template as resolved for one public profile — either the profile override or the inherited default.
 */
class ProfileTemplate
{
    public function __construct(
        public readonly string $channel,
        public readonly string $message_type,
        public readonly string $body,
        public readonly bool $is_custom,
        public readonly bool $is_active,
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
            is_custom: $data['is_custom'],
            is_active: $data['is_active'],
            subject: $data['subject'] ?? null,
            button_text: $data['button_text'] ?? null,
            button_url_template: $data['button_url_template'] ?? null,
        );
    }
}

/**
 * Request body for updating an authorization's owner-editable metadata (F22)
 */
class UpdateAuthorizationBody
{
    public function __construct(
        public readonly ?string $label,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            label: $data['label'] ?? null,
        );
    }
}

/**
 * Request body for updating a Circle (name and owner name — the profile binding is create-only)
 */
class UpdateCircleBody
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $owner_name = null,
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
            owner_name: $data['owner_name'] ?? null,
        );
    }
}

/**
 * Request body for updating a HITL configuration
 */
class UpdateHitlBody
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?array $channels = null,
        public readonly ?float $timeout_seconds = null,
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
            channels: $data['channels'] ?? null,
            timeout_seconds: $data['timeout_seconds'] ?? null,
        );
    }
}

/**
 * Per-channel setup block of a multi-channel verification group
 */
class MultiChannelChannelBlock
{
    public function __construct(
        public readonly string $channel,
        public readonly string $verification_id,
        public readonly string $status,
        public readonly ?string $deep_link = null,
        public readonly ?string $qr_code = null,
        public readonly ?string $qr_text = null,
        public readonly ?string $message = null,
        public readonly ?string $instructions = null,
        public readonly ?string $phone_number = null,
        public readonly ?string $sms_message = null,
        public readonly ?array $sms_dids = null,
        public readonly ?string $suggested_region = null,
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
            verification_id: $data['verification_id'],
            status: $data['status'],
            deep_link: $data['deep_link'] ?? null,
            qr_code: $data['qr_code'] ?? null,
            qr_text: $data['qr_text'] ?? null,
            message: $data['message'] ?? null,
            instructions: $data['instructions'] ?? null,
            phone_number: $data['phone_number'] ?? null,
            sms_message: $data['sms_message'] ?? null,
            sms_dids: $data['sms_dids'] ?? null,
            suggested_region: $data['suggested_region'] ?? null,
        );
    }
}

/**
 * Polling status of a multi-channel verification group
 */
class MultiChannelGroupStatusResponse
{
    public function __construct(
        public readonly string $group_id,
        public readonly string $status,
        public readonly ?string $winning_channel,
        public readonly array $channels,
        public readonly mixed $proof = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            group_id: $data['group_id'],
            status: $data['status'],
            winning_channel: $data['winning_channel'] ?? null,
            channels: $data['channels'],
            proof: $data['proof'] ?? null,
        );
    }
}

/**
 * Multi-channel verification group with one setup block per channel
 */
class MultiChannelVerificationResponse
{
    /**
     * @param list<MultiChannelChannelBlock> $channels
     */
    public function __construct(
        public readonly string $group_id,
        public readonly string $type,
        public readonly string $identifier,
        public readonly string $status,
        public readonly string $expires_at,
        public readonly array $channels,
        public readonly string $created_at,
        public readonly ?string $external_user_id = null,
        public readonly ?array $client_metadata = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            group_id: $data['group_id'],
            type: $data['type'],
            identifier: $data['identifier'],
            status: $data['status'],
            expires_at: $data['expires_at'],
            channels: array_map(static fn (array $item) => MultiChannelChannelBlock::fromArray($item), $data['channels']),
            created_at: $data['created_at'],
            external_user_id: $data['external_user_id'] ?? null,
            client_metadata: $data['client_metadata'] ?? null,
        );
    }
}

/**
 * An active asset offered for display on a public profile
 */
class ProfileAssetOption
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $identifier,
        public readonly string $channel,
        public readonly bool $is_public,
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
            identifier: $data['identifier'],
            channel: $data['channel'],
            is_public: $data['is_public'],
            verified_at: $data['verified_at'] ?? null,
        );
    }
}

/**
 * Account settings
 */
class SettingsResponse
{
    public function __construct(
        public readonly BrandingSettings $branding,
        public readonly WebhookSettingsResponse $webhook,
        public readonly ?int $proof_expiry_days,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            branding: BrandingSettings::fromArray($data['branding']),
            webhook: WebhookSettingsResponse::fromArray($data['webhook']),
            proof_expiry_days: $data['proof_expiry_days'] ?? null,
        );
    }
}

// ============================================================================
// Verification Requests
// ============================================================================

/**
 * Context for custom and confirmation action types
 */
class ActionContext
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?float $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $entity = null,
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
            amount: $data['amount'] ?? null,
            currency: $data['currency'] ?? null,
            entity: $data['entity'] ?? null,
        );
    }
}

class CancelRequestResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $message,
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
            message: $data['message'],
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
        public readonly ?string $channel = null,
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
        public readonly ?string $profile_id = null,
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
            profile_id: $data['profile_id'] ?? null,
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
        public readonly ?string $profile_id = null,
        public readonly ?string $verified_at = null,
        public readonly ?string $proof_id = null,
        public readonly ?string $proof_status = null,
        public readonly ?string $proof_revoked_reason = null,
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
            profile_id: $data['profile_id'] ?? null,
            verified_at: $data['verified_at'] ?? null,
            proof_id: $data['proof_id'] ?? null,
            proof_status: $data['proof_status'] ?? null,
            proof_revoked_reason: $data['proof_revoked_reason'] ?? null,
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
        public readonly ?array $channel_metadata = null,
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
            channel_metadata: $data['channel_metadata'] ?? null,
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
        public readonly string $proof_expires_at,
        public readonly string $created_at,
        public readonly ?string $proof_id = null,
        public readonly ?string $proof_status = null,
        public readonly ?string $proof_token = null,
        public readonly ?bool $has_proof = null,
        public readonly ?string $verified_at = null,
        public readonly ?array $metadata = null,
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
            proof_expires_at: $data['proof_expires_at'],
            created_at: $data['created_at'],
            proof_id: $data['proof_id'] ?? null,
            proof_status: $data['proof_status'] ?? null,
            proof_token: $data['proof_token'] ?? null,
            has_proof: $data['has_proof'] ?? null,
            verified_at: $data['verified_at'] ?? null,
            metadata: $data['metadata'] ?? null,
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
        public readonly ?string $qr_text = null,
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
            qr_text: $data['qr_text'] ?? null,
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
    /**
     * @param list<AuthSessionListItem> $sessions
     */
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
            sessions: array_map(static fn (array $item) => AuthSessionListItem::fromArray($item), $data['sessions']),
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
// Authorizations
// ============================================================================

class AuthorizationExportQuery
{
    public function __construct(
        public readonly ?string $format = null,
        public readonly ?string $from = null,
        public readonly ?string $to = null,
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
            format: $data['format'] ?? null,
            from: $data['from'] ?? null,
            to: $data['to'] ?? null,
            status: $data['status'] ?? null,
        );
    }
}

class AuthorizationListQuery
{
    public function __construct(
        public readonly ?int $page = null,
        public readonly ?int $limit = null,
        public readonly ?string $status = null,
        public readonly ?string $phone = null,
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
            page: $data['page'] ?? null,
            limit: $data['limit'] ?? null,
            status: $data['status'] ?? null,
            phone: $data['phone'] ?? null,
            channel: $data['channel'] ?? null,
        );
    }
}

/**
 * Authorization granting permission to send confirmations via a specific channel
 */
class Authorization
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $channel,
        public readonly string $scope,
        public readonly string $business_name,
        public readonly ?string $recipient_display_name,
        public readonly bool $enrolled,
        public readonly string $expires_at,
        public readonly int $confirmations_sent,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly mixed $hitl_id = null,
        public readonly mixed $circle_id = null,
        public readonly ?string $recipient = null,
        public readonly ?string $phone = null,
        public readonly ?string $bsuid = null,
        public readonly ?string $label = null,
        public readonly ?string $verification_id = null,
        public readonly ?string $proof_token = null,
        public readonly ?string $authorized_at = null,
        public readonly ?string $denied_at = null,
        public readonly ?string $revoked_at = null,
        public readonly ?string $revocation_reason = null,
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
            status: $data['status'],
            channel: $data['channel'],
            scope: $data['scope'],
            business_name: $data['business_name'],
            recipient_display_name: $data['recipient_display_name'] ?? null,
            enrolled: $data['enrolled'],
            expires_at: $data['expires_at'],
            confirmations_sent: $data['confirmations_sent'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
            hitl_id: $data['hitl_id'] ?? null,
            circle_id: $data['circle_id'] ?? null,
            recipient: $data['recipient'] ?? null,
            phone: $data['phone'] ?? null,
            bsuid: $data['bsuid'] ?? null,
            label: $data['label'] ?? null,
            verification_id: $data['verification_id'] ?? null,
            proof_token: $data['proof_token'] ?? null,
            authorized_at: $data['authorized_at'] ?? null,
            denied_at: $data['denied_at'] ?? null,
            revoked_at: $data['revoked_at'] ?? null,
            revocation_reason: $data['revocation_reason'] ?? null,
            last_used_at: $data['last_used_at'] ?? null,
        );
    }
}

class AuthorizationListResponse
{
    /**
     * @param list<Authorization> $data
     */
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
            data: array_map(static fn (array $item) => Authorization::fromArray($item), $data['data']),
            pagination: $data['pagination'],
        );
    }
}

/**
 * Body of `authorization.approved` / `authorization.denied` — the recipient's answer to a consent request. The recipient's phone/identifier is not included; join on `authorization_id`.
 */
class AuthorizationWebhookPayload
{
    public function __construct(
        public readonly string $event,
        public readonly string $authorization_id,
        public readonly string $status,
        public readonly string $channel,
        public readonly mixed $hitl_id = null,
        public readonly ?string $scope = null,
        public readonly ?string $proof_token = null,
        public readonly ?string $decided_at = null,
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
            event: $data['event'],
            authorization_id: $data['authorization_id'],
            status: $data['status'],
            channel: $data['channel'],
            hitl_id: $data['hitl_id'] ?? null,
            scope: $data['scope'] ?? null,
            proof_token: $data['proof_token'] ?? null,
            decided_at: $data['decided_at'] ?? null,
            expires_at: $data['expires_at'] ?? null,
        );
    }
}

/**
 * Request body for creating an authorization
 */
class CreateAuthorizationBody
{
    public function __construct(
        public readonly mixed $hitl_id,
        public readonly string $recipient,
        public readonly string $phone,
        public readonly string $channel,
        public readonly string $business_name,
        public readonly ?string $bsuid = null,
        public readonly ?string $scope = null,
        public readonly mixed $business_profile_id = null,
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
            hitl_id: $data['hitl_id'],
            recipient: $data['recipient'],
            phone: $data['phone'],
            channel: $data['channel'],
            business_name: $data['business_name'],
            bsuid: $data['bsuid'] ?? null,
            scope: $data['scope'] ?? null,
            business_profile_id: $data['business_profile_id'] ?? null,
            expires_in: $data['expires_in'] ?? null,
        );
    }
}

/**
 * Response from creating an authorization request
 */
class CreateAuthorizationResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly mixed $hitl_id,
        public readonly string $channel,
        public readonly string $scope,
        public readonly string $business_name,
        public readonly string $authorization_url,
        public readonly string $expires_at,
        public readonly string $created_at,
        public readonly ?string $recipient = null,
        public readonly ?string $phone = null,
        public readonly ?string $bsuid = null,
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
            hitl_id: $data['hitl_id'],
            channel: $data['channel'],
            scope: $data['scope'],
            business_name: $data['business_name'],
            authorization_url: $data['authorization_url'],
            expires_at: $data['expires_at'],
            created_at: $data['created_at'],
            recipient: $data['recipient'] ?? null,
            phone: $data['phone'] ?? null,
            bsuid: $data['bsuid'] ?? null,
        );
    }
}

class RevokeAuthorizationBody
{
    public function __construct(
        public readonly ?string $reason = null,
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

/**
 * Request body for updating user settings
 */
class UpdateSettingsRequest
{
    public function __construct(
        public readonly ?BrandingSettings $branding = null,
        public readonly ?WebhookSettings $webhook = null,
        public readonly ?int $proof_expiry_days = null,
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
            webhook: isset($data['webhook']) ? WebhookSettings::fromArray($data['webhook']) : null,
            proof_expiry_days: $data['proof_expiry_days'] ?? null,
        );
    }
}

/**
 * Account settings as stored after the update. Carries `webhook.secret` when THIS request created or rotated it — the only response that ever does.
 */
class UpdateSettingsResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly BrandingSettings $branding,
        public readonly WebhookSettingsResponse $webhook,
        public readonly ?int $proof_expiry_days,
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
            branding: BrandingSettings::fromArray($data['branding']),
            webhook: WebhookSettingsResponse::fromArray($data['webhook']),
            proof_expiry_days: $data['proof_expiry_days'] ?? null,
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
        public readonly ?string $code = null,
        public readonly ?string $instruction = null,
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
            code: $data['code'] ?? null,
            instruction: $data['instruction'] ?? null,
            expires_at: $data['expires_at'] ?? null,
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
 * Verification request as listed: a summary — read the request itself for its assets
 */
class VerificationRequestListItem
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly array $computed_state,
        public readonly string $action_type,
        public readonly int $asset_count,
        public readonly array $progress,
        public readonly string $expires_at,
        public readonly string $created_at,
        public readonly ?string $reference_id = null,
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
            computed_state: $data['computed_state'],
            action_type: $data['action_type'],
            asset_count: $data['asset_count'],
            progress: $data['progress'],
            expires_at: $data['expires_at'],
            created_at: $data['created_at'],
            reference_id: $data['reference_id'] ?? null,
            completed_at: $data['completed_at'] ?? null,
        );
    }
}

class VerificationRequestListResponse
{
    /**
     * @param list<VerificationRequestListItem> $data
     */
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
            data: array_map(static fn (array $item) => VerificationRequestListItem::fromArray($item), $data['data']),
            pagination: PaginationResponse::fromArray($data['pagination']),
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
        public readonly ?int $proof_expiry_days = null,
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
            proof_expiry_days: $data['proof_expiry_days'] ?? null,
        );
    }
}

/**
 * Request body for creating a multi-asset verification request
 */
class CreateVerificationRequestBody
{
    /**
     * @param list<RequestedAsset> $assets
     */
    public function __construct(
        public readonly array $assets,
        public readonly ?string $reference_id = null,
        public readonly mixed $public_profile_id = null,
        public readonly mixed $action_type = null,
        public readonly ?ActionContext $action_context = null,
        public readonly ?string $validity_requirement = null,
        public readonly ?bool $partial_ok = null,
        public readonly ?string $redirect_url = null,
        public readonly ?string $callback_url = null,
        public readonly ?int $expires_in = null,
        public readonly ?int $proof_expiry_days = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            assets: array_map(static fn (array $item) => RequestedAsset::fromArray($item), $data['assets']),
            reference_id: $data['reference_id'] ?? null,
            public_profile_id: $data['public_profile_id'] ?? null,
            action_type: $data['action_type'] ?? null,
            action_context: isset($data['action_context']) ? ActionContext::fromArray($data['action_context']) : null,
            validity_requirement: $data['validity_requirement'] ?? null,
            partial_ok: $data['partial_ok'] ?? null,
            redirect_url: $data['redirect_url'] ?? null,
            callback_url: $data['callback_url'] ?? null,
            expires_in: $data['expires_in'] ?? null,
            proof_expiry_days: $data['proof_expiry_days'] ?? null,
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
        public readonly bool $test_mode,
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
        public readonly string $status,
        public readonly string $created_at,
        public readonly ?string $identifier = null,
        public readonly ?Challenge $challenge = null,
        public readonly ?Proof $proof = null,
        public readonly ?string $external_user_id = null,
        public readonly ?int $attempts = null,
        public readonly ?int $max_attempts = null,
        public readonly ?string $updated_at = null,
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
            status: $data['status'],
            created_at: $data['created_at'],
            identifier: $data['identifier'] ?? null,
            challenge: isset($data['challenge']) ? Challenge::fromArray($data['challenge']) : null,
            proof: isset($data['proof']) ? Proof::fromArray($data['proof']) : null,
            external_user_id: $data['external_user_id'] ?? null,
            attempts: $data['attempts'] ?? null,
            max_attempts: $data['max_attempts'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            verified_at: $data['verified_at'] ?? null,
        );
    }
}

/**
 * Verification as listed
 */
class VerificationListItem
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $channel,
        public readonly string $status,
        public readonly string $environment,
        public readonly string $created_at,
        public readonly ?string $identifier = null,
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
            status: $data['status'],
            environment: $data['environment'],
            created_at: $data['created_at'],
            identifier: $data['identifier'] ?? null,
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
    /**
     * @param list<VerificationListItem> $data
     */
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
            data: array_map(static fn (array $item) => VerificationListItem::fromArray($item), $data['data']),
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
        public readonly ?string $reference_id = null,
        public readonly ?array $computed_state = null,
        public readonly ?ActionContext $action_context = null,
        public readonly ?string $validity_requirement = null,
        public readonly ?string $verification_url = null,
        public readonly ?string $redirect_url = null,
        public readonly ?string $callback_url = null,
        public readonly ?array $progress = null,
        public readonly ?string $completed_at = null,
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
            id: $data['id'],
            status: $data['status'],
            assets: $data['assets'],
            action_type: $data['action_type'],
            partial_ok: $data['partial_ok'],
            expires_at: $data['expires_at'],
            created_at: $data['created_at'],
            reference_id: $data['reference_id'] ?? null,
            computed_state: $data['computed_state'] ?? null,
            action_context: isset($data['action_context']) ? ActionContext::fromArray($data['action_context']) : null,
            validity_requirement: $data['validity_requirement'] ?? null,
            verification_url: $data['verification_url'] ?? null,
            redirect_url: $data['redirect_url'] ?? null,
            callback_url: $data['callback_url'] ?? null,
            progress: $data['progress'] ?? null,
            completed_at: $data['completed_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
        );
    }
}

/**
 * Proof tokens for verified assets in a verification request
 */
class VerificationRequestProofsResponse
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
    /**
     * @param list<ProfileProofEntry> $proofs
     */
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
            proofs: array_map(static fn (array $item) => ProfileProofEntry::fromArray($item), $data['proofs']),
        );
    }
}

/**
 * Request body for updating a profile
 */
class UpdateProfileRequest
{
    /**
     * @param list<CustomLink>|null $custom_links
     */
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
            custom_links: isset($data['custom_links']) ? array_map(static fn (array $item) => CustomLink::fromArray($item), $data['custom_links']) : null,
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
    /**
     * @param list<CustomLink>|null $custom_links
     */
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
            custom_links: isset($data['custom_links']) ? array_map(static fn (array $item) => CustomLink::fromArray($item), $data['custom_links']) : null,
            proofs: $data['proofs'] ?? null,
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
    /**
     * @param list<VerifiedUserSummary> $data
     */
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
            data: array_map(static fn (array $item) => VerifiedUserSummary::fromArray($item), $data['data']),
            pagination: $data['pagination'],
        );
    }
}

// ============================================================================
// Proofs
// ============================================================================

/**
 * JSON Web Key. The key-type-specific members are optional because one key set may carry both an EC and an RSA key: `crv`/`x`/`y` are present on EC keys, `n`/`e` on RSA keys.
 */
class JWK
{
    public function __construct(
        public readonly string $kty,
        public readonly string $use,
        public readonly string $alg,
        public readonly string $kid,
        public readonly ?string $crv = null,
        public readonly ?string $x = null,
        public readonly ?string $y = null,
        public readonly ?string $n = null,
        public readonly ?string $e = null,
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
            use: $data['use'],
            alg: $data['alg'],
            kid: $data['kid'],
            crv: $data['crv'] ?? null,
            x: $data['x'] ?? null,
            y: $data['y'] ?? null,
            n: $data['n'] ?? null,
            e: $data['e'] ?? null,
        );
    }
}

/**
 * JSON Web Key Set for offline proof verification
 */
class JWKS
{
    /**
     * @param list<JWK> $keys
     */
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
            keys: array_map(static fn (array $item) => JWK::fromArray($item), $data['keys']),
        );
    }
}

/**
 * Public Proof of Me page payload for a username (proof.holdings/me/{username})
 */
class ProofMePageResponse
{
    public function __construct(
        public readonly string $username,
        public readonly bool $proof_me_enabled,
        public readonly string $telegram_bot_username,
        public readonly string $whatsapp_number,
        public readonly string $telegram_deep_link,
        public readonly string $whatsapp_deep_link,
        public readonly string $qr_code,
        public readonly ?string $display_name = null,
        public readonly ?string $avatar_url = null,
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
            proof_me_enabled: $data['proof_me_enabled'],
            telegram_bot_username: $data['telegram_bot_username'],
            whatsapp_number: $data['whatsapp_number'],
            telegram_deep_link: $data['telegram_deep_link'],
            whatsapp_deep_link: $data['whatsapp_deep_link'],
            qr_code: $data['qr_code'],
            display_name: $data['display_name'] ?? null,
            avatar_url: $data['avatar_url'] ?? null,
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
        public readonly ?string $suspended_at,
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
            suspended_at: $data['suspended_at'] ?? null,
            expires_at: $data['expires_at'] ?? null,
            is_expired: $data['is_expired'],
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
        public readonly string $reason,
        public readonly ?string $proof_id = null,
        public readonly ?string $proof_type = null,
        public readonly ?string $verification_id = null,
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
            reason: $data['reason'],
            proof_id: $data['proof_id'] ?? null,
            proof_type: $data['proof_type'] ?? null,
            verification_id: $data['verification_id'] ?? null,
        );
    }
}

/**
 * Entry in the revocation list
 */
class RevokedProof
{
    public function __construct(
        public readonly ?string $proof_id,
        public readonly ?string $public_proof_id,
        public readonly string $proof_type,
        public readonly ?string $revoked_at,
        public readonly ?string $reason,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            proof_id: $data['proof_id'] ?? null,
            public_proof_id: $data['public_proof_id'] ?? null,
            proof_type: $data['proof_type'],
            revoked_at: $data['revoked_at'] ?? null,
            reason: $data['reason'] ?? null,
        );
    }
}

/**
 * Signed list of revoked proofs
 */
class RevocationList
{
    /**
     * @param list<RevokedProof> $revoked
     */
    public function __construct(
        public readonly array $revoked,
        public readonly string $issued_at,
        public readonly float $count,
        public readonly bool $has_more,
        public readonly string $status_list_uri,
        public readonly ?string $signature,
        public readonly ?string $kid,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            revoked: array_map(static fn (array $item) => RevokedProof::fromArray($item), $data['revoked']),
            issued_at: $data['issued_at'],
            count: $data['count'],
            has_more: $data['has_more'],
            status_list_uri: $data['status_list_uri'],
            signature: $data['signature'] ?? null,
            kid: $data['kid'] ?? null,
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
 * Proof validation result. Returns verification details when valid, or reason/message when invalid. A DELEGATION token answers with `token_type: "delegation"` and the `delegation` block instead of `verification`; its status resolves the cascade down to the domain control proof, which neither the status list (it packs each row's own bit) nor an offline signature check can show. Delegation-specific reasons: `unknown_delegation` (no registry record — delegations are minted fail-closed, so this is invalid, not merely unresolvable), `status_unavailable`, and `identifier_unverifiable` when an `identifier` binding is requested for a delegation.
 */
class ValidateProofResponse
{
    public function __construct(
        public readonly bool $valid,
        public readonly ?string $proof_id = null,
        public readonly ?array $verification = null,
        public readonly ?string $reason = null,
        public readonly ?string $suspended_at = null,
        public readonly ?string $message = null,
        public readonly ?string $revoked_at = null,
        public readonly ?string $revoked_reason = null,
        public readonly ?string $token_type = null,
        public readonly ?array $delegation = null,
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
            proof_id: $data['proof_id'] ?? null,
            verification: $data['verification'] ?? null,
            reason: $data['reason'] ?? null,
            suspended_at: $data['suspended_at'] ?? null,
            message: $data['message'] ?? null,
            revoked_at: $data['revoked_at'] ?? null,
            revoked_reason: $data['revoked_reason'] ?? null,
            token_type: $data['token_type'] ?? null,
            delegation: $data['delegation'] ?? null,
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
        public readonly mixed $confidence = null,
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
            confidence: $data['confidence'] ?? null,
        );
    }
}

// ============================================================================
// Templates
// ============================================================================

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
        public readonly ?string $profile_id = null,
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
            profile_id: $data['profile_id'] ?? null,
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

class TemplateScopeQuery
{
    public function __construct(
        public readonly mixed $profile_id = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            profile_id: $data['profile_id'] ?? null,
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
// API Keys
// ============================================================================

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
        public readonly ?string $profile_id = null,
        public readonly ?string $last_used_at = null,
        public readonly ?string $expires_at = null,
        public readonly ?string $revoked_at = null,
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
            profile_id: $data['profile_id'] ?? null,
            last_used_at: $data['last_used_at'] ?? null,
            expires_at: $data['expires_at'] ?? null,
            revoked_at: $data['revoked_at'] ?? null,
        );
    }
}

/**
 * Request body for creating an API key
 */
class CreateAPIKeyRequest
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $environment = null,
        public readonly ?array $scopes = null,
        public readonly mixed $profile_id = null,
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
            profile_id: $data['profile_id'] ?? null,
        );
    }
}

// ============================================================================
// Webhook Deliveries
// ============================================================================

class RetryDeliveryResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly string $delivery_id,
        public readonly string $next_retry_at,
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
            delivery_id: $data['delivery_id'],
            next_retry_at: $data['next_retry_at'],
        );
    }
}

/**
 * Webhook delivery statistics
 */
class WebhookDeliveryStats
{
    public function __construct(
        public readonly int $total,
        public readonly array $by_status,
        public readonly string $delivery_rate,
        public readonly int $recent_failures_24h,
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
            by_status: $data['by_status'],
            delivery_rate: $data['delivery_rate'],
            recent_failures_24h: $data['recent_failures_24h'],
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
        public readonly ?string $verification_request_id,
        public readonly string $event_type,
        public readonly string $url,
        public readonly string $status,
        public readonly int $attempts,
        public readonly int $max_attempts,
        public readonly ?string $last_attempt_at,
        public readonly ?string $next_retry_at,
        public readonly ?string $delivered_at,
        public readonly ?int $response_status,
        public readonly ?string $error_message,
        public readonly string $created_at,
        public readonly array $payload,
        public readonly ?string $response_body,
        public readonly string $updated_at,
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
            verification_request_id: $data['verification_request_id'] ?? null,
            event_type: $data['event_type'],
            url: $data['url'],
            status: $data['status'],
            attempts: $data['attempts'],
            max_attempts: $data['max_attempts'],
            last_attempt_at: $data['last_attempt_at'] ?? null,
            next_retry_at: $data['next_retry_at'] ?? null,
            delivered_at: $data['delivered_at'] ?? null,
            response_status: $data['response_status'] ?? null,
            error_message: $data['error_message'] ?? null,
            created_at: $data['created_at'],
            payload: $data['payload'],
            response_body: $data['response_body'] ?? null,
            updated_at: $data['updated_at'],
        );
    }
}

/**
 * Webhook delivery as listed: the payload and the receiver's response body are only on the detail
 */
class WebhookDeliveryListItem
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $verification_request_id,
        public readonly string $event_type,
        public readonly string $url,
        public readonly string $status,
        public readonly int $attempts,
        public readonly int $max_attempts,
        public readonly ?string $last_attempt_at,
        public readonly ?string $next_retry_at,
        public readonly ?string $delivered_at,
        public readonly ?int $response_status,
        public readonly ?string $error_message,
        public readonly string $created_at,
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
            verification_request_id: $data['verification_request_id'] ?? null,
            event_type: $data['event_type'],
            url: $data['url'],
            status: $data['status'],
            attempts: $data['attempts'],
            max_attempts: $data['max_attempts'],
            last_attempt_at: $data['last_attempt_at'] ?? null,
            next_retry_at: $data['next_retry_at'] ?? null,
            delivered_at: $data['delivered_at'] ?? null,
            response_status: $data['response_status'] ?? null,
            error_message: $data['error_message'] ?? null,
            created_at: $data['created_at'],
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
    /**
     * @param list<WebhookDeliveryListItem> $data
     */
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
            data: array_map(static fn (array $item) => WebhookDeliveryListItem::fromArray($item), $data['data']),
            pagination: PaginationResponse::fromArray($data['pagination']),
        );
    }
}

/**
 * Proof of a verified asset included in webhook payload
 */
class WebhookProof
{
    public function __construct(
        public readonly string $type,
        public readonly ?string $identifier,
        public readonly ?string $channel,
        public readonly ?string $verified_at = null,
        public readonly ?string $proof_token = null,
        public readonly mixed $confidence = null,
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
            verified_at: $data['verified_at'] ?? null,
            proof_token: $data['proof_token'] ?? null,
            confidence: $data['confidence'] ?? null,
        );
    }
}

/**
 * Outbound webhook payload for verification request events (`verification_request.*`), and for `confirmation.approved` when it comes from a verification request created with `action_type: "confirmation"`
 */
class WebhookPayload
{
    /**
     * @param list<WebhookProof> $proofs
     */
    public function __construct(
        public readonly string $event,
        public readonly string $request_id,
        public readonly string $status,
        public readonly string $action_type,
        public readonly array $proofs,
        public readonly array $progress,
        public readonly ?string $reference_id = null,
        public readonly ?ActionContext $action_context = null,
        public readonly ?array $client_metadata = null,
        public readonly ?string $completed_at = null,
        public readonly ?bool $test = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            event: $data['event'],
            request_id: $data['request_id'],
            status: $data['status'],
            action_type: $data['action_type'],
            proofs: array_map(static fn (array $item) => WebhookProof::fromArray($item), $data['proofs']),
            progress: $data['progress'],
            reference_id: $data['reference_id'] ?? null,
            action_context: isset($data['action_context']) ? ActionContext::fromArray($data['action_context']) : null,
            client_metadata: $data['client_metadata'] ?? null,
            completed_at: $data['completed_at'] ?? null,
            test: $data['test'] ?? null,
        );
    }
}

/**
 * Outbound webhook configuration. The signing secret is created with the first endpoint; deliveries carry it as an HMAC-SHA256 `X-Proof-Signature` over `<X-Proof-Timestamp>.<body>`.
 */
class WebhookSettings
{
    public function __construct(
        public readonly ?string $url = null,
        public readonly ?array $events = null,
        public readonly ?bool $rotate_secret = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'] ?? null,
            events: $data['events'] ?? null,
            rotate_secret: $data['rotate_secret'] ?? null,
        );
    }
}

/**
 * Outbound webhook configuration as stored.
 */
class WebhookSettingsResponse
{
    public function __construct(
        public readonly ?string $url,
        public readonly array $events,
        public readonly bool $secret_set,
        public readonly ?string $secret = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'] ?? null,
            events: $data['events'],
            secret_set: $data['secret_set'],
            secret: $data['secret'] ?? null,
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

/**
 * The 201 of the public demo create. `success` is always true (a failure answers with an error envelope) and `request_id` is the id every subsequent public read takes — `GET /api/v1/verify/request/{id}` and the per-asset status poll. Deliberately NOT the shared `VerificationRequest` component: this path is public, and that component carries the authenticated-owner-only `computed_state` envelope, which must not be advertised here. It names `request_id` rather than `id` — the field the controller has always sent.
 */
class TryItResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $verification_url,
        public readonly string $request_id,
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
            verification_url: $data['verification_url'],
            request_id: $data['request_id'],
            expires_at: $data['expires_at'],
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
        public readonly ?string $phone_id = null,
        public readonly ?int $ttl_seconds = null,
        public readonly ?bool $single_use = null,
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
            phone_id: $data['phone_id'] ?? null,
            ttl_seconds: $data['ttl_seconds'] ?? null,
            single_use: $data['single_use'] ?? null,
        );
    }
}
