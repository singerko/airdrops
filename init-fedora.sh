#!/bin/bash

set -e

echo "🚀 Initializing Airdrop Portal on Fedora..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; }

# Improved detection for Fedora
detect_container_engine() {
    # Check if docker-compose actually uses podman
    if command -v docker-compose &> /dev/null; then
        COMPOSE_HELP=$(docker-compose --help 2>&1 || true)
        if echo "$COMPOSE_HELP" | grep -q "podman-compose"; then
            CONTAINER_ENGINE="podman"
            COMPOSE_COMMAND="docker-compose"
            log_info "Detected: Fedora's docker-compose (actually podman-compose)"
            return 0
        fi
    fi
    
    # Check for real docker
    if command -v docker &> /dev/null && systemctl is-active --quiet docker 2>/dev/null; then
        CONTAINER_ENGINE="docker"
        COMPOSE_COMMAND="docker-compose"
        log_info "Detected: Real Docker with docker-compose"
        return 0
    fi
    
    # Check for podman
    if command -v podman &> /dev/null; then
        CONTAINER_ENGINE="podman"
        
        if command -v podman-compose &> /dev/null; then
            COMPOSE_COMMAND="podman-compose"
            log_info "Detected: Podman with podman-compose"
        elif command -v docker-compose &> /dev/null; then
            COMPOSE_COMMAND="docker-compose"
            log_info "Detected: Podman with docker-compose wrapper"
            # Setup podman socket for docker-compose compatibility
            if ! systemctl --user is-active --quiet podman.socket; then
                log_info "Starting Podman socket for docker-compose compatibility..."
                systemctl --user enable --now podman.socket
                export DOCKER_HOST=unix:///run/user/$UID/podman/podman.sock
            fi
        else
            log_error "Podman found but no compose tool available!"
            log_info "Install with: sudo dnf install podman-compose"
            exit 1
        fi
        return 0
    fi
    
    log_error "No container engine found!"
    log_info "Install Docker: sudo dnf install docker docker-compose"
    log_info "Or install Podman: sudo dnf install podman podman-compose"
    exit 1
}

# Run detection
detect_container_engine

log_info "Using: $CONTAINER_ENGINE with $COMPOSE_COMMAND"

# Special setup for podman if needed
if [ "$CONTAINER_ENGINE" = "podman" ]; then
    # Ensure podman socket is running if using docker-compose command
    if [ "$COMPOSE_COMMAND" = "docker-compose" ]; then
        if ! systemctl --user is-active --quiet podman.socket; then
            log_info "Starting Podman socket..."
            systemctl --user enable --now podman.socket
        fi
        export DOCKER_HOST=unix:///run/user/$UID/podman/podman.sock
        log_info "Set DOCKER_HOST for podman compatibility"
    fi
fi

# Check project structure
log_info "Checking project structure..."

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
        log_warning "Creating basic .env file..."
        cat > .env << 'EOF'
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
MAIL_FROM_NAME="${APP_NAME}"
EOF
        log_success "Basic .env file created"
    fi
fi

# Stop existing containers
log_info "Stopping existing containers..."
$COMPOSE_COMMAND down 2>/dev/null || true

# Build and start containers
log_info "Building and starting containers..."
$COMPOSE_COMMAND up -d --build

# Wait for database to be ready
log_info "Waiting for database to be ready..."
sleep 30

# Check if app container is running
APP_STATUS=$($COMPOSE_COMMAND ps 2>/dev/null | grep "airdrop_app" || echo "not found")
if ! echo "$APP_STATUS" | grep -q "Up"; then
    log_error "App container failed to start. Check logs with: $COMPOSE_COMMAND logs app"
    log_info "Container status: $APP_STATUS"
    exit 1
fi

# Generate application key
log_info "Generating application key..."
$COMPOSE_COMMAND exec -T app php artisan key:generate

# Wait a bit more for database
sleep 10

# Run database migrations
log_info "Running database migrations..."
$COMPOSE_COMMAND exec -T app php artisan migrate --force

# Seed the database
log_info "Seeding database with initial data..."
$COMPOSE_COMMAND exec -T app php artisan db:seed --force

# Create storage symlink
log_info "Creating storage symlink..."
$COMPOSE_COMMAND exec -T app php artisan storage:link

# Clear and cache config
log_info "Optimizing application..."
$COMPOSE_COMMAND exec -T app php artisan config:cache
$COMPOSE_COMMAND exec -T app php artisan route:cache
$COMPOSE_COMMAND exec -T app php artisan view:cache

# Set permissions
log_info "Setting proper permissions..."
$COMPOSE_COMMAND exec -T app chown -R airdrop:www-data /var/www/storage
$COMPOSE_COMMAND exec -T app chown -R airdrop:www-data /var/www/bootstrap/cache

log_success "🎉 Airdrop Portal initialization completed!"

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
echo "🐳 Container Commands:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 View logs:    $COMPOSE_COMMAND logs -f"
echo "🔄 Restart:      $COMPOSE_COMMAND restart"
echo "🛑 Stop:         $COMPOSE_COMMAND down"
echo "🗑️  Clean up:     $COMPOSE_COMMAND down -v"
echo ""
echo "💾 Container Engine: $CONTAINER_ENGINE"
echo "🔧 Compose Tool: $COMPOSE_COMMAND"

# Check if all services are healthy
log_info "Checking service health..."
sleep 5

services=("app" "db" "mailhog")
all_healthy=true

for service in "${services[@]}"; do
    if $COMPOSE_COMMAND ps | grep -q "${service}.*Up"; then
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
else
    log_warning "Some services failed to start. Check logs with: $COMPOSE_COMMAND logs"
fi

# Environment info for troubleshooting
echo ""
echo "🔍 Environment Information:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "OS: $(cat /etc/fedora-release 2>/dev/null || echo 'Unknown')"
echo "Container Engine: $CONTAINER_ENGINE"
echo "Compose Command: $COMPOSE_COMMAND"
if [ "$CONTAINER_ENGINE" = "podman" ] && [ "$COMPOSE_COMMAND" = "docker-compose" ]; then
    echo "Podman Socket: ${DOCKER_HOST:-Not set}"
fi
