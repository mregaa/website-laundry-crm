# PROSES BISNIS TARGET
## Aplikasi Laundry CRM Berbasis Web

---

## KONTEKS BISNIS

**Profil Usaha:**
- Nama: Outlet Laundry Skala Kecil
- Kapasitas: 1–3 mesin cuci
- Sumber Daya Manusia: 2 staff operasional + 1 owner
- Jam Operasional: Senin–Sabtu (08.00–18.00), Minggu (08.00–17.00)
- Jenis Layanan: Mayoritas kiloan (kg), sebagian satuan (item/bundle)
- Sistem: Website Laundry CRM berbasis Laravel

---

## A. PROSES BISNIS TARGET (NARASI)

Proses bisnis target menggambarkan alur operasional laundry yang telah terintegrasi dengan sistem informasi Laundry CRM. Proses ini dirancang untuk meningkatkan efisiensi, akurasi pencatatan, dan kualitas layanan pelanggan.

### 1. PENDAFTARAN PELANGGAN BARU

**Aktor:** Staff  
**Kondisi Awal:** Pelanggan datang ke outlet untuk pertama kali  
**Aktivitas:**

1. Staff membuka menu **Customers** di sistem
2. Staff mengklik tombol **Add Customer**
3. Staff menginput data pelanggan:
   - Nama lengkap (wajib)
   - Nomor telepon (wajib, format 08xx atau +62)
   - Email (opsional)
   - Alamat lengkap (opsional)
   - Tanggal lahir (opsional)
4. Sistem menyimpan data pelanggan ke database
5. Sistem otomatis menginisialisasi:
   - Membership tier: Bronze (default)
   - Loyalty points: 0
6. Staff mendapatkan konfirmasi pelanggan berhasil terdaftar

**Output:** Data pelanggan tersimpan dan siap untuk membuat order

---

### 2. PEMBUATAN ORDER LAUNDRY

**Aktor:** Staff  
**Kondisi Awal:** Pelanggan (terdaftar) datang dengan cucian  
**Aktivitas:**

1. Staff menerima dan menghitung cucian pelanggan secara manual
2. Staff membuka menu **Orders** → **Create Order**
3. Staff memilih nama pelanggan dari dropdown
4. Staff menambahkan item layanan:
   - Pilih jenis layanan (contoh: Cuci Kering, Cuci Setrika, Setrika Saja)
   - Input quantity berdasarkan satuan layanan (kg/item/bundle)
   - Tambahkan catatan khusus per item jika diperlukan (opsional)
   - Staff dapat menambahkan multiple items dalam satu order
5. Staff menentukan jadwal:
   - Pickup date (tanggal terima) - default hari ini
   - Delivery date (estimasi selesai) - default 2 hari ke depan
6. Staff menambahkan instruksi khusus jika ada (contoh: "Pisahkan baju putih", "Jangan gunakan pewangi")
7. **Sistem melakukan kalkulasi otomatis:**
   - Subtotal = Σ(quantity × unit price setiap item)
   - Tax = 10% × Subtotal
   - Total = Subtotal + Tax - Discount (jika ada)
8. Staff dapat mengaplikasikan reward/diskon jika pelanggan memiliki poin loyalty cukup (opsional)
9. Staff mengklik **Create Order**
10. **Sistem otomatis:**
    - Generate nomor order (format: ORD-YYYYMMDD-XXXX)
    - Set status order: **In Progress**
    - Set payment status: **Pending**
    - Menyimpan order ke database
    - Membuat entry pertama di Order Status History
11. Sistem menampilkan konfirmasi order berhasil dibuat
12. Staff mencetak/mencatat nomor order untuk ditempelkan pada cucian

**Output:** Order tercatat dalam sistem dengan status In Progress

---

### 3. PROSES PENCUCIAN (OPERASIONAL FISIK)

**Aktor:** Staff Operasional  
**Kondisi Awal:** Order dengan status In Progress  
**Aktivitas:**

1. Staff mengambil cucian yang sudah diberi label nomor order
2. Staff melakukan proses pencucian secara manual melalui tahapan:
   - Penerimaan dan sortir (pisahkan warna, jenis kain)
   - Pencucian di mesin cuci
   - Pengeringan (spinner/dijemur)
   - Penyetrikaan (jika layanan cuci setrika)
   - Pelipatan dan pengemasan
