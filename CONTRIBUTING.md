# Contributing

Thanks for improving Scout API.

## Development setup

Use `docs/onboarding/LOCAL_SETUP.md` as the source of truth for local bootstrap and daily checks.

1. Install dependencies and bootstrap the project:

```bash
composer run setup
```

2. Start local development:

```bash
composer run dev
```

## Branch and PR rules

- Create a feature/fix branch from `main`.
- Keep pull requests focused on one change set.
- Include tests for behavior changes.
- Update docs when API behavior or environment variables change.

## Quality checks before opening a PR

Run these checks locally:

```bash
composer test
vendor/bin/pint --test
composer analyse
```

If formatting fails, run:

```bash
vendor/bin/pint
```

If static analysis reports issues, fix them before submitting PR.

## Commit guidance

- Use clear commit messages in imperative form.
- Reference issue/ticket IDs when available.
- Avoid committing secrets or local `.env` changes.
- Do not commit `vendor/`; dependencies are installed locally via Composer.

## Reporting bugs

Open an issue with:

- expected behavior
- actual behavior
- reproduction steps
- relevant logs or request payload samples
