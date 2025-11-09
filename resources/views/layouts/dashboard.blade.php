<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Dashboard - Search inside a book</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/bootstrap/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/adminlte.min.css') }}">

    <style>
        /* Reset completo */
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: Arial, sans-serif;
        }

        /* Sidebar customizado */
        #custom-sidebar {
            position: fixed !important;
            left: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 250px !important;
            background: #222 !important;
            color: #fff !important;
            z-index: 9999 !important;
            overflow: hidden !important;
        }

        /* Conteúdo principal */
        .content-wrapper {
            margin-left: 250px !important;
            padding: 20px !important;
            background: #f4f6f9 !important;
            min-height: 100vh !important;
        }

        /* Links do menu */
        #custom-sidebar a {
            color: #fff !important;
            text-decoration: none !important;
            display: block !important;
            padding: 12px 24px !important;
            transition: background-color 0.3s !important;
        }

        #custom-sidebar a:hover {
            background-color: #333 !important;
        }

        /* Ocultar qualquer elemento suspeito */
        [style*="color: red"],
        [style*="color:red"],
        [style*="background: red"],
        [style*="background:red"],
        [id*="extension"],
        [class*="extension"],
        .text-danger {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }
    </style>
</head>

<body class="hold-transition layout-fixed sidebar-open">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
            </li>
        </ul>
    </nav>

    <!-- Sidebar completamente novo -->
    <div id="custom-sidebar" style="position:fixed;left:0;top:0;bottom:0;width:250px;background:#222;color:#fff;padding:20px 0;z-index:1040;overflow:hidden;">
        <div style="text-align:center;margin-bottom:24px;background:#222;">
            <div style="width:180px;height:40px;background:#fff;border-radius:8px;margin:0 auto;display:flex;align-items:center;justify-content:center;font-weight:bold;color:#222;">
                Publica.la
            </div>
        </div>
        <div style="background:#222;">
            <div style="margin-bottom:16px;">
                <a href="{{ route('dashboard') }}" style="color:#fff;text-decoration:none;font-weight:bold;display:block;padding:12px 24px;background:#222;border:none;">
                    📊 Dashboard
                </a>
            </div>
            <div>
                <a href="{{ route('search.index') }}" style="color:#fff;text-decoration:none;font-weight:bold;display:block;padding:12px 24px;background:#222;border:none;">
                    🔍 Buscar en el libro
                </a>
            </div>
        </div>
    </div>

    <!-- Conteúdo -->
    <div class="content-wrapper p-4" style="background:#f4f6f9;min-height:100vh;">
        <section class="content-header">
            <div class="container-fluid">
                <h1>@yield('page_title', 'Dashboard')</h1>
            </div>
        </section>

        @yield('content')

        <footer class="main-footer text-sm">
            <div class="float-right d-none d-sm-inline">Powered by AdminLTE & Laravel</div>
            <strong>&copy; 2025 Publica.la.</strong> Todos los derechos reservados.
        </footer>
    </div>
</div>

<!-- Scripts básicos -->
<script src="{{ asset('AdminLTE/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('AdminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE removido para evitar interferências -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Limpeza total do DOM - remove qualquer elemento suspeito
        function cleanSidebar() {
            const sidebar = document.getElementById('custom-sidebar');
            if (sidebar) {
                // Remove qualquer elemento filho que não deveria estar lá
                const allowedElements = sidebar.querySelectorAll('div, a');
                const allElements = sidebar.querySelectorAll('*');
                
                allElements.forEach(el => {
                    const text = el.textContent || '';
                    const hasRedStyle = el.style.color === 'red' || 
                                       el.style.backgroundColor === 'red' ||
                                       el.getAttribute('style')?.includes('red');
                    
                    if (hasRedStyle || 
                        text.includes('debug') || 
                        text.includes('Debug') || 
                        text.includes('DEBUG') ||
                        el.id?.includes('extension') ||
                        el.className?.includes('extension')) {
                        el.remove();
                    }
                });
            }
        }

        // Executa limpeza
        cleanSidebar();
        
        // Executa limpeza a cada 100ms por 5 segundos para capturar injeções tardias
        let cleanupCount = 0;
        const cleanupInterval = setInterval(() => {
            cleanSidebar();
            cleanupCount++;
            if (cleanupCount > 50) { // 5 segundos
                clearInterval(cleanupInterval);
            }
        }, 100);
    });
</script>
</body>
</html>
