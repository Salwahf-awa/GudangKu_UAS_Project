@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="auth">
    <div class="card">
        <h1>Gudang<span style="color:var(--accent)">Ku</span></h1>
        <p class="sub">Masuk untuk mengelola inventaris gudang.</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email') <div class="err">{{ $message }}</div> @enderror

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
            @error('password') <div class="err">{{ $message }}</div> @enderror

            <label style="font-weight:400;display:flex;gap:8px;align-items:center">
                <input type="checkbox" name="remember" value="1" style="width:auto"> Ingat saya
            </label>

            <div style="margin-top:18px"><button class="btn" type="submit" style="width:100%">Login</button></div>
        </form>

        <p class="muted" style="text-align:center;margin-top:16px">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
    </div>
</div>
@endsection