3. Staff menyimpan cucian yang sudah selesai di area "Ready for Pickup"

**Catatan:**  
- Proses ini berjalan secara **manual** di lapangan
- **Tidak ada update status detail** di sistem (washing, drying, ironing)
- Status order tetap **In Progress** hingga semua cucian selesai dikemas

**Output:** Cucian selesai dan siap diambil pelanggan

---

### 4. UPDATE STATUS MENJADI READY (SIAP DIAMBIL)

**Aktor:** Staff  
**Kondisi Awal:** Cucian sudah selesai dan dikemas  
**Aktivitas:**

1. Staff membuka menu **Orders** → pilih order yang sudah selesai
2. Staff mengklik order untuk melihat detail
3. Staff mengklik tombol **Update Status**
4. Staff memilih status **Ready** (Siap Diambil)
5. Staff dapat menambahkan notes (opsional): "Cucian sudah siap, dapat diambil kapan saja"
6. Staff mengklik **Update**
7. **Sistem otomatis:**
   - Mengubah status order dari In Progress menjadi **Ready**
   - Mencatat perubahan status di Order Status History dengan timestamp
   - **Menampilkan tombol WhatsApp** di halaman detail order
8. Sistem menampilkan konfirmasi status berhasil diupdate

**Output:** Status order = Ready, tombol WhatsApp muncul

---

### 5. NOTIFIKASI VIA WHATSAPP

**Aktor:** Staff  
**Kondisi Awal:** Order dengan status Ready  
**Aktivitas:**

1. Staff melihat halaman detail order
2. Staff mengklik tombol **WhatsApp Customer** (warna hijau dengan icon WhatsApp)
3. **Sistem otomatis:**
   - Mengambil nomor telepon pelanggan dari database
   - Mengkonversi format nomor (08xx → 62xxx)
   - Generate template pesan WhatsApp:
     ```
     Halo [Nama Customer],
     
     Kami informasikan bahwa pesanan laundry Anda sudah SELESAI dan SIAP DIAMBIL
     
     No. Pesanan : ORD-20260102-0001
     Nama        : [Nama Customer]
     Total       : Rp 50.000 (belum dibayar/lunas)
     Status      : Siap diambil
     
     Silakan datang ke outlet kami untuk mengambil pesanan Anda.
     Terima kasih telah mempercayakan laundry Anda kepada kami
     ```
   - Membuka aplikasi WhatsApp Web (wa.me) dengan template pesan sudah terisi
4. Staff mereview pesan yang sudah ter-generate
5. Staff mengklik tombol **Send** di WhatsApp Web
6. Pesan terkirim ke customer

**Catatan:**  
- Notifikasi WhatsApp **hanya 1 kali** saat status Ready
- **Tidak menggunakan WhatsApp Business API** (no automation, no webhook)
- Menggunakan **wa.me link** (Click-to-Chat)

**Output:** Customer menerima notifikasi WhatsApp bahwa cucian sudah siap

---

### 6. PELANGGAN MENGAMBIL CUCIAN

**Aktor:** Staff dan Customer  
**Kondisi Awal:** Customer datang ke outlet  
**Aktivitas:**

1. Customer datang dan menyebutkan nomor order atau nama
2. Staff membuka menu **Orders** dan mencari order menggunakan:
   - Search by order number, atau
   - Search by customer name
3. Staff mengklik order untuk melihat detail
4. Staff mengambil cucian dari area penyimpanan berdasarkan nomor order
5. Staff menyerahkan cucian kepada customer
6. Staff melakukan verifikasi jumlah dan kondisi cucian bersama customer

**Output:** Cucian diserahkan ke customer

---

### 7. PENCATATAN PEMBAYARAN

**Aktor:** Staff  
**Kondisi Awal:** Customer siap membayar  
**Aktivitas:**

