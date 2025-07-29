#!/bin/bash
# init.sh (upravený bez Redis)

set -e

echo "🚀 Initializing Airdrop Portal (without Redis)..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Helper functions
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    log_error "Docker is not running. Please start Docker and try again."
    exit 1
fi

# Check if docker-compose is available
if ! command -v docker-compose &> /dev/null; then
    log_error "docker-compose is not installed. Please install docker-compose and try again."
    exit 1
fi

log_info "Checking project structure..."

# Create necessary directories
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p docker/nginx
mkdir -p docker/supervisor
mkdir -p docker/cron

log_success "Project directories created"

# Copy environment file if it doesn't exist
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        log_success "Environment file created from .env.example"
    else
        log_warning ".env.example not found. Creating basic .env file..."
        cat > .env << EOF
APP_NAME="Airdrop Portal"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=airdrop_portal
DB_USERNAME=airdrop_user
DB_PASSWORD=airdrop_password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@airdropportal.io"
MAIL_FROM_NAME="\${APP_NAME}"
EOF
        log_success "Basic .env file created"
    fi
fi

# Stop existing containers
log_info "Stopping existing containers..."
docker-compose down 2>/dev/null || true

# Build and start containers (without Redis)
log_info "Building and starting Docker containers (without Redis)..."
docker-compose up -d --build

# Wait for database to be ready
log_info "Waiting for database to be ready..."
sleep 30

# Check if app container is running
if ! docker-compose ps | grep -q "airdrop_app.*Up"; then
    log_error "App container failed to start. Check logs with: docker-compose logs app"
    exit 1
fi

# Generate application key
log_info "Generating application key..."
docker-compose exec -T app php artisan key:generate

# Wait a bit more for database
sleep 10

# Run database migrations
log_info "Running database migrations..."
docker-compose exec -T app php artisan migrate --force

# Seed the database
log_info "Seeding database with initial data..."
docker-compose exec -T app php artisan db:seed --force

# Create storage symlink
log_info "Creating storage symlink..."
docker-compose exec -T app php artisan storage:link

# Clear and cache config
log_info "Optimizing application..."
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan route:cache
docker-compose exec -T app php artisan view:cache

# Set permissions
log_info "Setting proper permissions..."
docker-compose exec -T app chown -R airdrop:www-data /var/www/storage
docker-compose exec -T app chown -R airdrop:www-data /var/www/bootstrap/cache

log_success "🎉 Airdrop Portal initialization completed (without Redis)!"

echo ""
echo "📋 Access Information:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🌐 Frontend:     http://localhost:8080"
echo "👤 Admin Panel:  http://localhost:8080/admin"
echo "📧 Mailhog:      http://localhost:8025"
echo ""
echo "🔐 Default Admin Credentials:"
echo "📧 Email:        admin@example.com"
echo "🔑 Password:     admin123"
echo ""
log_warning "⚠️  IMPORTANT: Change the admin password on first login!"
echo ""
echo "💾 Storage Information:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📁 Cache:        File-based (storage/framework/cache)"
echo "🗃️  Sessions:     File-based (storage/framework/sessions)"
echo "📋 Queue:        Database-based (jobs table)"
echo "💌 Nonces:       Database-based (wallet_nonces table)"
echo ""
echo "🐳 Docker Commands:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 View logs:    docker-compose logs -f"
echo "🔄 Restart:      docker-compose restart"
echo "🛑 Stop:         docker-compose down"
echo "🗑️  Clean up:     docker-compose down -v"
echo ""

# Check if all services are healthy
log_info "Checking service health..."
sleep 5

services=("app" "db" "mailhog")
all_healthy=true

for service in "${services[@]}"; do
    if docker-compose ps | grep -q "${service}.*Up"; then
        log_success "$service is running"
    else
        log_error "$service failed to start"
        all_healthy=false
    fi
done

if [ "$all_healthy" = true ]; then
    log_success "🎊 All services are running successfully!"
    echo ""
    echo "🚀 Your Airdrop Portal is ready at: http://localhost:8080"
    echo ""
    echo "📝 Note: This setup uses file-based cache and sessions instead of Redis"
    echo "📝 Perfect for shared hosting environments!"
else
    log_warning "Some services failed to start. Check logs with: docker-compose logs"
fi
