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
        public readonly ?array $components = null,
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
            components: $data['components'] ?? null,
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

/**
 * The emailed cancel link still acts; nothing was changed by this read
 */
class AccountDeletionCancelTokenCheck
{
    public function __construct(
        public readonly string $valid,
        public readonly string $state,
        public readonly ?string $scheduled_for,
        public readonly ?string $hold_reason,
        public readonly ?string $display_name,
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
            state: $data['state'],
            scheduled_for: $data['scheduled_for'] ?? null,
            hold_reason: $data['hold_reason'] ?? null,
            display_name: $data['display_name'] ?? null,
        );
    }
}

/**
 * The deletion is cancelled and the lock lifted
 */
class AccountDeletionCancelledResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $ok,
        public readonly string $was_state,
        public readonly int $proofs_unsuspended,
        public readonly int $proofs_kept_suspended,
        public readonly array $not_restored,
        public readonly array $notice,
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
            ok: $data['ok'],
            was_state: $data['was_state'],
            proofs_unsuspended: $data['proofs_unsuspended'],
            proofs_kept_suspended: $data['proofs_kept_suspended'],
            not_restored: $data['not_restored'],
            notice: $data['notice'],
        );
    }
}

/**
 * The account is locked and its deletion scheduled
 */
class AccountDeletionScheduledResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $state,
        public readonly string $scheduled_for,
        public readonly bool $already_scheduled,
        public readonly int $proofs_suspended,
        public readonly array $notice,
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
            state: $data['state'],
            scheduled_for: $data['scheduled_for'],
            already_scheduled: $data['already_scheduled'],
            proofs_suspended: $data['proofs_suspended'],
            notice: $data['notice'],
        );
    }
}

/**
 * Where the account stands with its deletion — the lock page's read
 */
class AccountDeletionStatus
{
    public function __construct(
        public readonly string $state,
        public readonly ?string $scheduled_for,
        public readonly ?string $requested_at,
        public readonly ?string $hold_reason,
        public readonly ?array $final_invoice,
        public readonly bool $can_cancel,
        public readonly int $grace_days,
        public readonly bool $delayed,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            state: $data['state'],
            scheduled_for: $data['scheduled_for'] ?? null,
            requested_at: $data['requested_at'] ?? null,
            hold_reason: $data['hold_reason'] ?? null,
            final_invoice: $data['final_invoice'] ?? null,
            can_cancel: $data['can_cancel'],
            grace_days: $data['grace_days'],
            delayed: $data['delayed'],
        );
    }
}

/**
 * One data export of the account — prepared in the background, downloadable only by the signed-in owner
 */
class AccountExportJob
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly ?string $error,
        public readonly string $requested_at,
        public readonly ?string $ready_at,
        public readonly string $expires_at,
        public readonly ?string $downloaded_at,
        public readonly ?int $size_bytes,
        public readonly array $counts,
        public readonly ?string $download_path,
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
            error: $data['error'] ?? null,
            requested_at: $data['requested_at'],
            ready_at: $data['ready_at'] ?? null,
            expires_at: $data['expires_at'],
            downloaded_at: $data['downloaded_at'] ?? null,
            size_bytes: $data['size_bytes'] ?? null,
            counts: $data['counts'],
            download_path: $data['download_path'] ?? null,
        );
    }
}

/**
 * A signed receipt for one download of an export; kept after the export and after the account is deleted
 */
class AccountExportReceipt
{
    public function __construct(
        public readonly string $id,
        public readonly string $export_id,
        public readonly string $downloaded_by,
        public readonly string $downloaded_at,
        public readonly string $sha256,
        public readonly int $size_bytes,
        public readonly string $receipt,
        public readonly bool $notice_suppressed,
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
            export_id: $data['export_id'],
            downloaded_by: $data['downloaded_by'],
            downloaded_at: $data['downloaded_at'],
            sha256: $data['sha256'],
            size_bytes: $data['size_bytes'],
            receipt: $data['receipt'],
            notice_suppressed: $data['notice_suppressed'],
        );
    }
}

/**
 * The account's most recent export, or null, and the receipts of its downloads
 */
class AccountExportLatest
{
    /**
     * @param list<AccountExportReceipt>|null $receipts
     */
    public function __construct(
        public readonly ?AccountExportJob $latest,
        public readonly ?array $receipts = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            latest: isset($data['latest']) ? AccountExportJob::fromArray($data['latest']) : null,
            receipts: isset($data['receipts']) ? array_map(static fn (array $item) => AccountExportReceipt::fromArray($item), $data['receipts']) : null,
        );
    }
}

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
 * One place the claim check read, and what it found there
 */
class ArtifactClaimLocation
{
    public function __construct(
        public readonly string $id,
        public readonly string $location,
        public readonly ?string $url,
        public readonly string $result,
        public readonly ?string $reason,
        public readonly string $detail,
        public readonly string $checked_at,
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
            location: $data['location'],
            url: $data['url'] ?? null,
            result: $data['result'],
            reason: $data['reason'] ?? null,
            detail: $data['detail'],
            checked_at: $data['checked_at'],
        );
    }
}

/**
 * A public profile of the delegating account that shows the principal domain. Exactly one of the two fields is present
 */
class ArtifactClaimProfileLink
{
    public function __construct(
        public readonly ?string $username = null,
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
            username: $data['username'] ?? null,
            profile_id: $data['profile_id'] ?? null,
        );
    }
}

/**
 * An in-force delegation whose token was found in at least one checked location
 */
class ArtifactClaimDelegation
{
    public function __construct(
        public readonly string $principal,
        public readonly string $proof_id,
        public readonly array $scope,
        public readonly string $issued_at,
        public readonly string $expires_at,
        public readonly string $token,
        public readonly array $found_at,
        public readonly ?ArtifactClaimProfileLink $profile,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            principal: $data['principal'],
            proof_id: $data['proof_id'],
            scope: $data['scope'],
            issued_at: $data['issued_at'],
            expires_at: $data['expires_at'],
            token: $data['token'],
            found_at: $data['found_at'],
            profile: isset($data['profile']) ? ArtifactClaimProfileLink::fromArray($data['profile']) : null,
        );
    }
}

/**
 * Whether a domain has published a delegation for this exact artifact, and every place that was checked. A delegation attests who authorized the artifact — not that it is safe, audited or endorsed
 */
class ArtifactClaim
{
    /**
     * @param list<ArtifactClaimLocation> $locations
     * @param list<ArtifactClaimDelegation> $delegations
     */
    public function __construct(
        public readonly string $delegate,
        public readonly string $delegate_type,
        public readonly ?string $artifact_path,
        public readonly string $claim,
        public readonly string $checked_at,
        public readonly array $locations,
        public readonly array $delegations,
        public readonly ?array $listing,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            delegate: $data['delegate'],
            delegate_type: $data['delegate_type'],
            artifact_path: $data['artifact_path'] ?? null,
            claim: $data['claim'],
            checked_at: $data['checked_at'],
            locations: array_map(static fn (array $item) => ArtifactClaimLocation::fromArray($item), $data['locations']),
            delegations: array_map(static fn (array $item) => ArtifactClaimDelegation::fromArray($item), $data['delegations']),
            listing: $data['listing'] ?? null,
        );
    }
}

/**
 * Artifacts whose stored answer is `claimed` and which have a page, ordered by delegate
 */
class ArtifactClaimedList
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

class AssetRevocationItem
{
    public function __construct(
        public readonly string $asset_id,
        public readonly string $outcome,
        public readonly ?string $type = null,
        public readonly ?string $identifier = null,
        public readonly ?array $revoked_asset_ids = null,
        public readonly ?array $delegations_revoked = null,
        public readonly ?string $domain_status = null,
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
            outcome: $data['outcome'],
            type: $data['type'] ?? null,
            identifier: $data['identifier'] ?? null,
            revoked_asset_ids: $data['revoked_asset_ids'] ?? null,
            delegations_revoked: $data['delegations_revoked'] ?? null,
            domain_status: $data['domain_status'] ?? null,
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
 * Switch auto top-up on or off. Switching it on needs a card saved by an earlier top-up.
 */
class AutoTopUpRequest
{
    public function __construct(
        public readonly bool $enabled,
        public readonly mixed $amount = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            enabled: $data['enabled'],
            amount: $data['amount'] ?? null,
        );
    }
}

/**
 * The prepaid balance, from which proofs past the plan's included amount are drawn
 */
class BalanceResponse
{
    public function __construct(
        public readonly bool $billing_enabled,
        public readonly array $balance,
        public readonly int $low_balance_threshold,
        public readonly array $top_up_amounts,
        public readonly array $auto_top_up,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            billing_enabled: $data['billing_enabled'],
            balance: $data['balance'],
            low_balance_threshold: $data['low_balance_threshold'],
            top_up_amounts: $data['top_up_amounts'],
            auto_top_up: $data['auto_top_up'],
        );
    }
}

/**
 * A sub-account's callback signing secret.
 */
class CallbackSecret
{
    public function __construct(
        public readonly string $profile_id,
        public readonly string $secret,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            profile_id: $data['profile_id'],
            secret: $data['secret'],
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
 * The account's company profile: who the client is, printed on its invoices.
 */
class CompanyProfile
{
    public function __construct(
        public readonly string $legal_name,
        public readonly ?string $registration_code,
        public readonly string $country,
        public readonly ?string $vat_number,
        public readonly bool $no_vat_number,
        public readonly string $address_line1,
        public readonly ?string $address_line2,
        public readonly string $city,
        public readonly string $postal_code,
        public readonly string $billing_email,
        public readonly ?string $updated_at,
        public readonly array $vat_check,
        public readonly bool $complete,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            legal_name: $data['legal_name'],
            registration_code: $data['registration_code'] ?? null,
            country: $data['country'],
            vat_number: $data['vat_number'] ?? null,
            no_vat_number: $data['no_vat_number'],
            address_line1: $data['address_line1'],
            address_line2: $data['address_line2'] ?? null,
            city: $data['city'],
            postal_code: $data['postal_code'],
            billing_email: $data['billing_email'],
            updated_at: $data['updated_at'] ?? null,
            vat_check: $data['vat_check'],
            complete: $data['complete'],
        );
    }
}

/**
 * The account's company profile.
 */
class CompanyResponse
{
    public function __construct(
        public readonly ?CompanyProfile $company,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            company: isset($data['company']) ? CompanyProfile::fromArray($data['company']) : null,
        );
    }
}

/**
 * One "Send again": when, who asked, and which channels it went to
 */
