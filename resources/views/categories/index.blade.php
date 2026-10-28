@extends('layouts.app')
@section('title', 'Kategori')

@section('content')
<div class="row between">
    <div><h1>Kategori</h1><p class="sub">Pengelompokan produk gudang (khusus admin).</p></div>
    <a class="btn" href="{{ route('categories.create') }}">+ Tambah kategori</a>
</div>

<div class="card">
    <table>
        <tr><th>Nama</th><th>Deskripsi</th><th class="num">Jumlah produk</th><th class="num">Aksi</th></tr>
        @forelse($categories as $c)
            <tr>
                <td>{{ $c->name }}</td>
                <td>{{ $c->description }}</td>
                <td class="num">{{ $c->products_count }}</td>
                <td class="num">
                    <a class="btn btn-sm btn-sec" href="{{ route('categories.edit', $c) }}">Edit</a>
                    <form class="inline" method="POST" action="{{ route('categories.destroy', $c) }}" onsubmit="return confirm('Hapus kategori ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="muted">Belum ada kategori.</td></tr>
        @endforelse
    </table>
    @include('partials.pager', ['p' => $categories])
</div>
@endsection
