---
name: laravel
description: Build, modify, debug, and review Laravel applications, including routing, controllers, models, migrations, validation, authentication, queues, APIs, and tests.
---

# Laravel

Use this skill when the task involves Laravel or PHP code in a Laravel application.

## Workflow

- Inspect the existing Laravel version, folder structure, routes, configuration, and established conventions before changing code.
- Keep business rules in appropriate domain/service layers; keep controllers focused on HTTP concerns and request orchestration.
- Use Form Requests for non-trivial validation, policies/gates for authorization, and migrations for every schema change.
- Prefer Eloquent relationships and query scopes over duplicated query logic. Watch for N+1 queries and unintended mass assignment.
- Treat `.env` values as secrets and never commit credentials. Update `.env.example` when a new required setting is introduced.
- Preserve backwards compatibility for existing routes and API responses unless the user explicitly requests a breaking change.

## Verification

- Run the narrowest relevant test suite first, then broader tests when practical.
- Check migrations from a clean database when schema changes are involved.
- Run formatting/static checks already used by the project, such as Pint, PHPStan, or Psalm, when configured.
- Report any checks that could not run because dependencies or services are unavailable.
