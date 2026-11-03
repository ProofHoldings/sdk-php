# Changelog

Notable changes to `proof-holdings/sdk` on Packagist. Each version is a `vX.Y.Z` tag of
[ProofHoldings/sdk-php](https://github.com/ProofHoldings/sdk-php).

## 1.3.0 — 2026-11-03

### Added

- `billing->topUp(['auto_top_up' => true, ...])` and `billing->autoTopUp(['enabled' => true])` now answer `403 owner_session_required`: switching automatic top-ups on charges the saved card later with no one present, so it is the account owner's act on the dashboard. Switching them off still works. Plain top-ups are unchanged.
- `SubscriptionResponse` gains `scheduled_plan` and `scheduled_change_at` (both nullable, null when nothing is scheduled): the plan a downgrade made on the billing page moves the account to at the end of the paid period, and when. Absent from an API that predates plan changes.
- `WebhookDeliveryListQuery` gains `$endpoint_id` and `$profile_id` (`?string`), the delivery-list filters by webhook endpoint and by sub-account. A field the API document annotates where it references a shared type is now generated as that type rather than `mixed`. Fields 1.2.0 shipped as `mixed` stay `mixed` until the next major (listed in `sdks/shared/NEXT_MAJOR.md`).
- `billing` resource (`$proof->billing`): `subscription()`, `checkout($params)`, `portal($params)`, `topUp($params)`, `balance()`, `balanceLedger($params)` and `autoTopUp($params)` over `/api/v1/billing/*` — the subscription, Stripe checkout and portal links, and the prepaid balance. Returns plain arrays shaped as the named OpenAPI components. `amount` is the int `10`, `50` or `200` (euros). Their redirect URLs must be on `proof.holdings` (or `localhost`): the API refuses any other host. The four writes are refused to a key bound to one sub-account (`403 account_key_required`).
- `BalanceEmptyException` (402 `balance_empty`), with `topUpUrl`, `used`, `included` and `balance` (euro cents). Once billing is live, any method that creates a proof request can throw it when the plan's included proofs are used and the prepaid balance is empty; proofs already issued keep working.
- `BillingUnavailableException` (503 `billing_unavailable`), a `ServerException`: checkout, portal and top-up while billing is not live.
- `WebhookDelivery::$token_omitted_reason` (`?string`, `source_gone` | `token_absent` | `token_changed`): why the last send carried `proof_token: null`. A delivery's stored `payload` now holds every `proof_token` as null — the body POSTed to the receiver still carries the token. Webhook receivers: a retried delivery carries the same `proof_token` as the first attempt, or `proof_token: null` with `token_omitted_reason` ∈ {`source_gone`, `token_absent`, `token_changed`} — never a different token. Null from an API that predates it.
- `$proof->confirmations->verifyDecisionSignature($confirmation, $publicKey)` checks the approver's
  own signature on a decision, locally, given the `Confirmation` that `retrieve()` returns or the API's
  array, against the public key you hold: your HITL config's own key for an owner decision, the key you
  recorded when an approver enrolled for theirs. It answers `'verified'`, `'unsigned'` (no signed
  evidence), `'key_mismatch'` (the decision carries another key), `'input_mismatch'` (the signed input
  names another confirmation, action or comment) or `'signature_invalid'`. The key is required: a key
  the server sent is no evidence against the server.
  The key must be an RSA public key PEM (`PUBLIC KEY` or `RSA PUBLIC KEY`) with a 4096- to 16384-bit
  modulus and an odd exponent from 3 to 2^31 - 1; any other key answers `signature_invalid`.
- Webhook endpoints: `$proof->webhookEndpoints` with `list()`, `create($params)`, `get($id)`, `update($id, $params)`, `delete($id)` and `rotateSecret($id)` (`/api/v1/webhook-endpoints*`). Each endpoint has its own `events`, `scope` (`all` sub-accounts, or `selected` ones named in `profile_ids`) and signing secret. The secret is returned once, in `secret`, by `create()` and `rotateSecret()`; the other methods carry only `secret_set`. Methods return plain arrays.
- `$proof->profiles->rotateCallbackSecret($profileId)` rotates a sub-account's callback signing secret and returns `['profile_id' => ..., 'secret' => ...]`, the only time the new secret is returned. An API key needs the `webhooks:write` scope and may rotate only the sub-account it is bound to.
- `confirmations->list()` documents the `profile_id` filter the API now accepts: only that sub-account's confirmations (sent through its HITL configs or created by its API keys). A key bound to another sub-account is refused with `403 profile_mismatch`. The params array already passed it through.
- `Template` carries `last_fallback_at` and `last_fallback_variables`: when a message last went out with the platform default instead of the template, because rendering it would have left a raw `{{variable}}`, and the variable names that caused it. Both are cleared when the template is saved again.
- Generated `TemplateSendablePair`, and `sendable` on the `$proof->templates->list()` response: what is sent — each channel and message type some message is sent for, with the variables its sends fill. Generated `TemplateSendWarning`, and `warnings` on the template save and preview responses: a template that would never be sent as written is still saved (200, as before), and each warning says why — `template_not_sent` for a pair no message is sent for, `template_variable_not_filled` (with `variables`) for a variable that message never fills. `sendable` and `warnings` are optional: an API that predates them omits the fields, so read their absence as an empty list. The live template tests read `telegram/verification_request` instead of an SMS template, which is never sent.
- Generated response DTOs for operations whose contract named the generic `SuccessResponse` or the public `PublicProfile` while the API sent something else: `ProfileOwnerResponse` (a sub-account as its owner sees it, with `PublicProofItem` and `ProfileOwnerLink`) and `ProfileOwnerListResponse` for the profile reads, writes and lists; `ClaimUsernameResponse`; `PublicProofsResponse` for the shown-proofs updates; `ExtendVerificationRequestResponse` and `ShareVerificationRequestEmailResponse`. Types only: the `$proof->profiles` methods these responses come from still return arrays, and nothing on the wire changed.
- Generated response DTOs for the domain and DNS-credential operations and the public profile reads, which the contract described as `SuccessResponse`, a bare `Domain` or `PublicProfile`: `DomainListResponse`, `DomainDetailResponse` (with `DomainVerificationInstructions`), `DomainVerificationResult` for the DNS check and the emailed-code confirmation, `DomainEmailVerificationResponse`, `DomainCredentialCheckResponse` (with `DomainCredentialAccess`), `DnsCredentialResponse`, the domain parts `DomainDnsRecord`, `DomainResendStatus` and `DomainEmailVerificationState`, and `PublicProfileView` (with `PublicProfileProof` and `PublicProfileLink`) for the two public profile reads. Types only: the methods that call these routes still return arrays, and nothing on the wire changed.
- Generated response DTOs for operations the contract described only as an open object: `UsageResponse` (with `UsageBalance`, `UsageTopChannel` and `UsagePeriod`) for the usage read; `DnsProvidersResponse` (with `DnsProviderMetadataItem`, camelCase as the API sends it) and `DnsCredentialListResponse` for the DNS providers and saved credentials, whose items are `DnsCredentialResponse`, which gains the optional `last_used_at`, `last_validated_at` and `validation_error` the list sends; `TemplateDefaultsResponse` for the platform default templates; `OwnVerificationRequestListResponse` and `IncomingVerificationRequestListResponse` (with `OwnVerificationRequestListItem` and `IncomingVerificationRequestListItem`) for the account's own request lists; `HitlKeysResponse`, `HitlKeysPutResponse`, `HitlApproverKeysResponse` and `HitlApproverKey` for a HITL config's keypair and its approvers' keys, where a key holding `confirmations:create` without `hitl:read` is sent only `public_key` and `kid` (and `authorization_id`), and `public_key` is null when the stored key material cannot be read; `DelegationCarriersResponse` (with `DelegationCarrier`) for where a delegation is still visible; and `RefreshSessionResponse` for the session renewal (camelCase as sent; kept as it is because the dashboard reads these names). Types only: the methods that call these routes still return arrays, and nothing on the wire changed.
- Generated response DTOs for the public verification-request recipient flow and Lookalike Watch, which the contract described only as an open object: `PublicVerificationRequest` for the public read; `VerificationRequestExistingProofs` for the recipient's reusable proofs; `StartAssetVerificationResponse` for starting an asset, one flat type whose type- and channel-specific fields are optional; `AssetCredentialsCheck` for the saved DNS credential check; `AssetAutoVerifyResult`, `AssetCodeSubmitResult` and `AssetVerificationCheckResult` for auto-verify, code submission and the DNS/HTTP check; `VerificationRequestAssetStatus` for the asset status poll; and `LookalikeWatchSubscription`, `LookalikeAlertList` (with `LookalikeAlert`) and `LookalikeReportList` (with `LookalikeReport`) for Lookalike Watch, whose pages carry `page`, `limit` and `total` but no `total_pages`. Types only: no method's return type changed, and nothing on the wire changed.
- Generated response DTOs for the account-session flows whose contract named the generic `SuccessResponse`: `AddPhoneSessionResponse` and `AddPhoneStatusResponse` for adding a phone; `AddEmailSessionResponse`, `AddEmailStatusResponse` and `VerifyEmailOtpResponse` for adding an email; `TwoFactorSessionResponse`, `TwoFactorEmailSelectionResponse` (the `requires_email_selection` answer), `TwoFactorStatusResponse`, `TwoFactorVerifyResponse` and `TwoFactorMagicLinkResponse` for a 2FA step-up; `UserDomainVerificationSessionResponse`, `UserDomainVerificationStatusResponse` and `UserDomainVerificationCheckResponse` for the self-service domain check; and `SmsNumbersByRegion`, the three SMS numbers the phone-add and 2FA sessions list. Types only: no `$proof` method returns these DTOs (the session streams' polling fallbacks read three of these endpoints as arrays), and nothing on the wire changed.
- A confirmation carries `resends`: each "Send again" (`POST /api/v1/confirmations/{id}/resend`) with when it happened, whether a dashboard session or which API key asked, and the channels it went to (by authorization id or `owner`, never a phone number or link), the last 50 kept. Absent from an API that predates it.

### Changed

- A `503 billing_unavailable` is no longer retried: it throws `BillingUnavailableException` after one request. Every other 5xx is retried as before. Before, a 402 threw a plain `ProofException`; it now throws `BalanceEmptyException`, which extends `ProofException`, so existing `catch` blocks still match.
- The API now documents `AccountDeletionCancelledResponse.ok`, `AccountDeletionCancelTokenCheck.valid`, and `success` on `IdentifierRemovedResponse`, `DomainRemovedResponse` and `ProjectDeletedResponse` as booleans (always `true`); they were declared as a literal `true`, which the type generator published as a `string`. The DTO parameters keep their released `string` type in 1.x, because code written for 1.x passes one (`new IdentifierRemovedResponse('true', …)`), and become `bool` in the next major (listed in `sdks/shared/NEXT_MAJOR.md`).
- `confirmations->approvalLink()` is documented as what it is: the HITL config owner's link, decided with the config's own key. It is not an approver's link; an approver's link goes only to their own channel, which `POST /api/v1/confirmations/{id}/resend` ("Send again") sends it to again. Every call returns the same owner link until "Send again" reaches the owner; "Send again" replaces it, and the next call returns a new one — the replaced owner link answers `410 approval_link_superseded`, as an approver's earlier links do once "Send again" reaches them. Calling it again cancels nothing.
- The generated `SubmitChallengeRequest` type now carries `code`, the field the API accepts. `code` is an appended optional constructor parameter. `challenge_code` and `source_identifier`, which the API never accepted, stay exactly as 1.x shipped them (`$challenge_code` still the first, required parameter), marked deprecated, and go in the next major version; `Verifications::submit()` sends `code` alone and does not use this type. A new `SubmitChallengeResponse` type describes what submitting a code returns; `Verifications::submit()` is unchanged.
- The generated `Domain` DTO now describes what the API sends: every field `GET /me/domains` returns is declared (`detected_provider`, `dns_records`, `verified_providers`, `resend`, `email_verification` and the rest) as appended optional constructor parameters, so the 1.x constructor order is unchanged, and `status` may now be `verifying`, `expired`, `revoking` or `reverification_required`. `provider` and `email_sending_enabled`, which the API never sent, stay, marked deprecated (read `detected_provider` and `for_email_sending`), and go in the next major version. `PublicProfile` is likewise kept and marked deprecated: no endpoint returns it; the public reads return `PublicProfileView`. It is removed in the next major (listed in `sdks/shared/NEXT_MAJOR.md`).
- `$proof->profiles->listTemplates()`, `updateTemplate()`, `deleteTemplate()` and `previewTemplate()` are now documented as removed in the next major. They still send the request, exactly as in 1.2.0, so a call fails with the server's answer: 410 `endpoint_retired`, naming the replacement, or the authentication or validation error that comes first.

### Fixed

- `$proof->proofs->verifyOffline()` refuses a correctly signed token that lacks the claims the issuer requires of a proof (a non-empty `type`, `channel` and `identifier_hash`, and a `user_id` or `tenant_id`), answering `valid: false` with `reason` `invalid`, as `POST /proofs/validate` does. It used to answer `valid: true` for anything signed with the proof key, including a data-export download receipt.
- `TopUpRequest::$amount` and `AutoTopUpRequest::$amount` stay `mixed`, as 1.2.0 shipped them: pass the int `10`, `50` or `200`, since the API refuses a string. They become `int` / `?int` in the next major (listed in `sdks/shared/NEXT_MAJOR.md`). No released method sends either type.
- `CheckoutResponse` and `PortalResponse` name the fields the API sends, `checkout_url` and `portal_url`. They declared `url`, which the API never sent, so `fromArray()` threw a `TypeError` on every real response. `url` stays a required `string`, as released, and `fromArray()` now fills it from `checkout_url` / `portal_url`, so `->url` holds the Stripe URL. It is removed in the next major (listed in `sdks/shared/NEXT_MAJOR.md`).
- `Verifications::list(['channel' => ...])` filters by channel. The API used to drop the `channel` filter and return every channel; it now applies it, and refuses an unknown channel with `400 validation_error`.
- `$proof->verifications->waitForCompletion()` and `stream()` end on `cancelled`, the status of the
  losing channel in a multi-channel verification. They used to keep polling until the timeout.
  `Polling::VERIFICATION_TERMINAL_STATES` keeps the four states 1.2.0 released; it gains `cancelled` in
  the next major (listed in `sdks/shared/NEXT_MAJOR.md`).
- `$proof->proofs->revoke($id)` succeeds without a reason: it sends no body, and the API records `user_request`. It used to send the JSON body `[]`, which the API refused with `400 validation_error`. The README's example passes `'user_request'`; it passed `'User requested'`, which the API refuses with `400 validation_error`: `$reason` takes only `user_request`, `fraud`, `key_compromise`, `asset_transferred` or `other`, now named in the method's doc comment, which also says that a delegation proof (`ph_dlg_*`) refuses `asset_transferred`. The parameter stays `?string` through 1.x; narrowing it is listed in `sdks/shared/NEXT_MAJOR.md`.

## 1.2.0 — 2026-10-26

Code written for 1.1.0 keeps working, with the declared exceptions under "Changed" and "Fixed".
What the API no longer has is kept and marked `@deprecated`, and is removed in 2.0. Generated
constructors keep their 1.1.0 parameter order; new parameters are appended and optional.

### Added

- SMS sessions: `$proof->sessions->create(['channel' => 'sms'])` now works, the same way as SMS
  sign-in. The person texts a short code to one of our numbers; generated types
  `CreateSessionResponse`, `SessionSmsNumbers` and `SessionSmsNumber` describe the response. An SMS
  proof carries `confidence: "standard"`.

- Every `$proof->templates` method except `getDefaults()` takes a trailing `?string $profileId` naming
  the project (sub-account) whose templates it reads or writes, sent as the `profile_id` query
  parameter. Without it a project-bound key uses its own project and an account-wide key the account's
  primary one. A project-bound key naming another project is refused with 403 `profile_mismatch`.
- `HttpClient::post()`, `put()` and `delete()` take an optional `$query` array.

- The generated `Session` type gains `channel`, `phone_number`, `verified_at` and `verification_id`,
  the fields `GET /api/v1/sessions/{id}` returns.

- `$proof->authSessions->stream()`'s polling fallback ends on `completed_elsewhere`, a new terminal
  auth-session status: an email sign-in that the person finished in a different browser from the one
  that started it ("Sign in here" on the emailed link's page). That session never reaches `verified`
  and issues no cookies to the starter.

### Changed

- The `Templates` methods and `HttpClient::post()`, `put()` and `delete()` gained a trailing optional
  parameter. Every call written for 1.1.0 works unchanged; only a subclass overriding one of them with
  the 1.1.0 signature breaks. Declared in `sdks/api-breaks/php.json`.

### Fixed

- `Session::fromArray()`, `SettingsResponse::fromArray()`, `UpdateSettingsResponse::fromArray()` and
  `WebhookDelivery::fromArray()` threw a `TypeError` on current responses, because the API no longer
  sends `expires_at`, `branding` or (always) `payload`. Those properties are now nullable and read
  `null` when absent. Declared in `sdks/api-breaks/php.json`.

- `$proof->authSessions->stream()`'s polling fallback read `/api/v1/sessions/{id}`, the phone-first
  sessions route, instead of `/api/v1/auth/sessions/{id}`, so it never saw the auth session it was
  streaming. It now polls the auth-session route.

### Deprecated

- `Session::$challenge` and `Session::$expires_at`: `GET /api/v1/sessions/{id}` never returned them.
- `CreateSessionRequest::$phone`: a session no longer takes a phone number (the person shares it in
  the chat), and the server ignores it.
- `BrandingSettings`, `ProfileTemplate`, and `$branding` on `SettingsResponse` and
  `UpdateSettingsResponse`: account-level branding is retired, since branding belongs to each project.
  A settings update carrying `branding` is refused with 400 `branding_moved`.

- A `business_name` entry in `$proof->authorizations->create()`, and `CreateAuthorizationBody::$business_name`
  (still the fifth constructor parameter, as in 1.1.0), are deprecated: the server ignores the name,
  and the consent message names the approval setup's sub-account.

- `$proof->profiles->listTemplates()`, `updateTemplate()`, `deleteTemplate()` and
  `previewTemplate()`: their routes are retired and answer 410 `endpoint_retired`. Use
  `$proof->templates->list()`, `upsert()`, `delete()` and `preview()` with the project id.

## 1.1.0 — 2026-10-14

### Added

- `new Proof()` without a key resolves one: a non-empty `PROOF_API_KEY`, else the entry that
  `npx @proof-holdings/mcp-server login` saved in `~/.proof-holdings/credentials` for the client's base
  URL. The base URL is the argument, else `PROOF_BASE_URL`, else `https://api.proof.holdings`. An
  explicit key behaves as before; an explicit empty key is still an error.
- `$proof->verifications->deleteDomainVerification($id)` deletes a domain verification attempt that is
  not verified (pending, verifying or failed). A verified domain is refused with 409 `domain_verified`.
- Types for account deletion and export, identifier, domain and project removal, and the
  `account.deletion_scheduled` / `account.deletion_cancelled` webhook events.
- Types for the artifact claim lookup and for the Delegations section of a public profile.
- API-key listings carry `replaced_key_ids`: a key's usage covers every key it replaced.

### Changed

- Requires PHP 8.3. The SDK uses typed class constants, which need 8.3, and 1.0.0 already did while
  declaring `>=8.1`.
- Requires `guzzlehttp/guzzle` 7.15.2 or later, the first 7.x release without the advisories
  published against earlier ones (CVE-2026-69246 among them).

### Fixed

- The README imported `Proof\Proof`; the namespace is `ProofHoldings`.

## 1.0.0 — 2026-10-07

- First tagged release.
