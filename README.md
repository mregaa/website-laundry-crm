# Website Laundry CRM

Aplikasi web berbasis Laravel untuk mengelola operasional bisnis laundry secara menyeluruh, mencakup manajemen order, customer, keuangan, dan program loyalitas.

## 📋 Gambaran Aplikasi

Aplikasi ini dirancang untuk membantu pemilik dan staff laundry dalam mengelola operasional bisnis sehari-hari, mulai dari pencatatan order, tracking status laundry, notifikasi WhatsApp ke customer, pencatatan keuangan (pemasukan & pengeluaran), hingga program loyalitas pelanggan.

**Tampilan Website:**
- **Desktop**: Akses penuh ke semua fitur termasuk laporan keuangan dan manajemen data master
- **Mobile**: Fokus pada operasional harian seperti input order, update status, pembayaran, dan pendataan customer baru

## ✨ Fitur Utama

### 1. Dashboard Statistik
**Statistik Hari Ini**
- **Deadline Hari Ini**: jumlah order dengan `delivery_date` hari ini
- **Sedang Diproses**: jumlah order berstatus `in_progress`
- **Siap Diambil**: jumlah order berstatus `ready`
- **Pendapatan Hari Ini**: total pemasukan (berdasarkan data transaksi pemasukan)

**Statistik Periode (dengan filter tanggal)**
Dashboard mendukung filter rentang tanggal `start_date` s/d `end_date`, dan menampilkan:
- **Total Order** (pada periode)
- **Pendapatan** (total pemasukan pada periode)
- **Pengeluaran** (total biaya pada periode)
- **Keuntungan** (Pendapatan − Pengeluaran)

- **Ringkasan Status Order**: jumlah order per status (in_progress, ready, completed, cancelled)
- **Recent Orders**: daftar order terbaru
- **Top Customers**: ringkasan pelanggan dengan nilai transaksi tertinggi pada periode (jika tersedia)

### 2. Manajemen Order Laundry
- **Create Order**: Buat order baru dengan multiple service items
- **Order Tracking**: Pencarian order berdasarkan nomor order atau nama customer
- **Status Order**:
  - **In Progress**: Order sedang dikerjakan (mencakup proses cuci, keringkan, setrika, dll)
  - **Ready**: Order siap diambil customer ✅
  - **Completed**: Order telah diambil dan selesai
  - **Cancelled**: Order dibatalkan
- **WhatsApp Click-to-Chat**: 
  - Tombol WhatsApp otomatis muncul ketika status = **ready**
  - Mengirim template pesan "Pesanan Anda sudah SELESAI dan SIAP DIAMBIL"
  - Format: No. Pesanan, Nama, Total (status pembayaran), Status
  - **Tidak menggunakan WhatsApp Business API**, hanya link wa.me
- **Payment Tracking**: 
  - Status pembayaran: pending, partial, paid, refunded
  - Tambah pembayaran dengan berbagai metode (Cash, Transfer, E-wallet)
  - Histori pembayaran per order
- **Order Status History**: Catat setiap perubahan status dengan timestamp dan user
- **Express Service**: Dukungan layanan kilat sesuai konfigurasi layanan
- **Auto-Generated Order Number**: Format ORD-YYYYMMDD-XXXX

### 3. Manajemen Customer & Loyalty
- **Customer Profiles**: Data lengkap customer (nama, telepon, email, alamat, tanggal lahir)
- **Loyalty Program**:
  - Akumulasi poin otomatis dari transaksi
  - Membership tier: Bronze, Silver, Gold, Platinum
  - Loyalty Transactions: Riwayat poin earned, redeemed, expired
  - Customer Rewards: Redeem poin untuk diskon atau hadiah
- **Customer Analytics**: Total spending, jumlah order, poin loyalty

