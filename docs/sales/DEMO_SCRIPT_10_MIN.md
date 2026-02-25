# 10-Minute Demo Script

## Goal

Show customer value in one flow: secure access -> scouting operations -> communication -> launch confidence.

## 0:00 - 1:00 Intro

- Positioning: "This is a launch-ready scouting backend focused on secure and fast matching."
- Explain roles: player, team, manager.

## 1:00 - 3:00 Auth and Session Trust

Use:
- `/auth/sessions`

Show:
- active sessions/devices
- refresh current session (token rotation)
- revoke a device

Message:
- "Operational security is built into daily usage, not added later."

## 3:00 - 6:00 Core Product Flow

Use:
- `/app/core`

Show:
- load profile + opportunities
- filter/sort/pagination behavior
- player application submission
- team-side status updates for incoming applications

Message:
- "Opportunity to decision flow is structured and auditable."

## 6:00 - 8:00 Messaging + Media

Use:
- `/app/communication`

Show:
- compose/send message
- inbox/sent filters
- upload media, list media, delete media

Message:
- "Communication and player proof live in one consistent workflow."

## 8:00 - 9:00 Reliability and Security Proof

Show docs quickly:
- `docs/security/SECURITY_CHECKLIST_FINAL.md`
- `docs/security/PENTEST_SCENARIOS.md`
- `docs/runbooks/backup-restore.md`
- `docs/runbooks/rollback-procedure.md`

Message:
- "We have both technical controls and operational procedures."

## 9:00 - 10:00 Close and Offer

- Suggest pilot scope: one team, one scouting lane, 2-week validation.
- Define success criteria:
  - faster response time
  - reduced drop-off in applications
  - no critical security findings
