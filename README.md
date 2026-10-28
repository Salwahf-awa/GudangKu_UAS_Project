# GudangKu - Sistem Manajemen Inventaris Gudang

Aplikasi web full-stack berbasis Laravel (arsitektur MVC) + MySQL untuk mengelola produk,
kategori, supplier, serta transaksi barang masuk/keluar, dengan autentikasi dan kontrol akses
berbasis role (admin / staff). UAS Web Application Development - Universitas Cakrawala.

## Fitur
- Registrasi, login, logout (session), rate limit login
- RBAC: `admin` (akses penuh) dan `staff` (terbatas)
- CRUD Kategori, Supplier, Produk (validasi server-side)
- Transaksi stok masuk/keluar (DB transaction + cek stok cukup)
- Dashboard: total produk, total unit, nilai persediaan, stok menipis, stok per kategori
- Keamanan: CSRF, sanitasi XSS, Blade auto-escape, parameterized query (Eloquent/Query Builder), bcrypt

## Kebutuhan
PHP >= 8.2 (ekstensi pdo_mysql, mbstring, xml, curl, zip), Composer, MySQL 8 / MariaDB, Git.
Tidak membutuhkan Node/npm (CSS ditulis langsung di layout).

## Instalasi lokal (dari repository ini)
```bash
# 1. Clone repository
git clone <URL-REPOSITORY-ANDA> gudangku
cd gudangku

# 2. Pasang dependensi PHP
composer install

# 3. Siapkan environment
cp .env.example .env        # Windows CMD: copy .env.example .env
php artisan key:generate

# 4. Buat database kosong di MySQL (phpMyAdmin / MySQL Workbench / terminal)
#    CREATE DATABASE gudangku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
#    lalu sesuaikan DB_USERNAME dan DB_PASSWORD di file .env

# 5. Migrasi tabel + isi data dummy
php artisan migrate --seed

# 6. Jalankan
php artisan serve
```
Buka http://localhost:8000

Alternatif tanpa migrasi: impor berkas `database.sql` ke database `gudangku`
(skema + data dummy sudah ada di dalamnya).

## Akun demo (dari seeder)
| Role  | Email                | Password    |
|-------|----------------------|-------------|
| admin | admin@gudangku.test  | password123 |
| staff | staff@gudangku.test  | password123 |

## Hak akses
| Fitur                              | Admin | Staff |
|------------------------------------|:-----:|:-----:|
| Dashboard, lihat produk            |  v    |  v    |
| Tambah / ubah produk               |  v    |  v    |
| Catat transaksi stok               |  v    |  v    |
| Hapus produk                       |  v    |  -    |
| CRUD kategori & supplier           |  v    |  -    |
| Kelola role pengguna               |  v    |  -    |

## Menjalankan pengujian
```bash
php artisan test
```

## Struktur MVC
```
app/Models/                 Model Eloquent (User, Category, Supplier, Product, StockTransaction)
app/Http/Controllers/       Controller (Auth, Dashboard, Category, Supplier, Product, StockTransaction, User)
app/Http/Middleware/        RoleMiddleware (RBAC), SanitizeInput (anti-XSS)
resources/views/            View Blade (layouts, auth, products, ...)
routes/web.php              Definisi routing
database/migrations/        Skema database
database/seeders/           Data dummy
bootstrap/app.php           Registrasi middleware
```

## Deployment (Railway) - ringkas
1. Push proyek ke GitHub.
2. Railway -> New Project -> Deploy from GitHub repo, lalu tambahkan service MySQL.
3. Variables pada service aplikasi: `APP_KEY` (hasil `php artisan key:generate --show`), `APP_ENV=production`,
   `APP_DEBUG=false`, `APP_URL=https://<domain-railway>`, `DB_CONNECTION=mysql`,
   `DB_URL=${{MySQL.MYSQL_URL}}`, `SESSION_DRIVER=database`.
4. Jalankan `php artisan migrate --force` (pre-deploy / start command), lalu sekali `php artisan db:seed --force`.
5. Generate domain publik di tab Settings -> Networking.

Catatan: berkas `database.sql` diekspor dari MySQL setelah `php artisan migrate:fresh --seed`
(`mysqldump -u root -p gudangku > database.sql`).
