---
name: backend
description: Design and implement reliable backend APIs, services, data access, authentication, validation, and integrations independent of a specific framework.
---

# Backend

Use this skill for server-side features, APIs, database interactions, jobs, integrations, and backend debugging when no more specific framework skill applies.

## Workflow

- Discover the current runtime, entrypoints, API conventions, persistence layer, authentication model, and deployment assumptions first.
- Define request/response contracts, validation rules, authorization checks, failure behavior, and idempotency before implementing state-changing endpoints.
- Validate input at the boundary, normalize it once, and keep domain rules out of transport-specific code where practical.
- Use parameterized queries or the project’s safe ORM/query builder. Never interpolate untrusted input into SQL, shell commands, or URLs.
- Return consistent status codes and error shapes without leaking secrets, stack traces, or internal identifiers.
- Consider pagination, timeouts, retries, rate limits, transactions, observability, and concurrent requests for production-facing paths.

## Verification

- Add or update focused tests for success, validation failure, authorization failure, not-found, and dependency failure paths as relevant.
- Exercise the API contract and persistence behavior, not only isolated helper functions.
- Run the project’s formatter, linter, type checker, and test commands when available.