class ConfirmationResend
{
    public function __construct(
        public readonly string $at,
        public readonly array $actor,
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
            at: $data['at'],
            actor: $data['actor'],
            channels: $data['channels'],
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
 * A new webhook endpoint. A sub-account key may create only one scoped `selected` to exactly its own sub-account.
 */
class CreateWebhookEndpointRequest
{
    public function __construct(
        public readonly string $url,
        public readonly string $scope,
        public readonly ?array $events = null,
        public readonly ?array $profile_ids = null,
        public readonly ?string $status = null,
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
            url: $data['url'],
            scope: $data['scope'],
            events: $data['events'] ?? null,
            profile_ids: $data['profile_ids'] ?? null,
            status: $data['status'] ?? null,
            description: $data['description'] ?? null,
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
 * Create a delegation from a proven domain to an artifact. Strict, like the request validator: an unknown key is rejected with `400 validation_error`; the deprecated `scope` is accepted and ignored.
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
 * Where a delegation is still visible to a third party
 */
class DelegationCarrier
{
    public function __construct(
        public readonly string $carrier,
        public readonly string $state,
        public readonly string $reason,
        public readonly ?string $cardUrl = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            carrier: $data['carrier'],
            state: $data['state'],
            reason: $data['reason'],
            cardUrl: $data['cardUrl'] ?? null,
        );
    }
}

/**
 * Where this delegation is still visible to a third party
 */
class DelegationCarriersResponse
{
    /**
     * @param list<DelegationCarrier> $carriers
     */
    public function __construct(
        public readonly array $carriers,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            carriers: array_map(static fn (array $item) => DelegationCarrier::fromArray($item), $data['carriers']),
        );
    }
}

/**
 * The current token of an active delegation
 */
class DelegationTokenResponse
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
 * What one DNS provider needs and can do. The keys are camelCase as sent; this body predates the snake_case convention and is kept as it is.
 */
class DnsProviderMetadataItem
{
    public function __construct(
        public readonly string $name,
        public readonly array $requiredCredentials,
        public readonly array $optionalCredentials,
        public readonly array $capabilities,
        public readonly ?string $apiDocUrl = null,
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
            requiredCredentials: $data['requiredCredentials'],
            optionalCredentials: $data['optionalCredentials'],
            capabilities: $data['capabilities'],
            apiDocUrl: $data['apiDocUrl'] ?? null,
        );
    }
}

/**
 * The DNS providers credentials can be saved for
 */
class DnsProvidersResponse
{
    public function __construct(
        public readonly array $providers,
        public readonly int $count,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            providers: $data['providers'],
            count: $data['count'],
        );
    }
}

/**
 * The keypair stored
 */
class HitlKeysPutResponse
{
    public function __construct(
        public readonly string $kid,
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
            kid: $data['kid'],
            message: $data['message'],
        );
    }
}

/**
 * A HITL config's keypair. A caller with `hitl:read` (or a dashboard session) gets every field; a key holding `confirmations:create` without `hitl:read` gets `public_key` and `kid` only.
 */
class HitlKeysResponse
{
    public function __construct(
        public readonly ?string $public_key,
        public readonly string $kid,
        public readonly ?string $encrypted_private_key = null,
        public readonly ?string $kdf_salt = null,
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
            public_key: $data['public_key'] ?? null,
            kid: $data['kid'],
            encrypted_private_key: $data['encrypted_private_key'] ?? null,
            kdf_salt: $data['kdf_salt'] ?? null,
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
 * What removing a phone or email would do; `allowed: false` carries the reason instead of the counts.
 */
class IdentifierRemovalPreview
{
    public function __construct(
        public readonly bool $allowed,
        public readonly ?array $blocked,
        public readonly ?string $type = null,
        public readonly ?string $identifier = null,
        public readonly ?bool $is_primary = null,
        public readonly ?int $proofs = null,
        public readonly ?int $authorizations = null,
        public readonly ?array $hitl_configs_without_approver = null,
        public readonly ?int $circles = null,
        public readonly ?array $new_primary = null,
        public readonly ?string $lookalike_watch = null,
        public readonly ?array $notice_to = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            allowed: $data['allowed'],
            blocked: $data['blocked'] ?? null,
            type: $data['type'] ?? null,
            identifier: $data['identifier'] ?? null,
            is_primary: $data['is_primary'] ?? null,
            proofs: $data['proofs'] ?? null,
            authorizations: $data['authorizations'] ?? null,
            hitl_configs_without_approver: $data['hitl_configs_without_approver'] ?? null,
            circles: $data['circles'] ?? null,
            new_primary: $data['new_primary'] ?? null,
            lookalike_watch: $data['lookalike_watch'] ?? null,
            notice_to: $data['notice_to'] ?? null,
        );
    }
}

/**
 * The cascade a removal performed, line by line — the same lines the notice carries.
 */
class IdentifierRemovedResponse
{
    public function __construct(
        public readonly string $success,
        public readonly string $type,
        public readonly string $removed_id,
        public readonly string $identifier,
        public readonly int $proofs_revoked,
        public readonly int $authorizations_revoked,
        public readonly array $hitl_configs_without_approver,
        public readonly int $circles_repointed,
        public readonly ?array $new_primary,
        public readonly string $lookalike_watch,
        public readonly int $sessions_invalidated,
        public readonly array $notice,
        public readonly ?bool $sessions_signed_out = null,
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
            type: $data['type'],
            removed_id: $data['removed_id'],
            identifier: $data['identifier'],
            proofs_revoked: $data['proofs_revoked'],
            authorizations_revoked: $data['authorizations_revoked'],
            hitl_configs_without_approver: $data['hitl_configs_without_approver'],
            circles_repointed: $data['circles_repointed'],
            new_primary: $data['new_primary'] ?? null,
            lookalike_watch: $data['lookalike_watch'],
            sessions_invalidated: $data['sessions_invalidated'],
            notice: $data['notice'],
            sessions_signed_out: $data['sessions_signed_out'] ?? null,
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
 * One row of the prepaid balance history
 */
class LedgerEntry
{
    public function __construct(
        public readonly string $id,
        public readonly string $kind,
        public readonly int $amount,
        public readonly string $currency,
        public readonly int $mills,
        public readonly ?int $proofs,
        public readonly ?string $period,
        public readonly ?string $invoice_id,
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
            kind: $data['kind'],
            amount: $data['amount'],
            currency: $data['currency'],
            mills: $data['mills'],
            proofs: $data['proofs'] ?? null,
            period: $data['period'] ?? null,
            invoice_id: $data['invoice_id'] ?? null,
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
        );
    }
}

/**
 * One page of the prepaid balance history, newest first
 */
class LedgerListResponse
{
    /**
     * @param list<LedgerEntry> $data
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
            data: array_map(static fn (array $item) => LedgerEntry::fromArray($item), $data['data']),
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
            type: $data['type'],
            identifier: $data['identifier'],
            channels: $data['channels'],
            external_user_id: $data['external_user_id'] ?? null,
            client_metadata: $data['client_metadata'] ?? null,
            proof_expiry_days: $data['proof_expiry_days'] ?? null,
            profile_id: $data['profile_id'] ?? null,
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
 * Per-API-key usage: verification counts by type/channel/status + per-collection totals (requests, confirmations, authorizations) + attribution metadata. The counts cover the key and every key it replaced (`api_key.replaced_key_ids`). Counts only — no recipient identifiers (SEC-001). Data is returned for revoked keys (audit).
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

/**
 * Whether the signed-in caller holds saved DNS provider credentials that can verify this domain asset
 */
class AssetCredentialsCheck
{
    public function __construct(
        public readonly bool $authenticated,
        public readonly bool $has_credentials,
        public readonly ?string $message = null,
        public readonly ?array $credentials = null,
        public readonly ?string $domain = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            authenticated: $data['authenticated'],
            has_credentials: $data['has_credentials'],
            message: $data['message'] ?? null,
            credentials: $data['credentials'] ?? null,
            domain: $data['domain'] ?? null,
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
    /**
     * @param list<ConfirmationResend>|null $resends
     */
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
        public readonly ?array $resends = null,
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
            resends: isset($data['resends']) ? array_map(static fn (array $item) => ConfirmationResend::fromArray($item), $data['resends']) : null,
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
            hitl_id: $data['hitl_id'] ?? null,
            start_date: $data['start_date'] ?? null,
            end_date: $data['end_date'] ?? null,
            profile_id: $data['profile_id'] ?? null,
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
 * Counts only — the response never carries a link
 */
class ConfirmationResendResponse
{
    public function __construct(
        public readonly string $confirmation_id,
        public readonly int $resent,
        public readonly int $skipped,
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
            resent: $data['resent'],
            skipped: $data['skipped'],
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
            event: $data['event'],
            confirmation_id: $data['confirmation_id'],
            hitl_id: $data['hitl_id'],
            status: $data['status'],
            message: $data['message'],
            encrypted: $data['encrypted'],
            response: $data['response'] ?? null,
            proof_token: $data['proof_token'] ?? null,
            resolved_at: $data['resolved_at'] ?? null,
            profile_id: $data['profile_id'] ?? null,
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
 * A saved DNS provider credential. The secret is never returned.
 */
class DnsCredentialResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $provider,
        public readonly string $name,
        public readonly bool $is_valid,
        public readonly int $domains_count,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly ?string $last_used_at = null,
        public readonly ?string $last_validated_at = null,
        public readonly ?string $validation_error = null,
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
            name: $data['name'],
            is_valid: $data['is_valid'],
            domains_count: $data['domains_count'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
            last_used_at: $data['last_used_at'] ?? null,
            last_validated_at: $data['last_validated_at'] ?? null,
            validation_error: $data['validation_error'] ?? null,
        );
    }
}

/**
 * The account's saved DNS provider credentials, ordered by provider
 */
class DnsCredentialListResponse
{
    /**
     * @param list<DnsCredentialResponse> $data
     */
    public function __construct(
        public readonly array $data,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            data: array_map(static fn (array $item) => DnsCredentialResponse::fromArray($item), $data['data']),
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
 * An enrolled approver's public key. `authorization_id` is the approver's consent, which a v2 envelope is keyed by. `recipient` and `channel` are absent for a key holding `confirmations:create` without `hitl:read`.
 */
class HitlApproverKey
{
    public function __construct(
        public readonly string $authorization_id,
        public readonly ?string $public_key,
        public readonly string $kid,
        public readonly ?string $recipient = null,
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
            authorization_id: $data['authorization_id'],
            public_key: $data['public_key'] ?? null,
            kid: $data['kid'],
            recipient: $data['recipient'] ?? null,
            channel: $data['channel'] ?? null,
        );
    }
}

/**
 * The approvers enrolled against a HITL config with a live consent
 */
class HitlApproverKeysResponse
{
    /**
     * @param list<HitlApproverKey> $approver_keys
     */
    public function __construct(
        public readonly array $approver_keys,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            approver_keys: array_map(static fn (array $item) => HitlApproverKey::fromArray($item), $data['approver_keys']),
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
 * An artifact whose name resembles one of the brand tokens, with the evidence checked
 */
class LookalikeAlert
{
    public function __construct(
        public readonly string $id,
        public readonly string $registry,
        public readonly string $artifact_name,
        public readonly ?string $canonical_id,
        public readonly string $match_kind,
        public readonly string $brand_token,
        public readonly string $match_evidence,
        public readonly array $evidence,
        public readonly string $first_seen_at,
        public readonly ?string $notified_at,
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
            registry: $data['registry'],
            artifact_name: $data['artifact_name'],
            canonical_id: $data['canonical_id'] ?? null,
            match_kind: $data['match_kind'],
            brand_token: $data['brand_token'],
            match_evidence: $data['match_evidence'],
            evidence: $data['evidence'],
            first_seen_at: $data['first_seen_at'],
            notified_at: $data['notified_at'] ?? null,
            created_at: $data['created_at'],
        );
    }
}

/**
 * A page of alerts, newest first
 */
class LookalikeAlertList
{
    /**
     * @param list<LookalikeAlert> $data
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
            data: array_map(static fn (array $item) => LookalikeAlert::fromArray($item), $data['data']),
            pagination: $data['pagination'],
        );
    }
}

/**
 * One month's report; a month with no alerts is a report too
 */
class LookalikeReport
{
    public function __construct(
        public readonly string $id,
        public readonly string $period,
        public readonly string $period_start,
        public readonly string $period_end,
        public readonly int $alert_count,
        public readonly array $artifacts_checked,
        public readonly array $bounds,
        public readonly ?string $sent_at,
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
            period: $data['period'],
            period_start: $data['period_start'],
            period_end: $data['period_end'],
            alert_count: $data['alert_count'],
            artifacts_checked: $data['artifacts_checked'],
            bounds: $data['bounds'],
            sent_at: $data['sent_at'] ?? null,
        );
    }
}

/**
 * A page of monthly reports, latest first
 */
class LookalikeReportList
{
    /**
     * @param list<LookalikeReport> $data
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
            data: array_map(static fn (array $item) => LookalikeReport::fromArray($item), $data['data']),
            pagination: $data['pagination'],
        );
    }
}

/**
 * The account's Lookalike Watch subscription. Subscribe, read and cancel all answer it; only the cancel answer reads `canceled`, since a read after cancelling answers 404.
 */
class LookalikeWatchSubscription
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly array $brand_tokens,
        public readonly array $owned_namespaces,
        public readonly string $notify_email,
        public readonly ?string $activated_at,
        public readonly ?string $last_report_period,
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
            status: $data['status'],
            brand_tokens: $data['brand_tokens'],
            owned_namespaces: $data['owned_namespaces'],
            notify_email: $data['notify_email'],
            activated_at: $data['activated_at'] ?? null,
            last_report_period: $data['last_report_period'] ?? null,
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
        );
    }
}

/**
 * What a plan change would cost now and when it would take effect. Nothing is changed.
 */
class PlanChangePreviewResponse
{
    public function __construct(
        public readonly string $plan,
        public readonly string $change,
        public readonly int $amount_due,
        public readonly string $currency,
        public readonly ?string $effective_at,
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
            change: $data['change'],
            amount_due: $data['amount_due'],
            currency: $data['currency'],
            effective_at: $data['effective_at'] ?? null,
        );
    }
}

/**
 * The plan to move to. Custom is agreed per customer and cannot be chosen here.
 */
class PlanChangeRequest
{
    public function __construct(
        public readonly string $plan,
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
        );
    }
}

/**
 * The outcome of a plan change
 */
class PlanChangeResponse
{
    public function __construct(
        public readonly string $plan,
        public readonly string $change,
        public readonly string $status,
        public readonly ?string $effective_at,
        public readonly ?string $payment_url,
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
            change: $data['change'],
            status: $data['status'],
            effective_at: $data['effective_at'] ?? null,
            payment_url: $data['payment_url'] ?? null,
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
 * A custom link on a sub-account, as stored (with its id)
 */
class ProfileOwnerLink
{
    public function __construct(
        public readonly string $_id,
        public readonly string $title,
        public readonly string $url,
        public readonly bool $is_visible,
        public readonly int $display_order,
        public readonly ?string $icon = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            _id: $data['_id'],
            title: $data['title'],
            url: $data['url'],
            is_visible: $data['is_visible'],
            display_order: $data['display_order'],
            icon: $data['icon'] ?? null,
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
 * A proof the owner chose to show on a sub-account, as the owner sees it
 */
class PublicProofItem
{
    public function __construct(
        public readonly string $asset_id,
        public readonly string $type,
        public readonly string $display_value,
        public readonly string $verified_at,
        public readonly bool $is_visible,
        public readonly int $display_order,
        public readonly string $mask_level,
        public readonly ?string $label = null,
        public readonly ?string $channel = null,
        public readonly ?bool $is_revoked = null,
        public readonly ?string $delegate_type = null,
        public readonly ?string $delegation_id = null,
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
            type: $data['type'],
            display_value: $data['display_value'],
            verified_at: $data['verified_at'],
            is_visible: $data['is_visible'],
            display_order: $data['display_order'],
            mask_level: $data['mask_level'],
            label: $data['label'] ?? null,
            channel: $data['channel'] ?? null,
            is_revoked: $data['is_revoked'] ?? null,
            delegate_type: $data['delegate_type'] ?? null,
            delegation_id: $data['delegation_id'] ?? null,
        );
    }
}

/**
 * A sub-account as its owner sees it
 */
class ProfileOwnerResponse
{
    /**
     * @param list<PublicProofItem> $public_proofs
     * @param list<ProfileOwnerLink> $custom_links
     */
    public function __construct(
        public readonly string $id,
        public readonly string $profile_id,
        public readonly bool $is_primary,
        public readonly string $verification_level,
        public readonly bool $is_business,
        public readonly array $public_proofs,
        public readonly string $proof_order,
        public readonly array $custom_links,
        public readonly ProfileTheme $theme,
        public readonly bool $is_public,
        public readonly bool $show_verification_dates,
        public readonly bool $show_proof_channels,
        public readonly bool $show_delegations,
        public readonly ?string $support_email,
        public readonly ?string $email_theme,
        public readonly int $view_count,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly ?string $username = null,
        public readonly ?string $display_name = null,
        public readonly ?string $bio = null,
        public readonly ?string $avatar_url = null,
        public readonly ?string $business_name = null,
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
            profile_id: $data['profile_id'],
            is_primary: $data['is_primary'],
            verification_level: $data['verification_level'],
            is_business: $data['is_business'],
            public_proofs: array_map(static fn (array $item) => PublicProofItem::fromArray($item), $data['public_proofs']),
            proof_order: $data['proof_order'],
            custom_links: array_map(static fn (array $item) => ProfileOwnerLink::fromArray($item), $data['custom_links']),
            theme: ProfileTheme::fromArray($data['theme']),
            is_public: $data['is_public'],
            show_verification_dates: $data['show_verification_dates'],
            show_proof_channels: $data['show_proof_channels'],
            show_delegations: $data['show_delegations'],
            support_email: $data['support_email'] ?? null,
            email_theme: $data['email_theme'] ?? null,
            view_count: $data['view_count'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
            username: $data['username'] ?? null,
            display_name: $data['display_name'] ?? null,
            bio: $data['bio'] ?? null,
            avatar_url: $data['avatar_url'] ?? null,
            business_name: $data['business_name'] ?? null,
        );
    }
}

/**
 * The account's sub-accounts, as their owner sees them
 */
class ProfileOwnerListResponse
{
    /**
     * @param list<ProfileOwnerResponse> $profiles
     */
    public function __construct(
        public readonly array $profiles,
        public readonly int $count,
        public readonly int $max_profiles,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            profiles: array_map(static fn (array $item) => ProfileOwnerResponse::fromArray($item), $data['profiles']),
            count: $data['count'],
            max_profiles: $data['max_profiles'],
        );
    }
}

/**
 * A sub-account's shown proofs after an update
 */
class PublicProofsResponse
{
    /**
     * @param list<PublicProofItem> $public_proofs
     */
    public function __construct(
        public readonly array $public_proofs,
        public readonly string $proof_order,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            public_proofs: array_map(static fn (array $item) => PublicProofItem::fromArray($item), $data['public_proofs']),
            proof_order: $data['proof_order'],
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
 * Readiness probe response — `ready` is true only when both MongoDB and Redis answer
 */
class ReadinessResponse
{
    public function __construct(
        public readonly bool $ready,
        public readonly array $checks,
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
            ready: $data['ready'],
            checks: $data['checks'],
            timestamp: $data['timestamp'],
        );
    }
}

/**
 * The session renewed; the new tokens travel in cookies. camelCase as sent; kept as it is because the dashboard reads these names.
 */
class RefreshSessionResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $expiresAt,
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
            expiresAt: $data['expiresAt'],
            expiresIn: $data['expiresIn'],
        );
    }
}

/**
 * A re-issued proof, by reference — never the token itself
 */
class ReissuedProofItem
{
    public function __construct(
        public readonly string $proof_id,
        public readonly string $reissued_at,
        public readonly string $token_url,
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
            reissued_at: $data['reissued_at'],
            token_url: $data['token_url'],
        );
    }
}

/**
 * Your proofs re-issued at or after `reissued_since`, oldest first
 */
class ReissuedProofListResponse
{
    /**
     * @param list<ReissuedProofItem> $data
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
            data: array_map(static fn (array $item) => ReissuedProofItem::fromArray($item), $data['data']),
            pagination: PaginationResponse::fromArray($data['pagination']),
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
 * The verification link was emailed
 */
class ShareVerificationRequestEmailResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly ?string $messageId = null,
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
            messageId: $data['messageId'] ?? null,
        );
    }
}

