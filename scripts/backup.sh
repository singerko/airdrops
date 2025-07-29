#!/bin/bash
# scripts/backup.sh - Database backup script

set -e

BACKUP_DIR="./backups"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="airdrop_portal_backup_${TIMESTAMP}.sql"

mkdir -p $BACKUP_DIR

echo "Creating database backup..."
docker-compose exec -T db mysqldump -u root -proot_password airdrop_portal > "${BACKUP_DIR}/${BACKUP_FILE}"

# Compress the backup
gzip "${BACKUP_DIR}/${BACKUP_FILE}"

echo "Backup created: ${BACKUP_DIR}/${BACKUP_FILE}.gz"

# Keep only last 7 backups
find $BACKUP_DIR -name "airdrop_portal_backup_*.sql.gz" -type f -mtime +7 -delete

echo "Old backups cleaned up (keeping last 7 days)"

