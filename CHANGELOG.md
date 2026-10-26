# Changelog

Notable changes to `proof-holdings/sdk` on Packagist. Each version is a `vX.Y.Z` tag of
[ProofHoldings/sdk-php](https://github.com/ProofHoldings/sdk-php).

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

### Changed

- The `Templates` methods and `HttpClient::post()`, `put()` and `delete()` gained a trailing optional
  parameter. Every call written for 1.1.0 works unchanged; only a subclass overriding one of them with
  the 1.1.0 signature breaks. Declared in `sdks/api-breaks/php.json`.

### Fixed

- `Session::fromArray()`, `SettingsResponse::fromArray()`, `UpdateSettingsResponse::fromArray()` and
  `WebhookDelivery::fromArray()` threw a `TypeError` on current responses, because the API no longer
  sends `expires_at`, `branding` or (always) `payload`. Those properties are now nullable and read
  `null` when absent. Declared in `sdks/api-breaks/php.json`.

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
