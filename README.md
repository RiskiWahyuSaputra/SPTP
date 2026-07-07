# Sistem Pengajuan Transaksi Pengeluaran (SPTP)

Aplikasi web berbasis Laravel untuk digitalisasi proses pengajuan dan persetujuan transaksi pengeluaran perusahaan dengan workflow approval berjenjang yang dinamis.

## Fitur

- **Manajemen Pengajuan** — Staff dapat membuat, mengedit, dan mengirim pengajuan dengan lampiran dokumen
- **Workflow Approval Dinamis** — Routing otomatis berdasarkan kategori dan nilai pengajuan (7 kondisi)
- **RBAC** — 5 role: Staff, SPV, Manager, Direktur, Finance
- **Validasi Budget** — Pengecekan budget per kategori sebelum approval
- **Manajemen Kas** — Finance memvalidasi saldo sebelum pembayaran
- **Dashboard Per Role** — Statistik dan antrian sesuai role masing-masing
- **Riwayat Approval** — Timeline keputusan lengkap dengan catatan

## Persyaratan Sistem

- PHP ^8.2
- Composer
- MySQL 8.0+ / MariaDB 10+
- Node.js & NPM (untuk build frontend)

## Instalasi

```bash
# Clone repository
git clone https://github.com/username/sptp.git
cd sptp

# Install dependensi PHP
composer install

# Copy environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sptp
DB_USERNAME=root
DB_PASSWORD=

# Buat database
mysql -u root -e "CREATE DATABASE sptp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Jalankan migrasi & seeder
php artisan migrate --seed

# Install dependensi frontend
npm install
npm run build

# Buat storage link
php artisan storage:link
```

## Menjalankan Aplikasi

```bash
php artisan serve
```

Akses di http://localhost:8000

## Akun Testing

| Role | Email | Password |
|------|-------|----------|
| Staff | staff@test.com | password |
| SPV | spv@test.com | password |
| Manager | manager@test.com | password |
| Direktur | direktur@test.com | password |
| Finance | finance@test.com | password |

## Struktur Database

### Entity Relationship

```
roles ──┐── users ──┐── submissions ──┐── approvals
        │           │                 │
        │           │                 ├── submission_attachments
        │           │                 └── payments
        └── categories ── budgets
```

### Tabel Utama

| Tabel | Deskripsi |
|-------|-----------|
| `roles` | Master data role (Staff, SPV, Manager, Direktur, Finance) |
| `users` | Data pengguna dengan relasi role_id |
| `categories` | Kategori pengajuan (PO Produk, Operasional, ATK, dll) |
| `budgets` | Alokasi anggaran per kategori per periode (YYYY-MM) |
| `submissions` | Data pengajuan (nomor unik, nilai, status workflow) |
| `submission_attachments` | Lampiran file (PDF/JPG/PNG, max 5MB) |
| `approvals` | Riwayat keputusan approval per level (sequence, decision, notes) |
| `payments` | Riwayat pembayaran (balance_before, balance_after, status) |
| `cash_balances` | Saldo kas perusahaan |

## Workflow Approval

```
Staff → SPV → {PO Produk?} → Ya → Direktur → Finance
             {Tidak} → {Nilai > 5jt?} → Ya → Manager → {Nilai > 10jt?} → Ya → Direktur
                                         Tidak             Tidak
                                          ↓                   ↓
                                    Cek Budget          Cek Budget
                                          ↓                   ↓
                                    SPV Approve         Manager Approve
                                          ↓                   ↓
                                     Finance ←───────────────┘
                                          ↓
                                    Cek Saldo → Paid / Rejected
```

### 7 Kondisi Bisnis

1. Kategori PO Produk → langsung ke Direktur
2. Bukan PO Produk & >Rp5jt → Staff → SPV → Manager
3. >Rp10jt → eskalasi Manager → Direktur
4. Budget tidak cukup → Rejected
5. Approver reject → Rejected
6. Semua approval selesai → Waiting Finance
7. Saldo cukup → Paid; tidak cukup → Rejected

## Tech Stack

- **Backend:** Laravel 12, PHP 8.3
- **Frontend:** Bootstrap 5.3, Bootstrap Icons
- **Database:** MySQL
- **Auth:** Laravel Breeze
- **Build:** Vite
