@extends('layouts.app')
@section('title', $supplier->exists ? 'Edit Supplier' : 'Tambah Supplier')

@section('content')
<h1>{{ $supplier->exists ? 'Edit Supplier' : 'Tambah Supplier' }}</h1>

<div class="card" style="max-width:560px">
    <form method="POST" action="{{ $supplier->exists ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
        @csrf
        @if($supplier->exists) @method('PUT') @endif

        <label for="name">Nama supplier</label>
        <input id="name" name="name" value="{{ old('name', $supplier->name) }}" required maxlength="150">
        @error('name') <div class="err">{{ $message }}</div> @enderror

        <label for="phone">Telepon</label>
        <input id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}" maxlength="30">
        @error('phone') <div class="err">{{ $message }}</div> @enderror

        <label for="address">Alamat</label>
        <input id="address" name="address" value="{{ old('address', $supplier->address) }}" maxlength="255">
        @error('address') <div class="err">{{ $message }}</div> @enderror

        <div class="row" style="margin-top:18px">
            <button class="btn" type="submit">Simpan</button>
            <a class="btn btn-sec" href="{{ route('suppliers.index') }}">Batal</a>
        </div>
    </form>
</div>
@endsection