### 4. Manajemen Service (Price List)
- **Service Catalog**: Daftar layanan laundry (Cuci Kering, Cuci Setrika, Setrika Saja, dll)
- **Flexible Pricing**: Harga berdasarkan satuan (kg, item, bundle)
- **Estimated Time**: Durasi estimasi pengerjaan (dalam menit)
- **Service Status**: Aktif/Non-aktif untuk ditampilkan di order form

### 5. Manajemen Keuangan (Financial)
- **Transactions**: 
  - **Income**: Transaksi pemasukan (otomatis dari pembayaran order)
  - **Expense**: Transaksi pengeluaran
  - Kategori: Order Payment, Salary, Utilities, Supplies, Maintenance, Marketing, Rent, Equipment, Transportation, Other
  - Auto-Generated Transaction Number: TRX-YYYYMMDD-XXXX
- **Expenses**: 
  - Input pengeluaran operasional (gaji, PLN, PDAM, supplies, dll)
  - Kategori lengkap sesuai kebutuhan bisnis
  - Expense Number: EXP-YYYYMMDD-XXXX
- **Financial Reports**:
  - Filter berdasarkan periode (tanggal mulai - tanggal akhir)
  - Revenue, Expenses, Profit/Loss, Pending Payments
  - Export ke Excel

### 6. Program Rewards (Loyalty)
- **Reward Catalog**: Daftar reward yang bisa ditukar dengan poin
- **Points Required**: Minimal poin untuk redeem
- **Reward Value**: Nilai diskon dalam rupiah
- **Redeem Reward**: Customer menukar poin dengan reward
- **Reward Status**: Available, Redeemed, Expired

## 🔄 Flow Operasional (End-to-End)

### Alur Kerja Standar:

1. **Customer datang / telepon / chat** → Staff catat data customer (jika baru)

2. **Buat Order Baru** (`orders.create`)
   - Pilih customer
   - Tambah service items (jenis layanan + quantity)
   - Sistem hitung subtotal, tax (10%), diskon (opsional), total
   - Tentukan pickup date & delivery date
   - Status awal: **In Progress**

3. **Proses Laundry** (Status: **In Progress**)
   - Laundry dikerjakan melalui tahapan: terima → sortir → cuci → keringkan → setrika → lipat
   - Staff tidak perlu update status detail, cukup tetap di **In Progress** sampai selesai

4. **Order Siap Diambil** (Status: **Ready**)
   - Setelah laundry selesai dikemas, staff update status ke **Ready**
   - **Tombol WhatsApp muncul otomatis** di halaman detail order
   - Staff klik tombol WhatsApp → Template pesan otomatis terisi
   - Kirim pesan WA ke customer: "Pesanan Anda sudah SELESAI dan SIAP DIAMBIL"

5. **Pembayaran**
   - Customer datang ambil laundry
   - Staff input/cek pembayaran (`orders.add-payment`)
   - Sistem otomatis membuat **Transaction** dengan type=income ketika payment dibuat
   - Payment status: pending → partial → **paid**

6. **Order Selesai** (Status: **Completed**)
   - Setelah customer ambil laundry dan bayar lunas, update status ke **Completed**
   - Order selesai dan tercatat dalam histori

### Catatan Penting:
- **Data statistik dashboard** berasal dari tabel `transactions` (income/expense) dan filter tanggal
- **Revenue** dihitung dari transaksi type=income (bukan langsung dari total order)
- **WhatsApp** bersifat semi-auto: template otomatis, tapi staff yang klik send via wa.me (tanpa API)

## 💻 Instalasi & Setup (Local Development)

### Prerequisites
- PHP 8.2+ 
- Composer
- MySQL 5.7+ / MariaDB 10.3+
- Web server (Apache/Nginx) atau gunakan built-in PHP server

### Langkah Instalasi:

#### 1. Clone Repository (jika dari Git)
```bash
git clone https://github.com/mregaa/website-laundry-crm.git
cd website-laundry-crm
```

#### 2. Install Dependencies
```bash
composer install
```

#### 3. Setup Environment
```bash
# Copy file .env
copy .env.example .env
# atau di Linux/Mac
cp .env.example .env
```

