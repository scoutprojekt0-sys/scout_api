#!/usr/bin/env bash
set -euo pipefail

FRONT_URL="${1:-}"
if [[ -z "$FRONT_URL" ]]; then
  echo "Usage: ./scripts/smoke-frontend.sh https://nextscout.app"
  exit 1
fi

check_contains() {
  local path="$1"
  local needle="$2"
  local url="${FRONT_URL%/}${path}"
  echo "Checking ${url}"
  body="$(curl -fsS --max-time 12 "$url")"
  echo "$body" | grep -q "$needle"
}

check_contains "/" "NextScout"
check_contains "/index.html" "Veri Durumu"
check_contains "/professional-players.html" "Karsilastir"
check_contains "/player-profile.html" "Transfer Gecmisi"

echo "Frontend smoke checks passed."

