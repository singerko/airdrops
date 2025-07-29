# 🌐 Nasadenie na Shared Hosting (bez Docker)

## 📋 Požiadavky webhostingu:
- PHP 8.2+ 
- MySQL 8.0+ (alebo MariaDB 10.3+)
- Composer (alebo možnosť spustiť `composer install`)
- Node.js (pre build frontend assets)
- Cron jobs podpora

## 🚀 Postup nasadenia:

### 1. Príprava na lokálnom počítači:

```bash
# Vytvorte produkčný build
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Vytvorte zip súbor pre upload
zip -r airdrop-portal.zip . -x "node_modules/*" ".git/*" "storage/logs/*"
```

### 2. Upload na webhosting:

```bash
# Nahrajte zip súbor a rozbaľte v root priečinku vášho hostingu
# Štruktúra by mala byť:
public_html/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          # Tento obsah presunúť do public_html root
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env
└── artisan
```

### 3. Konfigurácia .env súboru:

```env
# .env pre shared hosting
APP_NAME="Airdrop Portal"
APP_ENV=production
APP_KEY=base64:GENEROVANÝ_KEY
APP_DEBUG=false
APP_URL=https://vasadomena.com

# Database (od vášho hostingu)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=vas_db_nazov
DB_USERNAME=vas_db_user
DB_PASSWORD=vas_db_heslo

# Cache & Sessions (file-based)
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Mail (SMTP od hostingu alebo external)
MAIL_MAILER=smtp
MAIL_HOST=smtp.vashosting.com
MAIL_PORT=587
MAIL_USERNAME=noreply@vasadomena.com
MAIL_PASSWORD=vas_email_heslo
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@vasadomena.com
MAIL_FROM_NAME="Airdrop Portal"

# Social Login (potrebné registrovať aplikácie)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
TWITTER_CLIENT_ID=
TWITTER_CLIENT_SECRET=

# AI Translation (voliteľné)
OPENAI_API_KEY=
```

### 4. Databázový setup:

```bash
# Cez SSH alebo cPanel File Manager spustite:
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Cron jobs setup (v cPanel):

```bash
# Pridajte do cron jobs (každú minútu):
* * * * * cd /path/to/your/site && php artisan schedule:run >> /dev/null 2>&1

# Alebo ak nemáte každú minútu, aspoň tieto dôležité:
# Každú hodinu - update statusov:
0 * * * * cd /path/to/your/site && php artisan airdrops:update-statuses

# Dvakrát denne - deadline reminders:
0 9,21 * * * cd /path/to/your/site && php artisan notifications:deadline-reminders

# Každú nedeľu - weekly digest:
0 9 * * 0 cd /path/to/your/site && php artisan notifications:weekly-digest

# Denne - cleanup:
0 2 * * * cd /path/to/your/site && php artisan cleanup:expired-data
```

### 6. File permissions:

```bash
# Nastavte správne práva:
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### 7. Queue worker (ak hosting podporuje):

```bash
# V supervisord alebo ako background process:
php artisan queue:work database --sleep=3 --tries=3 --max-time=3600
```

## 🔧 Alternatívne riešenia:

### Ak nemáte SSH prístup:

1. **Database setup cez phpMyAdmin:**
   - Importujte SQL dump z migracií
   - Spustite seeder SQL príkazy

2. **File management cez cPanel:**
   - Nahrajte súbory cez File Manager
   - Upravte .env cez editor

3. **Queue bez worker:**
   - Použite `QUEUE_CONNECTION=sync` (synchronné spracovanie)
   - Alebo setup cron job na `php artisan queue:work --once`

### Optimalizácie pre shared hosting:

```php
// config/app.php - pre lepší performance
'debug' => false,
'log_level' => 'error',

// config/cache.php - ak chcete databázu namiesto súborov
'default' => 'database',

// config/session.php - ak chcete databázu namiesto súborov  
'driver' => 'database',
```

### Backup script pre shared hosting:

```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="./backups"
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -h localhost -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > $BACKUP_DIR/db_backup_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz storage/ .env

echo "Backup completed: $DATE"
```

## 🚨 Bezpečnostné nastavenia:

### .htaccess pre public priečinok:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]

# Bezpečnosť
<Files .env>
    Order allow,deny
    Deny from all
</Files>

# Cache headers
<IfModule mod_expires.c>
    ExpiresActive on
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
</IfModule>
```

### Skrytie Laravel súborov:
```apache
# V root .htaccess
<Files composer.json>
    Order allow,deny
    Deny from all
</Files>

<Files composer.lock>
    Order allow,deny  
    Deny from all
</Files>

<Files package.json>
    Order allow,deny
    Deny from all
</Files>
```

## 📊 Monitoring pre shared hosting:

```php
// app/Console/Commands/HealthCheck.php
public function handle()
{
    $checks = [
        'database' => $this->checkDatabase(),
        'storage' => $this->checkStorage(),
        'cache' => $this->checkCache(),
        'queue' => $this->checkQueue(),
    ];
    
    // Pošlite email ak niečo nefunguje
    if (in_array(false, $checks)) {
        Mail::to('admin@vasadomena.com')->send(new HealthCheckAlert($checks));
    }
}
```

Takto môžete nasadiť projekt na akýkoľvek štandardný webhosting bez potreby Redis alebo špeciálnych služieb!
