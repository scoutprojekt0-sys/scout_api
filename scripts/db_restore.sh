#!/usr/bin/env bash
set -euo pipefail

# SQLite restore helper.
# Usage:
#   ./scripts/db_restore.sh /path/to/backup.sqlite

if [[ $# -lt 1 ]]; then
  echo "Usage: $0 /path/to/backup.sqlite" >&2
  exit 1
fi

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SOURCE_FILE="$1"
DB_PATH="${DB_DATABASE:-${ROOT_DIR}/database/database.sqlite}"

if [[ "${SOURCE_FILE}" != /* ]]; then
  SOURCE_FILE="${ROOT_DIR}/${SOURCE_FILE}"
fi

if [[ "${DB_PATH}" != /* ]]; then
  DB_PATH="${ROOT_DIR}/${DB_PATH}"
fi

if [[ ! -f "${SOURCE_FILE}" ]]; then
  echo "Backup file not found: ${SOURCE_FILE}" >&2
  exit 1
fi

mkdir -p "$(dirname "${DB_PATH}")"
cp "${SOURCE_FILE}" "${DB_PATH}"

echo "Database restored from: ${SOURCE_FILE}"
echo "Target database: ${DB_PATH}"
