# LaundryPro CRM System

A comprehensive web application built with Laravel for managing laundry business operations, including CRM, order management, financial tracking, and inventory management.

## Features

### 1. Customer Relationship Management (CRM)
- **Customer Management**: Add, edit, view, and delete customer records
- **Customer Profiles**: Store name, contact details, address, birthdate
- **Loyalty Program**: 
  - Automatic points accumulation (1 point per $10 spent)
  - Tiered membership system (Bronze, Silver, Gold, Platinum)
  - Point redemption for rewards
  - Transaction history tracking
- **Customer Analytics**: Total orders, spending history, pending orders

### 2. Laundry Order Management
- **Order Creation**: Create orders with multiple service items
- **Order Tracking**: Real-time status updates through workflow stages:
  - Received → Sorting → Washing → Drying → Ironing → Folding → Ready → Out for Delivery → Completed
- **Status History**: Track all status changes with timestamps
- **Express Service**: Option for priority processing
- **Payment Tracking**: 
  - Multiple payment methods (Cash, Card, Bank Transfer, E-wallet)
  - Partial payment support
  - Payment history
- **Order Search**: Search by order number or customer name
- **Automatic Numbering**: Auto-generated order numbers (ORD-YYYYMMDD-XXXX)

### 3. Financial Management
- **Transaction Tracking**: 
  - Automatic transaction creation for order payments
  - Expense recording with categories
  - Transaction categories (Order Payment, Salary, Utilities, Supplies, etc.)
- **Financial Reports**:
  - Daily, weekly, monthly revenue reports
  - Expense breakdown by category
  - Profit/loss calculations
  - Revenue trends visualization
- **Payment Management**:
  - Record partial and full payments
  - Track payment methods
  - Outstanding balance tracking

### 4. Service Management
- **Service Catalog**: Define laundry services with pricing
- **Flexible Pricing**: Price per kg, piece, item, or load
- **Service Status**: Activate/deactivate services
- **Service Types**: 
  - Wash & Fold
  - Wash & Iron
  - Dry Cleaning
  - Ironing Only
  - Comforter Cleaning
  - Curtain Cleaning

### 5. Inventory Management
- **Stock Tracking**: Monitor inventory levels for supplies
- **Low Stock Alerts**: Automatic alerts when stock reaches reorder level
- **Inventory Categories**:
  - Detergent
  - Fabric Softener
  - Bleach
  - Starch
  - Hangers
  - Bags
  - Other supplies
- **Stock Adjustments**: Record stock in, stock out, and adjustments
- **Transaction History**: Track all inventory movements
- **SKU Management**: Unique SKU codes for each item

### 6. Rewards System
- **Reward Management**: Create and manage reward programs
- **Point-Based Redemption**: Customers redeem points for rewards
- **Flexible Rewards**: Percentage or fixed amount discounts
- **Reward Expiration**: Set expiration dates for redeemed rewards
- **Usage Tracking**: Track when rewards are used

### 7. Dashboard & Analytics
- **Real-Time Statistics**:
  - Today's orders, revenue, new customers
  - Monthly totals and profit
  - Order status distribution
- **Visual Analytics**:
  - Revenue trends (7-day chart)
  - Top customers ranking
  - Low stock items alert
  - Overdue orders warning
- **Quick Actions**: Access to recent orders and pending payments

## Technology Stack

- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Database**: MySQL/PostgreSQL/SQLite
- **Frontend**: 
  - Blade Templates
  - Tailwind CSS (via CDN)
  - Font Awesome Icons
  - Vanilla JavaScript
- **Authentication**: Laravel built-in authentication (expandable)

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js & NPM (optional, for asset compilation)

