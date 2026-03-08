#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${1:-}"
if [[ -z "$BASE_URL" ]]; then
  echo "Usage: ./scripts/smoke-prod.sh https://api.example.com"
  exit 1
fi

check() {
  local path="$1"
  local url="${BASE_URL}${path}"
  echo "Checking ${url}"
  curl -fsS --max-time 12 "$url" >/dev/null
}

check "/api/ping"
check "/api/health"
check "/api/public/players?limit=5"
check "/api/club-needs?limit=5"
check "/api/trending/week"

echo "Smoke checks passed."

