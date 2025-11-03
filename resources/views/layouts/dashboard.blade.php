<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Search inside a book</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/bootstrap/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">

    <!-- 🔁 Fallbacks (se o Bootstrap/FontAwesome locais falharem) -->
    <script>
        // Testa se o Bootstrap local foi carregado
        document.addEventListener("DOMContentLoaded", function() {
            const checkBootstrap = document.createElement("div");
            checkBootstrap.className = "d-none d-sm-block";
            document.body.appendChild(checkBootstrap);
            const style = window.getComputedStyle(checkBootstrap);
            if (!style.display || style.display === "block") {
                const head = document.querySelector("head");
                const bootstrapCDN = document.createElement("link");
                bootstrapCDN.rel = "stylesheet";
                bootstrapCDN.href = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css";
                head.appendChild(bootstrapCDN);

                const faCDN = document.createElement("link");
                faCDN.rel = "stylesheet";
                faCDN.href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css";
                head.appendChild(faCDN);
            }
        });
    </script>

    <style>
    /* 🔧 Corrige a sidebar deslocada */
    .main-sidebar {
        transform: none !important;
        left: 0 !important;
        visibility: visible !important;
        transition: none !important;
        position: fixed !important;
        top: 0;
        bottom: 0;
        height: 100vh !important;
        overflow-y: auto;
    }

    /* Mantém o conteúdo deslocado à direita da sidebar */
    .content-wrapper {
        margin-left: 250px !important;
        min-height: 100vh;
        background: #f4f6f9;
        overflow-x: hidden;
        padding-bottom: 50px;
    }

    body.sidebar-mini .content-wrapper,
    body.sidebar-open .content-wrapper {
        margin-left: 250px !important;
    }

    /* Ajuste visual do botão toggle (os "pontinhos") */
    .main-header .nav-link[data-widget="pushmenu"] i {
        color: #333;
    }

    /* Barra superior e footer fixos */
    .main-header {
        position: sticky;
        top: 0;
        z-index: 1030;
    }

    .main-footer {
        position: relative;
        z-index: 1030;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed sidebar-open">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <img src="{{ asset('logo_publicala.png') }}" alt="Logo" class="brand-image elevation-3"
                style="opacity:.95; width:180px; height:40px; margin-top:2px; margin-bottom:2px; background:#fff; border-radius:8px; object-fit:contain;">
            <span class="brand-text font-weight-bold ml-2"></span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="{{ route('search.index') }}" class="nav-link {{ request()->routeIs('search.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-search"></i>
                            <p>Buscar en el libro</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
    <!-- /.sidebar -->

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Header -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@yield('page_title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Conteúdo principal -->
        @yield('content')

        <!-- Footer -->
        <footer class="main-footer text-sm">
            <div class="float-right d-none d-sm-inline">
                Powered by AdminLTE & Laravel
            </div>
            <strong>&copy; 2025 Publica.la.</strong> Todos los derechos reservados.
        </footer>
    </div>
    <!-- /.content-wrapper -->
</div>
<!-- /.wrapper -->

<!-- Scripts base -->
<script src="{{ asset('AdminLTE/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('AdminLTE/dist/js/adminlte.min.js') }}"></script>

<!-- Fallback JS se os locais não carregarem -->
<script>
window.addEventListener('load', () => {
    if (typeof $.fn === 'undefined' || typeof $.fn.modal === 'undefined') {
        const jqueryCDN = document.createElement('script');
        jqueryCDN.src = "https://code.jquery.com/jquery-3.6.0.min.js";
        document.body.appendChild(jqueryCDN);

        const bootstrapCDN = document.createElement('script');
        bootstrapCDN.src = "https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js";
        document.body.appendChild(bootstrapCDN);
    }
});
</script>

<!-- Script para manter a sidebar aberta -->
<script>
(function() {
    function expandSidebar() {
        try {
            if (typeof $.fn.PushMenu === 'function') {
                try {
                    $('[data-widget="pushmenu"]').PushMenu('expand');
                } catch {
                    $('[data-widget="pushmenu"]').PushMenu('toggle');
                }
            } else {
                document.body.classList.remove('sidebar-collapse');
                document.body.classList.add('sidebar-open');
            }

            const aside = document.querySelector('.main-sidebar');
            if (aside) {
                aside.style.transform = 'none';
                aside.style.left = '0';
                aside.style.visibility = 'visible';
            }
        } catch (err) {
            console.error('Erro expandSidebar:', err);
        }
    }

    expandSidebar();
    setTimeout(expandSidebar, 300);
    setTimeout(expandSidebar, 600);

    if (localStorage.getItem('sidebar_state') === 'open') {
        setTimeout(expandSidebar, 800);
    }

    $(document).on('shown.lte.pushmenu', () => localStorage.setItem('sidebar_state','open'));
    $(document).on('collapsed.lte.pushmenu', () => localStorage.setItem('sidebar_state','closed'));
})();
</script>

</body>
</html>