1. Staff membuka halaman detail order (jika belum terbuka)
2. Staff mengklik tombol **Add Payment**
3. Staff menginput data pembayaran:
   - Amount (jumlah dibayar) - maksimal sesuai sisa tagihan
   - Payment method: Cash, Card, Bank Transfer, atau E-wallet
   - Notes (opsional): contoh "Bayar via BCA transfer"
4. Staff mengklik **Submit Payment**
5. **Sistem otomatis melakukan:**
   - Membuat record Payment baru dengan payment_number unik
   - Update `paid_amount` pada order (akumulatif)
   - Update `payment_status`:
     - **Pending** → jika paid_amount = 0
     - **Partial** → jika 0 < paid_amount < total
     - **Paid** → jika paid_amount >= total
   - **Membuat Transaction otomatis** dengan:
     - Type: **Income**
     - Category: **Order Payment**
     - Amount: sesuai jumlah payment
     - Transaction date: saat ini
     - Transaction number: TRX-YYYYMMDD-XXXX
     - Description: "Payment received for order ORD-20260102-0001"
6. Sistem menampilkan konfirmasi pembayaran berhasil dicatat
7. Staff mencetak/mencatat bukti pembayaran (jika diperlukan)

**Catatan:**  
- Sistem mendukung **partial payment** (cicilan)
- Transaksi keuangan tercatat **otomatis** saat payment dibuat
- Data ini yang digunakan untuk **statistik dashboard**

**Output:** Pembayaran tercatat, transaction income dibuat otomatis

---

### 8. UPDATE STATUS MENJADI COMPLETED

**Aktor:** Staff  
**Kondisi Awal:** Customer sudah mengambil cucian dan membayar  
**Aktivitas:**

1. Staff membuka halaman detail order
2. Staff mengklik tombol **Update Status**
3. Staff memilih status **Completed** (Selesai)
4. Staff mengklik **Update**
5. **Sistem otomatis:**
   - Mengubah status order dari Ready menjadi **Completed**
   - Mencatat perubahan status di Order Status History
   - **Jika payment_status = Paid**, sistem memberikan **Loyalty Points**:
     - Base points: +10 poin per order
     - Weight points: +2 poin per kg (untuk layanan kiloan)
     - Total points = 10 + (2 × total_kg)
   - Membuat record LoyaltyTransaction dengan type **Earned**
   - Update `loyalty_points` customer (akumulatif)
6. Sistem menampilkan konfirmasi order selesai

**Output:** Order selesai, loyalty points diberikan (jika lunas)

---

### 9. MONITORING DASHBOARD (OWNER/ADMIN)

**Aktor:** Owner/Admin  
**Kondisi Awal:** Sistem berjalan dengan data transaksi  
**Aktivitas:**

1. Owner membuka halaman **Dashboard**
2. **Sistem menampilkan statistik real-time:**
   - **Hari Ini:**
     - Jumlah order
     - Revenue (dari transactions type=income)
     - Order completed
     - Pending payments
   - **Bulan Ini:**
     - Total order
     - Revenue
     - Expenses
     - Profit (revenue - expenses)
     - Customer baru
   - **30 Hari Terakhir:**
     - Order count
     - Revenue
     - Expenses
     - Profit
3. Owner melihat visualisasi:
   - Order Status Distribution (pie chart)
   - Recent Orders (tabel 10 order terbaru)
   - Pending Payments (order belum lunas)
   - Low Stock Alert (inventory menipis)
   - Top Customers bulan ini (5 teratas berdasarkan spending)
4. Owner dapat mengklik data untuk melihat detail

**Sumber Data:**  
- Revenue & Expenses dari tabel **Transactions** (bukan orders.total)
- Filter berdasarkan `transaction_date`

**Output:** Owner mendapat insight bisnis real-time

---

### 10. PENCATATAN PENGELUARAN (EXPENSE)

**Aktor:** Staff/Owner  
**Kondisi Awal:** Terjadi pengeluaran operasional  
**Aktivitas:**

1. Staff/Owner membuka menu **Financial** → **Expenses**
2. Staff/Owner mengklik tombol **Add Expense**
3. Staff/Owner menginput data pengeluaran:
   - Category: Salary, Utilities, Supplies, Maintenance, Marketing, Rent, Equipment, Transportation, Other
   - Amount: nominal pengeluaran
   - Vendor: nama supplier/penerima (opsional)
   - Description: keterangan detail (wajib)
   - Expense Date: tanggal pengeluaran
   - Receipt: upload foto/PDF bukti (opsional)