### Step 1: Clone or Navigate to Project
```bash
cd "Website Laundry/laundry-crm"
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Environment Configuration
```bash
cp .env.example .env
```

Edit `.env` file and configure your database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laundry_crm
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

For development, you can use SQLite:
```env
DB_CONNECTION=sqlite
# DB_HOST, DB_PORT, DB_DATABASE, etc. can be commented out
```

### Step 4: Generate Application Key
```bash
php artisan key:generate
```

### Step 5: Run Migrations
```bash
php artisan migrate
```

### Step 6: Seed Database (Optional)
```bash
php artisan db:seed
```

This will create:
- 6 sample services
- 3 reward programs
- 6 inventory items
- 5 sample customers
- 1 sample order

### Step 7: Create Storage Link
```bash
php artisan storage:link
```

### Step 8: Start Development Server
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Database Schema

### Core Tables
- `customers` - Customer information and loyalty data
- `orders` - Order records with status and payment tracking
- `order_items` - Individual services within orders
- `services` - Available laundry services
- `order_status_histories` - Status change tracking
- `payments` - Payment records for orders
- `transactions` - All financial transactions
- `expenses` - Business expenses
- `loyalty_transactions` - Loyalty point movements
- `rewards` - Available rewards
- `customer_rewards` - Redeemed rewards
- `inventory_items` - Inventory stock items
- `inventory_transactions` - Stock movements

## Usage Guide

### Managing Customers
1. Navigate to **Customers** menu
2. Click **Add Customer** to create new customer
3. Fill in required information (Name, Phone)
4. Optional: Add email, address, birthdate
5. View customer details to see order history and loyalty points

### Creating Orders
1. Navigate to **Orders** → **Create Order**
2. Select customer from dropdown
3. Add order items (service + quantity)
4. Set pickup and delivery dates
5. Add special instructions if needed
6. Click **Create Order**

### Tracking Orders
1. Navigate to **Orders**
2. Click on order number to view details
3. Update status using the status dropdown
4. Add payments as received
5. View complete status history

### Managing Inventory
1. Navigate to **Inventory**
2. Monitor current stock levels
3. Click **Adjust Stock** to add or remove stock
4. View transaction history for each item
5. Dashboard shows low stock alerts automatically

### Financial Reports
1. Navigate to **Financial**
2. Select date range
3. View income, expenses, and profit
4. Generate detailed reports
5. Export data for accounting purposes

### Rewards Management
1. Navigate to **Rewards**
2. Create new reward programs
3. Set points required and discount amount
4. Customers can redeem in their profile

## Testing

### Run Unit Tests
```bash
php artisan test
```

### Run Specific Test
```bash
php artisan test --filter CustomerTest
```

## Deployment

### Production Checklist
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Generate new application key
4. Configure production database
5. Run migrations: `php artisan migrate --force`
6. Optimize application:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
7. Set appropriate file permissions
8. Configure web server (Apache/Nginx)

### Environment Variables for Production
```env
APP_NAME="LaundryPro"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### Web Server Configuration

#### Nginx Example
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/laundry-crm/public;

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
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache Example
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /path/to/laundry-crm/public

    <Directory /path/to/laundry-crm/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/laundry-error.log
    CustomLog ${APACHE_LOG_DIR}/laundry-access.log combined
</VirtualHost>
```

## API Endpoints (Future Enhancement)

The application is structured to easily add RESTful API endpoints:
- `/api/customers` - Customer CRUD
- `/api/orders` - Order management
- `/api/services` - Service catalog
- `/api/inventory` - Inventory tracking

## Security Features

- CSRF protection on all forms
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade templating
- Password hashing (if authentication added)
- Input validation on all forms
- Soft deletes for data recovery

## Performance Optimization

- Database indexing on frequently queried columns
- Eager loading to prevent N+1 queries
- Query optimization with proper relationships
- Caching support (ready to implement)

## Customization

### Adding New Order Status
Edit `Order` model and migration:
```php
'status' => 'required|in:received,sorting,washing,drying,ironing,folding,ready,out_for_delivery,completed,cancelled,your_new_status'
```

### Adding New Payment Method
Edit `Transaction` and `Payment` models:
```php
'payment_method' => 'required|in:cash,card,bank_transfer,e-wallet,your_new_method'
```

### Customizing Loyalty Points
Edit `Customer` model's `addLoyaltyPoints` method:
```php
$points = floor($order->total / 10); // Change ratio here
```

## Troubleshooting

### Database Connection Error
- Verify database credentials in `.env`
- Ensure database exists
- Check database server is running

### Permission Errors
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Composer Install Fails
```bash
composer install --ignore-platform-reqs
```

### Migration Errors
```bash
php artisan migrate:fresh --seed
```

## Support & Contribution

### Reporting Issues
Please report issues with:
- Laravel version
- PHP version
- Error message
- Steps to reproduce

### Future Enhancements
- SMS notifications for order updates
- Email receipts and invoices
- Barcode scanning for inventory
- Mobile app integration
- Advanced reporting with charts
- Multi-location support
- Employee management
- Customer portal
- Automated billing

## License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Credits

Developed as a comprehensive CRM and management solution for small to medium laundry businesses.

**Technologies Used:**
- Laravel Framework
- Tailwind CSS
- Font Awesome
- MySQL/PostgreSQL

---

For questions or support, please refer to the Laravel documentation at https://laravel.com/docs
