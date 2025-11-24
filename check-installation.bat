@echo off
REM LaundryPro CRM - Installation Verification Script (Windows)
REM This script checks if everything is properly installed

echo ==================================
echo LaundryPro CRM - Installation Check
echo ==================================
echo.

REM Check PHP version
echo Checking PHP version...
php -r "echo 'PHP ' . PHP_VERSION . PHP_EOL;"
echo.

REM Check Composer
echo Checking Composer...
composer --version
echo.

REM Check if .env exists
echo Checking .env file...
if exist .env (
    echo [OK] .env file found
) else (
    echo [!] .env file missing - Run: copy .env.example .env
)
echo.

REM Check if APP_KEY is set
echo Checking APP_KEY...
findstr /C:"APP_KEY=" .env | findstr /V /C:"APP_KEY=$" > nul
if %errorlevel% equ 0 (
    echo [OK] APP_KEY is set
) else (
    echo [!] APP_KEY not set - Run: php artisan key:generate
)
echo.

REM Check vendor directory
echo Checking dependencies...
if exist vendor (
    echo [OK] Dependencies installed
) else (
    echo [!] Dependencies missing - Run: composer install
)
echo.

REM Check database file (SQLite)
echo Checking database...
if exist database\database.sqlite (
    echo [OK] database.sqlite exists
) else (
    echo [!] database.sqlite missing - Run: type nul ^> database\database.sqlite
)
echo.

REM Check storage directory
echo Checking storage directory...
if exist storage (
    echo [OK] Storage directory exists
) else (
    echo [!] Storage directory missing
)
echo.

echo ==================================
echo Check Complete!
echo.
echo Next steps:
echo 1. If any items are missing, follow the suggestions above
echo 2. Run: php artisan migrate
echo 3. Run: php artisan db:seed
echo 4. Run: php artisan serve
echo 5. Visit http://localhost:8000 in your browser
echo.
echo For detailed instructions, see QUICKSTART.md
echo.

pause
