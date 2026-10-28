@extends('layouts.app')
@section('title', 'Transaksi Stok')

@section('content')
<div class="row between">
    <div><h1>Transaksi Stok</h1><p class="sub">Riwayat barang masuk dan keluar (tidak bisa diedit/dihapus sebagai jejak audit).</p></div>
    <a class="btn" href="{{ route('transactions.create') }}">+ Catat transaksi</a>
</div>

<div class="card">
    <div class="row" style="margin-bottom:14px">
        <a class="btn btn-sm {{ $type ? 'btn-sec' : '' }}" href="{{ route('transactions.index') }}">Semua</a>
        <a class="btn btn-sm {{ $type === 'in' ? '' : 'btn-sec' }}" href="{{ route('transactions.index', ['type' => 'in']) }}">Masuk</a>
        <a class="btn btn-sm {{ $type === 'out' ? '' : 'btn-sec' }}" href="{{ route('transactions.index', ['type' => 'out']) }}">Keluar</a>
    </div>

    <table>
        <tr><th>Tanggal</th><th>Produk</th><th>Tipe</th><th class="num">Jumlah</th><th>Catatan</th><th>Oleh</th></tr>
        @forelse($transactions as $t)
            <tr>
                <td>{{ $t->transaction_date->format('d/m/Y') }}</td>
                <td>{{ $t->product->name }}</td>
                <td><span class="badge {{ $t->type === 'in' ? 'b-in' : 'b-out' }}">{{ $t->type === 'in' ? 'Masuk' : 'Keluar' }}</span></td>
                <td class="num">{{ $t->quantity }}</td>
                <td>{{ $t->note }}</td>
                <td>{{ $t->user->name }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="muted">Belum ada transaksi.</td></tr>
        @endforelse
    </table>
    @include('partials.pager', ['p' => $transactions])
</div>
@endsection
