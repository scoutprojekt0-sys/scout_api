#!/usr/bin/env bash
set -euo pipefail

# SQLite backup helper.
# Usage:
#   ./scripts/db_backup.sh
#   ./scripts/db_backup.sh /custom/output/dir

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKUP_DIR="${1:-${ROOT_DIR}/backups}"
TIMESTAMP="$(date +%Y%m%d_%H%M%S)"

DB_PATH="${DB_DATABASE:-${ROOT_DIR}/database/database.sqlite}"
if [[ "${DB_PATH}" != /* ]]; then
  DB_PATH="${ROOT_DIR}/${DB_PATH}"
fi

if [[ ! -f "${DB_PATH}" ]]; then
  echo "Database file not found: ${DB_PATH}" >&2
  exit 1
fi

mkdir -p "${BACKUP_DIR}"

DEST_FILE="${BACKUP_DIR}/backup_${TIMESTAMP}.sqlite"
cp "${DB_PATH}" "${DEST_FILE}"

echo "Backup created: ${DEST_FILE}"