Edit file `.env` dan sesuaikan database configuration:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laundry_crm
DB_USERNAME=root
DB_PASSWORD=
```

#### 4. Generate Application Key
```bash
php artisan key:generate
```

#### 5. Buat Database
Buat database MySQL dengan nama sesuai `DB_DATABASE` di `.env`:
```sql
CREATE DATABASE laundry_crm;
```

#### 6. Run Migration & Seeder
```bash
# Jalankan migration dan seeder sekaligus
php artisan migrate:fresh --seed
```

**Seeder `RealCaseIndonesiaSeeder` akan generate:**
- 2 Users (admin dan kasir)
- 12 Customers dengan nama Indonesia
- 9 Services (Cuci Kering, Cuci Setrika, dll)
- 35 Orders dengan berbagai status
- 92 Order Items
- 32 Payments
- 40 Transactions (income & expense)
- 12 Expenses (PLN, PDAM, Gaji, dll)
- 16 Loyalty Transactions
- 10 Customer Rewards
- 6 Rewards
- 78 Order Status Histories

**Data Login Default:**
- Email: `admin@laundry.id` | Password: `password`
- Email: `kasir@laundry.id` | Password: `password`

#### 7. (Opsional) Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## 🚀 Menjalankan Aplikasi

### Untuk Development di Laptop/PC:

```bash
php artisan serve
```

Akses di browser: `http://127.0.0.1:8000` atau `http://localhost:8000`

### Untuk Akses dari HP (Mobile Testing):

Agar aplikasi bisa diakses dari HP yang terhubung ke Wi-Fi yang sama dengan laptop:

#### 1. Jalankan server dengan IP 0.0.0.0
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

#### 2. Cari IP Address Laptop
**Windows:**
```bash
ipconfig
```
Cari **IPv4 Address** di adapter Wi-Fi (contoh: `192.168.1.5`)

**Linux/Mac:**
```bash
ifconfig
# atau
ip addr show
```

#### 3. Akses dari HP
Buka browser di HP, ketik:
```
http://192.168.1.5:8000
```
*(Ganti `192.168.1.5` dengan IP laptop Anda)*

#### 4. Troubleshooting Firewall (jika tidak bisa akses)
**Windows:**
- Buka **Windows Defender Firewall** → Advanced Settings
- Inbound Rules → New Rule → Port → TCP → 8000 → Allow
- Atau temporary: `netsh advfirewall set allprofiles state off` (tidak direkomendasikan untuk production)

**Linux:**
```bash
sudo ufw allow 8000/tcp
```

## 📁 Struktur Folder Penting

```
website-laundry-crm/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── DashboardController.php      # Dashboard & statistik
│   │       ├── OrderController.php          # CRUD Order, update status, add payment
│   │       ├── CustomerController.php       # CRUD Customer, loyalty management
│   │       ├── ServiceController.php        # CRUD Service/Price List
│   │       ├── FinancialController.php      # Transactions, Expenses, Reports
│   │       └── RewardController.php         # CRUD Rewards, redeem
│   └── Models/
│       ├── Order.php                        # Model Order + WhatsApp methods
│       ├── Customer.php                     # Model Customer + loyalty
│       ├── Service.php
│       ├── Payment.php
│       ├── Transaction.php                  # Income/Expense
│       ├── Expense.php
│       ├── LoyaltyTransaction.php
│       ├── Reward.php
│       └── CustomerReward.php
├── database/
│   ├── migrations/                          # Database schema
│   └── seeders/
│       ├── DatabaseSeeder.php               # Entry point seeder
│       └── RealCaseIndonesiaSeeder.php      # Seeder data Indonesia lengkap
├── resources/
│   └── views/
│       ├── dashboard.blade.php              # Halaman dashboard
│       ├── orders/                          # Views untuk orders
│       │   ├── index.blade.php              # List orders
│       │   ├── show.blade.php               # Detail order (ada tombol WA)
│       │   ├── create.blade.php
│       │   └── edit.blade.php
│       ├── customers/                       # Views untuk customers
│       ├── services/                        # Views untuk services
│       ├── financial/                       # Views untuk financial
│       ├── rewards/                         # Views untuk rewards
│       ├── layouts/
│       │   └── app.blade.php                # Main layout
│       └── components/
│           └── bottom-nav.blade.php         # Bottom navigation mobile
├── routes/
│   └── web.php                              # Routing aplikasi
├── .env                                     # Environment config
└── README.md
```