/**
 * Our SMS numbers, one entry per region and always all three. A region with no number configured carries an empty `did` and no QR code.
 */
class SmsNumbersByRegion
{
    public function __construct(
        public readonly SessionSmsNumber $americas,
        public readonly SessionSmsNumber $europe,
        public readonly SessionSmsNumber $israel,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            americas: SessionSmsNumber::fromArray($data['americas']),
            europe: SessionSmsNumber::fromArray($data['europe']),
            israel: SessionSmsNumber::fromArray($data['israel']),
        );
    }
}

/**
 * The account's subscription
 */
class SubscriptionResponse
{
    public function __construct(
        public readonly string $plan,
        public readonly ?string $status,
        public readonly ?string $current_period_start,
        public readonly ?string $current_period_end,
        public readonly bool $cancel_at_period_end,
        public readonly ?string $scheduled_plan = null,
        public readonly ?string $scheduled_change_at = null,
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
            status: $data['status'] ?? null,
            current_period_start: $data['current_period_start'] ?? null,
            current_period_end: $data['current_period_end'] ?? null,
            cancel_at_period_end: $data['cancel_at_period_end'],
            scheduled_plan: $data['scheduled_plan'] ?? null,
            scheduled_change_at: $data['scheduled_change_at'] ?? null,
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
 *
 * @deprecated Removed from the API (templates belong to a sub-account: use the templates resource with the sub-account's id); kept so code written for an earlier 1.x release keeps compiling, and dropped in the next major version.
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
 * Request body for buying a prepaid balance top-up
 */
class TopUpRequest
{
    public function __construct(
        public readonly mixed $amount,
        public readonly string $success_url,
        public readonly string $cancel_url,
        public readonly ?bool $auto_top_up = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            amount: $data['amount'],
            success_url: $data['success_url'],
            cancel_url: $data['cancel_url'],
            auto_top_up: $data['auto_top_up'] ?? null,
        );
    }
}

/**
 * Top-up checkout session creation response
 */
class TopUpResponse
{
    public function __construct(
        public readonly string $checkout_url,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            checkout_url: $data['checkout_url'],
        );
    }
}

/**
 * No session started: choose which address the code goes to
 */
class TwoFactorEmailSelectionResponse
{
    public function __construct(
        public readonly bool $requires_email_selection,
        public readonly array $available_emails,
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
            requires_email_selection: $data['requires_email_selection'],
            available_emails: $data['available_emails'],
            message: $data['message'],
        );
    }
}

/**
 * The step-up is complete
 */
class TwoFactorMagicLinkResponse
{
    public function __construct(
        public readonly string $status,
        public readonly string $action_type,
        public readonly ?bool $already_verified = null,
        public readonly ?string $verified_at = null,
        public readonly ?string $grant_expires_at = null,
        public readonly ?bool $grant_single_use = null,
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
            action_type: $data['action_type'],
            already_verified: $data['already_verified'] ?? null,
            verified_at: $data['verified_at'] ?? null,
            grant_expires_at: $data['grant_expires_at'] ?? null,
            grant_single_use: $data['grant_single_use'] ?? null,
        );
    }
}

/**
 * A started 2FA session
 */
