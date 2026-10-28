<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockTransactionController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type');

        $transactions = StockTransaction::with(['product', 'user'])
            ->when(in_array($type, ['in', 'out'], true), fn ($q) => $q->where('type', $type))
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('transactions.index', compact('transactions', 'type'));
    }

    public function create(): View
    {
        return view('transactions.create', [
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', Rule::in(['in', 'out'])],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        // Bila di dalam closure dilempar ValidationException, transaksi DB otomatis
        // di-rollback dan Laravel mengembalikan user ke form beserta pesan error.
        DB::transaction(function () use ($data, $request) {
            // lockForUpdate: kunci baris produk agar dua transaksi bersamaan
            // tidak membaca stok yang sama (mencegah stok jadi negatif / salah hitung).
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            if ($data['type'] === 'out' && $product->stock < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok tidak cukup. Stok {$product->name} saat ini: {$product->stock}.",
                ]);
            }

            StockTransaction::create([
                'product_id' => $product->id,
                'user_id' => $request->user()->id, // dari session, bukan dari input form
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'note' => $data['note'] ?? null,
                'transaction_date' => $data['transaction_date'],
            ]);

            $product->stock += $data['type'] === 'in' ? $data['quantity'] : -$data['quantity'];
            $product->save();
        });

        return redirect()->route('transactions.index')->with('success', 'Transaksi stok berhasil dicatat.');
    }
}
