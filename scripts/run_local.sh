#!/usr/bin/env bash
set -euo pipefail

export STATTRAX_DB_HOST="${STATTRAX_DB_HOST:-localhost}"
export STATTRAX_DB_PORT="${STATTRAX_DB_PORT:-3306}"
export STATTRAX_DB_NAME="${STATTRAX_DB_NAME:-stattrax}"
export STATTRAX_DB_USER="${STATTRAX_DB_USER:-root}"
export STATTRAX_DB_PASS="${STATTRAX_DB_PASS:-}"

php -S localhost:8000 -t public
