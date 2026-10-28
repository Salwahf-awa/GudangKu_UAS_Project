@extends('layouts.app')
@section('title', 'Registrasi')

@section('content')
<div class="auth">
    <div class="card">
        <h1>Daftar Akun</h1>
        <p class="sub">Akun baru otomatis berperan sebagai <strong>staff</strong>.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <label for="name">Nama lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="100">
            @error('name') <div class="err">{{ $message }}</div> @enderror

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            @error('email') <div class="err">{{ $message }}</div> @enderror

            <label for="password">Password (min. 8 karakter, huruf &amp; angka)</label>
            <input id="password" type="password" name="password" required>
            @error('password') <div class="err">{{ $message }}</div> @enderror

            <label for="password_confirmation">Ulangi password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>

            <div style="margin-top:18px"><button class="btn" type="submit" style="width:100%">Daftar</button></div>
        </form>

        <p class="muted" style="text-align:center;margin-top:16px">Sudah punya akun? <a href="{{ route('login') }}">Login</a></p>
    </div>
</div>
@endsection
