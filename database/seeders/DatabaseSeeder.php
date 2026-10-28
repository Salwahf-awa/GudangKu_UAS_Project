<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Data dummy untuk demo & pengujian (diizinkan oleh soal: "dummy data (seeders)").
     *
     * Akun demo:
     *   admin@gudangku.test / password123  (role: admin)
     *   staff@gudangku.test / password123  (role: staff)
     */
    public function run(): void
    {
        // --- Users (role di-set eksplisit karena tidak ada di $fillable) ---
        $admin = new User(['name' => 'Admin Gudang', 'email' => 'admin@gudangku.test', 'password' => 'password123']);
        $admin->role = 'admin';
        $admin->save();

        $staff = new User(['name' => 'Staff Gudang', 'email' => 'staff@gudangku.test', 'password' => 'password123']);
        $staff->role = 'staff';
        $staff->save();

        // --- Categories ---
        $categories = [];
        foreach ([
            ['Elektronik', 'Perangkat dan aksesoris elektronik'],
            ['Alat Tulis', 'Perlengkapan tulis dan kantor'],
            ['Makanan Kemasan', 'Makanan dan minuman dalam kemasan'],
            ['Perkakas', 'Alat bantu teknis dan perbaikan'],
        ] as [$name, $desc]) {
            $categories[$name] = Category::create(['name' => $name, 'description' => $desc]);
        }

        // --- Suppliers ---
        $suppliers = [];
        foreach ([
            ['PT Sinar Elektronik', '021-5551001', 'Jl. Mangga Dua No. 10, Jakarta'],
            ['CV Kertas Jaya', '022-5552002', 'Jl. Asia Afrika No. 5, Bandung'],
            ['UD Pangan Makmur', '0251-5553003', 'Jl. Pajajaran No. 20, Bogor'],
        ] as [$name, $phone, $address]) {
            $suppliers[$name] = Supplier::create(['name' => $name, 'phone' => $phone, 'address' => $address]);
        }

        // --- Products (stok awal 0, lalu diisi lewat transaksi masuk agar riwayat konsisten) ---
        $rows = [
            ['ELK-001', 'Mouse Wireless', 95000, 5, 'Elektronik', 'PT Sinar Elektronik'],
            ['ELK-002', 'Keyboard USB', 150000, 5, 'Elektronik', 'PT Sinar Elektronik'],
            ['ELK-003', 'Kabel HDMI 2m', 45000, 10, 'Elektronik', 'PT Sinar Elektronik'],
            ['ATK-001', 'Kertas A4 80gr (rim)', 52000, 20, 'Alat Tulis', 'CV Kertas Jaya'],
            ['ATK-002', 'Pulpen Hitam (lusin)', 24000, 10, 'Alat Tulis', 'CV Kertas Jaya'],
            ['MKN-001', 'Mie Instan (dus)', 115000, 8, 'Makanan Kemasan', 'UD Pangan Makmur'],
            ['MKN-002', 'Air Mineral 600ml (dus)', 42000, 10, 'Makanan Kemasan', 'UD Pangan Makmur'],
            ['PRK-001', 'Obeng Set', 68000, 4, 'Perkakas', null],
        ];

        $products = [];
        foreach ($rows as [$sku, $name, $price, $min, $cat, $sup]) {
            $p = new Product([
                'sku' => $sku,
                'name' => $name,
                'price' => $price,
                'min_stock' => $min,
                'category_id' => $categories[$cat]->id,
                'supplier_id' => $sup ? $suppliers[$sup]->id : null,
            ]);
            $p->stock = 0;
            $p->save();
            $products[$sku] = $p;
        }

        // --- Transaksi stok: [sku, tipe, jumlah, catatan, hari yang lalu] ---
        $txs = [
            ['ELK-001', 'in', 40, 'Stok awal', 20],
            ['ELK-002', 'in', 25, 'Stok awal', 20],
            ['ELK-003', 'in', 12, 'Stok awal', 20],
            ['ATK-001', 'in', 60, 'Stok awal', 18],
            ['ATK-002', 'in', 30, 'Stok awal', 18],
            ['MKN-001', 'in', 30, 'Stok awal', 15],
            ['MKN-002', 'in', 40, 'Stok awal', 15],
            ['PRK-001', 'in', 10, 'Stok awal', 15],
            ['ELK-001', 'out', 15, 'Pesanan kantor cabang', 10],
            ['ELK-003', 'out', 5, 'Dipakai ruang rapat', 8],
            ['ATK-001', 'out', 45, 'Distribusi bulanan', 6],
            ['MKN-001', 'out', 25, 'Pesanan toko', 4],
            ['PRK-001', 'out', 7, 'Dipinjam tim teknisi', 2],
        ];

        foreach ($txs as [$sku, $type, $qty, $note, $daysAgo]) {
            $product = $products[$sku];

            StockTransaction::create([
                'product_id' => $product->id,
                'user_id' => $type === 'in' ? $admin->id : $staff->id,
                'type' => $type,
                'quantity' => $qty,
                'note' => $note,
                'transaction_date' => now()->subDays($daysAgo)->toDateString(),
            ]);

            // Sinkronkan kolom stok produk dengan transaksi.
            $product->stock += ($type === 'in') ? $qty : -$qty;
            $product->save();
        }
    }
}
