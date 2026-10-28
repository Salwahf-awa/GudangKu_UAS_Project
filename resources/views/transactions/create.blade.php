@extends('layouts.app')
@section('title', 'Catat Transaksi')

@section('content')
<h1>Catat Transaksi Stok</h1>

<div class="card" style="max-width:560px">
    <form method="POST" action="{{ route('transactions.store') }}">
        @csrf

        <label for="product_id">Produk</label>
        <select id="product_id" name="product_id" required>
            <option value="">-- pilih produk --</option>
            @foreach($products as $p)
                <option value="{{ $p->id }}" @selected((string) old('product_id') === (string) $p->id)>{{ $p->sku }} - {{ $p->name }} (stok: {{ $p->stock }})</option>
            @endforeach
        </select>
        @error('product_id') <div class="err">{{ $message }}</div> @enderror

        <div class="two">
            <div>
                <label for="type">Tipe</label>
                <select id="type" name="type" required>
                    <option value="in" @selected(old('type') === 'in')>Barang masuk</option>
                    <option value="out" @selected(old('type') === 'out')>Barang keluar</option>
                </select>
                @error('type') <div class="err">{{ $message }}</div> @enderror
            </div>
            <div>
                <label for="quantity">Jumlah</label>
                <input id="quantity" type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}" required>
                @error('quantity') <div class="err">{{ $message }}</div> @enderror
            </div>
        </div>

        <label for="transaction_date">Tanggal</label>
        <input id="transaction_date" type="date" name="transaction_date" value="{{ old('transaction_date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required>
        @error('transaction_date') <div class="err">{{ $message }}</div> @enderror

        <label for="note">Catatan (opsional)</label>
        <input id="note" name="note" value="{{ old('note') }}" maxlength="255">
        @error('note') <div class="err">{{ $message }}</div> @enderror

        <div class="row" style="margin-top:18px">
            <button class="btn" type="submit">Simpan transaksi</button>
            <a class="btn btn-sec" href="{{ route('transactions.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
