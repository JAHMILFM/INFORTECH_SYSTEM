<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Infortech') }} – CRM</title>
    <meta name="description" content="Sistema CRM premium de Infortech para gestión de clientes y servicios.">

    <script>
        // Corrección Falencia 1: Script de Modo Oscuro en el Head para evitar FOUC
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        // Recuperar estado del mini-sidebar
        if(localStorage.getItem('sidebar-mini') === 'true') {
            document.body.classList.add('sidebar-mini');
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    
    <!-- Corrección Falencia 2 y 3: CSS Modularizado con clases corregidas -->
    <link href="{{ asset('assets/css/theme.css') }}?v={{ time() }}" rel="stylesheet">
</head>
<body>

<div id="preloader"><div class="loader-ring"></div></div>
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<nav id="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <img src="{{ asset('assets/images/logo-blanco.png') }}" alt="Infortech" style="max-width: 150px; height: auto;">
    </a>

    <div class="sidebar-nav">
        <div class="nav-label">Principal</div>

        <a href="{{ route('dashboard') }}" class="nav-item-link {{ Route::is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill nav-icon"></i> <span>Dashboard</span>
        </a>

        <a href="{{ route('companies.index') }}" class="nav-item-link {{ Route::is('companies.*') ? 'active' : '' }}">
            <i class="bi bi-buildings-fill nav-icon"></i> <span>Mis Clientes</span>
        </a>

        @if(auth()->user()->role === 'SuperAdmin')
        <div class="nav-label mt-2">Administración</div>

        <a class="nav-item-link collapse-toggle {{ Route::is('users.*') || Route::is('audit.*') ? 'active' : '' }}"
           data-bs-toggle="collapse" href="#collapseConfig"
           aria-expanded="{{ Route::is('users.*') || Route::is('audit.*') ? 'true' : 'false' }}">
            <i class="bi bi-gear-fill nav-icon"></i> <span>Configuración</span>
        </a>
        <div class="collapse {{ Route::is('users.*') || Route::is('audit.*') ? 'show' : '' }}" id="collapseConfig">
            <div class="sub-nav">
                <a href="{{ route('users.index') }}" class="nav-item-link {{ Route::is('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill nav-icon"></i> <span>Gestión de Accesos</span>
                </a>
                <a href="{{ route('audit.index') }}" class="nav-item-link {{ Route::is('audit.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text nav-icon"></i> <span>Historial de Auditoría</span>
                </a>
                <a href="{{ route('export.master') }}" class="nav-item-link">
                    <i class="bi bi-file-earmark-excel-fill nav-icon" style="color:#22c55e;"></i> <span>Backup Excel Maestro</span>
                </a>
            </div>
        </div>
        @endif
    </div>

    <div class="sidebar-footer">
        <div class="user-pill">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="user-role">{{ auth()->user()->role ?? 'User' }}</div>
            </div>
        </div>
    </div>
</nav>

<!-- TOPBAR -->
<header id="topbar">
    <button id="sidebar-toggle" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
    <!-- Corrección Falencia 5: Botón para Mini-Sidebar en Desktop -->
    <button id="sidebar-toggle-desktop" onclick="toggleMiniSidebar()"><i class="bi bi-layout-sidebar-inset"></i></button>

    <div class="topbar-title">
        @yield('page-title', 'Dashboard')
    </div>

    <!-- Corrección Falencia 6: Búsqueda AJAX sin recarga -->
    <div class="search-wrap d-none d-md-block">
        <i class="bi bi-search"></i>
        <input type="text" id="live-search-input" class="search-input" placeholder="Búsqueda instantánea..." autocomplete="off">
        <div id="search-results-dropdown" class="search-results-dropdown">
            <!-- Resultados inyectados vía JS -->
        </div>
    </div>

    <div class="topbar-actions ms-auto">
        <button id="theme-toggle" class="btn" style="background:var(--surface); border:1px solid var(--border); color:var(--text); width:36px; height:36px; border-radius:8px; display:flex; align-items:center; justify-content:center;">
            <i class="bi bi-moon-stars-fill"></i>
        </button>
        <div class="dropdown">
            <a class="profile-btn" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="p-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                <div>
                    <div class="p-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="p-role">{{ auth()->user()->role ?? 'User' }}</div>
                </div>
                <i class="bi bi-chevron-down ms-1" style="font-size:10px;color:var(--text-muted);"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark-custom">
                <li>
                    <a href="{{ route('profile.index') }}" class="dropdown-item">
                        <i class="bi bi-person-circle"></i> Mi Perfil
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger" style="background:none;border:none;width:100%;">
                            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- MAIN CONTENT -->
<main id="main-content">
    <!-- Corrección Falencia 9: Breadcrumbs dinámicos -->
    @hasSection('breadcrumb')
        <div class="breadcrumb-custom mb-3">
            <a href="{{ route('dashboard') }}"><i class="bi bi-house-door-fill"></i> Inicio</a>
            @yield('breadcrumb')
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.addEventListener('load', () => {
        const p = document.getElementById('preloader');
        p.style.opacity = '0';
        setTimeout(() => p.style.display = 'none', 400);
    });

    function toggleSidebar() {
        const s = document.getElementById('sidebar');
        const o = document.getElementById('sidebar-overlay');
        s.classList.toggle('show');
        o.style.display = s.classList.contains('show') ? 'block' : 'none';
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('show');
        document.getElementById('sidebar-overlay').style.display = 'none';
    }
    function toggleMiniSidebar() {
        document.body.classList.toggle('sidebar-mini');
        localStorage.setItem('sidebar-mini', document.body.classList.contains('sidebar-mini'));
    }

    // Corrección Falencia 8: Configuración de Toastr
    toastr.options = { 
        positionClass: 'toast-bottom-right', 
        progressBar: true, 
        timeOut: 3500, 
        closeButton: true,
        preventDuplicates: true, // Evita spam visual
        maxOpened: 3
    };
    @if(session('toastr_success')) toastr.success("{{ session('toastr_success') }}"); @endif
    @if(session('toastr_error'))   toastr.error("{{ session('toastr_error') }}");     @endif

    // Corrección Falencia 6: Live Search AJAX Logic
    let searchTimeout;
    const searchInput = document.getElementById('live-search-input');
    const searchDropdown = document.getElementById('search-results-dropdown');
    
    if(searchInput && searchDropdown) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            if(query.length < 2) {
                searchDropdown.classList.remove('show');
                return;
            }
            searchTimeout = setTimeout(() => {
                fetch(`/search/live?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        searchDropdown.innerHTML = '';
                        if(data.companies.length === 0 && data.services.length === 0) {
                            searchDropdown.innerHTML = `<div class="p-3 text-center text-muted" style="font-size:13px;">No se encontraron resultados para "${query}"</div>`;
                        } else {
                            if(data.companies.length > 0) {
                                let html = `<div class="search-result-group"><div class="search-result-title">Empresas</div>`;
                                data.companies.forEach(c => {
                                    html += `<a href="/companies/${c.id}" class="search-result-item"><i class="bi bi-building icon"></i> ${c.name}</a>`;
                                });
                                html += `</div>`;
                                searchDropdown.innerHTML += html;
                            }
                            if(data.services.length > 0) {
                                let html = `<div class="search-result-group"><div class="search-result-title">Servicios Asociados</div>`;
                                data.services.forEach(s => {
                                    html += `<a href="/companies/${s.company_id}" class="search-result-item"><i class="bi bi-hdd-network icon"></i> ${s.data.username || s.data.cuenta || 'Servicio'} <span class="badge-soft-info ms-auto" style="font-size:10px;">${s.type}</span></a>`;
                                });
                                html += `</div>`;
                                searchDropdown.innerHTML += html;
                            }
                        }
                        searchDropdown.classList.add('show');
                    });
            }, 300); // 300ms debounce
        });

        document.addEventListener('click', function(e) {
            if(!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
                searchDropdown.classList.remove('show');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = themeToggle.querySelector('i');
        const htmlElement = document.documentElement;
        
        function updateIcon(theme) {
            if (theme === 'light') {
                themeIcon.className = 'bi bi-sun-fill text-warning';
            } else {
                themeIcon.className = 'bi bi-moon-stars-fill text-info';
            }
        }
        
        updateIcon(htmlElement.getAttribute('data-theme'));
        
        themeToggle.addEventListener('click', () => {
            let currentTheme = htmlElement.getAttribute('data-theme');
            let newTheme = currentTheme === 'light' ? 'dark' : 'light';
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateIcon(newTheme);
        });
    });
</script>

@yield('scripts')
</body>
</html>