4. Staff/Owner mengklik **Submit**
5. **Sistem otomatis:**
   - Membuat record Expense baru dengan expense_number unik
   - **Membuat Transaction otomatis** dengan:
     - Type: **Expense**
     - Category: sesuai pilihan
     - Amount: sesuai nominal
     - Transaction date: sesuai expense date
     - Transaction number: TRX-YYYYMMDD-XXXX
6. Sistem menampilkan konfirmasi expense berhasil dicatat
7. Expense tercatat dalam laporan keuangan

**Contoh Expense:**
- Gaji staff bulanan
- Tagihan listrik (PLN)
- Tagihan air (PDAM)
- Pembelian detergen & pewangi
- Biaya maintenance mesin cuci

**Output:** Pengeluaran tercatat, transaction expense dibuat otomatis

---

### 11. LAPORAN KEUANGAN

**Aktor:** Owner/Admin  
**Kondisi Awal:** Owner ingin melihat laporan keuangan  
**Aktivitas:**

1. Owner membuka menu **Financial** → **Report**
2. Owner memilih periode laporan:
   - Type: Daily, Weekly, Monthly, Custom
   - Start Date: tanggal mulai
   - End Date: tanggal akhir
3. Owner mengklik **Generate Report**
4. **Sistem menghitung dan menampilkan:**
   - Total Income (dari transactions type=income)
   - Total Expenses (dari transactions type=expense)
   - Net Profit/Loss = Income - Expenses
   - Breakdown by Category
   - Transaction details
5. Owner dapat mengklik **Export** untuk download laporan (Excel/PDF)

**Output:** Laporan keuangan periode tertentu

---

### 12. MANAJEMEN INVENTORY

**Aktor:** Staff/Owner  
**Kondisi Awal:** Inventory perlu diupdate  
**Aktivitas:**

#### A. Monitoring Inventory
1. Staff/Owner membuka menu **Inventory**
2. Sistem menampilkan:
   - Daftar inventory items (detergen, softener, pewangi, plastik, hanger)
   - Quantity saat ini
   - Reorder level (batas minimum)
   - Status: Low Stock jika quantity <= reorder_level
3. Dashboard menampilkan **Low Stock Alert** untuk item yang perlu direstock

#### B. Adjust Stock (Pembelian/Koreksi)
1. Staff/Owner mengklik item inventory
2. Staff/Owner mengklik **Adjust Stock**
3. Staff/Owner menginput:
   - Type: Stock In (pembelian), Stock Out (penyesuaian), Adjustment (koreksi)
   - Quantity: jumlah yang ditambah/kurangi
   - Reference Number: nomor PO atau referensi (opsional)
   - Notes: keterangan
4. Sistem membuat **InventoryTransaction** baru
5. Sistem update `quantity` item (akumulatif)

#### C. Usage Tracking (Otomatis dari Seeder)
- Ketika order completed, sistem bisa otomatis mengurangi stok inventory
- Membuat InventoryTransaction dengan type **Usage**
- Reference ke order_id

**Output:** Inventory terpantau dan tercatat dengan baik

---

### 13. PROGRAM LOYALTY & REWARDS

**Aktor:** Staff dan Customer  
**Kondisi Awal:** Customer memiliki loyalty points cukup  
**Aktivitas:**

#### A. Akumulasi Poin (Otomatis)
- Sistem memberikan poin otomatis saat order completed dan paid:
  - +10 poin per order
  - +2 poin per kg (untuk layanan kiloan)
- Sistem mencatat dalam LoyaltyTransaction (type: Earned)
- Loyalty points terakumulasi di profil customer

#### B. Redeem Reward saat Order
1. Saat create order, staff bisa memilih reward yang tersedia
2. Sistem validasi apakah customer memiliki cukup poin
3. Jika valid:
   - Sistem apply diskon ke order
   - Sistem deduct loyalty points customer
   - Sistem membuat LoyaltyTransaction (type: Redeemed)
   - Sistem membuat CustomerReward record

