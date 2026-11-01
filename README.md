# GudangKu - Sistem Manajemen Inventaris Gudang

Aplikasi web full-stack berbasis Laravel (arsitektur MVC) + MySQL untuk mengelola produk,
kategori, supplier, serta transaksi barang masuk/keluar, dengan autentikasi dan kontrol akses
berbasis role (admin / staff). UAS Web Application Development - Universitas Cakrawala.

**Pembuat:** Salwa Hasna Fadhila (NIM 25110300002)
**Aplikasi live:** https://gudangku-uas-project.wasmer.app
**Repository:** https://github.com/Salwahf-awa/GudangKu_UAS_Project

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
git clone https://github.com/Salwahf-awa/GudangKu_UAS_Project.git gudangku
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

## Akun demo (dari seeder; berlaku di lokal dan di aplikasi live)
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
app/Providers/              AppServiceProvider (SET autocommit=1 khusus production)
config/database.php         Koneksi MySQL (membaca DB_NAME dan opsi TLS untuk hosting)
resources/views/            View Blade (layouts, auth, products, ...)
routes/web.php              Definisi routing
database/migrations/        Skema database
database/seeders/           Data dummy
bootstrap/app.php           Registrasi middleware
```

## Deployment (Wasmer Edge) - ringkas
1. Push proyek ke GitHub (`.env` dan `.env.production` masuk `.gitignore`; hanya `.env.example` yang di-commit).
2. Wasmer Edge -> login dengan GitHub -> install GitHub App -> Import Git Repository -> pilih repository ini (branch `main`).
3. Pada "Configure your app": pilih framework Laravel dan tambahkan database MySQL terkelola.
   Wasmer menyuntikkan `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USERNAME`, `DB_PASSWORD`.
4. Environment Variables: `APP_KEY` (hasil `php artisan key:generate --show`), `APP_ENV=production`,
   `APP_DEBUG=false`, `CACHE_STORE=file`, `SESSION_DRIVER=database`. Tulis nilai tanpa spasi di depan/belakang.
5. Dari komputer lokal, jalankan migrasi dan seeder ke database hosting (memakai file `.env.production` yang
   tidak di-commit): `php artisan migrate --force --env=production` lalu `php artisan db:seed --force --env=production`.
6. Setiap `git push` ke `main` otomatis men-deploy ulang. Buka URL aplikasi dan uji login admin dan staff.

Catatan: koneksi database di hosting ini berjalan dengan `autocommit` mati, sehingga data tidak tersimpan.
Karena itu `AppServiceProvider` menjalankan `SET autocommit=1` khusus saat `APP_ENV=production`.

Catatan: berkas `database.sql` diekspor dari MySQL setelah `php artisan migrate:fresh --seed`
(`mysqldump -u root -p gudangku > database.sql`).
