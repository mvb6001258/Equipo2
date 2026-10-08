<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Trazabilidad Agrícola Blockchain') | agroTrace</title>

    <!-- Google Font: Source Sans Pro & JetBrains Mono -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AdminLTE 3 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- STRICT FLAT UI OVERRIDES (Zero Gradients, Zero Heavy Shadows, Industrial & Institutional) -->
    <style>
        /* Flat UI Engine Reset */
        *, *::before, *::after {
            box-shadow: none !important;
            text-shadow: none !important;
        }

        body {
            background-color: #f4f6f9;
            font-family: 'Source Sans Pro', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        .main-header, .main-sidebar, .card, .btn, .info-box, .modal-content, 
        .timeline-item, .badge, .form-control, .input-group-text, .alert {
            background-image: none !important;
            box-shadow: none !important;
            border-radius: 2px !important;
        }

        .card {
            border: 1px solid #dce2e6 !important;
            margin-bottom: 1.5rem;
        }

        .card-header {
            border-bottom: 1px solid #dce2e6 !important;
            font-weight: 700;
        }

        /* Navbar & Sidebar Flat Styling */
        .main-header {
            border-bottom: 2px solid #002b49 !important;
        }

        .brand-link {
            border-bottom: 1px solid #1a3a5c !important;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        /* Timeline Flat Customization */
        .timeline > div > .timeline-item {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-left: 4px solid #17a2b8 !important;
        }

        .timeline > div > .timeline-item.block-genesis {
            border-left: 4px solid #28a745 !important;
        }

        .timeline > div > .timeline-item > .timeline-header {
            border-bottom: 1px solid #f1f5f9;
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
        }

        /* Monospace Hash Blocks */
        .hash-code {
            font-family: 'Courier New', Consolas, Monaco, monospace;
            font-size: 0.85rem;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 4px 8px;
            color: #1e293b;
            display: inline-block;
            word-break: break-all;
        }

        .hash-badge-prev {
            border-left: 3px solid #64748b !important;
        }

        .hash-badge-curr {
            border-left: 3px solid #2563eb !important;
        }

        .block-counter {
            font-family: monospace;
            font-size: 0.9rem;
            font-weight: bold;
        }
    </style>
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-dark bg-navy">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('traceability.index') }}" class="nav-link active"><i class="fas fa-cubes mr-1"></i> Panel de Trazabilidad</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <span class="nav-link text-uppercase text-xs font-weight-bold tracking-wider text-light">
                    <i class="fas fa-shield-halved text-success mr-1"></i> Criptografía SHA-256 Activa
                </span>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-navy elevation-0">
        <!-- Brand Logo -->
        <a href="{{ route('traceability.index') }}" class="brand-link bg-navy text-white text-center">
            <i class="fas fa-link text-success mr-2"></i>
            <span class="brand-text font-weight-bold">agroTrace<span class="text-success">Chain</span></span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-3">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-header text-uppercase text-xs font-weight-bold text-muted">Módulos Core</li>
                    <li class="nav-item">
                        <a href="{{ route('traceability.index') }}" class="nav-link active">
                            <i class="nav-icon fas fa-boxes-stacked"></i>
                            <p>Lotes Agrícolas</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header bg-white border-bottom mb-3 py-3">
            <div class="container-fluid">
                <div class="row mb-1">
                    <div class="col-sm-6">
                        <h1 class="m-0 font-weight-bold text-navy">@yield('page_title', 'Trazabilidad Agrícola Blockchain')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right bg-transparent m-0 p-0 text-sm">
                            <li class="breadcrumb-item"><a href="{{ route('traceability.index') }}">Inicio</a></li>
                            <li class="breadcrumb-item active">@yield('breadcrumb', 'Panel General')</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 rounded-0" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-0" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i> Por favor verifique los siguientes errores:
                        <ul class="mb-0 mt-1 pl-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer text-sm border-top bg-white">
        <div class="float-right d-none d-sm-inline">
            <strong>Algoritmo:</strong> SHA-256 Immutable Ledger
        </div>
        <strong>Demostración Universitaria &copy; {{ date('Y') }} <a href="#" class="text-navy">agroTrace Blockchain</a>.</strong> Sistema de Trazabilidad Agroindustrial.
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

@stack('scripts')
</body>
</html>
