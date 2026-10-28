@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<h1>Dashboard</h1>
<p class="sub">Ringkasan kondisi gudang saat ini.</p>

<div class="grid">
    <div class="stat"><div class="n">{{ number_format($totalProducts, 0, ',', '.') }}</div><div class="l">Jenis produk</div></div>
    <div class="stat"><div class="n">{{ number_format($totalStock, 0, ',', '.') }}</div><div class="l">Total unit di gudang</div></div>
    <div class="stat"><div class="n">{{ $totalCategories }}</div><div class="l">Kategori</div></div>
    <div class="stat"><div class="n">Rp {{ number_format($inventoryValue, 0, ',', '.') }}</div><div class="l">Nilai persediaan</div></div>
</div>

<div class="two">
    <div class="card">
        <h3 style="margin-top:0">Stok menipis</h3>
        @if($lowStock->isEmpty())
            <p class="muted">Semua produk stoknya aman.</p>
        @else
            <table>
                <tr><th>Produk</th><th>Kategori</th><th class="num">Stok</th><th class="num">Min.</th></tr>
                @foreach($lowStock as $p)
                    <tr>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->category->name }}</td>
                        <td class="num"><span class="badge b-low">{{ $p->stock }}</span></td>
                        <td class="num">{{ $p->min_stock }}</td>
                    </tr>
                @endforeach
            </table>
        @endif
    </div>

    <div class="card">
        <h3 style="margin-top:0">Stok per kategori</h3>
        <table>
            <tr><th>Kategori</th><th class="num">Total unit</th></tr>
            @foreach($stockPerCategory as $row)
                <tr><td>{{ $row->name }}</td><td class="num">{{ number_format($row->total_stock, 0, ',', '.') }}</td></tr>
            @endforeach
        </table>
    </div>
</div>

<div class="card">
    <div class="row between"><h3 style="margin:0">Transaksi terbaru</h3><a class="btn btn-sm" href="{{ route('transactions.create') }}">+ Catat transaksi</a></div>
    <table style="margin-top:10px">
        <tr><th>Tanggal</th><th>Produk</th><th>Tipe</th><th class="num">Jumlah</th><th>Oleh</th></tr>
        @forelse($recent as $t)
            <tr>
                <td>{{ $t->transaction_date->format('d/m/Y') }}</td>
                <td>{{ $t->product->name }}</td>
                <td><span class="badge {{ $t->type === 'in' ? 'b-in' : 'b-out' }}">{{ $t->type === 'in' ? 'Masuk' : 'Keluar' }}</span></td>
                <td class="num">{{ $t->quantity }}</td>
                <td>{{ $t->user->name }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">Belum ada transaksi.</td></tr>
        @endforelse
    </table>
</div>
@endsection
