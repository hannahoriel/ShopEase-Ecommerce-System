#!/usr/bin/env bash
set -eu

cd /home/site/wwwroot

while true; do
    php artisan returns:auto-confirm-receipts || true
    sleep 60
done