class TwoFactorSessionResponse
{
    public function __construct(
        public readonly string $session_id,
        public readonly string $channel,
        public readonly string $status,
        public readonly string $expires_at,
        public readonly string $instructions,
        public readonly ?bool $second_method_recommended = null,
        public readonly ?string $deep_link = null,
        public readonly ?string $qr_code = null,
        public readonly ?string $qr_text = null,
        public readonly ?string $sms_code = null,
        public readonly ?string $sms_message = null,
        public readonly ?SmsNumbersByRegion $sms_dids = null,
        public readonly ?string $email = null,
        public readonly ?bool $email_sent = null,
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
            channel: $data['channel'],
            status: $data['status'],
            expires_at: $data['expires_at'],
            instructions: $data['instructions'],
            second_method_recommended: $data['second_method_recommended'] ?? null,
            deep_link: $data['deep_link'] ?? null,
            qr_code: $data['qr_code'] ?? null,
            qr_text: $data['qr_text'] ?? null,
            sms_code: $data['sms_code'] ?? null,
            sms_message: $data['sms_message'] ?? null,
            sms_dids: isset($data['sms_dids']) ? SmsNumbersByRegion::fromArray($data['sms_dids']) : null,
            email: $data['email'] ?? null,
            email_sent: $data['email_sent'] ?? null,
        );
    }
}

/**
 * A 2FA session's state; the grant fields are present once `status` is `verified`
 */
class TwoFactorStatusResponse
{
    public function __construct(
        public readonly string $session_id,
        public readonly string $status,
        public readonly string $action_type,
        public readonly ?string $verified_at = null,
        public readonly ?string $grant_expires_at = null,
        public readonly ?bool $grant_single_use = null,
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
            status: $data['status'],
            action_type: $data['action_type'],
            verified_at: $data['verified_at'] ?? null,
            grant_expires_at: $data['grant_expires_at'] ?? null,
            grant_single_use: $data['grant_single_use'] ?? null,
        );
    }
}

/**
 * The step-up is complete and its grant stored
 */
class TwoFactorVerifyResponse
{
    public function __construct(
        public readonly string $status,
        public readonly string $verified_at,
        public readonly string $grant_expires_at,
        public readonly bool $grant_single_use,
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
            verified_at: $data['verified_at'],
            grant_expires_at: $data['grant_expires_at'],
            grant_single_use: $data['grant_single_use'],
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
 * The whole company profile; a save replaces it. A VAT number must be an EU number belonging to `country` — refused otherwise with `400 vat_number_not_eu`, `vat_country_mismatch` or `vat_number_invalid`; neither a number nor `no_vat_number` is `400 vat_number_required`.
 */
class UpdateCompanyRequest
{
    public function __construct(
        public readonly string $legal_name,
        public readonly string $country,
        public readonly string $address_line1,
        public readonly string $city,
        public readonly string $postal_code,
        public readonly string $billing_email,
        public readonly ?string $registration_code = null,
        public readonly ?string $vat_number = null,
        public readonly ?bool $no_vat_number = null,
        public readonly ?string $address_line2 = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            legal_name: $data['legal_name'],
            country: $data['country'],
            address_line1: $data['address_line1'],
            city: $data['city'],
            postal_code: $data['postal_code'],
            billing_email: $data['billing_email'],
            registration_code: $data['registration_code'] ?? null,
            vat_number: $data['vat_number'] ?? null,
            no_vat_number: $data['no_vat_number'] ?? null,
            address_line2: $data['address_line2'] ?? null,
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
 * Fields to change. Moving to `all` drops `profile_ids`; `profile_ids` sent alone keeps a `selected` endpoint selected. A sub-account key can never widen an endpoint.
 */
class UpdateWebhookEndpointRequest
{
    public function __construct(
        public readonly ?string $url = null,
        public readonly ?array $events = null,
        public readonly ?string $scope = null,
        public readonly ?array $profile_ids = null,
        public readonly ?string $status = null,
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
            url: $data['url'] ?? null,
            events: $data['events'] ?? null,
            scope: $data['scope'] ?? null,
            profile_ids: $data['profile_ids'] ?? null,
            status: $data['status'] ?? null,
            description: $data['description'] ?? null,
        );
    }
}

/**
 * The prepaid balance as `GET /me/billing/balance` reports it, without `billing_enabled` (the usage read carries that itself)
 */
class UsageBalance
{
    public function __construct(
        public readonly array $balance,
        public readonly int $low_balance_threshold,
        public readonly array $top_up_amounts,
        public readonly array $auto_top_up,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            balance: $data['balance'],
            low_balance_threshold: $data['low_balance_threshold'],
            top_up_amounts: $data['top_up_amounts'],
            auto_top_up: $data['auto_top_up'],
        );
    }
}

/**
 * One month's production usage record
 */
class UsagePeriod
{
    public function __construct(
        public readonly string $period,
        public readonly int $total_verifications,
        public readonly int $total_confirmations,
        public readonly int $total_authorizations,
        public readonly int $total_delegations,
        public readonly array $by_type,
        public readonly array $by_channel,
        public readonly array $by_hitl_channel,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            period: $data['period'],
            total_verifications: $data['total_verifications'],
            total_confirmations: $data['total_confirmations'],
            total_authorizations: $data['total_authorizations'],
            total_delegations: $data['total_delegations'],
            by_type: $data['by_type'],
            by_channel: $data['by_channel'],
            by_hitl_channel: $data['by_hitl_channel'],
        );
    }
}

/**
 * One channel's completion this period
 */
class UsageTopChannel
{
    public function __construct(
        public readonly string $channel,
        public readonly int $count,
        public readonly float $completion_rate,
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
            count: $data['count'],
            completion_rate: $data['completion_rate'],
        );
    }
}

/**
 * Usage for the current billing period, with the months asked for
 */
class UsageResponse
{
    /**
     * @param list<UsageTopChannel> $top_channels
     * @param list<UsagePeriod> $periods
     */
    public function __construct(
        public readonly string $plan,
        public readonly int $limit,
        public readonly bool $billing_enabled,
        public readonly bool $card_on_file,
        public readonly ?int $hard_limit,
        public readonly UsageBalance $balance,
        public readonly string $current_period,
        public readonly int $current_usage,
        public readonly int $percentage,
        public readonly int $days_remaining,
        public readonly int $projected_eom_usage,
        public readonly ?bool $is_on_track,
        public readonly bool $projection_unreliable,
        public readonly array $top_channels,
        public readonly array $channels_with_zero_completions,
        public readonly array $periods,
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
            limit: $data['limit'],
            billing_enabled: $data['billing_enabled'],
            card_on_file: $data['card_on_file'],
            hard_limit: $data['hard_limit'] ?? null,
            balance: UsageBalance::fromArray($data['balance']),
            current_period: $data['current_period'],
            current_usage: $data['current_usage'],
            percentage: $data['percentage'],
            days_remaining: $data['days_remaining'],
            projected_eom_usage: $data['projected_eom_usage'],
            is_on_track: $data['is_on_track'] ?? null,
            projection_unreliable: $data['projection_unreliable'],
            top_channels: array_map(static fn (array $item) => UsageTopChannel::fromArray($item), $data['top_channels']),
            channels_with_zero_completions: $data['channels_with_zero_completions'],
            periods: array_map(static fn (array $item) => UsagePeriod::fromArray($item), $data['periods']),
        );
    }
}

/**
 * A live API key as the owner's key list shows it. Never carries the key value.
 */
class UserAPIKeyListItem
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $prefix,
        public readonly string $environment,
        public readonly array $scopes,
        public readonly ?string $profile_id,
        public readonly ?string $expires_at,
        public readonly string $created_at,
        public readonly array $replaced_key_ids,
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
            prefix: $data['prefix'],
            environment: $data['environment'],
            scopes: $data['scopes'],
            profile_id: $data['profile_id'] ?? null,
            expires_at: $data['expires_at'] ?? null,
            created_at: $data['created_at'],
            replaced_key_ids: $data['replaced_key_ids'],
            last_used_at: $data['last_used_at'] ?? null,
        );
    }
}

/**
 * The result of one check of the published record or file
 */
class UserDomainVerificationCheckResponse
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $verified_at = null,
        public readonly ?array $asset = null,
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
            status: $data['status'],
            verified_at: $data['verified_at'] ?? null,
            asset: $data['asset'] ?? null,
            message: $data['message'] ?? null,
        );
    }
}

/**
 * A self-service domain verification session
 */
class UserDomainVerificationSessionResponse
{
    public function __construct(
        public readonly string $session_id,
        public readonly string $domain,
        public readonly string $channel,
        public readonly string $status,
        public readonly string $expires_at,
        public readonly array $challenge,
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
            domain: $data['domain'],
            channel: $data['channel'],
            status: $data['status'],
            expires_at: $data['expires_at'],
            challenge: $data['challenge'],
        );
    }
}

/**
 * A self-service domain verification session's state
 */
class UserDomainVerificationStatusResponse
{
    public function __construct(
        public readonly string $session_id,
        public readonly string $domain,
        public readonly string $channel,
        public readonly string $status,
        public readonly string $created_at,
        public readonly ?string $verified_at = null,
        public readonly ?array $challenge = null,
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
            domain: $data['domain'],
            channel: $data['channel'],
            status: $data['status'],
            created_at: $data['created_at'],
            verified_at: $data['verified_at'] ?? null,
            challenge: $data['challenge'] ?? null,
        );
    }
}

/**
 * An asset verified from the signed-in account's existing proof or saved DNS credentials, with the request's status and progress after it
 */
class AssetAutoVerifyResult
{
    public function __construct(
        public readonly string $status,
        public readonly string $verified_at,
        public readonly string $request_status,
        public readonly array $progress,
        public readonly string $method,
        public readonly ?string $original_verified_at = null,
        public readonly ?string $provider = null,
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
            verified_at: $data['verified_at'],
            request_status: $data['request_status'],
            progress: $data['progress'],
            method: $data['method'],
            original_verified_at: $data['original_verified_at'] ?? null,
            provider: $data['provider'] ?? null,
        );
    }
}

/**
 * The asset, verified by the submitted code, with the request's status and progress after it. A wrong code answers 400.
 */
class AssetCodeSubmitResult
{
    public function __construct(
        public readonly string $status,
        public readonly string $verified_at,
        public readonly string $request_status,
        public readonly array $progress,
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
            verified_at: $data['verified_at'],
            request_status: $data['request_status'],
            progress: $data['progress'],
        );
    }
}

/**
 * A DNS or HTTP check of a domain asset: verified (with the request's `request_status` and `progress` after it), or still pending with the attempts left. A record not found yet is this 200, not an error.
 */
class AssetVerificationCheckResult
{
    public function __construct(
        public readonly string $status,
        public readonly ?string $verified_at = null,
        public readonly ?string $request_status = null,
        public readonly ?array $progress = null,
        public readonly ?string $message = null,
        public readonly ?int $attempts_remaining = null,
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
            verified_at: $data['verified_at'] ?? null,
            request_status: $data['request_status'] ?? null,
            progress: $data['progress'] ?? null,
            message: $data['message'] ?? null,
            attempts_remaining: $data['attempts_remaining'] ?? null,
        );
    }
}

/**
 * The request's new expiry
 */
class ExtendVerificationRequestResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly string $expires_at,
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
            message: $data['message'],
            expires_at: $data['expires_at'],
            status: $data['status'],
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
 * A verification request the account was asked to complete
 */
class IncomingVerificationRequestListItem
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly array $computed_state,
        public readonly string $action_type,
        public readonly array $assets,
        public readonly array $progress,
        public readonly string $expires_at,
        public readonly string $created_at,
        public readonly ?string $reference_id = null,
        public readonly ?string $completed_at = null,
        public readonly ?array $requester = null,
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
            assets: $data['assets'],
            progress: $data['progress'],
            expires_at: $data['expires_at'],
            created_at: $data['created_at'],
            reference_id: $data['reference_id'] ?? null,
            completed_at: $data['completed_at'] ?? null,
            requester: $data['requester'] ?? null,
        );
    }
}

/**
 * The verification requests the account was asked to complete, newest first
 */
