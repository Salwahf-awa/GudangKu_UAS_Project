<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /**
     * 'stock' tidak boleh diisi dari form produk; stok hanya berubah
     * lewat transaksi stok (StockTransactionController).
     * Pengecualian: stok awal saat produk dibuat di-set eksplisit di controller.
     */
    protected $fillable = [
        'sku',
        'name',
        'price',
        'min_stock',
        'category_id',
        'supplier_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }
}
