@extends('layouts.app')
@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
<h1>{{ $category->exists ? 'Edit Kategori' : 'Tambah Kategori' }}</h1>

<div class="card" style="max-width:560px">
    <form method="POST" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
        @csrf
        @if($category->exists) @method('PUT') @endif

        <label for="name">Nama kategori</label>
        <input id="name" name="name" value="{{ old('name', $category->name) }}" required maxlength="100">
        @error('name') <div class="err">{{ $message }}</div> @enderror

        <label for="description">Deskripsi (opsional)</label>
        <textarea id="description" name="description" rows="3" maxlength="500">{{ old('description', $category->description) }}</textarea>
        @error('description') <div class="err">{{ $message }}</div> @enderror

        <div class="row" style="margin-top:18px">
            <button class="btn" type="submit">Simpan</button>
            <a class="btn btn-sec" href="{{ route('categories.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
