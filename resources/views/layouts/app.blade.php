<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Infortech') }} - CRM Premium</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}">
    <link href="{{ asset('assets/vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/chartist/css/chartist.min.css') }}">
    <link href="{{ asset('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    
    <!-- Premium UX / UI Fonts & Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* 1. Global Typography */
        body, h1, h2, h3, h4, h5, h6, .btn, .nav-text, .form-control {
            font-family: 'Inter', sans-serif !important;
        }

        /* 2. Micro-animaciones en Tarjetas */
        .card {
            border-radius: 14px !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03) !important;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
        }

        /* 3. Botones Flotantes y Degradados */
        .btn {
            border-radius: 8px !important;
            font-weight: 500 !important;
            letter-spacing: 0.3px;
            transition: all 0.2s ease-in-out !important;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0,0,0,0.15) !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
            border: none !important;
        }
        .btn-info {
            background: linear-gradient(135deg, #11cdef 0%, #1171ef 100%) !important;
            border: none !important;
        }

        /* 4. Scrollbars (Estilo macOS) */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.02);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.15);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0,0,0,0.3);
        }

        /* 5. Modales suaves (Glassmorphism sutil) */
        .modal-content {
            border-radius: 16px !important;
            border: none;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2) !important;
        }
        .modal-backdrop.show {
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            opacity: 0.7 !important;
        }

        /* 6. Brand Logo */
        .brand-logo h3 {
            font-weight: 800;
            background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* 7. Filas de tabla interactivas */
        .table-hover tbody tr {
            transition: all 0.2s ease;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(79, 172, 254, 0.04) !important;
            transform: scale(1.002);
        }
        
        /* 8. Badges Premium (Soft Translucent) */
        .badge {
            padding: 0.45em 0.85em !important;
            font-weight: 600 !important;
            letter-spacing: 0.4px;
            border-radius: 8px !important;
        }
        .bg-success, .badge-success { background: rgba(45,206,137,0.15) !important; color: #2dce89 !important; border: 1px solid rgba(45,206,137,0.3) !important; }
        .bg-danger, .badge-danger { background: rgba(245,54,92,0.15) !important; color: #f5365c !important; border: 1px solid rgba(245,54,92,0.3) !important; }
        .bg-warning, .badge-warning { background: rgba(251,99,64,0.15) !important; color: #fb6340 !important; border: 1px solid rgba(251,99,64,0.3) !important; }
        .bg-info, .badge-info { background: rgba(17,205,239,0.15) !important; color: #11cdef !important; border: 1px solid rgba(17,205,239,0.3) !important; }
        .bg-secondary, .badge-secondary { background: rgba(136,152,170,0.15) !important; color: #8898aa !important; border: 1px solid rgba(136,152,170,0.3) !important; }
        
        /* 9. Inputs y Switches Premium */
        .form-control {
            border-radius: 8px !important;
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: none !important;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #4facfe;
            box-shadow: 0 0 0 4px rgba(79, 172, 254, 0.15) !important;
        }
        .form-switch .form-check-input {
            width: 40px !important;
            height: 20px !important;
            cursor: pointer;
            border: none;
            background-color: #e9ecef;
            transition: background-color 0.3s ease;
        }
        .form-switch .form-check-input:checked {
            background-color: #2dce89;
        }
        
        /* 10. Sidebar & Nav Glassmorphism */
        .header {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .metismenu li a {
            transition: all 0.3s ease !important;
            border-radius: 8px;
            margin: 0 10px;
        }
        .metismenu li a:hover {
            background: rgba(79, 172, 254, 0.08) !important;
            transform: translateX(4px);
            color: #4facfe !important;
        }
        .metismenu li.mm-active > a {
            background: linear-gradient(135deg, rgba(79, 172, 254, 0.1) 0%, rgba(0, 242, 254, 0.1) 100%) !important;
            color: #4facfe !important;
            font-weight: 600;
        }
        .nav-header {
            background: #fff !important;
            border-right: 1px solid rgba(0,0,0,0.05);
        }
        
        /* 11. DataTables UX Premium */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 8px !important;
            border: none !important;
            background: transparent !important;
            transition: all 0.2s ease !important;
            font-weight: 500 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: rgba(79, 172, 254, 0.1) !important;
            color: #4facfe !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
            color: #fff !important;
            box-shadow: 0 4px 10px rgba(79, 172, 254, 0.3) !important;
        }
        table.dataTable.no-footer {
            border-bottom: 1px solid rgba(0,0,0,0.05) !important;
        }
        
        /* 12. Modal Animations Pop */
        .modal.fade .modal-dialog {
            transform: scale(0.95);
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .modal.show .modal-dialog {
            transform: scale(1);
        }
        
        /* 13. Advanced Interactivity */
        .btn:active {
            transform: scale(0.96) !important;
        }
        
        /* 14. Sidebar Shadows & Layout */
        .deznav {
            box-shadow: 2px 0 25px rgba(0,0,0,0.04) !important;
            border-right: none !important;
        }
        .content-body {
            background-color: #f8f9fe !important; /* Soft premium background */
        }
        
        /* 15. Preloader Glass */
        #preloader {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
    </style>
</head>
<body>
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <div id="main-wrapper">
        <div class="nav-header">
            <a href="{{ route('dashboard') }}" class="brand-logo d-flex align-items-center justify-content-center">
                <h3 class="mt-3 mb-0" style="letter-spacing: 1px;">INFORTECH</h3>
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="dashboard_bar">Dashboard</div>
                            <form action="{{ route('search') }}" method="GET" class="d-inline-block ms-4">
                                <div class="input-group search-area">
                                    <input type="text" name="q" class="form-control" placeholder="Buscar correos, clientes..." value="{{ request('q') }}">
                                    <span class="input-group-text"><button type="submit" class="btn p-0 m-0 border-0"><i class="flaticon-381-search-2"></i></button></span>
                                </div>
                            </form>
                        </div>
                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown" style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    <img src="{{ asset('assets/images/profile/pic1.jpg') }}" width="20" alt="" style="border-radius:50%; box-shadow: 0 2px 10px rgba(0,0,0,0.1);"/>
                                    <div class="header-info">
                                        <span class="fw-bold">{{ auth()->user()->name ?? 'Admin' }}</span>
                                        <small class="text-primary fw-semibold">{{ auth()->user()->role ?? 'User' }}</small>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a href="{{ route('profile.index') }}" class="dropdown-item ai-icon">
                                        <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        <span class="ms-2">Mi Perfil</span>
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item ai-icon" style="background: none; border: none; width: 100%; text-align: left;">
                                            <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                            <span class="ms-2">Logout </span>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <div class="deznav">
            <div class="deznav-scroll">
                <ul class="metismenu" id="menu">
                    <li class="{{ Route::is('dashboard') ? 'mm-active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="ai-icon" aria-expanded="false">
                            <i class="flaticon-381-networking"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="{{ Route::is('companies.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('companies.index') }}" class="ai-icon" aria-expanded="false">
                            <i class="flaticon-381-heart"></i>
                            <span class="nav-text">Mis Clientes</span>
                        </a>
                    </li>
                    <li class="{{ Route::is('users.*') || Route::is('audit.*') ? 'mm-active' : '' }}">
                        <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="{{ Route::is('users.*') || Route::is('audit.*') ? 'true' : 'false' }}">
                            <i class="flaticon-381-settings-2"></i>
                            <span class="nav-text">Configuración</span>
                        </a>
                        <ul aria-expanded="{{ Route::is('users.*') || Route::is('audit.*') ? 'true' : 'false' }}" class="{{ Route::is('users.*') || Route::is('audit.*') ? 'mm-show' : '' }}">
                            @if(auth()->user()->role === 'SuperAdmin')
                            <li class="{{ Route::is('users.*') ? 'mm-active' : '' }}"><a href="{{ route('users.index') }}">Gestión de Accesos</a></li>
                            <li class="{{ Route::is('audit.*') ? 'mm-active' : '' }}"><a href="{{ route('audit.index') }}">Historial de Auditoría</a></li>
                            <li><a href="{{ route('export.master') }}" class="text-success"><i class="fa fa-file-excel text-success me-1"></i> Backup Excel Maestro</a></li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <div class="content-body">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        <div class="footer">
            <div class="copyright">
                <p>Copyright © Designed &amp; Developed by <a href="#" target="_blank">Infortech</a> 2026</p>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/js/deznav-init.js') }}"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
