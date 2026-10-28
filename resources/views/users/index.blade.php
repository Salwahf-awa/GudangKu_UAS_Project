@extends('layouts.app')
@section('title', 'Pengguna')

@section('content')
<h1>Pengguna</h1>
<p class="sub">Kelola role pengguna (khusus admin).</p>

<div class="card">
    <table>
        <tr><th>Nama</th><th>Email</th><th>Role</th><th class="num">Ubah role</th></tr>
        @foreach($users as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td><span class="badge {{ $u->isAdmin() ? 'b-admin' : 'b-staff' }}">{{ $u->role }}</span></td>
                <td class="num">
                    @if($u->id === auth()->id())
                        <span class="muted">(akun Anda)</span>
                    @else
                        <form class="inline" method="POST" action="{{ route('users.role', $u) }}">
                            @csrf @method('PATCH')
                            <select name="role" style="width:auto;display:inline-block">
                                <option value="staff" @selected($u->role === 'staff')>staff</option>
                                <option value="admin" @selected($u->role === 'admin')>admin</option>
                            </select>
                            <button class="btn btn-sm" type="submit">Simpan</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
    @include('partials.pager', ['p' => $users])
</div>
@endsection
