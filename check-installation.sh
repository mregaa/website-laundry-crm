#!/bin/bash

# LaundryPro CRM - Installation Verification Script
# This script checks if everything is properly installed

echo "=================================="
echo "LaundryPro CRM - Installation Check"
echo "=================================="
echo ""

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check PHP version
echo -n "Checking PHP version... "
PHP_VERSION=$(php -r "echo PHP_VERSION;")
PHP_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")
PHP_MINOR=$(php -r "echo PHP_MINOR_VERSION;")

if [ "$PHP_MAJOR" -ge 8 ] && [ "$PHP_MINOR" -ge 2 ]; then
    echo -e "${GREEN}✓${NC} PHP $PHP_VERSION"
else
    echo -e "${RED}✗${NC} PHP $PHP_VERSION (Requires PHP 8.2+)"
fi

# Check Composer
echo -n "Checking Composer... "
if command -v composer &> /dev/null; then
    COMPOSER_VERSION=$(composer --version | cut -d' ' -f3)
    echo -e "${GREEN}✓${NC} Composer $COMPOSER_VERSION"
else
    echo -e "${RED}✗${NC} Composer not found"
fi

# Check if .env exists
echo -n "Checking .env file... "
if [ -f .env ]; then
    echo -e "${GREEN}✓${NC} Found"
else
    echo -e "${YELLOW}!${NC} Missing (Run: cp .env.example .env)"
fi

# Check if APP_KEY is set
echo -n "Checking APP_KEY... "
if [ -f .env ]; then
    APP_KEY=$(grep "^APP_KEY=" .env | cut -d'=' -f2)
    if [ ! -z "$APP_KEY" ]; then
        echo -e "${GREEN}✓${NC} Set"
    else
        echo -e "${YELLOW}!${NC} Not set (Run: php artisan key:generate)"
    fi
else
    echo -e "${RED}✗${NC} .env file missing"
fi

# Check vendor directory
echo -n "Checking dependencies... "
if [ -d "vendor" ]; then
    echo -e "${GREEN}✓${NC} Installed"
else
    echo -e "${YELLOW}!${NC} Missing (Run: composer install)"
fi

# Check database file (SQLite)
echo -n "Checking database... "
DB_CONNECTION=$(grep "^DB_CONNECTION=" .env | cut -d'=' -f2)
if [ "$DB_CONNECTION" = "sqlite" ]; then
    if [ -f "database/database.sqlite" ]; then
        echo -e "${GREEN}✓${NC} database.sqlite exists"
    else
        echo -e "${YELLOW}!${NC} Missing (Run: touch database/database.sqlite)"
    fi
else
    echo -e "${YELLOW}!${NC} Using $DB_CONNECTION (ensure it's configured)"
fi

# Check storage permissions
echo -n "Checking storage permissions... "
if [ -w "storage" ]; then
    echo -e "${GREEN}✓${NC} Writable"
else
    echo -e "${RED}✗${NC} Not writable (Run: chmod -R 775 storage)"
fi

# Check if migrations are run
echo -n "Checking migrations... "
if php artisan migrate:status &> /dev/null; then
    MIGRATIONS=$(php artisan migrate:status | grep "Ran" | wc -l)
    if [ "$MIGRATIONS" -gt 0 ]; then
        echo -e "${GREEN}✓${NC} $MIGRATIONS migrations executed"
    else
        echo -e "${YELLOW}!${NC} No migrations run (Run: php artisan migrate)"
    fi
else
    echo -e "${YELLOW}!${NC} Cannot check (Database might not be configured)"
fi

# Check if server is running
echo -n "Checking if server is running... "
if curl -s http://localhost:8000 > /dev/null 2>&1; then
    echo -e "${GREEN}✓${NC} Server running on http://localhost:8000"
else
    echo -e "${YELLOW}!${NC} Server not running (Run: php artisan serve)"
fi

echo ""
echo "=================================="
echo "Check Complete!"
echo ""

# Summary
echo "Next steps:"
echo "1. If any items are missing, follow the suggestions above"
echo "2. Run 'php artisan migrate' if migrations not executed"
echo "3. Run 'php artisan db:seed' to load sample data"
echo "4. Run 'php artisan serve' to start the application"
echo "5. Visit http://localhost:8000 in your browser"
echo ""
echo "For detailed instructions, see QUICKSTART.md"
