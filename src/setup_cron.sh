#!/bin/bash

# Find the absolute path to the PHP binary
PHP_PATH=$(which php)

# Resolve the full path to cron.php (this script must be in the same directory)
PROJECT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CRON_FILE="$PROJECT_PATH/cron.php"

# Define the cron job (runs at the top of every hour)
CRON_JOB="0 * * * * $PHP_PATH $CRON_FILE"

# Check if the cron job already exists
( crontab -l 2>/dev/null | grep -Fv "$CRON_FILE"; echo "$CRON_JOB" ) | sort | uniq | crontab -

echo "Cron job installed to run every hour: $PHP_PATH $CRON_FILE"
