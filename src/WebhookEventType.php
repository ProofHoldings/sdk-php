<?php

declare(strict_types=1);

namespace ProofHoldings;

/**
 * Webhook event types the platform delivers. Mirrors the backend OpenAPI
 * `WebhookEventType` enum (`src/openapi/schemas.ts`).
 *
 * The generated `Types.php` models `WebhookPayload.event` as a plain string
 * (the SDK type generator skips enums for PHP), so this hand-written enum
 * provides the typed event-name vocabulary for consumers — the backing values
 * match the event strings carried on `WebhookDelivery.event_type` /
 * `WebhookPayload.event`.
 */
enum WebhookEventType: string
{
    case VerificationRequestCompleted = 'verification_request.completed';
    case VerificationRequestPartial = 'verification_request.partial';
    case VerificationRequestExpired = 'verification_request.expired';
    case VerificationCompleted = 'verification.completed';
    case ConfirmationApproved = 'confirmation.approved';
    case ConfirmationDenied = 'confirmation.denied';
    case AuthorizationApproved = 'authorization.approved';
    case AuthorizationDenied = 'authorization.denied';
    case ProofRevoked = 'proof.revoked';
    case ProofSuspended = 'proof.suspended';
    case ProofUnsuspended = 'proof.unsuspended';
    case AccountDeletionScheduled = 'account.deletion_scheduled';
    case AccountDeletionCancelled = 'account.deletion_cancelled';
}