#### C. Membership Tier
- Bronze: default (0+ points)
- Silver: 100+ points
- Gold: 500+ points
- Platinum: 1000+ points
- Tier dapat mempengaruhi benefit/diskon khusus

**Output:** Customer mendapat benefit dari program loyalty

---

## B. DIAGRAM ALUR PROSES BISNIS TARGET (FLOWCHART)

```
┌─────────────────────────────────────────────────────────────────┐
│                    PROSES BISNIS LAUNDRY CRM                    │
└─────────────────────────────────────────────────────────────────┘

[START]
   │
   ▼
┌──────────────────────────────┐
│ Customer Datang ke Outlet    │ (Manual)
└──────────────┬───────────────┘
               │
               ▼
       ┌───────────────┐
       │ Customer Baru? │
       └───┬───────┬───┘
           │ Ya    │ Tidak
           ▼       │
   ┌─────────────┐ │
   │ Staff Input │ │
   │ Data Customer│ │ (Sistem: Create Customer)
   │ ke Sistem   │ │
   └──────┬──────┘ │
          │        │
          └────┬───┘
               │
               ▼
┌──────────────────────────────┐
│ Staff Terima & Hitung Cucian │ (Manual)
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│ Staff Input Order ke Sistem  │ (Sistem: Create Order)
│ - Pilih Customer             │
│ - Tambah Service Items       │
│ - Tentukan Jadwal            │
│ - Instruksi Khusus           │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│ SISTEM OTOMATIS:             │
│ - Generate Order Number      │
│ - Kalkulasi Subtotal + Tax   │
│ - Set Status: In Progress    │
│ - Set Payment: Pending       │
└──────────────┬───────────────┘
               │
               ▼
       ┌───────────────┐
       │ Ada Reward?   │
       └───┬───────┬───┘
           │ Ya    │ Tidak
           ▼       │
   ┌─────────────┐ │
   │ Apply Diskon│ │ (Sistem: Redeem Reward)
   │ & Kurangi   │ │
   │ Poin        │ │
   └──────┬──────┘ │
          │        │
          └────┬───┘
               │
               ▼
┌──────────────────────────────┐
│ Staff Label Cucian dengan    │ (Manual)
│ Nomor Order                  │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│ PROSES PENCUCIAN (Manual):   │
│ 1. Terima & Sortir           │
│ 2. Cuci di Mesin             │
│ 3. Keringkan (Spinner/Jemur) │
│ 4. Setrika (jika perlu)      │
│ 5. Lipat & Kemas             │
│                              │
│ Status Sistem: In Progress   │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│ Staff Update Status: READY   │ (Sistem: Update Status)
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│ SISTEM OTOMATIS:             │
│ - Status → Ready             │
│ - Tampilkan Tombol WhatsApp  │
│ - Catat Status History       │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│ Staff Klik Tombol WhatsApp   │ (Sistem: Generate WA Link)
│ → Buka WhatsApp Web          │
│ → Template Pesan Terisi      │
│ → Staff Send Manual          │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│ Customer Terima Notifikasi   │ (Manual)
│ WhatsApp                     │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│ Customer Datang Ambil Cucian │ (Manual)
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│ Staff Cari Order di Sistem   │ (Sistem: Search)
│ - Search by Order Number     │
│ - Search by Customer Name    │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│ Staff Serahkan Cucian        │ (Manual)
└──────────────┬───────────────┘
               │
               ▼
       ┌───────────────┐
       │ Bayar Penuh?  │
       └───┬───────┬───┘
           │ Ya    │ Tidak/Sebagian
           │       │
           │       ▼
           │  ┌─────────────┐
           │  │ Staff Input │ (Sistem: Partial Payment)
           │  │ Pembayaran  │
           │  │ Sebagian    │
           │  └──────┬──────┘
           │         │
           ▼         ▼
   ┌─────────────────────────┐
   │ Staff Input Pembayaran  │ (Sistem: Add Payment)
   │ - Amount                │
   │ - Method (Cash/Transfer)│
   │ - Notes                 │
   └──────────┬──────────────┘
              │
              ▼
   ┌─────────────────────────┐
   │ SISTEM OTOMATIS:        │
   │ - Create Payment Record │
   │ - Update Paid Amount    │
   │ - Update Payment Status │
   │ - CREATE TRANSACTION    │
   │   (Type: Income)        │
   └──────────┬──────────────┘
              │
              ▼
   ┌─────────────────────────┐
   │ Staff Update Status:    │ (Sistem: Update Status)
   │ COMPLETED               │
   └──────────┬──────────────┘
              │
              ▼
   ┌─────────────────────────┐
   │ SISTEM OTOMATIS:        │
   │ - Status → Completed    │
   │ - Jika Lunas:           │
   │   * Hitung Loyalty Poin │
   │   * Add Poin ke Customer│
   │   * Create Loyalty TX   │
   └──────────┬──────────────┘
              │
              ▼
          [END]


┌─────────────────────────────────────────────────────────────────┐
│                  PROSES PARALEL: MONITORING                     │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────────────────┐
│ Owner Buka Dashboard         │ (Sistem: Real-time Stats)
│ - Statistik Hari Ini         │
│ - Statistik Bulan Ini        │
│ - Revenue vs Expenses        │
│ - Order Status Distribution  │
│ - Recent Orders              │
│ - Pending Payments           │
│ - Low Stock Alert            │
│ - Top Customers              │
└──────────────────────────────┘

┌──────────────────────────────┐
│ Owner/Staff Input Expense    │ (Sistem: Create Expense)
│ - Gaji, PLN, PDAM, Supplies │
│ - Upload Receipt (opsional)  │
│                              │
│ SISTEM OTOMATIS:             │
│ - Create Expense Record      │
│ - CREATE TRANSACTION         │
│   (Type: Expense)            │
└──────────────────────────────┘

┌──────────────────────────────┐
│ Owner Generate Report        │ (Sistem: Financial Report)
│ - Pilih Periode              │
│ - Income vs Expense          │
│ - Profit/Loss                │
│ - Export Excel/PDF           │
└──────────────────────────────┘

┌──────────────────────────────┐
│ Staff/Owner Monitor Inventory│ (Sistem: Inventory Management)
│ - Cek Stock Level            │
│ - Low Stock Alert            │
│ - Adjust Stock (Pembelian)   │
│ - Usage Tracking (Otomatis)  │
└──────────────────────────────┘
```