class IncomingVerificationRequestListResponse
{
    /**
     * @param list<IncomingVerificationRequestListItem> $data
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
            data: array_map(static fn (array $item) => IncomingVerificationRequestListItem::fromArray($item), $data['data']),
            pagination: $data['pagination'],
        );
    }
}

/**
 * A verification request the account made, as its own list shows it. `public_profile_id` is the sub-account the request belongs to, absent when the request records no sub-account.
 */
class OwnVerificationRequestListItem
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly array $computed_state,
        public readonly string $action_type,
        public readonly string $source,
        public readonly ?string $api_key_id,
        public readonly ?string $api_key_model,
        public readonly ?string $agent_name,
        public readonly array $assets,
        public readonly array $progress,
        public readonly string $expires_at,
        public readonly string $created_at,
        public readonly ?string $reference_id = null,
        public readonly ?string $completed_at = null,
        public readonly ?array $branding = null,
        public readonly ?string $public_profile_id = null,
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
            source: $data['source'],
            api_key_id: $data['api_key_id'] ?? null,
            api_key_model: $data['api_key_model'] ?? null,
            agent_name: $data['agent_name'] ?? null,
            assets: $data['assets'],
            progress: $data['progress'],
            expires_at: $data['expires_at'],
            created_at: $data['created_at'],
            reference_id: $data['reference_id'] ?? null,
            completed_at: $data['completed_at'] ?? null,
            branding: $data['branding'] ?? null,
            public_profile_id: $data['public_profile_id'] ?? null,
        );
    }
}

/**
 * The verification requests the account made, newest first
 */
class OwnVerificationRequestListResponse
{
    /**
     * @param list<OwnVerificationRequestListItem> $data
     */
    public function __construct(
        public readonly array $data,
        public readonly array $pagination,
        public readonly array $stats,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            data: array_map(static fn (array $item) => OwnVerificationRequestListItem::fromArray($item), $data['data']),
            pagination: $data['pagination'],
            stats: $data['stats'],
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
        public readonly ?string $proof_expires_at = null,
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
            proof_expires_at: $data['proof_expires_at'] ?? null,
        );
    }
}

/**
 * A verification request as its recipient sees it: masked identifiers, branding and progress, never a proof token. `action_context` is null when the request carries none.
 */
class PublicVerificationRequest
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $action_type,
        public readonly ?ActionContext $action_context,
        public readonly ?array $business,
        public readonly array $assets,
        public readonly array $progress,
        public readonly bool $partial_ok,
        public readonly string $expires_at,
        public readonly string $created_at,
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
            action_type: $data['action_type'],
            action_context: isset($data['action_context']) ? ActionContext::fromArray($data['action_context']) : null,
            business: $data['business'] ?? null,
            assets: $data['assets'],
            progress: $data['progress'],
            partial_ok: $data['partial_ok'],
            expires_at: $data['expires_at'],
            created_at: $data['created_at'],
            redirect_url: $data['redirect_url'] ?? null,
            completed_at: $data['completed_at'] ?? null,
        );
    }
}

/**
 * The address is now on the account
 */
class VerifyEmailOtpResponse
{
    public function __construct(
        public readonly string $status,
        public readonly string $email,
        public readonly string $verified_at,
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
            email: $data['email'],
            verified_at: $data['verified_at'],
        );
    }
}

/**
 * Account settings
 */
class SettingsResponse
{
    public function __construct(
        /** @deprecated Removed from the API (account-level branding is retired: each sub-account carries its own branding, set with the profiles resource); kept so code written for an earlier 1.x release keeps compiling, and dropped in the next major version. */
        public readonly ?BrandingSettings $branding,
        public readonly WebhookSettingsResponse $webhook,
        public readonly ?int $proof_expiry_days,
        public readonly ?array $hitl = null,
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
            webhook: WebhookSettingsResponse::fromArray($data['webhook']),
            proof_expiry_days: $data['proof_expiry_days'] ?? null,
            hitl: $data['hitl'] ?? null,
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
 * Whether one saved DNS credential can reach a domain
 */
class DomainCredentialAccess
{
    public function __construct(
        public readonly string $provider,
        public readonly string $name,
        public readonly bool $has_access,
        public readonly ?bool $is_registrar = null,
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
            provider: $data['provider'],
            name: $data['name'],
            has_access: $data['has_access'],
            is_registrar: $data['is_registrar'] ?? null,
            error: $data['error'] ?? null,
        );
    }
}

/**
 * Which of the account's DNS credentials can reach a domain
 */
class DomainCredentialCheckResponse
{
    /**
     * @param list<DomainCredentialAccess> $accessible_credentials
     */
    public function __construct(
        public readonly bool $has_credentials,
        public readonly array $accessible_credentials,
        public readonly string $detected_provider,
        public readonly ?bool $has_accessible_credential = null,
        public readonly ?bool $has_registrar_credentials = null,
        public readonly ?string $registrar_provider = null,
        public readonly ?bool $can_try_ownership_verification = null,
        public readonly ?string $suggestion = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            has_credentials: $data['has_credentials'],
            accessible_credentials: array_map(static fn (array $item) => DomainCredentialAccess::fromArray($item), $data['accessible_credentials']),
            detected_provider: $data['detected_provider'],
            has_accessible_credential: $data['has_accessible_credential'] ?? null,
            has_registrar_credentials: $data['has_registrar_credentials'] ?? null,
            registrar_provider: $data['registrar_provider'] ?? null,
            can_try_ownership_verification: $data['can_try_ownership_verification'] ?? null,
            suggestion: $data['suggestion'] ?? null,
        );
    }
}

/**
 * A DNS record a domain needs, for ownership or for email sending
 */
class DomainDnsRecord
{
    public function __construct(
        public readonly string $type,
        public readonly string $name,
        public readonly string $value,
        public readonly string $purpose,
        public readonly bool $verified,
        public readonly ?int $priority = null,
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
            type: $data['type'],
            name: $data['name'],
            value: $data['value'],
            purpose: $data['purpose'],
            verified: $data['verified'],
            priority: $data['priority'] ?? null,
            verified_at: $data['verified_at'] ?? null,
        );
    }
}

/**
 * An email verification of the domain. The code itself is never returned.
 */
class DomainEmailVerificationState
{
    public function __construct(
        public readonly string $email_prefix,
        public readonly string $email_address,
        public readonly string $expires_at,
        public readonly int $attempts,
        public readonly ?string $last_sent_at = null,
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
            email_prefix: $data['email_prefix'],
            email_address: $data['email_address'],
            expires_at: $data['expires_at'],
            attempts: $data['attempts'],
            last_sent_at: $data['last_sent_at'] ?? null,
            verified_at: $data['verified_at'] ?? null,
        );
    }
}

class DomainRemovedResponse
{
    public function __construct(
        public readonly string $success,
        public readonly string $domain,
        public readonly int $proofs_revoked,
        public readonly array $delegations_revoked,
        public readonly array $external,
        public readonly array $carriers,
        public readonly array $notice,
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
            domain: $data['domain'],
            proofs_revoked: $data['proofs_revoked'],
            delegations_revoked: $data['delegations_revoked'],
            external: $data['external'],
            carriers: $data['carriers'],
            notice: $data['notice'],
        );
    }
}

/**
 * Email-sending setup for a domain. Present once email sending has been set up.
 */
class DomainResendStatus
{
    /**
     * @param list<DomainDnsRecord> $dkim_records
     */
    public function __construct(
        public readonly string $status,
        public readonly array $dkim_records,
        public readonly ?string $domain_id = null,
        public readonly ?string $registered_at = null,
        public readonly ?string $verified_at = null,
        public readonly ?string $last_checked_at = null,
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
            dkim_records: array_map(static fn (array $item) => DomainDnsRecord::fromArray($item), $data['dkim_records']),
            domain_id: $data['domain_id'] ?? null,
            registered_at: $data['registered_at'] ?? null,
            verified_at: $data['verified_at'] ?? null,
            last_checked_at: $data['last_checked_at'] ?? null,
        );
    }
}

/**
 * How to show control of the domain by the method it is set to
 */
class DomainVerificationInstructions
{
    public function __construct(
        public readonly string $method,
        public readonly array $steps,
        public readonly array $http_file,
        public readonly ?array $dns_record = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            method: $data['method'],
            steps: $data['steps'],
            http_file: $data['http_file'],
            dns_record: $data['dns_record'] ?? null,
        );
    }
}

/**
 * Domain record
 */
class Domain
{
    /**
     * @param list<DomainDnsRecord>|null $dns_records
     */
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
        public readonly ?string $proof_expires_at = null,
        public readonly ?string $detected_provider = null,
        public readonly ?string $provider_name = null,
        public readonly ?bool $supports_auto_dns = null,
        public readonly ?string $verification_subtype = null,
        public readonly ?bool $for_email_sending = null,
        public readonly ?bool $for_verification = null,
        public readonly ?array $dns_records = null,
        public readonly ?string $actual_dns_host = null,
        public readonly ?string $actual_dns_host_name = null,
        public readonly ?bool $has_dns_mismatch = null,
        public readonly ?string $dns_mismatch_warning = null,
        public readonly ?string $txt_record_name = null,
        public readonly ?string $txt_record_value = null,
        public readonly ?string $http_file_path = null,
        public readonly ?string $http_file_content = null,
        public readonly ?string $oauth_url = null,
        public readonly ?bool $oauth_connected = null,
        public readonly ?bool $godaddy_connected = null,
        public readonly ?bool $supports_api_token = null,
        public readonly ?bool $supports_api_key = null,
        public readonly ?bool $has_provider_credentials = null,
        public readonly ?string $provider_credential_name = null,
        public readonly ?string $from_email = null,
        public readonly ?DomainResendStatus $resend = null,
        public readonly ?DomainEmailVerificationState $email_verification = null,
        public readonly ?array $verified_providers = null,
        public readonly ?int $check_count = null,
        public readonly ?string $last_checked_at = null,
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
            proof_expires_at: $data['proof_expires_at'] ?? null,
            detected_provider: $data['detected_provider'] ?? null,
            provider_name: $data['provider_name'] ?? null,
            supports_auto_dns: $data['supports_auto_dns'] ?? null,
            verification_subtype: $data['verification_subtype'] ?? null,
            for_email_sending: $data['for_email_sending'] ?? null,
            for_verification: $data['for_verification'] ?? null,
            dns_records: isset($data['dns_records']) ? array_map(static fn (array $item) => DomainDnsRecord::fromArray($item), $data['dns_records']) : null,
            actual_dns_host: $data['actual_dns_host'] ?? null,
            actual_dns_host_name: $data['actual_dns_host_name'] ?? null,
            has_dns_mismatch: $data['has_dns_mismatch'] ?? null,
            dns_mismatch_warning: $data['dns_mismatch_warning'] ?? null,
            txt_record_name: $data['txt_record_name'] ?? null,
            txt_record_value: $data['txt_record_value'] ?? null,
            http_file_path: $data['http_file_path'] ?? null,
            http_file_content: $data['http_file_content'] ?? null,
            oauth_url: $data['oauth_url'] ?? null,
            oauth_connected: $data['oauth_connected'] ?? null,
            godaddy_connected: $data['godaddy_connected'] ?? null,
            supports_api_token: $data['supports_api_token'] ?? null,
            supports_api_key: $data['supports_api_key'] ?? null,
            has_provider_credentials: $data['has_provider_credentials'] ?? null,
            provider_credential_name: $data['provider_credential_name'] ?? null,
            from_email: $data['from_email'] ?? null,
            resend: isset($data['resend']) ? DomainResendStatus::fromArray($data['resend']) : null,
            email_verification: isset($data['email_verification']) ? DomainEmailVerificationState::fromArray($data['email_verification']) : null,
            verified_providers: $data['verified_providers'] ?? null,
            check_count: $data['check_count'] ?? null,
            last_checked_at: $data['last_checked_at'] ?? null,
            error_message: $data['error_message'] ?? null,
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
 * A domain with the steps to verify it
 */
class DomainDetailResponse
{
    public function __construct(
        public readonly Domain $data,
        public readonly DomainVerificationInstructions $instructions,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            data: Domain::fromArray($data['data']),
            instructions: DomainVerificationInstructions::fromArray($data['instructions']),
        );
    }
}

