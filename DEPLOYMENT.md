# LaundryPro CRM - Deployment Guide

This guide provides detailed instructions for deploying the LaundryPro CRM system to various production environments.

## Table of Contents
1. [Pre-Deployment Checklist](#pre-deployment-checklist)
2. [Shared Hosting Deployment](#shared-hosting-deployment)
3. [VPS/Cloud Deployment](#vpscloud-deployment)
4. [Docker Deployment](#docker-deployment)
5. [Laravel Forge Deployment](#laravel-forge-deployment)
6. [Post-Deployment Tasks](#post-deployment-tasks)
7. [Monitoring and Maintenance](#monitoring-and-maintenance)

## Pre-Deployment Checklist

### 1. Code Preparation
- [ ] All features tested locally
- [ ] Database migrations verified
- [ ] Seeders tested (if used)
- [ ] All dependencies in `composer.json`
- [ ] Environment variables documented
- [ ] Remove debug statements
- [ ] Code reviewed and optimized

### 2. Security Checklist
- [ ] `APP_DEBUG=false` in production
- [ ] Strong `APP_KEY` generated
- [ ] Secure database credentials
- [ ] HTTPS configured
- [ ] CSRF protection enabled
- [ ] File upload validation in place
- [ ] SQL injection prevention verified

### 3. Performance Optimization
- [ ] Config cached: `php artisan config:cache`
- [ ] Routes cached: `php artisan route:cache`
- [ ] Views cached: `php artisan view:cache`
- [ ] Database indexes reviewed
- [ ] N+1 query problems resolved

## Shared Hosting Deployment

### Step 1: Prepare Your Files
```bash
# On local machine
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 2: Upload Files
1. Upload all files except `.env` to server
2. Upload Laravel files to a directory above public_html (e.g., `/home/username/laravel`)
3. Move contents of `public` folder to `public_html`

### Step 3: Update Index File
Edit `public_html/index.php`:
```php
// Update these paths
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
```

### Step 4: Configure Environment
1. Create `.env` file in Laravel root directory
2. Copy contents from `.env.example`
3. Update database credentials
4. Set `APP_ENV=production`
5. Set `APP_DEBUG=false`
6. Generate app key: `php artisan key:generate`

### Step 5: Set Permissions
```bash
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs
```

### Step 6: Database Setup
```bash
# Via SSH or terminal
php artisan migrate --force
php artisan db:seed --force
```

## VPS/Cloud Deployment

### Prerequisites
- Ubuntu 22.04 LTS (or similar)
- Root or sudo access
- Domain name (optional)

### Step 1: Server Setup

#### Install Required Software
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Nginx
sudo apt install nginx -y

# Install PHP 8.2 and extensions
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd -y

# Install MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Step 2: Database Setup
```bash
sudo mysql -u root -p

CREATE DATABASE laundry_crm;
CREATE USER 'laundry_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON laundry_crm.* TO 'laundry_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Deploy Application
```bash
# Clone or upload application
cd /var/www
sudo git clone your-repository-url laundry-crm
cd laundry-crm

# Install dependencies
sudo composer install --optimize-autoloader --no-dev

# Set permissions
sudo chown -R www-data:www-data /var/www/laundry-crm
sudo chmod -R 755 /var/www/laundry-crm
sudo chmod -R 775 /var/www/laundry-crm/storage
sudo chmod -R 775 /var/www/laundry-crm/bootstrap/cache

# Configure environment
sudo cp .env.example .env
sudo nano .env  # Edit configuration
sudo php artisan key:generate
sudo php artisan storage:link

# Run migrations
sudo php artisan migrate --force
sudo php artisan db:seed --force

# Optimize
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache
```

### Step 4: Configure Nginx
```bash
sudo nano /etc/nginx/sites-available/laundry-crm
```

Add configuration:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/laundry-crm/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/laundry-crm /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Step 5: SSL Certificate (Let's Encrypt)
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

## Docker Deployment

### Dockerfile
Create `Dockerfile` in project root:
```dockerfile
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nginx

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

EXPOSE 9000
CMD ["php-fpm"]
```

### docker-compose.yml
```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: laundry-app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - laundry-network

  nginx:
    image: nginx:alpine
    container_name: laundry-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www
      - ./docker/nginx:/etc/nginx/conf.d
    networks:
      - laundry-network

  db:
    image: mysql:8.0
    container_name: laundry-db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: laundry_crm
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_USER: laundry_user
      MYSQL_PASSWORD: laundry_password
    volumes:
      - dbdata:/var/lib/mysql
    networks:
      - laundry-network

networks:
  laundry-network:
    driver: bridge

volumes:
  dbdata:
```

Deploy:
```bash
docker-compose up -d
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force
```

## Laravel Forge Deployment

### Step 1: Connect Server
1. Log in to Laravel Forge
2. Click "Servers" → "Create Server"
3. Choose cloud provider (DigitalOcean, AWS, etc.)
4. Select server size and region

### Step 2: Create Site
1. Go to "Sites" → "New Site"
2. Enter domain name
3. Select project type: Laravel
4. Set root directory: `/public`

### Step 3: Deploy Repository
1. Connect Git repository
2. Set deployment branch (main/master)
3. Configure deployment script:

```bash
cd /home/forge/your-domain.com
git pull origin main
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

### Step 4: Configure Environment
1. Go to "Environment" tab
2. Edit `.env` variables
3. Click "Save"

### Step 5: Enable Quick Deploy
1. Enable "Quick Deploy" for automatic deployments
2. Push to main branch triggers deployment

## Post-Deployment Tasks

### 1. Verify Installation
```bash
# Check application status
php artisan about

# Test database connection
php artisan migrate:status

# Verify routes
php artisan route:list
```

### 2. Configure Scheduler (For Cron Jobs)
Add to crontab:
```bash
* * * * * cd /path/to/laundry-crm && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Configure Queue Worker
```bash
# Create systemd service
sudo nano /etc/systemd/system/laundry-queue.service
```

Add:
```ini
[Unit]
Description=Laundry CRM Queue Worker

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/laundry-crm/artisan queue:work --sleep=3 --tries=3

[Install]
WantedBy=multi-user.target
```

Enable:
```bash
sudo systemctl enable laundry-queue
sudo systemctl start laundry-queue
```

### 4. Set Up Backups

#### Database Backup Script
```bash
#!/bin/bash
# backup-db.sh
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/laundry-crm"
mkdir -p $BACKUP_DIR

mysqldump -u laundry_user -p'password' laundry_crm > $BACKUP_DIR/db_backup_$DATE.sql
gzip $BACKUP_DIR/db_backup_$DATE.sql

# Keep only last 7 days
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +7 -delete
```

Add to crontab (daily at 2 AM):
```bash
0 2 * * * /path/to/backup-db.sh
```

## Monitoring and Maintenance

### 1. Application Monitoring
- Use Laravel Telescope for debugging (dev only)
- Set up error logging service (Sentry, Bugsnag)
- Monitor server resources (CPU, RAM, Disk)

### 2. Performance Monitoring
```bash
# Check logs
tail -f storage/logs/laravel.log

# Monitor PHP-FPM
sudo systemctl status php8.2-fpm

# Monitor Nginx
sudo systemctl status nginx

# Check MySQL
sudo systemctl status mysql
```

### 3. Regular Maintenance
```bash
# Clear old logs
php artisan log:clear

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Then rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Security Updates
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Update Composer dependencies
composer update --no-dev

# Check for security vulnerabilities
composer audit
```

## Troubleshooting

### Common Issues

#### 500 Internal Server Error
- Check storage permissions: `chmod -R 775 storage`
- Check `.env` configuration
- Check logs: `storage/logs/laravel.log`

#### Database Connection Error
- Verify database credentials in `.env`
- Check MySQL is running: `sudo systemctl status mysql`
- Test connection: `php artisan migrate:status`

#### Permission Denied
```bash
sudo chown -R www-data:www-data /var/www/laundry-crm
sudo chmod -R 755 /var/www/laundry-crm
sudo chmod -R 775 storage bootstrap/cache
```

#### Route Not Found
```bash
php artisan route:clear
php artisan route:cache
```

## Rollback Procedure

If deployment fails:
```bash
# Revert to previous version
git checkout previous-commit-hash

# Rollback migrations
php artisan migrate:rollback

# Clear caches
php artisan cache:clear
php artisan config:clear

# Restore database backup
mysql -u laundry_user -p laundry_crm < backup.sql
```

## Support

For deployment issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server logs: `/var/log/nginx/error.log`
3. Check PHP-FPM logs: `/var/log/php8.2-fpm.log`
4. Review Laravel documentation: https://laravel.com/docs

---

**Remember**: Always test deployment process in staging environment before production!
