<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'GudangKu') - GudangKu</title>
    <style>
        :root { --brand:#1f3a5f; --accent:#d9a441; --bg:#f4f6f9; --line:#dde2ea; --danger:#c0392b; --ok:#1e8449; --warn:#b9770e; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background:var(--bg); color:#1c2530; line-height:1.5; }
        nav { background:var(--brand); color:#fff; padding:0 20px; display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
        nav .logo { font-weight:700; font-size:1.15rem; margin-right:18px; padding:14px 0; }
        nav .logo span { color:var(--accent); }
        nav a { color:#cfd8e6; text-decoration:none; padding:16px 12px; font-size:.95rem; }
        nav a:hover, nav a.active { color:#fff; background:rgba(255,255,255,.1); }
        nav .spacer { flex:1; }
        nav .who { font-size:.85rem; color:#cfd8e6; margin-right:10px; }
        nav form { margin:0; }
        .badge { display:inline-block; padding:2px 9px; border-radius:99px; font-size:.75rem; font-weight:600; }
        .b-admin { background:var(--accent); color:#3a2a00; } .b-staff { background:#cfe3ff; color:#12335f; }
        .b-in { background:#d5f0df; color:var(--ok); } .b-out { background:#fbe0dc; color:var(--danger); }
        .b-low { background:#fdebc8; color:var(--warn); } .b-ok { background:#d5f0df; color:var(--ok); }
        main { max-width:1100px; margin:26px auto; padding:0 18px; }
        h1 { margin:0 0 6px; font-size:1.55rem; color:var(--brand); }
        .sub { color:#5c6b7d; margin:0 0 18px; }
        .card { background:#fff; border:1px solid var(--line); border-radius:10px; padding:20px; margin-bottom:20px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:14px; margin-bottom:20px; }
        .stat { background:#fff; border:1px solid var(--line); border-radius:10px; padding:16px; border-left:5px solid var(--accent); }
        .stat .n { font-size:1.6rem; font-weight:700; color:var(--brand); } .stat .l { color:#5c6b7d; font-size:.85rem; }
        table { width:100%; border-collapse:collapse; font-size:.93rem; }
        th, td { text-align:left; padding:10px 8px; border-bottom:1px solid var(--line); vertical-align:middle; }
        th { background:#eef2f7; color:var(--brand); font-size:.82rem; text-transform:uppercase; letter-spacing:.03em; }
        .num { text-align:right; }
        label { display:block; font-weight:600; margin:14px 0 4px; font-size:.9rem; }
        input, select, textarea { width:100%; padding:9px 11px; border:1px solid #b9c3d1; border-radius:7px; font:inherit; background:#fff; }
        input:focus, select:focus, textarea:focus { outline:2px solid var(--accent); border-color:var(--accent); }
        .btn { display:inline-block; padding:9px 16px; border:0; border-radius:7px; background:var(--brand); color:#fff; font:inherit; font-weight:600; cursor:pointer; text-decoration:none; font-size:.92rem; }
        .btn:hover { background:#16304f; }
        .btn-sec { background:#e5eaf1; color:var(--brand); } .btn-sec:hover { background:#d4dce8; }
        .btn-danger { background:var(--danger); } .btn-danger:hover { background:#992d22; }
        .btn-sm { padding:5px 10px; font-size:.82rem; }
        .row { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
        .row.between { justify-content:space-between; }
        .alert { padding:11px 14px; border-radius:8px; margin-bottom:16px; font-size:.93rem; }
        .alert-ok { background:#d5f0df; color:#14532d; border:1px solid #a7dcbb; }
        .alert-err { background:#fbe0dc; color:#7b241c; border:1px solid #f0b6ae; }
        .err { color:var(--danger); font-size:.83rem; margin-top:4px; }
        .two { display:grid; grid-template-columns:1fr 1fr; gap:14px; } @media (max-width:700px){ .two{ grid-template-columns:1fr; } }
        .auth { max-width:420px; margin:50px auto; }
        .pager { display:flex; gap:10px; align-items:center; justify-content:center; margin-top:14px; font-size:.9rem; }
        .muted { color:#6b7a8c; font-size:.85rem; } .inline { display:inline; margin:0; }
    </style>
</head>
<body>
@auth
    <nav>
        <div class="logo">Gudang<span>Ku</span></div>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Produk</a>
        <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}">Transaksi Stok</a>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">Kategori</a>
            <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">Supplier</a>
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Pengguna</a>
        @endif
        <div class="spacer"></div>
        <span class="who">{{ auth()->user()->name }} <span class="badge {{ auth()->user()->isAdmin() ? 'b-admin' : 'b-staff' }}">{{ auth()->user()->role }}</span></span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-sec">Logout</button>
        </form>
    </nav>
@endauth

<main>
    @if(session('success'))
        <div class="alert alert-ok">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-err">{{ session('error') }}</div>
    @endif

    @yield('content')
</main>
</body>
</html>
