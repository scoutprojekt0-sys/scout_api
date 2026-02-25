# CI / Ruleset Alignment Runbook

Date: 2026-02-25
Scope: `feat/scout-api-hardening` and release branches

## Why this exists

PR merges can be blocked even when jobs look green if required status-check names in rulesets do not match actual workflow job names.

## Pre-merge Checklist

1. Verify target branch ruleset:
   - `Settings -> Rules -> Rulesets`
2. Confirm required checks exactly match live job names shown in PR `Checks` tab.
3. Confirm required workflows exist in target branch:
   - `.github/workflows/tests.yml`
   - `.github/workflows/api-smoke.yml`
   - `.github/workflows/security.yml`
   - `.github/workflows/codeql.yml`

## Common Failure Modes

1. `Expected — Waiting for status to be reported`
   - Cause: ruleset requires a check name that no longer runs.
   - Fix: update required check list to current names.

2. Workflow does not appear in Actions
   - Cause: workflow file not present in default/target branch.
   - Fix: merge workflow file into target branch.

3. `Process completed with exit code 1` in RC gate
   - Cause: script/path mismatch by branch context.
   - Fix: make optional steps conditional and avoid branch-specific file assumptions.

## Stable Naming Guidance

- Keep job names stable once marked as required checks.
- If renaming a job is unavoidable, update ruleset in the same PR.
- Avoid duplicate checks with ambiguous naming across push/pull_request unless ruleset is aligned.

## RC Workflow Safety Rules

1. Prefer explicit commands over branch-dependent Composer scripts.
2. Guard optional assets with file existence checks.
3. Keep smoke and E2E steps independent for easier reruns.

## Quick Recovery Playbook

1. Open PR `Checks` tab.
2. Identify failing or pending required checks.
3. Compare with ruleset required names.
4. Patch workflow/ruleset mismatch.
5. Re-run failed jobs.
6. Merge after all required checks are green.
