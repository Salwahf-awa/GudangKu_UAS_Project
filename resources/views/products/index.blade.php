@extends('layouts.app')
@section('title', 'Produk')

@section('content')
<div class="row between">
    <div><h1>Produk</h1><p class="sub">Daftar barang di gudang beserta stok saat ini.</p></div>
    <a class="btn" href="{{ route('products.create') }}">+ Tambah produk</a>
</div>

<div class="card">
    <form method="GET" action="{{ route('products.index') }}" class="row" style="margin-bottom:14px">
        <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama atau SKU..." style="max-width:280px">
        <select name="category" style="max-width:220px">
            <option value="">Semua kategori</option>
            @foreach($categories as $c)
                <option value="{{ $c->id }}" @selected((string) $categoryId === (string) $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <button class="btn btn-sec" type="submit">Cari</button>
        @if($search || $categoryId) <a href="{{ route('products.index') }}" class="muted">Reset</a> @endif
    </form>

    <table>
        <tr><th>SKU</th><th>Nama</th><th>Kategori</th><th>Supplier</th><th class="num">Harga</th><th class="num">Stok</th><th class="num">Aksi</th></tr>
        @forelse($products as $p)
            <tr>
                <td>{{ $p->sku }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category->name }}</td>
                <td>{{ $p->supplier?->name ?? '-' }}</td>
                <td class="num">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                <td class="num"><span class="badge {{ $p->isLowStock() ? 'b-low' : 'b-ok' }}">{{ $p->stock }}</span></td>
                <td class="num">
                    <a class="btn btn-sm btn-sec" href="{{ route('products.edit', $p) }}">Edit</a>
                    @if(auth()->user()->isAdmin())
                        <form class="inline" method="POST" action="{{ route('products.destroy', $p) }}" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                        </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="muted">Tidak ada produk yang cocok.</td></tr>
        @endforelse
    </table>
    @include('partials.pager', ['p' => $products])
</div>
@endsection
