<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ASCEND')</title>

    @stack('styles')

    <style>
        body{
            margin:0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            background:#f6f7fb;
        }

        .layout{
            display:flex;
            min-height:100vh;
        }

        /* SIDEBAR — FORCE GRADIENT */
        .sidebar{
            width:260px;
            background: linear-gradient(
                180deg,
                #7600bc 0%,
                #5e0094 55%,
                #2d0a4a 100%
            ) !important;
            color:#fff;
            padding:18px 14px;
        }

        .brand{
            display:flex;
            align-items:center;
            gap:10px;
            padding:10px 10px 18px;
            font-weight:800;
        }

        .brand-badge{
            width:36px;
            height:36px;
            border-radius:12px;
            background:rgba(255,255,255,.18);
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .nav a{
            display:flex;
            align-items:center;
            gap:12px;
            padding:11px 12px;
            border-radius:14px;
            text-decoration:none;
            color:#fff;
            opacity:.9;
            margin:4px 6px;
        }

        .nav a:hover,
        .nav .active{
            background:rgba(255,255,255,.18);
            opacity:1;
        }

        .iconbox{
            width:30px;
            height:30px;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            background:rgba(255,255,255,.18);
            font-size:14px;
        }

        /* MAIN */
        .main{
            flex:1;
            display:flex;
            flex-direction:column;
        }

        .topbar{
            height:64px;
            background:#fff;
            border-bottom:1px solid rgba(0,0,0,.06);
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:0 18px;
        }

        .content{
            padding:18px;
        }
    </style>
</head>

<body>
<div class="layout">

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-badge">A</div>
            <span>ASCEND</span>
        </div>

        <nav class="nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="iconbox">🏠</span>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admission.index') }}">
                <span class="iconbox">📝</span>
                <span>Admission</span>
            </a>

            <a href="#"><span class="iconbox">🗂️</span><span>Registrar</span></a>
            <a href="#"><span class="iconbox">🧮</span><span>Accounting</span></a>
            <a href="#"><span class="iconbox">🧾</span><span>Billing</span></a>
            <a href="#"><span class="iconbox">🏛️</span><span>Dean</span></a>
            <a href="#"><span class="iconbox">🧑‍🏫</span><span>Faculty</span></a>
            <a href="#"><span class="iconbox">👩‍🎓</span><span>Students</span></a>
            <a href="{{ route('utilities.terms.index') }}"><span class="iconbox">🛠️</span><span>Utilities</span></a>
            <a href="#"><span class="iconbox">⚙️</span><span>Settings</span></a>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <strong>@yield('title')</strong>
            <span>BryLe</span>
        </header>

        <section class="content">
            @yield('content')
        </section>
    </main>

</div>
</body>
</html>
