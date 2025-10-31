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

# Auto-detect Laravel root directory
detect_laravel_root() {
    local current_dir="$PWD"
    local check_dir="$current_dir"
    
    # If script is in a subdirectory, try to find Laravel root
    if [ -f "$current_dir/artisan" ]; then
        echo "$current_dir"
        return 0
    fi
    
    # Check parent directories up to 5 levels
    local depth=0
    while [ $depth -lt 5 ]; do
        if [ -f "$check_dir/artisan" ]; then
            echo "$check_dir"
            return 0
        fi
        check_dir="$(dirname "$check_dir")"
        depth=$((depth + 1))
    done
    
    # If not found, use current directory as fallback
    echo "$current_dir"
}

# Configuration variables (with auto-detection)
APP_PATH="${APP_PATH:-$(detect_laravel_root)}"
PHP_PATH="${PHP_PATH:-/usr/bin/php}"
SERVICE_USER="${SERVICE_USER:-www-data}"
SERVICE_GROUP="${SERVICE_GROUP:-www-data}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-default}"
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
    
    # Resolve absolute path
    APP_PATH=$(cd "$APP_PATH" && pwd)
    
    # Check if PHP is installed
    if [ ! -f "$PHP_PATH" ]; then
        print_error "PHP not found at $PHP_PATH"
        print_info "Trying to find PHP in common locations..."
        
        # Try common PHP locations
        for php_loc in "/usr/bin/php" "/usr/local/bin/php" "$(which php 2>/dev/null)"; do
            if [ -n "$php_loc" ] && [ -f "$php_loc" ]; then
                PHP_PATH="$php_loc"
                print_success "Found PHP at $PHP_PATH"
                break
            fi
        done
        
        if [ ! -f "$PHP_PATH" ]; then
            print_error "Could not find PHP. Please install PHP or set PHP_PATH environment variable"
            exit 1
        fi
    fi
    
    # Check if app directory exists
    if [ ! -d "$APP_PATH" ]; then
        print_error "Application directory not found: $APP_PATH"
        print_info "Current working directory: $PWD"
        print_info "Please run this script from the Laravel root directory or use --path option"
        exit 1
    fi
    
    # Check if artisan exists
    if [ ! -f "$APP_PATH/artisan" ]; then
        print_error "Laravel artisan file not found: $APP_PATH/artisan"
        print_info "This doesn't appear to be a Laravel application directory"
        print_info "Current directory: $APP_PATH"
        print_info "Please run this script from the Laravel root directory or use --path option"
        exit 1
    fi
    
    # Verify it's a Laravel app by checking for composer.json
    if [ ! -f "$APP_PATH/composer.json" ]; then
        print_warning "composer.json not found. This might not be a Laravel application."
        read -p "Continue anyway? (y/n) " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 0
        fi
    fi
    
    # Check if user exists
    if ! id "$SERVICE_USER" &>/dev/null; then
        print_warning "User $SERVICE_USER does not exist"
        print_info "Trying to find a suitable web server user..."
        
        # Try common web server users
        for user in "www-data" "nginx" "apache" "httpd" "www"; do
            if id "$user" &>/dev/null; then
                SERVICE_USER="$user"
                SERVICE_GROUP="$user"
                print_success "Using user: $SERVICE_USER"
                break
            fi
        done
        
        if ! id "$SERVICE_USER" &>/dev/null; then
            print_error "Could not find a suitable web server user"
            print_info "Please set SERVICE_USER environment variable or use --user option"
            exit 1
        fi
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
    -p, --path PATH     Set application path (auto-detected from current directory)
    -u, --user USER     Set service user (auto-detected: www-data, nginx, etc.)
    -q, --queue QUEUE   Set queue name (default: default)
    --php-path PATH     Set PHP path (auto-detected)
    --skip-start        Don't start services (only create and enable)
    --skip-permissions  Don't set file permissions
    --status            Show service status and exit

Auto-Detection:
    The script automatically detects:
    - Laravel root directory (from current directory)
    - PHP executable path
    - Web server user (www-data, nginx, apache, etc.)

Configuration Variables (can be overridden via environment variables):
    APP_PATH: Auto-detected or use --path option
    PHP_PATH: Auto-detected or use --php-path option  
    SERVICE_USER: Auto-detected or use --user option
    SERVICE_GROUP: Same as SERVICE_USER
    QUEUE_CONNECTION: default
    QUEUE_TRIES: 3
    QUEUE_TIMEOUT: 90
    QUEUE_SLEEP: 3
    QUEUE_MAX_JOBS: 1000
    QUEUE_MAX_TIME: 3600

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
            # Resolve to absolute path
            APP_PATH=$(cd "$APP_PATH" 2>/dev/null && pwd || echo "$APP_PATH")
            shift 2
            ;;
        -u|--user)
            SERVICE_USER="$2"
            SERVICE_GROUP="$2"  # Also set group to same as user
            shift 2
            ;;
        -q|--queue)
            QUEUE_CONNECTION="$2"
            shift 2
            ;;
        --php-path)
            PHP_PATH="$2"
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
    
    # Resolve absolute paths
    APP_PATH=$(cd "$APP_PATH" && pwd)
    
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