/**
 * A verification code emailed to an address on the domain
 */
class DomainEmailVerificationResponse
{
    public function __construct(
        public readonly Domain $data,
        public readonly string $message,
        public readonly ?string $email_sent_to = null,
        public readonly ?int $expires_in_minutes = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            data: Domain::fromArray($data['data']),
            message: $data['message'],
            email_sent_to: $data['email_sent_to'] ?? null,
            expires_in_minutes: $data['expires_in_minutes'] ?? null,
        );
    }
}

/**
 * The account's domains, newest first
 */
class DomainListResponse
{
    /**
     * @param list<Domain> $data
     */
    public function __construct(
        public readonly array $data,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            data: array_map(static fn (array $item) => Domain::fromArray($item), $data['data']),
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

/**
 * The domain after a verification check, by DNS or by an emailed code
 */
class DomainVerificationResult
{
    public function __construct(
        public readonly Domain $data,
        public readonly bool $verified,
        public readonly string $message,
        public readonly ?bool $renewed = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            data: Domain::fromArray($data['data']),
            verified: $data['verified'],
            message: $data['message'],
            renewed: $data['renewed'] ?? null,
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
 * An email-add session: a code was emailed to the address
 */
class AddEmailSessionResponse
{
    public function __construct(
        public readonly string $session_id,
        public readonly string $email,
        public readonly string $channel,
        public readonly string $status,
        public readonly string $expires_at,
        public readonly string $instructions,
        public readonly bool $email_sent,
        public readonly ?string $email_error = null,
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
            email: $data['email'],
            channel: $data['channel'],
            status: $data['status'],
            expires_at: $data['expires_at'],
            instructions: $data['instructions'],
            email_sent: $data['email_sent'],
            email_error: $data['email_error'] ?? null,
        );
    }
}

/**
 * An email-add session's state
 */
class AddEmailStatusResponse
{
    public function __construct(
        public readonly string $session_id,
        public readonly string $email,
        public readonly string $channel,
        public readonly string $status,
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
            session_id: $data['session_id'],
            email: $data['email'],
            channel: $data['channel'],
            status: $data['status'],
            verified_at: $data['verified_at'] ?? null,
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
 * A phone-add session's state
 */
class AddPhoneStatusResponse
{
    public function __construct(
        public readonly string $session_id,
        public readonly string $channel,
        public readonly string $status,
        public readonly ?string $phone_number = null,
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
            session_id: $data['session_id'],
            channel: $data['channel'],
            status: $data['status'],
            phone_number: $data['phone_number'] ?? null,
            verified_at: $data['verified_at'] ?? null,
        );
    }
}

/**
 * A phone-add session: the person proves the number by sending from it over `channel`
 */
class AddPhoneSessionResponse
{
    public function __construct(
        public readonly string $session_id,
        public readonly string $channel,
        public readonly string $status,
        public readonly string $expires_at,
        public readonly string $instructions,
        public readonly ?string $deep_link = null,
        public readonly ?string $qr_code = null,
        public readonly ?string $qr_text = null,
        public readonly ?string $sms_code = null,
        public readonly ?string $sms_message = null,
        public readonly ?SmsNumbersByRegion $sms_dids = null,
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
            channel: $data['channel'],
            status: $data['status'],
            expires_at: $data['expires_at'],
            instructions: $data['instructions'],
            deep_link: $data['deep_link'] ?? null,
            qr_code: $data['qr_code'] ?? null,
            qr_text: $data['qr_text'] ?? null,
            sms_code: $data['sms_code'] ?? null,
            sms_message: $data['sms_message'] ?? null,
            sms_dids: isset($data['sms_dids']) ? SmsNumbersByRegion::fromArray($data['sms_dids']) : null,
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
 * Session entry
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
        public readonly ?string $return_to = null,
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
            return_to: $data['return_to'] ?? null,
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
 * List of sessions
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
            event: $data['event'],
            authorization_id: $data['authorization_id'],
            status: $data['status'],
            channel: $data['channel'],
            hitl_id: $data['hitl_id'] ?? null,
            scope: $data['scope'] ?? null,
            proof_token: $data['proof_token'] ?? null,
            decided_at: $data['decided_at'] ?? null,
            expires_at: $data['expires_at'] ?? null,
            profile_id: $data['profile_id'] ?? null,
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
        public readonly ?string $checkout_url = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'] ?? $data['checkout_url'],
            checkout_url: $data['checkout_url'] ?? null,
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
        public readonly ?string $portal_url = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            url: $data['url'] ?? $data['portal_url'],
            portal_url: $data['portal_url'] ?? null,
        );
    }
}

// ============================================================================
// Settings
// ============================================================================

/**
 * Branding settings
 *
 * @deprecated Removed from the API (account-level branding is retired: each sub-account carries its own branding, set with the profiles resource); kept so code written for an earlier 1.x release keeps compiling, and dropped in the next major version.
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
        public readonly ?array $hitl = null,
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
            hitl: $data['hitl'] ?? null,
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
        /** @deprecated Removed from the API (account-level branding is retired: each sub-account carries its own branding, set with the profiles resource); kept so code written for an earlier 1.x release keeps compiling, and dropped in the next major version. */
        public readonly ?BrandingSettings $branding,
        public readonly WebhookSettingsResponse $webhook,
        public readonly ?int $proof_expiry_days,
        public readonly ?array $hitl = null,
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
            branding: isset($data['branding']) ? BrandingSettings::fromArray($data['branding']) : null,
            webhook: WebhookSettingsResponse::fromArray($data['webhook']),
            proof_expiry_days: $data['proof_expiry_days'] ?? null,
            hitl: $data['hitl'] ?? null,
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
 * Request body for submitting the code we emailed (`submitCodeSchema`)
 */
class SubmitChallengeRequest
{
    public function __construct(
        /** @deprecated Removed from the API (the route reads the code from `code`, which `verifications.submit` already sends); kept so code written for an earlier 1.x release keeps compiling, and dropped in the next major version. */
        public readonly string $challenge_code,
        /** @deprecated Removed from the API (the route never read it; send only `code`); kept so code written for an earlier 1.x release keeps compiling, and dropped in the next major version. */
        public readonly ?string $source_identifier = null,
        public readonly ?string $code = null,
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
            code: $data['code'] ?? null,
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

/**
 * The recipient's own proofs that could answer the request's unverified assets (Smart Reuse)
 */
class VerificationRequestExistingProofs
{
    public function __construct(
        public readonly string $request_id,
        public readonly bool $user_logged_in,
        public readonly array $assets,
        public readonly int $reusable_count,
        public readonly int $total_required,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            request_id: $data['request_id'],
            user_logged_in: $data['user_logged_in'],
            assets: $data['assets'],
            reusable_count: $data['reusable_count'],
            total_required: $data['total_required'],
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
 * Result of submitting the code we emailed
 */
class SubmitChallengeResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly int $attempts,
        public readonly int $max_attempts,
        public readonly ?string $verified_at = null,
        public readonly ?array $proof = null,
        public readonly ?string $message = null,
        public readonly ?int $attempts_remaining = null,
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
            attempts: $data['attempts'],
            max_attempts: $data['max_attempts'],
            verified_at: $data['verified_at'] ?? null,
            proof: $data['proof'] ?? null,
            message: $data['message'] ?? null,
            attempts_remaining: $data['attempts_remaining'] ?? null,
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
            type: $data['type'],
            channel: $data['channel'],
            identifier: $data['identifier'],
            external_user_id: $data['external_user_id'] ?? null,
            client_metadata: $data['client_metadata'] ?? null,
            dns_provider: $data['dns_provider'] ?? null,
            email_prefix: $data['email_prefix'] ?? null,
            bot_token: $data['bot_token'] ?? null,
            proof_expiry_days: $data['proof_expiry_days'] ?? null,
            profile_id: $data['profile_id'] ?? null,
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
 * Body of `verification.completed` — a direct verification (`POST /verifications`, or the winning channel of a multi-channel group) or a phone-first session that completed. Sent once per verification id.
 */
class VerificationCompletedWebhookPayload
{
    public function __construct(
        public readonly string $event,
        public readonly ?string $profile_id,
        public readonly string $verification_id,
        public readonly string $type,
        public readonly string $channel,
        public readonly ?string $identifier,
        public readonly string $status,
        public readonly string $environment,
        public readonly ?string $external_user_id,
        public readonly array $client_metadata,
        public readonly ?string $verified_at,
        public readonly string $proof_token,
        public readonly ?string $proof_expires_at,
        public readonly ?string $confidence = null,
        public readonly ?string $session_id = null,
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
            profile_id: $data['profile_id'] ?? null,
            verification_id: $data['verification_id'],
            type: $data['type'],
            channel: $data['channel'],
            identifier: $data['identifier'] ?? null,
            status: $data['status'],
            environment: $data['environment'],
            external_user_id: $data['external_user_id'] ?? null,
            client_metadata: $data['client_metadata'],
            verified_at: $data['verified_at'] ?? null,
            proof_token: $data['proof_token'],
            proof_expires_at: $data['proof_expires_at'] ?? null,
            confidence: $data['confidence'] ?? null,
            session_id: $data['session_id'] ?? null,
            test: $data['test'] ?? null,
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
            type: $data['type'] ?? null,
            channel: $data['channel'] ?? null,
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
 * One asset's verification status, polled by the recipient page. `request_status` is the request's, expiry re-derived: `cancelled` or `expired` ends the flow while the asset stays `pending`.
 */
class VerificationRequestAssetStatus
{
    public function __construct(
        public readonly int $asset_index,
        public readonly string $type,
        public readonly ?string $identifier,
        public readonly string $status,
        public readonly string $request_status,
        public readonly array $progress,
        public readonly ?string $verified_at = null,
        public readonly ?string $confidence = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            asset_index: $data['asset_index'],
            type: $data['type'],
            identifier: $data['identifier'] ?? null,
            status: $data['status'],
            request_status: $data['request_status'],
            progress: $data['progress'],
            verified_at: $data['verified_at'] ?? null,
            confidence: $data['confidence'] ?? null,
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
 * The claimed username
 */
class ClaimUsernameResponse
{
    public function __construct(
        public readonly string $username,
        public readonly string $username_claimed_at,
        public readonly string $profile_url,
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
            username_claimed_at: $data['username_claimed_at'],
            profile_url: $data['profile_url'],
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
        public readonly ?string $email_theme = null,
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
            display_name: $data['display_name'] ?? null,
            bio: $data['bio'] ?? null,
            avatar_url: $data['avatar_url'] ?? null,
            business_name: $data['business_name'] ?? null,
            is_business: $data['is_business'] ?? null,
            is_public: $data['is_public'] ?? null,
            is_primary: $data['is_primary'] ?? null,
            theme: isset($data['theme']) ? ProfileTheme::fromArray($data['theme']) : null,
            email_theme: $data['email_theme'] ?? null,
            support_email: $data['support_email'] ?? null,
        );
    }
}

/**
 * A published, in-force delegation for a domain the profile shows
 */
class PublicProfileDelegation
{
    public function __construct(
        public readonly string $delegate,
        public readonly string $delegate_type,
        public readonly string $principal,
        public readonly array $scope,
        public readonly string $issued_at,
        public readonly string $expires_at,
        public readonly ?string $artifact_path,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            delegate: $data['delegate'],
            delegate_type: $data['delegate_type'],
            principal: $data['principal'],
            scope: $data['scope'],
            issued_at: $data['issued_at'],
            expires_at: $data['expires_at'],
            artifact_path: $data['artifact_path'] ?? null,
        );
    }
}

/**
 * The asset a profile's preview card, share tags and badge lead with: the first proof in the profile's order that is not hidden.
 */
class PublicProfileLeadingProof
{
    public function __construct(
        public readonly string $type,
        public readonly string $display_value,
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
            type: $data['type'],
            display_value: $data['display_value'],
            label: $data['label'] ?? null,
        );
    }
}

/**
 * A visible link on a public profile.
 */
class PublicProfileLink
{
    public function __construct(
        public readonly string $title,
        public readonly string $url,
        public readonly ?string $icon = null,
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
        );
    }
}

/**
 * A proof shown on a public profile. The channel and the mask level are never published.
 */
class PublicProfileProof
{
    public function __construct(
        public readonly string $type,
        public readonly string $display_value,
        public readonly ?string $label = null,
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
            type: $data['type'],
            display_value: $data['display_value'],
            label: $data['label'] ?? null,
            verified_at: $data['verified_at'] ?? null,
        );
    }
}

/**
 * A public profile as anyone may read it (`GET /api/v1/profiles/p/{profileId}`, `GET /api/v1/profiles/u/{username}`).
 */
class PublicProfileView
{
    /**
     * @param list<PublicProfileProof> $proofs
     * @param list<PublicProfileLink> $links
     * @param list<PublicProfileDelegation>|null $delegations
     */
    public function __construct(
        public readonly string $profile_id,
        public readonly string $verification_level,
        public readonly bool $is_business,
        public readonly array $proofs,
        public readonly array $links,
        public readonly ?string $username = null,
        public readonly ?string $display_name = null,
        public readonly ?string $bio = null,
        public readonly ?string $avatar_url = null,
        public readonly ?string $business_name = null,
        public readonly ?PublicProfileLeadingProof $leading_proof = null,
        public readonly ?ProfileTheme $theme = null,
        public readonly ?array $delegations = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            profile_id: $data['profile_id'],
            verification_level: $data['verification_level'],
            is_business: $data['is_business'],
            proofs: array_map(static fn (array $item) => PublicProfileProof::fromArray($item), $data['proofs']),
            links: array_map(static fn (array $item) => PublicProfileLink::fromArray($item), $data['links']),
            username: $data['username'] ?? null,
            display_name: $data['display_name'] ?? null,
            bio: $data['bio'] ?? null,
            avatar_url: $data['avatar_url'] ?? null,
            business_name: $data['business_name'] ?? null,
            leading_proof: isset($data['leading_proof']) ? PublicProfileLeadingProof::fromArray($data['leading_proof']) : null,
            theme: isset($data['theme']) ? ProfileTheme::fromArray($data['theme']) : null,
            delegations: isset($data['delegations']) ? array_map(static fn (array $item) => PublicProfileDelegation::fromArray($item), $data['delegations']) : null,
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
        public readonly ?string $proof_order = null,
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
            proof_order: $data['proof_order'] ?? null,
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
        public readonly ?bool $show_delegations = null,
        public readonly ?ProfileTheme $theme = null,
        public readonly ?array $custom_links = null,
        public readonly ?string $email_theme = null,
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
            display_name: $data['display_name'] ?? null,
            bio: $data['bio'] ?? null,
            avatar_url: $data['avatar_url'] ?? null,
            business_name: $data['business_name'] ?? null,
            is_business: $data['is_business'] ?? null,
            is_public: $data['is_public'] ?? null,
            is_primary: $data['is_primary'] ?? null,
            show_verification_dates: $data['show_verification_dates'] ?? null,
            show_proof_channels: $data['show_proof_channels'] ?? null,
            show_delegations: $data['show_delegations'] ?? null,
            theme: isset($data['theme']) ? ProfileTheme::fromArray($data['theme']) : null,
            custom_links: isset($data['custom_links']) ? array_map(static fn (array $item) => CustomLink::fromArray($item), $data['custom_links']) : null,
            email_theme: $data['email_theme'] ?? null,
            support_email: $data['support_email'] ?? null,
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
 * Deprecated. NO endpoint returns this shape: the public reads return `PublicProfileView` and the owner's operations return `ProfileOwnerResponse`. Kept for released SDK types; removed in the next major.
 */
class PublicProfile
{
    /**
     * @param list<CustomLink>|null $custom_links
     * @param list<PublicProfileDelegation>|null $delegations
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
        public readonly ?bool $show_delegations = null,
        public readonly ?array $delegations = null,
        public readonly ?string $email_theme = null,
        public readonly ?string $support_email = null,
        public readonly ?PublicProfileLeadingProof $leading_proof = null,
        public readonly ?string $proof_order = null,
        public readonly ?string $verification_level = null,
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
            show_delegations: $data['show_delegations'] ?? null,
            delegations: isset($data['delegations']) ? array_map(static fn (array $item) => PublicProfileDelegation::fromArray($item), $data['delegations']) : null,
            email_theme: $data['email_theme'] ?? null,
            support_email: $data['support_email'] ?? null,
            leading_proof: isset($data['leading_proof']) ? PublicProfileLeadingProof::fromArray($data['leading_proof']) : null,
            proof_order: $data['proof_order'] ?? null,
            verification_level: $data['verification_level'] ?? null,
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
 * Request body for creating a phone-first verification session
 */
class CreateSessionRequest
{
    public function __construct(
        /** @deprecated Removed from the API (a session takes no phone number (the person shares it in the chat), and the server ignores it); kept so code written for an earlier 1.x release keeps compiling, and dropped in the next major version. */
        public readonly string $phone,
        public readonly ?string $channel = null,
        public readonly ?string $profile_id = null,
        public readonly ?array $client_metadata = null,
        public readonly ?array $context = null,
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
            profile_id: $data['profile_id'] ?? null,
            client_metadata: $data['client_metadata'] ?? null,
            context: $data['context'] ?? null,
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
 * One of our SMS numbers, for one region
 */
class SessionSmsNumber
{
    public function __construct(
        public readonly string $did,
        public readonly string $label,
        public readonly string $flag,
        public readonly ?string $qr_code = null,
        public readonly ?string $qr_text = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            did: $data['did'],
            label: $data['label'],
            flag: $data['flag'],
            qr_code: $data['qr_code'] ?? null,
            qr_text: $data['qr_text'] ?? null,
        );
    }
}

/**
 * Every number an `sms` session's code can be texted to, one per configured region (a region with no number is absent), each with its own QR code. `deep_link` uses the default region's number; the request comes from your server, so nothing in it says where the person is.
 */
class SessionSmsNumbers
{
    public function __construct(
        public readonly SessionSmsNumber $americas,
        public readonly ?SessionSmsNumber $europe = null,
        public readonly ?SessionSmsNumber $israel = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            americas: SessionSmsNumber::fromArray($data['americas']),
            europe: isset($data['europe']) ? SessionSmsNumber::fromArray($data['europe']) : null,
            israel: isset($data['israel']) ? SessionSmsNumber::fromArray($data['israel']) : null,
        );
    }
}

/**
 * A new phone-first session: what to show the person
 */
class CreateSessionResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $channel,
        public readonly string $status,
        public readonly string $deep_link,
        public readonly string $qr_code,
        public readonly string $qr_text,
        public readonly string $expires_at,
        public readonly string $instructions,
        public readonly ?string $sms_code = null,
        public readonly ?string $sms_message = null,
        public readonly ?SessionSmsNumbers $sms_dids = null,
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
            deep_link: $data['deep_link'],
            qr_code: $data['qr_code'],
            qr_text: $data['qr_text'],
            expires_at: $data['expires_at'],
            instructions: $data['instructions'],
            sms_code: $data['sms_code'] ?? null,
            sms_message: $data['sms_message'] ?? null,
            sms_dids: isset($data['sms_dids']) ? SessionSmsNumbers::fromArray($data['sms_dids']) : null,
        );
    }
}

/**
 * Phone-first session status
 */
class Session
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $created_at,
        /** @deprecated Removed from the API (GET /api/v1/sessions/{id} never returned it; the session's expiry is on the create response); kept so code written for an earlier 1.x release keeps compiling, and dropped in the next major version. */
        public readonly ?string $expires_at = null,
        /** @deprecated Removed from the API (GET /api/v1/sessions/{id} never returned it, and nothing replaces it); kept so code written for an earlier 1.x release keeps compiling, and dropped in the next major version. */
        public readonly ?Challenge $challenge = null,
        public readonly ?Proof $proof = null,
        public readonly ?string $channel = null,
        public readonly ?string $phone_number = null,
        public readonly ?string $verified_at = null,
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
            id: $data['id'],
            status: $data['status'],
            created_at: $data['created_at'],
            expires_at: $data['expires_at'] ?? null,
            challenge: isset($data['challenge']) ? Challenge::fromArray($data['challenge']) : null,
            proof: isset($data['proof']) ? Proof::fromArray($data['proof']) : null,
            channel: $data['channel'] ?? null,
            phone_number: $data['phone_number'] ?? null,
            verified_at: $data['verified_at'] ?? null,
            verification_id: $data['verification_id'] ?? null,
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
 * JSON Web Key. The key-type-specific members are optional because one key set may carry both an EC and an RSA key: `crv`/`x`/`y` are present on EC keys, `n`/`e` on RSA keys. A registry key also carries its family and signing window (`proof_purpose`, `not_before`, `not_after`, `max_token_lifetime`); a legacy key carries `proof_legacy` instead.
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
        public readonly ?string $proof_purpose = null,
        public readonly ?int $not_before = null,
        public readonly ?int $not_after = null,
        public readonly ?int $max_token_lifetime = null,
        public readonly ?bool $proof_legacy = null,
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
            proof_purpose: $data['proof_purpose'] ?? null,
            not_before: $data['not_before'] ?? null,
            not_after: $data['not_after'] ?? null,
            max_token_lifetime: $data['max_token_lifetime'] ?? null,
            proof_legacy: $data['proof_legacy'] ?? null,
        );
    }
}

/**
 * A revoked signing key. A token signed by it is `key_revoked` — fetch a re-signed copy rather than trusting it. Kept for 365 days after `revoked_at`.
 */
class JWKSTombstone
{
    public function __construct(
        public readonly string $kid,
        public readonly int $revoked_at,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            kid: $data['kid'],
            revoked_at: $data['revoked_at'],
        );
    }
}

/**
 * JSON Web Key Set for offline proof verification: every signing key still needed to verify a live token, plus the revoked ones as tombstones
 */
class JWKS
{
    /**
     * @param list<JWK> $keys
     * @param list<JWKSTombstone>|null $proof_tombstones
     */
    public function __construct(
        public readonly array $keys,
        public readonly ?array $proof_tombstones = null,
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
            proof_tombstones: isset($data['proof_tombstones']) ? array_map(static fn (array $item) => JWKSTombstone::fromArray($item), $data['proof_tombstones']) : null,
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
 * The current token of a proof you own. Served with `Cache-Control: no-store`; each read is audited.
 */
class ProofTokenResponse
{
    public function __construct(
        public readonly string $proof_id,
        public readonly string $token,
        public readonly string $status,
        public readonly ?string $reissued_at,
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
            proof_id: $data['proof_id'],
            token: $data['token'],
            status: $data['status'],
            reissued_at: $data['reissued_at'] ?? null,
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
        public readonly string $reason,
        public readonly ?string $proof_id = null,
        public readonly ?string $proof_type = null,
        public readonly ?string $verification_id = null,
        public readonly ?string $principal = null,
        public readonly ?bool $owned_domain = null,
        public readonly ?string $domain_status = null,
        public readonly ?array $delegations_revoked = null,
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
            principal: $data['principal'] ?? null,
            owned_domain: $data['owned_domain'] ?? null,
            domain_status: $data['domain_status'] ?? null,
            delegations_revoked: $data['delegations_revoked'] ?? null,
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
 * Proof validation result. Returns verification details when valid, or reason/message when invalid. A DELEGATION token answers with `token_type: "delegation"` and the `delegation` block instead of `verification`; its status resolves the cascade down to the domain control proof, which neither the status list (it packs each row's own bit) nor an offline signature check can show. Delegation-specific reasons: `unknown_delegation` (no registry record — delegations are minted fail-closed, so this is invalid, not merely unresolvable), `unsupported_version` (a delegation token of a schema major the issuer no longer accepts — 1.x after 2.0; its owner re-issues it), `status_unavailable`, and `identifier_unverifiable` when an `identifier` binding is requested for a delegation.
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
        public readonly ?string $last_fallback_at = null,
        public readonly ?array $last_fallback_variables = null,
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
            last_fallback_at: $data['last_fallback_at'] ?? null,
            last_fallback_variables: $data['last_fallback_variables'] ?? null,
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

/**
 * The platform default templates
 */
class TemplateDefaultsResponse
{
    public function __construct(
        public readonly array $defaults,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            defaults: $data['defaults'],
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
 * Why a saved template would never be sent as written. The save still succeeds.
 */
class TemplateSendWarning
{
    public function __construct(
        public readonly string $code,
        public readonly string $message,
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
            code: $data['code'],
            message: $data['message'],
            variables: $data['variables'] ?? null,
        );
    }
}

/**
 * A channel and message type some message is sent for, so a template saved for it is used.
 */
class TemplateSendablePair
{
    public function __construct(
        public readonly string $channel,
        public readonly string $message_type,
        public readonly array $variables,
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
            variables: $data['variables'],
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
 * A newly created or regenerated API key, with its full value — the one response that carries it.
 */
class APIKeyCreatedResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $key,
        public readonly string $name,
        public readonly string $prefix,
        public readonly string $environment,
        public readonly array $scopes,
        public readonly ?string $profile_id,
        public readonly string $created_at,
        public readonly string $warning,
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
            key: $data['key'],
            name: $data['name'],
            prefix: $data['prefix'],
            environment: $data['environment'],
            scopes: $data['scopes'],
            profile_id: $data['profile_id'] ?? null,
            created_at: $data['created_at'],
            warning: $data['warning'],
        );
    }
}

/**
 * An API key's redacted metadata, as the calling key reads itself (`GET /me/api-key`). Never carries the key value.
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
        public readonly ?array $replaced_key_ids = null,
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
            replaced_key_ids: $data['replaced_key_ids'] ?? null,
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
// Sub-accounts
// ============================================================================

class ProjectDeletedResponse
{
    public function __construct(
        public readonly string $success,
        public readonly string $message,
        public readonly array $project,
        public readonly bool $was_primary,
        public readonly int $api_keys_revoked,
        public readonly int $hitl_archived,
        public readonly int $hitl_detached,
        public readonly int $confirmations_denied,
        public readonly int $authorizations_revoked,
        public readonly int $requests_cancelled,
        public readonly int $requests_detached,
        public readonly int $verifications_detached,
        public readonly int $domains_detached,
        public readonly int $circles_detached,
        public readonly int $templates_deleted,
        public readonly bool $username_retired,
        public readonly ?array $new_primary,
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
            project: $data['project'],
            was_primary: $data['was_primary'],
            api_keys_revoked: $data['api_keys_revoked'],
            hitl_archived: $data['hitl_archived'],
            hitl_detached: $data['hitl_detached'],
            confirmations_denied: $data['confirmations_denied'],
            authorizations_revoked: $data['authorizations_revoked'],
            requests_cancelled: $data['requests_cancelled'],
            requests_detached: $data['requests_detached'],
            verifications_detached: $data['verifications_detached'],
            domains_detached: $data['domains_detached'],
            circles_detached: $data['circles_detached'],
            templates_deleted: $data['templates_deleted'],
            username_retired: $data['username_retired'],
            new_primary: $data['new_primary'] ?? null,
        );
    }
}

class ProjectDeletionPreview
{
    public function __construct(
        public readonly bool $allowed,
        public readonly ?array $blocked,
        public readonly array $project,
        public readonly array $counts,
        public readonly ?array $new_primary,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            allowed: $data['allowed'],
            blocked: $data['blocked'] ?? null,
            project: $data['project'],
            counts: $data['counts'],
            new_primary: $data['new_primary'] ?? null,
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
 * A webhook endpoint. Deliveries carry `X-Proof-Signature`, an HMAC-SHA256 with this endpoint's secret over `<X-Proof-Timestamp>.<body>`; the secret itself is returned only on create and rotate.
 */
class WebhookEndpoint
{
    public function __construct(
        public readonly string $id,
        public readonly string $url,
        public readonly array $events,
        public readonly string $scope,
        public readonly array $profile_ids,
        public readonly string $status,
        public readonly ?string $description,
        public readonly bool $legacy,
        public readonly bool $secret_set,
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
            url: $data['url'],
            events: $data['events'],
            scope: $data['scope'],
            profile_ids: $data['profile_ids'],
            status: $data['status'],
            description: $data['description'] ?? null,
            legacy: $data['legacy'],
            secret_set: $data['secret_set'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
        );
    }
}

/**
 * The endpoints the caller may see. A sub-account key sees only those scoped to exactly its own sub-account.
 */
class WebhookEndpointList
{
    /**
     * @param list<WebhookEndpoint> $data
     */
    public function __construct(
        public readonly array $data,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            data: array_map(static fn (array $item) => WebhookEndpoint::fromArray($item), $data['data']),
        );
    }
}

/**
 * A webhook endpoint with its signing secret, returned once (create, rotate).
 */
class WebhookEndpointWithSecret
{
    public function __construct(
        public readonly string $id,
        public readonly string $url,
        public readonly array $events,
        public readonly string $scope,
        public readonly array $profile_ids,
        public readonly string $status,
        public readonly ?string $description,
        public readonly bool $legacy,
        public readonly bool $secret_set,
        public readonly string $created_at,
        public readonly string $updated_at,
        public readonly string $secret,
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
            url: $data['url'],
            events: $data['events'],
            scope: $data['scope'],
            profile_ids: $data['profile_ids'],
            status: $data['status'],
            description: $data['description'] ?? null,
            legacy: $data['legacy'],
            secret_set: $data['secret_set'],
            created_at: $data['created_at'],
            updated_at: $data['updated_at'],
            secret: $data['secret'],
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
        public readonly ?array $payload,
        public readonly ?string $response_body,
        public readonly string $updated_at,
        public readonly ?string $endpoint_id = null,
        public readonly ?string $profile_id = null,
        public readonly ?string $token_omitted_reason = null,
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
            payload: $data['payload'] ?? null,
            response_body: $data['response_body'] ?? null,
            updated_at: $data['updated_at'],
            endpoint_id: $data['endpoint_id'] ?? null,
            profile_id: $data['profile_id'] ?? null,
            token_omitted_reason: $data['token_omitted_reason'] ?? null,
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
        public readonly ?string $endpoint_id = null,
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
            endpoint_id: $data['endpoint_id'] ?? null,
            profile_id: $data['profile_id'] ?? null,
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
        public readonly ?string $endpoint_id = null,
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
            event_type: $data['event_type'] ?? null,
            verification_request_id: $data['verification_request_id'] ?? null,
            endpoint_id: $data['endpoint_id'] ?? null,
            profile_id: $data['profile_id'] ?? null,
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
            profile_id: $data['profile_id'] ?? null,
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
 * Outbound webhook configuration — DEPRECATED (#421). An account switched over to webhook endpoints sees its account-wide (`legacy`) endpoint here: `url` is null while that endpoint is disabled, a `url` write updates or creates it, `url: null` disables it (its secret is kept), and `rotate_secret` rotates its secret. An account not yet switched over sees its settings fields, as before. Omitted for an API key bound to one sub-account.
 */
class WebhookSettingsResponse
{
    public function __construct(
        public readonly ?string $url,
        public readonly array $events,
        public readonly bool $secret_set,
        public readonly ?string $secret = null,
        public readonly ?string $deprecated = null,
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
            deprecated: $data['deprecated'] ?? null,
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

/**
 * A started verification for one asset: what to show the person, by asset type and channel. The six fields up to `expires_at` are always sent; each other field only for the type and channel its description names. sms: `sms_dids` lists all three regions here, a region with no number carrying an empty `did`.
 */
class StartAssetVerificationResponse
{
    public function __construct(
        public readonly string $verification_id,
        public readonly int $asset_index,
        public readonly string $type,
        public readonly string $channel,
        public readonly string $status,
        public readonly string $expires_at,
        public readonly ?string $instructions = null,
        public readonly ?string $deep_link = null,
        public readonly ?string $qr_code = null,
        public readonly ?string $qr_text = null,
        public readonly ?string $message = null,
        public readonly ?string $phone_number = null,
        public readonly ?string $sms_code = null,
        public readonly ?string $sms_message = null,
        public readonly ?SessionSmsNumbers $sms_dids = null,
        public readonly ?string $suggested_region = null,
        public readonly ?bool $email_sent = null,
        public readonly ?string $email_error = null,
        public readonly ?string $record_type = null,
        public readonly ?string $record_name = null,
        public readonly ?string $record_value = null,
        public readonly ?string $file_path = null,
        public readonly ?string $file_content = null,
        public readonly ?string $file_url = null,
        public readonly ?string $email_sent_to = null,
        public readonly ?int $otp_code_length = null,
        public readonly ?array $verified_data = null,
        public readonly ?string $proof_token = null,
    ) {}

    /**
     * Create from API response array.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            verification_id: $data['verification_id'],
            asset_index: $data['asset_index'],
            type: $data['type'],
            channel: $data['channel'],
            status: $data['status'],
            expires_at: $data['expires_at'],
            instructions: $data['instructions'] ?? null,
            deep_link: $data['deep_link'] ?? null,
            qr_code: $data['qr_code'] ?? null,
            qr_text: $data['qr_text'] ?? null,
            message: $data['message'] ?? null,
            phone_number: $data['phone_number'] ?? null,
            sms_code: $data['sms_code'] ?? null,
            sms_message: $data['sms_message'] ?? null,
            sms_dids: isset($data['sms_dids']) ? SessionSmsNumbers::fromArray($data['sms_dids']) : null,
            suggested_region: $data['suggested_region'] ?? null,
            email_sent: $data['email_sent'] ?? null,
            email_error: $data['email_error'] ?? null,
            record_type: $data['record_type'] ?? null,
            record_name: $data['record_name'] ?? null,
            record_value: $data['record_value'] ?? null,
            file_path: $data['file_path'] ?? null,
            file_content: $data['file_content'] ?? null,
            file_url: $data['file_url'] ?? null,
            email_sent_to: $data['email_sent_to'] ?? null,
            otp_code_length: $data['otp_code_length'] ?? null,
            verified_data: $data['verified_data'] ?? null,
            proof_token: $data['proof_token'] ?? null,
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
        public readonly ?array $targets = null,
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
            targets: $data['targets'] ?? null,
        );
    }
}
