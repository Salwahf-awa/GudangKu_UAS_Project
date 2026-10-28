<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockTransaction;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('q');
        $categoryId = $request->query('category');

        // Eloquent + eager loading (with) untuk menghindari masalah N+1 query.
        // Nilai pencarian dikirim sebagai binding parameter, bukan digabung ke string SQL.
        $products = Product::with(['category', 'supplier'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%'.$search.'%')
                      ->orWhere('sku', 'like', '%'.$search.'%');
                });
            })
            ->when($categoryId, fn ($query, $id) => $query->where('category_id', $id))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId'));
    }

    public function create(): View
    {
        return view('products.form', [
            'product' => new Product(),
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'min_stock' => ['required', 'integer', 'min:0', 'max:100000'],
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'initial_stock' => ['nullable', 'integer', 'min:0', 'max:1000000'],
        ]);

        $initial = (int) ($data['initial_stock'] ?? 0);
        unset($data['initial_stock']);

        // Produk + transaksi stok awal disimpan bersama atau tidak sama sekali.
        DB::transaction(function () use ($data, $initial, $request) {
            $product = new Product($data);
            $product->stock = $initial;
            $product->save();

            if ($initial > 0) {
                StockTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => $request->user()->id,
                    'type' => 'in',
                    'quantity' => $initial,
                    'note' => 'Stok awal',
                    'transaction_date' => now()->toDateString(),
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product->id)],
            'name' => ['required', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'min_stock' => ['required', 'integer', 'min:0', 'max:100000'],
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ]);

        // Stok tidak bisa diubah dari sini (hanya lewat transaksi stok).
        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        try {
            $product->delete();
        } catch (QueryException $e) {
            // Produk yang sudah punya riwayat transaksi dilindungi foreign key (restrict).
            return back()->with('error', 'Produk tidak bisa dihapus karena sudah memiliki riwayat transaksi stok.');
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
