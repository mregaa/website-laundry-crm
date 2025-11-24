# LaundryPro CRM - Quick Start Guide

Get your LaundryPro CRM system up and running in 5 minutes!

## Prerequisites

Before you begin, ensure you have:
- PHP 8.2 or higher installed
- Composer installed
- A web browser

## Quick Installation (Development)

### Step 1: Navigate to Project Directory
```bash
cd "c:\Users\zlire\OneDrive\Documents\Kulyeah\SEM-7\Capstone\Website Laundry\laundry-crm"
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Configure Environment
```bash
# Copy the environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Setup Database
The default configuration uses SQLite (no setup required). The database file will be created automatically.

```bash
# Run migrations to create tables
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### Step 5: Create Storage Link
```bash
php artisan storage:link
```

### Step 6: Start the Application
```bash
php artisan serve
```

The application will be available at: **http://localhost:8000**

## Sample Data

After seeding, you'll have:
- **6 Services**: Wash & Fold, Wash & Iron, Dry Cleaning, etc.
- **5 Customers**: With various loyalty tiers
- **6 Inventory Items**: Detergent, Fabric Softener, etc.
- **3 Reward Programs**: Discount rewards
- **1 Sample Order**: To demonstrate the system

## Default Navigation

Visit these pages to explore the system:

| Page | URL | Description |
|------|-----|-------------|
| Dashboard | http://localhost:8000 | Main analytics dashboard |
| Customers | http://localhost:8000/customers | Customer management |
| Orders | http://localhost:8000/orders | Order management |
| Services | http://localhost:8000/services | Service catalog |
| Financial | http://localhost:8000/financial | Financial reports |
| Inventory | http://localhost:8000/inventory | Inventory management |
| Rewards | http://localhost:8000/rewards | Loyalty rewards |

## Common Tasks

### Create a New Order
1. Go to **Orders** → **Add Order**
2. Select a customer
3. Add service items with quantities
4. Set pickup/delivery dates
5. Click **Create Order**

### Add a Customer
1. Go to **Customers** → **Add Customer**
2. Enter name and phone (required)
3. Add email, address, birthdate (optional)
4. Click **Save Customer**

### Track Order Status
1. Go to **Orders**
2. Click on an order number
3. Use the status dropdown to update progress
4. Add payments as received

### Manage Inventory
1. Go to **Inventory**
2. Click **Adjust Stock** on any item
3. Add or remove quantity
4. Dashboard shows low stock alerts

### View Financial Reports
1. Go to **Financial**
2. Select date range
3. View income, expenses, and profit
4. Check revenue trends

## Troubleshooting

### Port Already in Use
If port 8000 is busy, try a different port:
```bash
php artisan serve --port=8080
```

### Database Error
If you see database errors, recreate the database:
```bash
php artisan migrate:fresh --seed
```

### Permission Errors
On Windows, usually not an issue. On Mac/Linux:
```bash
chmod -R 775 storage bootstrap/cache
```

### Clear Cache
If things seem broken, clear all caches:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Using MySQL Instead of SQLite

If you prefer MySQL:

1. Create a database:
```sql
CREATE DATABASE laundry_crm;
```

2. Update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laundry_crm
DB_USERNAME=root
DB_PASSWORD=your_password
```

3. Run migrations:
```bash
php artisan migrate:fresh --seed
```

## Next Steps

### Customize the System
- Add more services in **Services** menu
- Create reward programs in **Rewards**
- Add your inventory items in **Inventory**
- Configure tax rates in `.env` file

### Explore Features
- Customer loyalty point system
- Order status tracking
- Financial reporting
- Inventory alerts
- Express service option

### For Production
See `DEPLOYMENT.md` for production deployment instructions.

## Need Help?

- Check `README.md` for full documentation
- Review `DEPLOYMENT.md` for deployment guide
- Check Laravel docs: https://laravel.com/docs
- Look at example data created by seeder

## System Requirements

### Minimum
- PHP 8.2
- 512MB RAM
- 100MB disk space

### Recommended
- PHP 8.3
- 1GB RAM
- 500MB disk space
- MySQL 8.0

## Features Overview

✅ **CRM Module**
- Customer management
- Loyalty points system
- Tiered membership (Bronze, Silver, Gold, Platinum)
- Customer transaction history

✅ **Order Management**
- Complete order workflow
- Status tracking (Received → Completed)
- Multiple service items per order
- Payment tracking
- Express service option

✅ **Financial Management**
- Income/expense tracking
- Financial reports
- Payment methods
- Transaction history
- Profit calculations

✅ **Inventory Management**
- Stock level tracking
- Low stock alerts
- Multiple categories
- Transaction history

✅ **Rewards System**
- Point-based rewards
- Discount redemption
- Expiration tracking

✅ **Dashboard Analytics**
- Real-time statistics
- Revenue trends
- Top customers
- Order distribution
- Alert notifications

## Tips for First Use

1. **Start with Services**: Define your laundry services first
2. **Add Customers**: Enter your existing customers
3. **Create Orders**: Practice creating and managing orders
4. **Track Payments**: Use the payment system for accurate records
5. **Monitor Inventory**: Keep track of supplies
6. **Review Analytics**: Check dashboard for insights

## Sample Workflow

### Complete Order Process
1. Customer calls/visits → **Add/Find Customer**
2. Create order → **Orders → Create Order**
3. Add services → Select services and quantities
4. Receive items → Status: **Received**
5. Process → Update status through workflow
6. Ready for pickup → Status: **Ready**
7. Customer pays → **Add Payment**
8. Complete → Status: **Completed**
9. Loyalty points → Automatically awarded

## Important Notes

- Orders automatically generate unique order numbers
- Payment status updates automatically based on payments
- Loyalty points are awarded when orders are paid (1 point per $10)
- Membership tiers upgrade automatically based on points
- Low stock alerts appear on dashboard
- All timestamps are automatically recorded

## Keyboard Shortcuts

- No special shortcuts implemented
- Use browser navigation (Back, Forward)
- Forms can be submitted with Enter key

## Data Management

### Backup Your Data
```bash
# Backup database (SQLite)
copy database\database.sqlite database\database.backup.sqlite

# For MySQL
mysqldump -u root -p laundry_crm > backup.sql
```

### Reset Everything
```bash
php artisan migrate:fresh --seed
```

This will:
- Drop all tables
- Recreate tables
- Load sample data

---

**You're all set!** Start exploring the LaundryPro CRM system. 🎉

Visit http://localhost:8000 to begin!
