#!/bin/bash
# scripts/restore.sh - Database restore script

set -e

if [ $# -eq 0 ]; then
    echo "Usage: $0 <backup_file.sql.gz>"
    echo "Available backups:"
    ls -la ./backups/airdrop_portal_backup_*.sql.gz 2>/dev/null || echo "No backups found"
    exit 1
fi

BACKUP_FILE=$1

if [ ! -f "$BACKUP_FILE" ]; then
    echo "Backup file not found: $BACKUP_FILE"
    exit 1
fi

echo "Restoring database from: $BACKUP_FILE"
echo "WARNING: This will overwrite the current database!"
read -p "Are you sure? (y/N): " -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    # Decompress and restore
    gunzip -c "$BACKUP_FILE" | docker-compose exec -T db mysql -u root -proot_password airdrop_portal
    echo "Database restored successfully"
    
    # Run migrations to ensure schema is up to date
    docker-compose exec app php artisan migrate --force
    echo "Migrations applied"
else
    echo "Restore cancelled"
fi
