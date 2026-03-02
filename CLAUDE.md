# PHP SDK — proof.holdings

## Overview
PHP client SDK for the proof.holdings API. Composer package `proof-holdings/sdk`, namespace `ProofHoldings\`. All resource methods return typed DTO classes with `fromArray()` deserialization, including recursive nested object handling.

## Architecture
- `src/Proof.php` — Main client class (`ProofHoldings\Proof`), API key auth, resource accessors
- `src/HttpClient.php` — HTTP layer (Guzzle), error handling, retries
- `src/Resources/*.php` — 20 resource classes (Verifications, Proofs, Sessions, etc.)
- `src/Types.php` — Auto-generated readonly DTO classes with `fromArray()` factories
- `src/Exceptions/*.php` — 11 typed exception classes (ProofException base, ValidationException, AuthenticationException, NotFoundException, RateLimitException, etc.)
- `src/Polling.php` — `waitUntilComplete()` polling helper
- `src/Version.php` — SDK version constant
- `tests/ResourcesTest.php` — Unit tests for all resource methods

## Type System
- Types are auto-generated from OpenAPI spec via `scripts/generate-sdk-types.ts`
- Propagated via `scripts/propagate-sdk-types.ts`
- All DTOs use `declare(strict_types=1)` and `readonly` promoted properties
- Nested objects are recursively deserialized via `ClassName::fromArray()`
- **Do not edit `src/Types.php` manually** — regenerate with `npm run sdk:generate-types`
- `composer.json` includes a `classmap` entry for `src/Types.php` because it defines 117 DTO classes in a single file, which PSR-4 cannot resolve (PSR-4 requires one class per file)

## Key Patterns
- All resource methods return typed DTO instances (e.g., `Verification`, `Session`)
- URL path parameters escaped with `rawurlencode()`
- Error responses throw `ProofException` with status code and message
- Polling uses `usleep()` intervals (no async cancellation support)