## 🔧 Konvensi & Catatan Teknis

### Data Statistik Dashboard
- Revenue & Expenses dihitung dari tabel **`transactions`** (bukan dari `orders.total`)
- Filter berdasarkan `transaction_date` sesuai periode (today, this month, last 30 days)
- Profit = Revenue (income) - Expenses (expense)

### WhatsApp Integration
- **Tidak menggunakan WhatsApp Business API** (no webhook, no automation)
- Menggunakan **wa.me link** (WhatsApp Click-to-Chat)
- Template pesan otomatis generate di `Order.php` method `whatsappMessage()`
- Format nomor: Indonesia (62xxx), otomatis convert dari 08xx
- Button WhatsApp hanya muncul ketika `status === 'ready'`

### Order Status Lifecycle
- **In Progress**: Order baru masuk dan sedang dikerjakan
- **Ready**: Order selesai dan siap diambil (trigger WhatsApp button)
- **Completed**: Order telah diambil customer
- **Cancelled**: Order dibatalkan

### Payment & Transaction Flow
1. Order dibuat → status pembayaran: **pending**
2. Staff add payment → status: **partial** atau **paid**
3. Saat payment dibuat → otomatis create **Transaction** dengan type=**income**
4. Transaksi ini yang dipakai untuk hitung revenue di dashboard

## 🐛 Troubleshooting

### Dashboard Kosong / Statistik 0
**Penyebab:** Tidak ada data transaksi di bulan/periode aktif
**Solusi:**
```bash
php artisan migrate:fresh --seed
```
Seeder akan generate data transaksi 30-60 hari terakhir.

### WhatsApp Template Karakter Aneh
**Penyebab:** Encoding UTF-8 atau emoji tidak support
**Solusi:** Template di `Order.php` sudah menggunakan plain text tanpa emoji. Pastikan tidak ada karakter special yang corrupt.

### Error "Column not found" saat migrate
**Penyebab:** Migration atau seeder tidak sinkron dengan model
**Solusi:**
```bash
php artisan migrate:fresh --seed
```

### Port 8000 Already in Use
**Solusi:** Gunakan port lain
```bash
php artisan serve --port=8001
```

### Tidak Bisa Akses dari HP
**Penyebab:** Firewall blocking atau IP salah
**Solusi:**
1. Pastikan HP dan laptop terhubung ke Wi-Fi yang sama
2. Cek IP laptop dengan `ipconfig` (Windows) atau `ifconfig` (Linux/Mac)
3. Allow port 8000 di firewall
4. Test akses: `http://IP-LAPTOP:8000`

## 📱 Mobile Responsiveness

Aplikasi ini **fully responsive** dengan bottom navigation untuk mobile view:
- **Dashboard**: Statistik dan overview
- **Orderan**: Daftar semua order
- **Proses**: Filter order status in_progress
- **Siap Ambil**: Filter order status ready
- **Pelanggan**: Daftar customer

Layout otomatis menyesuaikan untuk tampilan desktop dan mobile.

## 🔐 Security Notes

- CSRF protection enabled untuk semua POST requests
- SQL Injection protection via Eloquent ORM
- **Catatan:** Aplikasi ini untuk internal staff, kontrol akses masih bersifat sederhana dan dapat dikembangkan lebih lanjut.
- Untuk production: Implementasikan authentication middleware dan authorization policies

## 📄 License

Project ini dibuat untuk keperluan capstone/akademik.

**Developed with ❤️ from DeBeDe Team**