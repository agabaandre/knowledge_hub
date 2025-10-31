#!/bin/bash

# Laravel Queue and Schedule Systemd Setup Script
# This script automatically configures systemd services for Laravel queue worker and scheduler

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration variables
APP_PATH="/opt/homebrew/var/www/knowledge_hub"
PHP_PATH="/usr/bin/php"
SERVICE_USER="www-data"
SERVICE_GROUP="www-data"
QUEUE_CONNECTION="default"
QUEUE_TRIES=3
QUEUE_TIMEOUT=90
QUEUE_SLEEP=3
QUEUE_MAX_JOBS=1000
QUEUE_MAX_TIME=3600

# Functions
print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

check_root() {
    if [ "$EUID" -ne 0 ]; then 
        print_error "Please run as root (use sudo)"
        exit 1
    fi
}

check_prerequisites() {
    print_info "Checking prerequisites..."
    
    # Check if PHP is installed
    if [ ! -f "$PHP_PATH" ]; then
        print_error "PHP not found at $PHP_PATH"
        print_info "Please update PHP_PATH variable in the script"
        exit 1
    fi
    
    # Check if app directory exists
    if [ ! -d "$APP_PATH" ]; then
        print_error "Application directory not found: $APP_PATH"
        print_info "Please update APP_PATH variable in the script"
        exit 1
    fi
    
    # Check if artisan exists
    if [ ! -f "$APP_PATH/artisan" ]; then
        print_error "Laravel artisan file not found: $APP_PATH/artisan"
        exit 1
    fi
    
    # Check if user exists
    if ! id "$SERVICE_USER" &>/dev/null; then
        print_error "User $SERVICE_USER does not exist"
        print_info "Please update SERVICE_USER variable in the script"
        exit 1
    fi
    
    print_success "All prerequisites met"
}

create_queue_service() {
    print_info "Creating queue worker service file..."
    
    cat > /etc/systemd/system/laravel-queue-worker.service <<EOF
[Unit]
Description=Laravel Queue Worker
After=network.target mysql.service redis.service

[Service]
Type=simple
User=$SERVICE_USER
Group=$SERVICE_GROUP
Restart=always
RestartSec=3
WorkingDirectory=$APP_PATH
ExecStart=$PHP_PATH artisan queue:work --queue=$QUEUE_CONNECTION --tries=$QUEUE_TRIES --timeout=$QUEUE_TIMEOUT --sleep=$QUEUE_SLEEP --max-jobs=$QUEUE_MAX_JOBS --max-time=$QUEUE_MAX_TIME
StandardOutput=journal
StandardError=journal

# Optional: Increase memory limit
Environment="PHP_MEMORY_LIMIT=512M"

[Install]
WantedBy=multi-user.target
EOF

    print_success "Queue worker service file created"
}

create_schedule_service() {
    print_info "Creating scheduler service file..."
    
    cat > /etc/systemd/system/laravel-schedule.service <<EOF
[Unit]
Description=Laravel Scheduler
After=network.target mysql.service redis.service

[Service]
Type=simple
User=$SERVICE_USER
Group=$SERVICE_GROUP
Restart=always
RestartSec=60
WorkingDirectory=$APP_PATH
ExecStart=$PHP_PATH artisan schedule:run
StandardOutput=journal
StandardError=journal

[Install]
WantedBy=multi-user.target
EOF

    print_success "Scheduler service file created"
}

create_schedule_timer() {
    print_info "Creating scheduler timer file..."
    
    cat > /etc/systemd/system/laravel-schedule.timer <<EOF
[Unit]
Description=Run Laravel Scheduler Every Minute
Requires=laravel-schedule.service

[Timer]
OnBootSec=1min
OnUnitActiveSec=1min
AccuracySec=1s

[Install]
WantedBy=timers.target
EOF

    print_success "Scheduler timer file created"
}

set_permissions() {
    print_info "Setting permissions for application directories..."
    
    # Set ownership
    chown -R $SERVICE_USER:$SERVICE_GROUP "$APP_PATH/storage" 2>/dev/null || print_warning "Could not set ownership for storage"
    chown -R $SERVICE_USER:$SERVICE_GROUP "$APP_PATH/bootstrap/cache" 2>/dev/null || print_warning "Could not set ownership for bootstrap/cache"
    
    # Set permissions
    chmod -R 775 "$APP_PATH/storage" 2>/dev/null || print_warning "Could not set permissions for storage"
    chmod -R 775 "$APP_PATH/bootstrap/cache" 2>/dev/null || print_warning "Could not set permissions for bootstrap/cache"
    
    print_success "Permissions set"
}

enable_services() {
    print_info "Reloading systemd daemon..."
    systemctl daemon-reload
    
    print_info "Enabling services..."
    systemctl enable laravel-queue-worker.service
    systemctl enable laravel-schedule.timer
    
    print_success "Services enabled"
}

