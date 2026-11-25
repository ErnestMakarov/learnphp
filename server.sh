#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"
if [[ "${1:-}" == "--mysql" ]]; then
    export DB_DSN='mysql:host=127.0.0.1;port=33061;dbname=learnphp;charset=utf8mb4'
    export DB_USER='learnphp'
    export DB_PASSWORD='local-coursework'
elif [[ $# -gt 0 ]]; then
    echo 'Usage: bash server.sh [--mysql]' >&2
    exit 1
fi
exec php -S localhost:9999 -t public public/index.php
