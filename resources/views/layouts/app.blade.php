<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Arklen Agro')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/member.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
:root {
    --green-50:  #f2f9eb;
    --green-100: #e4f3d6;
    --green-500: #4f9d69; /* SS ka main green color */
    --green-600: #418457;
    --green-700: #346a46;
    --green-900: #ffffff; /* Sidebar background ko white kiya */
    --sidebar-w: 240px;
    --topbar-h: 70px;
    --bg:        #f8faf9; /* Soft background */
    --surface:   #ffffff;
    --border:    #edf2f7; /* Light border */
    --text:      #334155; /* Dark grey text */
    --text-muted:#64748b;
    --radius:    12px;
    --shadow:    0 4px 6px -1px rgba(0,0,0,.05);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; }
body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); font-size: 14px; line-height: 1.6; }
a { text-decoration: none; color: inherit; }

.layout { display: flex; min-height: 100vh; }

/* Clean & Light Sidebar (As per SS) */
.sidebar {
    width: var(--sidebar-w);
    background: var(--sidebar-bg, #ffffff);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 200;
    transition: transform .25s ease;
    height:calc(100vh - 20px);
}

.sidebar-logo {
    display: flex; align-items: center; gap: 10px;
    padding: 30px 24px;
}
.sidebar-logo strong { font-size: 20px; font-weight: 700; color: var(--green-500); letter-spacing: -.3px; }
.sidebar-logo span { display: none; } /* Extra text hata diya jaisa SS me hai */

.sidebar-nav { padding: 10px 16px; flex: 1; }

.nav-section-label {
    font-size: 13px; font-weight: 700;
    color: #1e293b;
    padding: 20px 8px 10px 12px;
}

.nav-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 16px; border-radius: 8px;
    color: var(--text-muted); font-size: 14px; font-weight: 500;
    transition: all .2s; margin-bottom: 4px;
}
.nav-item i { width: 18px; text-align: center; font-size: 15px; color: var(--text-muted); flex-shrink: 0; }
.nav-item:hover, .nav-item.active { background: #f0fdf4; color: var(--green-500); }
.nav-item:hover i, .nav-item.active i { color: var(--green-500); }

/* Dropdown styling to match the light theme */
.nav-dropdown-toggle .dropdown-arrow { margin-left: auto; font-size: 10px; transition: transform .25s ease; color: var(--text-muted); }
.nav-dropdown-toggle.open .dropdown-arrow { transform: rotate(180deg); }
.nav-dropdown { overflow: hidden; max-height: 0; transition: max-height .3s ease; }
.nav-dropdown.open { max-height: 300px; }
.nav-sub-item { padding-left: 44px !important; font-size: 13.5px !important; color: var(--text-muted) !important; }
.nav-sub-item:hover { background: #f0fdf4 !important; color: var(--green-500) !important; }
.nav-sub-item.active { background: transparent !important; color: var(--green-500) !important; font-weight: 600; }


.sidebar-user { display: flex; align-items: center; gap: 10px; padding: 8px; }
.sidebar-user-avatar { width: 32px; height: 32px; background: var(--green-500); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: #fff; }
.sidebar-user-info strong { display: block; font-size: 13px; font-weight: 600; color: var(--text); }
.sidebar-user-info span { font-size: 11px; color: var(--text-muted); }

.main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

/* Topbar Minimalist Transformation */
.topbar { height: var(--topbar-h); background: #ffffff; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 40px; position: sticky; top: 0; z-index: 100; }
.topbar-title { font-size: 22px; font-weight: 700; color: #1e293b; letter-spacing: -.4px; }
.topbar-right { display: flex; align-items: center; gap: 15px; }
.topbar-user-avatar { width: 35px; height: 35px; background: var(--green-500); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; color: #fff; }
.topbar-user-name { font-size: 14px; font-weight: 500; color: var(--text-muted); }

        .page-content { flex: 1; padding: 10px;
    height:auto }

        .flash-wrap { margin-bottom: 20px; }
        .alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 16px; border-radius: var(--radius); font-size: 13.5px; font-weight: 500; margin-bottom: 10px; animation: slideDown .2s ease; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
        .alert-success { background: var(--green-50); border: 1px solid var(--green-200); color: var(--green-800); }
        .alert-error   { background: #fff5f5; border: 1px solid #fecaca; color: #b91c1c; }
        .alert-warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
        .alert-info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
        .alert-close { margin-left: auto; background: none; border: none; cursor: pointer; color: inherit; opacity: .6; font-size: 12px; padding: 0 2px; }
        .alert-close:hover { opacity: 1; }

        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); }
        .card-pad { padding: 24px; overflow:visible !important;}

                    thead{
                background:#f5f7f9;
            }

      

       
    


        tbody tr:last-child td { border-bottom: none; }

        .avatar { width: 35px; height: 35px; border-radius: 50%; background: var(--green-700); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; }
        .avatar-lg { width: 72px; height: 72px; font-size: 22px; border-radius: var(--radius); }

        .badge { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: .2px; white-space: nowrap; }
        .badge-green    { background: var(--green-100); color: var(--green-800); border: 1px solid var(--green-200); }
        .badge-amber    { background: var(--amber-100); color: #92400e; border: 1px solid #fde68a; }
        .badge-active   { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-inactive { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }

        .form-control { height: 38px; padding: 0 12px; border: 1px solid var(--border); border-radius: 8px; font-size: 13px; font-family: inherit; color: var(--text); background: var(--surface); outline: none; transition: border-color .15s, box-shadow .15s; width: 100%; }
        .form-control:focus { border-color: var(--green-500); box-shadow: 0 0 0 3px rgba(109,184,42,.12); }
        textarea.form-control { height: auto; padding: 10px 12px; resize: vertical; }
        .form-label { display: block; font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 5px; letter-spacing: .3px; }
        .form-group { margin-bottom: 18px; }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 0 18px; height: 38px; border-radius: 8px; font-size: 13px; font-weight: 600; font-family: inherit; border: none; cursor: pointer; transition: all .15s; white-space: nowrap; text-decoration: none; }
        .btn-primary { background: var(--green-600); color: #fff; }
        .btn-primary:hover { background: var(--green-700); }
        .btn-secondary { background: var(--bg); color: var(--text-muted); border: 1px solid var(--border); }
        .btn-secondary:hover { background: var(--green-50); color: var(--green-700); border-color: var(--green-300); }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; }
        .btn-sm { height: 30px; padding: 0 12px; font-size: 12px; }

        .stat-card { background: var(--green-50); border: 1px solid var(--green-200); border-radius: var(--radius); padding: 16px; }
        .stat-label { font-size: 11px; color: var(--text-muted); margin-bottom: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: .3px; }
        .stat-val { font-size: 20px; font-weight: 800; color: var(--green-700); }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px,1fr)); gap: 12px; }

        .pagination { display: flex; gap: 4px; list-style: none; flex-wrap: wrap; }
        .pagination li a, .pagination li span { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 8px; border-radius: 7px; font-size: 12px; font-weight: 600; border: 1px solid var(--border); color: var(--text-muted); background: var(--surface); transition: all .15s; }
        .pagination li a:hover { background: var(--green-50); border-color: var(--green-300); color: var(--green-700); }
        .pagination li.active span { background: var(--green-600); border-color: var(--green-600); color: #fff; }
        .pagination li.disabled span { opacity: .4; cursor: not-allowed; }

        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 190; }

        /* ── Select2 Custom Style ── */
        .select2-container .select2-selection--single { height: 38px !important; border: 1px solid var(--border) !important; border-radius: 8px !important; font-family: 'Plus Jakarta Sans', sans-serif !important; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 38px !important; font-size: 13px !important; color: var(--text) !important; padding-left: 12px !important; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px !important; }
        .select2-container--default.select2-container--focus .select2-selection--single { border-color: var(--green-500) !important; box-shadow: 0 0 0 3px rgba(109,184,42,.12) !important; }
        .select2-dropdown { border: 1px solid var(--green-300) !important; border-radius: 8px !important; font-size: 13px !important; font-family: 'Plus Jakarta Sans', sans-serif !important; }
        .select2-container--default .select2-results__option--highlighted { background-color: var(--green-600) !important; }
        .select2-search--dropdown .select2-search__field { border: 1px solid var(--border) !important; border-radius: 6px !important; padding: 6px 10px !important; font-size: 13px !important; }
        .select2-container { width: 100% !important; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.open { display: block; }
            .main { margin-left: 0; }
            .topbar-menu-btn { display: flex; }
            .topbar { padding: 0 16px; }
            .page-content { padding: 16px; }
        }
        /* Desktop par toggle button hamesha hidden rahega */
.topbar-menu-btn { 
    display: none !important; 
}

/* 300px se 768px tak ki screens (Tablets aur Mobile) ke liye rules */
@media (min-width: 300px) and (max-width: 768px) {
    .sidebar { 
        transform: translateX(-100%); 
    }
    .sidebar.open { 
        transform: translateX(0); 
    }
    .sidebar-overlay.open { 
        display: block; 
    }
    .main { 
        margin-left: 0; 
    }
    /* Choti screen par button dikhega aur click sahi se register hoga */
    .topbar-menu-btn { 
        display: flex !important; 
        background: none; 
        border: none; 
        font-size: 20px; 
        color: var(--text-muted); 
        cursor: pointer; 
        padding: 4px;
        z-index: 999;
    }
    .topbar { 
        padding: 0 16px; 
    }
    .page-content { 
        padding: 16px; 
    }
}
    </style>

    @stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="layout">

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-logo">
            <img src="{{ asset('images/arklen-logo.png') }}"
                 alt="Arklen Agro"
                 style="width:38px;height:38px;object-fit:contain;flex-shrink:0;">
            <div class="sidebar-logo-text">
                <strong>Arklen Agro</strong>
                <span>Pvt. Ltd</span>
            </div>
        </div>

        <nav class="sidebar-nav">
           <div class="nav-section-label"></div>

<a href="{{ route('dashboard') }}"
   class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
    <i class="fas fa-chart-pie"></i> Dashboard
</a>

<a href="{{ route('members.index') }}"
               class="nav-item {{ request()->routeIs('members.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Members
            </a>

            <a href="{{ route('orders.index') }}"
               class="nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i class="fas fa-box"></i> Orders
            </a>

            <div class="nav-item nav-dropdown-toggle {{ request()->routeIs('products.*') ? 'open' : '' }}"
                 onclick="toggleDropdown(this)">
                <i class="fas fa-layer-group"></i>
                Master
                <i class="fas fa-chevron-down dropdown-arrow"></i>
            </div>

            <div class="nav-dropdown {{ request()->routeIs('products.*') ? 'open' : '' }}">
                <a href="{{ route('products.index') }}"
                   class="nav-item nav-sub-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-seedling"></i> Product
                </a>
            </div>

            <a href="{{ route('tree') }}"
               class="nav-item {{ request()->routeIs('tree') ? 'active' : '' }}">
                <i class="fas fa-sitemap"></i> Binary Tree
            </a>
        </nav>

        <!-- <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="sidebar-user-info">
                    <strong>{{ Auth::user()->name ?? 'Admin' }}</strong>
                    <span>Administrator</span>
                </div>
            </div>
        </div> -->

    </aside>

    <div class="main">

<header class="topbar">
    <div class="topbar-left">
        <button class="topbar-menu-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
    </div>
    
    <div class="topbar-right" style="position: relative;">
        <div class="topbar-user" id="profileDropdownTrigger" style="display: flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 20px; border: 1px solid var(--border); background: #ffffff; cursor: pointer; user-select: none;">
            <div class="topbar-user-avatar" style="width: 32px; height: 32px; background: var(--green-700); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800; color: #fff;">
                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
            <span class="topbar-user-name" style="font-size: 13px; font-weight: 600; color: var(--text);">{{ Auth::user()->name ?? 'Admin' }}</span>
            <i class="fas fa-chevron-down" style="font-size: 10px; color: var(--text-muted); margin-left: 2px;"></i>
        </div>

        <div id="profileDropdownCard" style="display: none; position: absolute; top: 110%; right: 0; background: #ffffff; border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.05); width: 150px; z-index: 1000; overflow: hidden;">
            <a href="#" style="display: block; padding: 10px 16px; color: var(--text); font-size: 13px; font-weight: 500; text-decoration: none; border-bottom: 1px solid var(--border);" onmouseover="this.style.background='#f8faf9'" onmouseout="this.style.background='none'">
                My Profile
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" style="display: block; width: 100%; text-align: left; background: none; border: none; padding: 10px 16px; color: #ef4444; font-size: 13px; font-weight: 500; cursor: pointer; font-family: inherit;" onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background='none'">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('profileDropdownTrigger').addEventListener('click', function(event) {
            event.stopPropagation();
            var card = document.getElementById('profileDropdownCard');
            if (card.style.display === 'none' || card.style.display === '') {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });

        document.addEventListener('click', function() {
            var card = document.getElementById('profileDropdownCard');
            if (card) {
                card.style.display = 'none';
            }
        });
    </script>
</header>

        <main class="page-content">
            <div class="flash-wrap">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-circle-check"></i>
                        {{ session('success') }}
                        <button class="alert-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-circle-exclamation"></i>
                        {{ session('error') }}
                        <button class="alert-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                @endif
                @if($errors->any() && !request()->routeIs('members.*') && !request()->routeIs('orders.*'))
                    <div class="alert alert-error">
                        <i class="fas fa-circle-exclamation"></i>
                        <div>
                            @foreach($errors->all() as $err)
                                <div>{{ $err }}</div>
                            @endforeach
                        </div>
                        <button class="alert-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>
                @endif
            </div>

            @yield('content')
        </main>

    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('open');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('open');
    }
    function toggleDropdown(el) {
        el.classList.toggle('open');
        el.nextElementSibling.classList.toggle('open');
    }
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 5000);
</script>

@stack('scripts')
*{
    outline:1px solid red;
}
</body>
</html>