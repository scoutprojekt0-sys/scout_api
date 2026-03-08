# NextScout Observability Notes

## Centralized Logging

- `config/logging.php` icinde `json_stderr` kanali eklendi.
- Production tavsiyesi:
  - `.env.production.example` -> `LOG_STACK=daily,json_stderr`
  - Container veya host agent ile stderr stream'ini topla (ELK, Loki, Datadog, CloudWatch).

## Health Endpoints

- `GET /api/ping` : lightweight reachability
- `GET /api/health` : app + db + cache check

## Suggested Alerts

- `/api/health` 5xx oranı > %1 (5 dk)
- queue backlog artışı
- log içinde `critical` seviye artışı

## Smoke Flows

- Backend smoke: `scripts/smoke-prod.sh`
- Frontend smoke: `scripts/smoke-frontend.sh`
- Workflow:
  - `.github/workflows/production-smoke.yml`
  - `.github/workflows/frontend-smoke.yml`

