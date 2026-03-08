# Local Setup

This is the fastest local onboarding flow for Scout API.

## Quick start

1. Bootstrap project:

```bash
composer run setup
```

2. Validate first-run prerequisites:

```bash
composer run verify:first-run
```

3. Start development:

```bash
composer run dev
```

## Daily quality checks

```bash
composer test
vendor/bin/pint --test
```

## Notes

- `vendor/` is generated locally by Composer and is not committed.
- Default local profile uses sqlite (`database/database.sqlite`).
- If required, copy `.env.example` to `.env` and adjust secrets for your environment.
