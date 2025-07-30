#!/bin/bash
# setup-fedora.sh - Špecifický setup pre Fedoru s podman

set -e

echo "🚀 Setting up Airdrop Portal on Fedora with Podman..."

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; }

# Check if we're in the right directory
if [ ! -f "composer.json" ] || [ ! -d "app" ]; then
    log_error "This doesn't appear to be a Laravel project directory!"
    log_info "Make sure you're in the airdrops project root."
    exit 1
fi

# Check for podman-compose
if ! command -v podman-compose &> /dev/null; then
    log_error "podman-compose not found!"
    log_info "Install with: sudo dnf install podman-compose"
    exit 1
fi

log_info "Found podman-compose, proceeding with setup..."

# Create .env if it doesn't exist
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
        log_success "Created .env from .env.example"
    else
        log_error ".env.example not found!"
        exit 1
    fi
else
    log_info ".env already exists, skipping..."
fi

# Create necessary directories
log_info "Creating necessary directories..."
mkdir -p storage/app/public
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set SELinux context if SELinux is enabled
if command -v getenforce &> /dev/null && [ "$(getenforce)" != "Disabled" ]; then
    log_info "Setting SELinux contexts for containers..."
    chcon -Rt container_file_t storage/ bootstrap/cache/ || log_warning "SELinux context setting failed (non-critical)"
fi

# Stop any existing containers
log_info "Stopping existing containers..."
podman-compose down 2>/dev/null || true

# Build and start containers
log_info "Building and starting containers..."
podman-compose up -d --build

if [ $? -ne 0 ]; then
    log_error "Failed to start containers!"
    log_info "Check for port conflicts or other issues."
    exit 1
fi

# Wait for containers to be ready
log_info "Waiting for containers to initialize..."
sleep 45

# Check if containers are running
if ! podman-compose ps | grep -q "airdrop_app.*Up"; then
    log_error "App container failed to start!"
    log_info "Check logs with: podman-compose logs app"
    exit 1
fi

log_success "Containers are running!"

# Laravel setup
log_info "Setting up Laravel application..."

# Generate app key
log_info "Generating application key..."
podman-compose exec -T app php artisan key:generate

# Wait for database
log_info "Waiting for database to be ready..."
sleep 15

# Run migrations
log_info "Running database migrations..."
podman-compose exec -T app php artisan migrate --force

if [ $? -ne 0 ]; then
    log_error "Database migration failed!"
    log_info "Check database logs: podman-compose logs db"
    exit 1
fi

# Seed database
log_info "Seeding database..."
podman-compose exec -T app php artisan db:seed --force

# Create storage symlink
log_info "Creating storage symlink..."
podman-compose exec -T app php artisan storage:link

# Clear and optimize
log_info "Optimizing application..."
podman-compose exec -T app php artisan config:cache
podman-compose exec -T app php artisan route:cache
podman-compose exec -T app php artisan view:cache

# Fix permissions
log_info "Setting container permissions..."
podman-compose exec -T app chown -R airdrop:www-data /var/www/storage
podman-compose exec -T app chown -R airdrop:www-data /var/www/bootstrap/cache

log_success "🎉 Setup completed successfully!"

echo ""
echo "📋 Access Information:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🌐 Frontend:     http://localhost:8080"
echo "👤 Admin Panel:  http://localhost:8080/admin"
echo "📧 Mailhog:      http://localhost:8025"
echo ""
echo "🔐 Default Admin Login:"
echo "📧 Email:        admin@example.com"
echo "🔑 Password:     admin123"
echo ""
log_warning "⚠️  Change admin password on first login!"
echo ""
echo "🔧 Useful Commands:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 View logs:     podman-compose logs -f"
echo "🔄 Restart:       podman-compose restart"
echo "🛑 Stop:          podman-compose down"
echo "🗑️  Clean up:      podman-compose down -v"
echo "🔍 Container status: podman-compose ps"
echo ""

# Final health check
log_info "Performing health check..."
sleep 5

services=("app" "db" "mailhog")
all_good=true

for service in "${services[@]}"; do
    if podman-compose ps | grep -q "${service}.*Up"; then
        log_success "$service is healthy"
    else
        log_error "$service is not running properly"
        all_good=false
    fi
done

if [ "$all_good" = true ]; then
    echo ""
    log_success "🎊 All services are healthy!"
    echo "🚀 Your Airdrop Portal is ready: http://localhost:8080"
else
    echo ""
    log_warning "Some services have issues. Check with: podman-compose logs"
fi
