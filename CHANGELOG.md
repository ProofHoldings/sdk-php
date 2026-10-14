# Changelog

Notable changes to `proof-holdings/sdk` on Packagist. Each version is a `vX.Y.Z` tag of
[ProofHoldings/sdk-php](https://github.com/ProofHoldings/sdk-php).

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
