#!/usr/bin/env bash
set -euo pipefail

DB_HOST="${STATTRAX_DB_HOST:-localhost}"
DB_PORT="${STATTRAX_DB_PORT:-3306}"
DB_NAME="${STATTRAX_DB_NAME:-stattrax}"
DB_USER="${STATTRAX_DB_USER:-root}"
OUT="${1:-database/stattrax_final_dump.sql}"

mkdir -p "$(dirname "$OUT")"

echo "Creating mysqldump for ${DB_NAME} -> ${OUT}"
mysqldump \
  --host="$DB_HOST" \
  --port="$DB_PORT" \
  --user="$DB_USER" \
  --password \
  --routines \
  --triggers \
  --events \
  --single-transaction \
  "$DB_NAME" > "$OUT"

echo "Done: $OUT"