start_services() {
    print_info "Starting services..."
    
    systemctl start laravel-queue-worker.service
    if [ $? -eq 0 ]; then
        print_success "Queue worker started"
    else
        print_error "Failed to start queue worker"
    fi
    
    systemctl start laravel-schedule.timer
    if [ $? -eq 0 ]; then
        print_success "Scheduler timer started"
    else
        print_error "Failed to start scheduler timer"
    fi
}

show_status() {
    print_info "Service Status:"
    echo ""
    
    echo "Queue Worker:"
    systemctl status laravel-queue-worker.service --no-pager -l || true
    echo ""
    
    echo "Scheduler Timer:"
    systemctl status laravel-schedule.timer --no-pager -l || true
    echo ""
    
    echo "Scheduler Service:"
    systemctl status laravel-schedule.service --no-pager -l || true
    echo ""
}

show_help() {
    cat <<EOF
Laravel Queue and Schedule Systemd Setup Script

Usage: sudo ./setup-systemd.sh [OPTIONS]

Options:
    -h, --help          Show this help message
    -p, --path PATH     Set application path (default: $APP_PATH)
    -u, --user USER     Set service user (default: $SERVICE_USER)
    -q, --queue QUEUE   Set queue name (default: $QUEUE_CONNECTION)
    --skip-start        Don't start services (only create and enable)
    --skip-permissions  Don't set file permissions
    --status            Show service status and exit

Configuration Variables (edit script to change):
    APP_PATH: $APP_PATH
    PHP_PATH: $PHP_PATH
    SERVICE_USER: $SERVICE_USER
    SERVICE_GROUP: $SERVICE_GROUP
    QUEUE_CONNECTION: $QUEUE_CONNECTION
    QUEUE_TRIES: $QUEUE_TRIES
    QUEUE_TIMEOUT: $QUEUE_TIMEOUT
    QUEUE_SLEEP: $QUEUE_SLEEP
    QUEUE_MAX_JOBS: $QUEUE_MAX_JOBS
    QUEUE_MAX_TIME: $QUEUE_MAX_TIME

Examples:
    sudo ./setup-systemd.sh
    sudo ./setup-systemd.sh --path /var/www/myapp --user nginx
    sudo ./setup-systemd.sh --skip-start
    sudo ./setup-systemd.sh --status
EOF
}

# Parse command line arguments
SKIP_START=false
SKIP_PERMISSIONS=false
SHOW_STATUS=false

while [[ $# -gt 0 ]]; do
    case $1 in
        -h|--help)
            show_help
            exit 0
            ;;
        -p|--path)
            APP_PATH="$2"
            shift 2
            ;;
        -u|--user)
            SERVICE_USER="$2"
            shift 2
            ;;
        -q|--queue)
            QUEUE_CONNECTION="$2"
            shift 2
            ;;
        --skip-start)
            SKIP_START=true
            shift
            ;;
        --skip-permissions)
            SKIP_PERMISSIONS=true
            shift
            ;;
        --status)
            SHOW_STATUS=true
            shift
            ;;
        *)
            print_error "Unknown option: $1"
            show_help
            exit 1
            ;;
    esac
done

# Main execution
main() {
    echo "=========================================="
    echo "Laravel Systemd Setup Script"
    echo "=========================================="
    echo ""
    
    check_root
    
    if [ "$SHOW_STATUS" = true ]; then
        show_status
        exit 0
    fi
    
    check_prerequisites
    
    echo ""
    print_info "Configuration:"
    echo "  Application Path: $APP_PATH"
    echo "  PHP Path: $PHP_PATH"
    echo "  Service User: $SERVICE_USER"
    echo "  Service Group: $SERVICE_GROUP"
    echo "  Queue: $QUEUE_CONNECTION"
    echo ""
    
    read -p "Continue with setup? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        print_info "Setup cancelled"
        exit 0
    fi
    
    echo ""
    
    # Create service files
    create_queue_service
    create_schedule_service
    create_schedule_timer
    
    # Set permissions
    if [ "$SKIP_PERMISSIONS" != true ]; then
        set_permissions
    fi
    
    # Enable services
    enable_services
    
    # Start services
    if [ "$SKIP_START" != true ]; then
        start_services
        echo ""
        show_status
    else
        print_info "Skipping service start (use --skip-start=false to start)"
    fi
    
    echo ""
    print_success "Setup completed!"
    echo ""
    print_info "Useful commands:"
    echo "  Check status:     systemctl status laravel-queue-worker.service"
    echo "  Check scheduler:   systemctl status laravel-schedule.timer"
    echo "  View logs:         journalctl -u laravel-queue-worker.service -f"
    echo "  Restart worker:    systemctl restart laravel-queue-worker.service"
    echo "  Stop worker:       systemctl stop laravel-queue-worker.service"
    echo ""
}

# Run main function
main