---

## C. CATATAN PENYESUAIAN DARI PROSES EKSISTING

### 1. PENYEDERHANAAN STATUS ORDER

**Proses Eksisting:**  
Sistem lama atau pencatatan manual biasanya menggunakan 10+ status detail:
- Received
- Sorting
- Washing
- Drying
- Ironing
- Folding
- Packing
- Ready
- Out for Delivery
- Completed

**Proses Target:**  
Status disederhanakan menjadi **4 Major Stages**:
- **In Progress** (mencakup: received, sorting, washing, drying, ironing, folding, packing)
- **Ready** (siap diambil customer)
- **Completed** (sudah diambil dan selesai)
- **Cancelled** (dibatalkan)

**Manfaat:**
- Staff tidak perlu update status terlalu sering
- Fokus pada milestone utama yang penting bagi customer
- Mengurangi kompleksitas operasional
- Status history tetap tercatat untuk audit

---

### 2. OTOMASI PENCATATAN TRANSAKSI KEUANGAN

**Proses Eksisting:**  
- Staff mencatat pembayaran di buku kas manual
- Staff mencatat transaksi income di buku terpisah
- Rekap keuangan dilakukan manual di akhir hari/bulan
- Risiko human error dan duplikasi pencatatan
- Sulit melacak transaksi spesifik

**Proses Target:**  
- **Saat Payment dibuat** → Sistem otomatis membuat Transaction (type: Income)
- **Saat Expense dibuat** → Sistem otomatis membuat Transaction (type: Expense)
- Semua transaksi tercatat dengan:
  - Transaction Number unik (TRX-YYYYMMDD-XXXX)
  - Timestamp yang akurat
  - Reference ke order/expense
  - Kategori yang jelas
- Dashboard menampilkan statistik real-time dari tabel Transactions

