# 🐧 Airdrop Portal - Fedora Setup Guide

Špecifické inštrukcie pre spustenie projektu na **Fedora Linux** s **Podman**.

## 🚀 Quick Start

```bash
# 1. Clone repository
git clone https://github.com/singerko/airdrops.git
cd airdrops

# 2. Switch to develop branch
git checkout develop

# 3. Install podman-compose (if not installed)
sudo dnf install podman-compose

# 4. Run Fedora-specific setup
chmod +x setup-fedora.sh
./setup-fedora.sh
```

## 📋 Požiadavky

### Systémové požiadavky:
- **Fedora 37+** (testované na Fedora 39)
- **Podman 4.0+**
- **podman-compose**
- **Git**

### Inštalácia závislostí:
```bash
# Základné nástroje
sudo dnf update
sudo dnf install podman podman-compose git

# Voliteľné - pre development
sudo dnf install nodejs npm composer
```

## 🔧 Podrobný Setup

### 1. Klonujte projekt:
```bash
git clone https://github.com/singerko/airdrops.git
cd airdrops
git checkout develop
```

### 2. Vytvorte environment súbor:
```bash
cp .env.example .env
```

### 3. Upravte .env (voliteľné):
```env
# Pre lokálny development sú defaultné hodnoty OK
APP_URL=http://localhost:8080
DB_HOST=db
DB_DATABASE=airdrop_portal
DB_USERNAME=airdrop_user
DB_PASSWORD=airdrop_password
```

### 4. Spustite containers:
```bash
# Použite podman-compose namiesto docker-compose
podman-compose up -d --build
```

### 5. Inicializujte aplikáciu:
```bash
# Počkajte ~60 sekúnd na štart databázy, potom:
podman-compose exec -T app php artisan key:generate
podman-compose exec -T app php artisan migrate --force
podman-compose exec -T app php artisan db:seed --force
podman-compose exec -T app php artisan storage:link
podman-compose exec -T app php artisan config:cache
```

## 🌐 Prístup k aplikácii

| Služba | URL | Popis |
|--------|-----|--------|
| **Frontend** | http://localhost:8080 | Hlavná stránka portálu |
| **Admin Panel** | http://localhost:8080/admin | Administrácia |
| **Mailhog** | http://localhost:8025 | Email testing |

### 🔐 Admin prístup:
- **Email:** `admin@example.com`
- **Password:** `admin123`
- ⚠️ **Zmeňte heslo pri prvom prihlásení!**

## 🛠️ Užitočné príkazy

### Container management:
```bash
# Zobraziť running containers
podman-compose ps

# Zobraziť logy
podman-compose logs -f

# Reštart services
podman-compose restart

# Zastaviť všetko
podman-compose down

# Kompletné vyčistenie (pozor na data!)
podman-compose down -v
```

### Laravel príkazy:
```bash
# Spustiť artisan príkaz
podman-compose exec app php artisan [command]

# Príklady:
podman-compose exec app php artisan migrate:status
podman-compose exec app php artisan queue:work
podman-compose exec app php artisan cache:clear
```

### Database prístup:
```bash
# Pripojiť sa k MySQL
podman-compose exec db mysql -u airdrop_user -p airdrop_portal

# Backup databázy
podman-compose exec db mysqldump -u airdrop_user -p airdrop_portal > backup.sql
```

## 🐛 Troubleshooting

### ❌ "Permission denied" chyby:
```bash
# SELinux kontexty
sudo setsebool -P container_manage_cgroup on
chcon -Rt container_file_t storage/ bootstrap/cache/

# File permissions
sudo chown -R $USER:$USER storage/ bootstrap/cache/
```

### ❌ "Port already in use":
```bash
# Skontrolovať obsadené porty
sudo ss -tulpn | grep -E ':(8080|3306|1025|8025)'

# Zastaviť konfliktné služby
sudo systemctl stop httpd nginx mysql

# Alebo zmeňte porty v docker-compose.yml
```

### ❌ Container neštartuje:
```bash
# Zobraziť detailné logy
podman-compose logs app
podman-compose logs db

# Reštart containers
podman-compose down
podman-compose up -d --build

# Vyčistiť všetko a začať znovu
podman-compose down -v
podman system prune -a
```

### ❌ Database connection chyby:
```bash
# Skontrolovať či MySQL container beží
podman-compose ps

# Počkať dlhšie na štart databázy
sleep 60

# Manuálne otestovať pripojenie
podman-compose exec app php artisan tinker
# V tinkeri: DB::connection()->getPdo();
```

### ❌ "docker-compose command not found":
```bash
# Použite podman-compose namiesto docker-compose
sed -i 's/docker-compose/podman-compose/g' *.sh

# Alebo vytvorte alias
echo 'alias docker-compose=podman-compose' >> ~/.bashrc
source ~/.bashrc
```

## 🔄 Development workflow

### Aktualizácia kódu:
```bash
# Pull latest changes
git pull origin develop

# Rebuild containers
podman-compose down
podman-compose up -d --build

# Run migrations if needed
podman-compose exec app php artisan migrate
```

### Logs monitoring:
```bash
# Všetky logy
podman-compose logs -f

# Špecifický service
podman-compose logs -f app
podman-compose logs -f db

# Laravel logy
podman-compose exec app tail -f storage/logs/laravel.log
```

## 📊 Performance optimalizácia

### Pre development:
```bash
# Clear všetky cache
podman-compose exec app php artisan cache:clear
podman-compose exec app php artisan config:clear
podman-compose exec app php artisan route:clear
podman-compose exec app php artisan view:clear
```

### Pre produkciu:
```bash
# Optimalizácia
podman-compose exec app php artisan config:cache
podman-compose exec app php artisan route:cache
podman-compose exec app php artisan view:cache
podman-compose exec app composer install --optimize-autoloader --no-dev
```

## 🆘 Získanie pomoci

1. **Skontrolujte logy:** `podman-compose logs -f`
2. **Overte container status:** `podman-compose ps`
3. **Reštartujte služby:** `podman-compose restart`
4. **GitHub Issues:** https://github.com/singerko/airdrops/issues

---

### 📝 Poznámky pre Fedoru:

- Projekt je optimalizovaný pre **podman** namiesto Docker
- **SELinux** môže vyžadovať dodatočné nastavenia
- **Firewall** rules môžu blokovať porty - použite `firewall-cmd` ak je potrebné
- Pre **production** odporúčame **nginx proxy** s **SSL certifikátmi**
