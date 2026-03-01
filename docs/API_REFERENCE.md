# API Reference (Scout API)

Base URL: `http://127.0.0.1:8000`

Auth: `Authorization: Bearer <token>`

## Postman E2E Quick Start

Collection: `postman/Scout_API_E2E.postman_collection.json`

Environment: `postman/Scout_API_E2E.postman_environment.json`

Run:

```bash
npm run test:api:local
```

### E2E Run Order (Cheat Sheet)

1. `Auth - Register Team`
2. `Auth - Register Player`
3. `Auth - Register Staff`
4. `Auth - Login Team`
5. `Auth - Login Player`
6. `Auth - Login Staff`
7. `Auth - Me (Team)`
8. `Auth - Update Me (Player)`
9. `News - Live`
10. `Opportunity - Create`
11. `Opportunity - List`
12. `Opportunity - Show`
13. `Opportunity - Update`
14. `Application - Apply`
15. `Application - Outgoing (Player)`
16. `Application - Incoming (Team)`
17. `Application - Change Status`
18. `Contact - Send Message`
19. `Contact - Inbox`
20. `Contact - Sent`
21. `Contact - Change Status`
22. `Media - Upload`
23. `Media - List By User`
24. `Players - List`
25. `Players - Show`
26. `Players - Update`
27. `Teams - List`
28. `Teams - Show`
29. `Teams - Update`
30. `Staff - List`
31. `Staff - Show`
32. `Staff - Update`
33. `Media - Delete`
34. `Opportunity - Delete`
35. `Auth - Logout Team`

Not:
- `Media - Upload` icin `media_file_path` gecerli bir lokal dosya olmalidir.
- Collection test scriptleri su degiskenleri otomatik gunceller:
  - `team_token`, `player_token`, `staff_token`
  - `team_user_id`, `player_user_id`, `staff_user_id`
  - `opportunity_id`, `application_id`, `contact_id`, `media_id`

## Auth

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout` (auth)
- `GET /api/auth/me` (auth)
- `PUT /api/auth/me` (auth)

## News

- `GET /api/news/live` (public)

## Players (auth)

- `GET /api/players`
- `GET /api/players/{player}`
- `PUT|PATCH /api/players/{player}`

Query:
- `position`, `city`, `age_min`, `age_max`, `page`, `per_page`

## Teams (auth)

- `GET /api/teams`
- `GET /api/teams/{team}`
- `PUT|PATCH /api/teams/{team}`

Query:
- `city`, `league_level`, `needs_text`, `page`, `per_page`

## Staff (auth)

- `GET /api/staff`
- `GET /api/staff/{staff}`
- `PUT|PATCH /api/staff/{staff}`

Query:
- `role_type` (`manager|coach|scout`), `organization`, `city`, `page`, `per_page`

## Media (auth)

- `POST /api/media` (multipart: `file`, optional `title`)
- `DELETE /api/media/{id}`
- `GET /api/users/{id}/media`

## Opportunities (auth)

- `GET /api/opportunities`
- `POST /api/opportunities` (team role)
- `GET /api/opportunities/{opportunity}`
- `PUT|PATCH /api/opportunities/{opportunity}` (owner team)
- `DELETE /api/opportunities/{opportunity}` (owner team)

Query:
- `status`, `position`, `city`, `age_min`, `age_max`, `team_user_id`, `page`, `per_page`

## Applications (auth)

- `POST /api/opportunities/{id}/apply` (player role)
- `GET /api/applications/incoming` (team role)
- `GET /api/applications/outgoing` (player role)
- `PATCH /api/applications/{id}/status` (owner team)

## Contacts (auth)

- `POST /api/contacts`
- `GET /api/contacts/inbox`
- `GET /api/contacts/sent`
- `PATCH /api/contacts/{id}/status`

## Response Shape

Genel olarak:

```json
{
  "ok": true,
  "message": "optional",
  "data": {}
}
```

Liste endpointlerinde `data` genellikle Laravel pagination objesidir (`current_page`, `data`, `per_page`, `total`).

## Sample Requests

`POST /api/auth/register`

```json
{
  "name": "Team User",
  "email": "team@example.com",
  "password": "Password123",
  "password_confirmation": "Password123",
  "role": "team",
  "city": "Istanbul"
}
```

`POST /api/opportunities` (team role)

```json
{
  "title": "Scout Needed",
  "position": "FW",
  "city": "Istanbul",
  "status": "open"
}
```

`POST /api/opportunities/{id}/apply` (player role)

```json
{
  "message": "I am interested"
}
```

`PATCH /api/applications/{id}/status` (owner team)

```json
{
  "status": "accepted"
}
```

`POST /api/contacts`

```json
{
  "to_user_id": 1,
  "subject": "Hello",
  "message": "Can we talk?"
}
```

## Postman Coverage Notes

Collection su an tum ana endpoint gruplarini icerir:
- Auth: register/login/me/update/logout
- News: live
- Players/Teams/Staff: list/show/update
- Opportunities: list/create/show/update/delete
- Applications: apply/incoming/outgoing/change status
- Contacts: store/inbox/sent/change status
- Media: upload/list-by-user/delete