**Manfaat:**
- Eliminasi duplikasi pencatatan
- Mengurangi human error
- Real-time financial reporting
- Audit trail yang jelas
- Data akurat untuk pengambilan keputusan

---

### 3. NOTIFIKASI WHATSAPP SEMI-OTOMATIS

**Proses Eksisting:**
- Staff menelepon customer satu per satu
- Staff mengirim SMS manual
- Staff mengirim WhatsApp manual dengan mengetik ulang
- Tidak ada template standar
- Informasi tidak konsisten
- Memakan waktu 3-5 menit per customer

**Proses Target:**
- **Tombol WhatsApp muncul otomatis** ketika status = Ready
- Sistem generate **template pesan standar** dengan data order
- Nomor telepon otomatis diformat (08xx → 62xxx)
- Staff hanya perlu **klik 2 kali** (tombol WhatsApp → Send)
- Template konsisten dan profesional
- Menggunakan **wa.me link** (tanpa perlu WhatsApp Business API)

**Manfaat:**
- Hemat waktu (dari 3-5 menit menjadi 10-15 detik per customer)
- Pesan konsisten dan profesional
- Mengurangi typo dan kesalahan informasi
- Tidak perlu biaya WhatsApp Business API
- Tetap ada kontrol human (staff yang klik send)

---

### 4. LOYALTY PROGRAM OTOMATIS

**Proses Eksisting:**
- Kartu stamp manual (cap basah)
- Kartu mudah hilang
- Sulit melacak poin customer
- Sistem reward tidak terstruktur
- Tidak ada data historis

**Proses Target:**
- **Sistem otomatis memberikan poin** saat order completed & paid
- Poin terakumulasi di database (tidak bisa hilang)
- Membership tier otomatis (Bronze, Silver, Gold, Platinum)
- Reward dapat di-redeem saat order
- Sistem validasi poin otomatis
- History lengkap (earned, redeemed, expired)

**Manfaat:**
- Customer tidak perlu bawa kartu fisik
- Data poin akurat dan terpercaya
- Meningkatkan customer retention
- Owner dapat analisis customer behavior
- Proses redeem reward transparan

---

### 5. DASHBOARD MONITORING REAL-TIME

**Proses Eksisting:**
- Owner hitung manual dari buku kas
- Rekap dilakukan end of day/month
- Tidak ada visualisasi
- Sulit identifikasi tren
- Keputusan berdasarkan "feeling"

**Proses Target:**
- **Dashboard real-time** dengan statistik:
  - Hari ini, bulan ini, 30 hari terakhir
  - Revenue, Expenses, Profit
  - Order status distribution
  - Low stock alert
  - Top customers
- Data langsung dari database (akurat)
- Visualisasi dengan chart
- **Dapat diakses dari HP** (responsive mobile)

**Manfaat:**
- Owner dapat monitor bisnis kapan saja, dimana saja
- Decision making berbasis data
- Early warning system (low stock, pending payments)
- Identifikasi tren dan pattern
- Meningkatkan kontrol operasional

---

### 6. INVENTORY MANAGEMENT TERSTRUKTUR

**Proses Eksisting:**
- Cek stok manual dengan mata
- Tidak ada record pembelian
- Tidak tau kapan harus restock
- Tidak tau pemakaian per order
- Sering kehabisan bahan mendadak

**Proses Target:**
- **Database inventory items** dengan kategori jelas
- **Reorder level alert** di dashboard
- **Inventory transactions** tercatat (stock in, stock out, usage, adjustment)
- **Usage tracking** per order (opsional, sudah ada di seeder)
- History lengkap untuk audit

**Manfaat:**
- Tidak kehabisan stok mendadak
- Pembelian terencana (reorder level)
- Data usage untuk cost analysis
- Kontrol inventory lebih baik
- Mengurangi pemborosan

---

### 7. SEARCH & TRACKING ORDER

**Proses Eksisting:**
- Mencari order di tumpukan nota manual
- Cek status dengan melihat cucian fisik
- Sulit tracking order yang banyak
- Customer sering menanyakan "sudah selesai belum?"

