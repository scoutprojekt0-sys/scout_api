# API Documentation

## Base URL

- **Local**: `http://localhost:8000/api`
- **Staging**: `https://scout-api-staging.up.railway.app/api`
- **Production**: `https://api.nextscout.com/api`

## Authentication

Scout API uses Laravel Sanctum for token-based authentication.

### Register

```http
POST /auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "player"
}
```

**Response**: `201 Created`

```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "token": "1|laravel_sanctum_token..."
}
```

### Login

```http
POST /auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response**: `200 OK`

```json
{
  "user": { "id": 1, "name": "John Doe", "email": "john@example.com" },
  "token": "2|laravel_sanctum_token..."
}
```

### Authenticated Requests

Include the token in the `Authorization` header:

```http
GET /auth/me
Authorization: Bearer {token}
```

## Public Endpoints

### News Feed

```http
GET /news/live
```

Returns aggregated news from external RSS feeds with fallback to internal opportunities.

### Public Players

```http
GET /public/players?search=midfielder&position=CM
```

### Billing Plans

```http
GET /billing/plans
```

### Discovery

- `GET /trending/week` - Trending players this week
- `GET /rising-stars` - Rising star players
- `GET /club-needs` - Current club needs

## Protected Endpoints (Requires Authentication)

### Opportunities

- `GET /opportunities` - List all opportunities
- `POST /opportunities` - Create new opportunity
- `GET /opportunities/{id}` - Get opportunity details
- `PATCH /opportunities/{id}` - Update opportunity
- `DELETE /opportunities/{id}` - Delete opportunity

### Applications

- `POST /opportunities/{id}/apply` - Apply to opportunity
- `GET /applications/incoming` - View received applications
- `GET /applications/outgoing` - View sent applications
- `PATCH /applications/{id}/status` - Update application status

### Media

- `POST /media` - Upload media (multipart/form-data with `file` field)
- `GET /users/{id}/media` - Get user's media
- `DELETE /media/{id}` - Delete media

### Billing

- `GET /billing/subscription` - Current subscription
- `POST /billing/subscribe` - Subscribe to plan
- `POST /billing/cancel` - Cancel subscription
- `GET /billing/payments` - Payment history
- `GET /billing/invoices` - Invoice history

## Rate Limiting

- **Auth endpoints** (`/auth/login`, `/auth/register`): 5 requests per minute
- **General API**: 60 requests per minute

## Error Responses

```json
{
  "message": "Unauthenticated."
}
```

```json
{
  "message": "Validation error",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

## Postman Collection

A complete Postman collection is available at:
- `postman/Scout_API_E2E.postman_collection.json`
- `postman/Scout_API_E2E.postman_environment.json`

Import both files into Postman for full API testing.

## Health Check

```http
GET /up
```

Returns Laravel health status.
