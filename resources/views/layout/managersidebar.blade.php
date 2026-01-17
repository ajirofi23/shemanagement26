<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHE Dashboard – PT AICC</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* ========================================================= */
        /* GLOBAL */
        /* ========================================================= */
        body {
            overflow-x: hidden;
            background-color: #f9fafb;
            font-family: 'Poppins', sans-serif;
        }

        /* ========================================================= */
        /* FIX MODAL DI ATAS SIDEBAR (PALING PENTING) */
        /* ========================================================= */
        .modal-backdrop {
            z-index: 2000 !important;
        }

        .modal {
            z-index: 2001 !important;
        }

        body.modal-open {
            overflow: hidden;
        }

        /* ========================================================= */
        /* SIDEBAR */
        /* ========================================================= */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #64707e 0%, #717d8b 100%);
            color: #fff;
            padding: 24px 0;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.2);
            z-index: 1040;
            /* DI BAWAH MODAL */
            transition: all 0.3s ease;
        }

        .brand {
            text-align: center;
            padding: 0 1.25rem 2rem;
        }

        #sidebarLogo {
            max-height: 40px;
            transition: all 0.3s ease;
        }

        .menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex: 1;
            overflow-y: auto;
            /* Enable vertical scrolling */
            overflow-x: hidden;
            /* Hide horizontal scrolling */
        }

        /* Custom Scrollbar for Menu */
        .menu::-webkit-scrollbar {
            width: 5px;
        }

        .menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .menu::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }

        .menu::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255, 255, 255, 0.4);
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 0.9rem 1.5rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.25s ease;
        }

        .menu a:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateX(4px);
        }

        .menu a.active {
            background: rgba(255, 255, 255, 0.25);
            border-left-color: #fca311;
        }

        .menu i {
            font-size: 1.25rem;
            min-width: 24px;
            text-align: center;
        }

        .logout {
            padding: 0 1.25rem;
        }

        .logout-btn {
            width: 100%;
            padding: 0.85rem 1.5rem;
            background: #dc2626;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* ========================================================= */
        /* TOPBAR */
        /* ========================================================= */
        .app-header {
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            height: 64px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            z-index: 1020;
            transition: left 0.3s ease;
        }

        .header-inner {
            height: 64px;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ========================================================= */
        /* MAIN CONTENT (NO STACKING CONTEXT!) */
        /* ========================================================= */
        .main-content {
            margin-left: 260px;
            padding: 2rem;
            padding-top: calc(2rem + 64px);
            min-height: 100vh;
            position: relative;
            z-index: auto;
            /* PENTING */
        }

        /* ========================================================= */
        /* MOBILE */
        /* ========================================================= */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-260px);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .app-header {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1035;
                display: none;
            }

            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>
</head>

<body>

    <header class="app-header">
        <div class="header-inner">
            <span class="fw-semibold">Manager – PT AICC</span>
            <span class="text-muted">Halo, {{ Auth::user()->nama ?? 'User' }}</span>
        </div>
    </header>

    <nav class="sidebar" id="sidebar">
        <div class="brand">
            <img src="{{ asset('template/logo/logo.png') }}" id="sidebarLogo">
        </div>

        <ul class="menu">
            {{-- DASHBOARD --}}
            <li>
                <a href="{{ url('/manager/dashboard') }}"
                    class="{{ request()->is('manager/dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <hr class="text-white opacity-50 mx-3">

            {{-- PROGRAM SAFETY --}}
            <li>
                <a href="{{ url('/manager/programsafety') }}"
                    class="{{ request()->is('manager/programsafety*') ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i>
                    <span>Program Safety</span>
                </a>
            </li>

            {{-- HYARI HATTO --}}
            <li>
                <a href="{{ url('/manager/hyari-hatto') }}"
                    class="{{ request()->is('manager/hyari-hatto*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i>
                    <span>Hyari Hatto</span>
                </a>
            </li>

            {{-- INSIDEN --}}
            <li>
                <a href="{{ url('/manager/insiden') }}" class="{{ request()->is('manager/insiden*') ? 'active' : '' }}">
                    <i class="bi bi-x-octagon"></i>
                    <span>Accident</span>
                </a>
            </li>

            {{-- KOMITMEN K3 --}}
            <li>
                <a href="{{ url('/manager/komitmen-k3') }}"
                    class="{{ request()->is('manager/komitmen-k3*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-check"></i>
                    <span>Komitmen K3</span>
                </a>
            </li>

            {{-- TEMUAN SAFETY RIDING --}}
            <li>
                <a href="{{ url('/manager/safety-riding') }}"
                    class="{{ request()->is('manager/safety-riding*') ? 'active' : '' }}">
                    <i class="bi bi-bicycle"></i>
                    <span>Temuan Safety Riding</span>
                </a>
            </li>

            {{-- TEMUAN SAFETY PATROL --}}
            <li>
                <a href="{{ url('/manager/safety-patrol') }}"
                    class="{{ request()->is('manager/safety-patrol*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i>
                    <span>Temuan Safety Patrol</span>
                </a>
            </li>
        </ul>


        <div class="logout">
            <button class="logout-btn"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </nav>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="main-content">
        @yield('content')

        <footer class="mt-4 text-center text-muted small">
            &copy; 2026 Safecore Team - HorizonU. All Rights Reserved.
        </footer>
    </div>

    {{-- MODAL HARUS DI SINI --}}
    @yield('modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>