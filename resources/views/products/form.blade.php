@extends('layouts.app')
@section('title', $product->exists ? 'Edit Produk' : 'Tambah Produk')

@section('content')
<h1>{{ $product->exists ? 'Edit Produk' : 'Tambah Produk' }}</h1>

<div class="card" style="max-width:640px">
    <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">
        @csrf
        @if($product->exists) @method('PUT') @endif

        <div class="two">
            <div>
                <label for="sku">SKU</label>
                <input id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required maxlength="50">
                @error('sku') <div class="err">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="name">Nama produk</label>
                <input id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="150">
                @error('name') <div class="err">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="two">
            <div>
                <label for="category_id">Kategori</label>
                <select id="category_id" name="category_id" required>
                    <option value="">-- pilih --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected((string) old('category_id', $product->category_id) === (string) $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <div class="err">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="supplier_id">Supplier (opsional)</label>
                <select id="supplier_id" name="supplier_id">
                    <option value="">-- tanpa supplier --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" @selected((string) old('supplier_id', $product->supplier_id) === (string) $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
                @error('supplier_id') <div class="err">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="two">
            <div>
                <label for="price">Harga (Rp)</label>
                <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $product->price) }}" required>
                @error('price') <div class="err">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="min_stock">Batas stok minimum</label>
                <input id="min_stock" type="number" min="0" name="min_stock" value="{{ old('min_stock', $product->min_stock ?? 5) }}" required>
                @error('min_stock') <div class="err">{{ $message }}</div> @enderror
            </div>
        </div>

        @unless($product->exists)
            <label for="initial_stock">Stok awal (opsional)</label>
            <input id="initial_stock" type="number" min="0" name="initial_stock" value="{{ old('initial_stock', 0) }}">
            @error('initial_stock') <div class="err">{{ $message }}</div> @enderror
        @else
            <p class="muted" style="margin-top:14px">Stok saat ini: <strong>{{ $product->stock }}</strong>. Stok hanya bisa diubah lewat menu Transaksi Stok.</p>
        @endunless

        <div class="row" style="margin-top:18px">
            <button class="btn" type="submit">Simpan</button>
            <a class="btn btn-sec" href="{{ route('products.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
