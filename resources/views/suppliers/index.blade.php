@extends('layouts.app')
@section('title', 'Supplier')

@section('content')
<div class="row between">
    <div><h1>Supplier</h1><p class="sub">Pemasok barang gudang (khusus admin).</p></div>
    <a class="btn" href="{{ route('suppliers.create') }}">+ Tambah supplier</a>
</div>

<div class="card">
    <table>
        <tr><th>Nama</th><th>Telepon</th><th>Alamat</th><th class="num">Produk</th><th class="num">Aksi</th></tr>
        @forelse($suppliers as $s)
            <tr>
                <td>{{ $s->name }}</td>
                <td>{{ $s->phone }}</td>
                <td>{{ $s->address }}</td>
                <td class="num">{{ $s->products_count }}</td>
                <td class="num">
                    <a class="btn btn-sm btn-sec" href="{{ route('suppliers.edit', $s) }}">Edit</a>
                    <form class="inline" method="POST" action="{{ route('suppliers.destroy', $s) }}" onsubmit="return confirm('Hapus supplier ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">Belum ada supplier.</td></tr>
        @endforelse
    </table>
    @include('partials.pager', ['p' => $suppliers])
</div>
@endsection