**Proses Target:**
- **Search by Order Number** atau **Customer Name**
- Filter by Status (in_progress, ready, completed)
- Filter by Payment Status (pending, partial, paid)
- Pagination untuk data banyak
- Mobile-friendly (dapat dicek dari HP)

**Manfaat:**
- Temukan order dalam hitungan detik
- Staff dapat jawab customer dengan cepat
- Transparansi status order
- Mengurangi komplain customer
- Meningkatkan service quality

---

### 8. PARTIAL PAYMENT SUPPORT

**Proses Eksisting:**
- Customer harus bayar lunas di muka atau di akhir
- Tidak ada opsi cicilan
- Sulit tracking pembayaran sebagian
- Risiko bad debt

**Proses Target:**
- **Sistem mendukung pembayaran bertahap**
- Payment status: Pending → Partial → Paid
- Setiap payment tercatat dengan timestamp
- Sisa tagihan terupdate otomatis
- Dashboard menampilkan pending payments

**Manfaat:**
- Fleksibilitas bagi customer
- Mengurangi bad debt (customer bisa DP)
- Tracking pembayaran akurat
- Transparansi sisa tagihan

---

### 9. MOBILE-FIRST INTERFACE

**Proses Eksisting:**
- Harus akses dari PC/Laptop
- Sulit akses di lapangan
- Owner tidak bisa monitor dari luar outlet

**Proses Target:**
- **Responsive design** (desktop & mobile)
- **Bottom navigation** khusus mobile:
  - Dashboard
  - Orderan
  - Proses (filter in_progress)
  - Siap Ambil (filter ready)
  - Pelanggan
- Dapat diakses dari HP dengan Wi-Fi yang sama

**Manfaat:**
- Staff dapat input order dari HP (jika perlu)
- Owner monitor bisnis dari rumah
- Fleksibilitas akses
- Meningkatkan produktivitas

---

### 10. AUDIT TRAIL & HISTORY

**Proses Eksisting:**
- Tidak ada record perubahan status
- Tidak tau siapa yang update apa
- Sulit investigasi jika ada masalah
- No accountability

**Proses Target:**
- **Order Status History** otomatis tercatat
- Setiap perubahan status tercatat dengan:
  - Status lama → status baru
  - Timestamp
  - User yang melakukan (jika ada auth)
  - Notes (opsional)
- Transaction history lengkap
- Inventory transaction history

**Manfaat:**
- Accountability dan transparansi
- Audit trail untuk troubleshooting
- Dapat trace back jika ada komplain
- Compliance untuk standar bisnis

---

## KESIMPULAN

Proses Bisnis Target yang telah dirancang dengan dukungan sistem Laundry CRM berbasis web ini menghadirkan **efisiensi, akurasi, dan transparansi** dalam operasional bisnis laundry. Dengan mengintegrasikan aktivitas manual dan otomasi sistem, bisnis dapat:

1. **Meningkatkan Kecepatan Layanan**: Notifikasi WhatsApp semi-otomatis dan search order cepat
2. **Mengurangi Human Error**: Kalkulasi otomatis dan pencatatan transaksi otomatis
3. **Meningkatkan Customer Satisfaction**: Status tracking, loyalty program, dan komunikasi konsisten
4. **Kontrol Keuangan Lebih Baik**: Dashboard real-time dan financial reporting
5. **Data-Driven Decision Making**: Statistik dan insight berbasis data aktual
6. **Skalabilitas**: Sistem dapat mendukung pertumbuhan bisnis tanpa perlu rekrut banyak staff

Proses bisnis ini **telah disesuaikan dengan fitur nyata** yang ada di sistem dan dapat langsung diimplementasikan untuk operasional harian outlet laundry.

---

**Dokumen ini disusun berdasarkan analisis codebase aktual:**
- Routes: web.php
- Controllers: Dashboard, Order, Financial, Customer, Service, Inventory
- Models: Order, Payment, Transaction, Customer, LoyaltyTransaction
- Views: Dashboard, Orders (index/show), Bottom Navigation
- Seeder: RealCaseIndonesiaSeeder

**Tanggal Penyusunan:** 2 Januari 2026  
**Versi:** 1.0  
**Status:** Final - Ready for Implementation
