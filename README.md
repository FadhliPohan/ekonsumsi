# E-Konsumsi

Aplikasi manajemen konsumsi berbasis web menggunakan Laravel 12.

## Fitur Utama

- **Dashboard** — Halaman utama setelah login
- **Manajemen Departemen** — CRUD departemen (nama, kode, lokasi, status aktif)
- **Manajemen Saldo** — Transaksi saldo masuk/keluar per departemen dengan log riwayat
- **User Management** — Kelola user, role, dan permission (Spatie Laravel Permission)

## Persyaratan Sistem

| Komponen | Versi                    |
| -------- | ------------------------ |
| PHP      | >= 8.2                   |
| Composer | >= 2.x                   |
| Node.js  | >= 18.x                  |
| MySQL    | >= 5.7 / MariaDB >= 10.3 |

> [!TIP]
> Disarankan menggunakan **Laragon** untuk setup lokal di Windows.

## Instalasi

### 1. Clone Repository

```bash
git clone <repository-url> e-konsumsi
cd e-konsumsi
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` sesuai konfigurasi database:

```env
APP_NAME=E-Konsumsi
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=e_konsumsi
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi & Seeder

```bash
php artisan migrate
php artisan db:seed
```

Seeder akan membuat:

- Role **Admin** dengan seluruh permission
- User default: `admin@example.com` / `password`
- 40 data departemen dummy

### 5. Build Assets

```bash
npm run build
```

### 6. Jalankan Aplikasi

```bash
php artisan serve
```

Akses di browser: [http://localhost:8000](http://localhost:8000)

**Atau dengan Laragon:** cukup arahkan document root ke folder project, akses via `http://e-konsumsi.test`.

## Penggunaan

### Login

Gunakan akun default setelah seeder:

| Email             | Password |
| ----------------- | -------- |
| admin@example.com | password |

### Manajemen Departemen

1. Buka menu **Master Data → Departemen**
2. Klik **Tambah** untuk membuat departemen baru (via modal)
3. Klik ikon **edit** atau **hapus** pada baris tabel untuk mengelola data

### Manajemen Saldo

1. Buka menu **Saldo → Transaksi Saldo** (hanya tersedia untuk role Admin)
2. Seluruh departemen aktif akan ditampilkan beserta saldo saat ini
3. Klik ikon **➕** untuk menambahkan transaksi (saldo masuk/keluar)
4. Klik ikon **📋** untuk melihat log riwayat transaksi

> [!IMPORTANT]
> Saldo tidak bisa dikurangi melebihi jumlah saldo saat ini.

### User & Role Management

1. Buka menu **User Management**
2. Kelola **Users** — tambah, edit, hapus, assign role
3. Kelola **Roles** — buat role baru, assign permission
4. Kelola **Permissions** — buat permission baru

## Struktur Modul

```
app/
├── Http/Controllers/
│   ├── MasterData/
│   │   └── DepartemenController.php
│   ├── Saldo/
│   │   └── SaldoController.php
│   ├── UserController.php
│   ├── RoleController.php
│   └── PermissionController.php
├── Models/
│   ├── masterData/
│   │   └── Departemen.php
│   └── saldo/
│       ├── Saldo.php
│       └── logSaldo.php
resources/views/
├── masterdata/departement/
├── saldo/
├── users/
├── roles/
├── permissions/
└── layouts/
```

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Blade, Bootstrap 5, jQuery
- **Auth:** Laravel UI
- **Permission:** Spatie Laravel Permission v6
- **Notifikasi:** Toastr

## License

MIT License
