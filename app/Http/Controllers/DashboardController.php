<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Eloquent: ringkasan angka
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalStock = (int) Product::sum('stock');
        $inventoryValue = (float) Product::sum(DB::raw('price * stock'));

        // Eloquent: produk dengan stok menipis (stok <= batas minimum)
        $lowStock = Product::with('category')
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->limit(8)
            ->get();

        // Query Builder: total stok per kategori (join + group by)
        $stockPerCategory = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('categories.name', DB::raw('SUM(products.stock) as total_stock'))
            ->groupBy('categories.name')
            ->orderByDesc('total_stock')
            ->get();

        // Eloquent dengan eager loading: 6 transaksi terbaru
        $recent = StockTransaction::with(['product', 'user'])
            ->latest('transaction_date')
            ->latest('id')
            ->limit(6)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalStock',
            'inventoryValue',
            'lowStock',
            'stockPerCategory',
            'recent'
        ));
    }
}
