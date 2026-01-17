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
        /* ---------------------------------------------------------------------- */
        /* ----------------------- GLOBAL & BODY STYLES ----------------------- */
        /* ---------------------------------------------------------------------- */
        body {
            overflow-x: hidden;
            background-color: #f9fafb;
            font-family: 'Poppins', sans-serif;
            transition: margin-left 0.3s ease;
        }

        /* === PERBAIKAN Z-INDEX MUTLAK UNTUK MODAL === */
        .modal-backdrop {
            z-index: 1070 !important;
        }

        .modal {
            z-index: 1071 !important;
        }

        /* ---------------------------- SIDEBAR STYLES ---------------------------- */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #64707e 0%, #717d8b 100%);
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 24px 0;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            z-index: 1051;
            transition: all 0.3s ease;
        }

        /* Gaya untuk Logo/Brand Container */
        .brand {
            text-align: center;
            padding: 0 1.25rem 2rem;
            letter-spacing: -1px;
            color: white;
            overflow: hidden;
        }

        /* Gaya untuk Gambar Logo */
        #sidebarLogo {
            max-width: 100%;
            height: auto;
            max-height: 40px;
            margin: 0 auto;
            display: block;
            transition: all 0.3s ease;
        }

        .menu {
            list-style: none;
            padding: 0 0 2rem;
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

        /* Gaya Link Menu Utama */
        .menu a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 0.9rem 1.5rem;
            color: rgba(255, 255, 255, 0.92);
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            transition: all 0.25s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border-left: 4px solid transparent;
            position: relative;
        }

        .menu a:hover {
            background: rgba(255, 255, 255, 0.12);
            color: white;
            transform: translateX(4px);
        }

        .menu a.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            border-left-color: #fca311;
        }

        .menu a i {
            font-size: 1.25rem;
            min-width: 24px;
            text-align: center;
        }

        /* Gaya untuk Dropdown (Sub-menu) */
        .menu-dropdown {
            /* Gaya untuk item yang memiliki sub-menu */
        }

        .dropdown-toggle-icon {
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        .menu-dropdown.open>a .dropdown-toggle-icon {
            transform: rotate(90deg);
        }

        .submenu {
            list-style: none;
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-in-out, padding 0.3s ease;
            background: rgba(0, 0, 0, 0.1);
        }

        .menu-dropdown.open .submenu {
            max-height: 500px;
            /* Cukup besar untuk menampung semua item */
        }

        .submenu li a {
            padding: 0.7rem 1.5rem 0.7rem 3.2rem;
            /* Indentasi lebih dalam */
            font-size: 0.95rem;
            font-weight: 400;
            gap: 10px;
            border-left: none;
        }

        .submenu li a.active {
            background: rgba(0, 0, 0, 0.2);
            border-left: 4px solid #fca311;
        }


        .logout {
            padding: 0 1.25rem;
        }

        .logout-btn {
            width: 100%;
            padding: 0.85rem 1.5rem;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
            white-space: nowrap;
        }

        .logout-btn:hover {
            background: #b91c1c;
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(220, 38, 38, 0.4);
        }

        /* ---------------------------- TOPBAR STYLES ----------------------------- */
        .app-header {
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            height: 64px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            z-index: 1020;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: left 0.3s ease;
        }

        .header-inner {
            height: 64px;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .app-title {
            font-weight: 600;
            font-size: 18px;
            color: #111827;
            letter-spacing: 0.2px;
        }

        .navbar-toggler-mobile {
            border: none;
            background: none;
            font-size: 1.5rem;
            color: #111827;
            margin-right: 15px;
            cursor: pointer;
            display: none;
        }

        /* ----------------------- MAIN CONTENT STYLES -------------------------- */
        .main-content {
            margin-left: 260px;
            padding: 2rem;
            padding-top: calc(2rem + 64px);
            min-height: 100vh;
            position: relative;
            z-index: 50;
            overflow-x: auto;
            transition: margin-left 0.3s ease;
        }

        /* ----------------------- TOGGLE DESKTOP STYLES ----------------------- */

        /* 1. Sidebar menyempit */
        body.toggled .sidebar {
            width: 65px;
            padding: 24px 0 0;
        }

        /* 2. Header & Content bergeser */
        body.toggled .app-header {
            left: 65px;
        }

        body.toggled .main-content {
            margin-left: 65px;
        }

        /* 3. Penyesuaian Brand/Logo saat Toggle */
        body.toggled .brand {
            padding: 0 0 2rem;
        }

        body.toggled #sidebarLogo {
            max-height: 30px;
            width: 30px;
            height: 30px;
        }

        /* 4. Menyembunyikan teks menu/logout */
        body.toggled .menu a span,
        body.toggled .dropdown-toggle-icon {
            display: none;
        }

        body.toggled .logout {
            padding: 0;
        }

        body.toggled .logout-btn span {
            display: none;
        }

        body.toggled .logout-btn {
            justify-content: center;
            border-radius: 0;
            padding: 0.85rem 0.5rem;
        }

        /* 5. Memastikan icon menu tetap di tengah/rapi saat menyempit */
        body.toggled .menu a {
            justify-content: center;
            padding: 0.9rem 0;
            gap: 0;
        }

        /* 6. Menyembunyikan Submenu saat toggled */
        body.toggled .submenu {
            display: none !important;
        }

        /* 7. Mengubah icon toggle saat sidebar menyempit */
        body.toggled #sidebarToggleDesktop i {
            transform: rotate(180deg);
        }

        /* ---------------------------- MEDIA QUERIES ----------------------------- */
        @media (max-width: 991.98px) {

            /* Sidebar Mobile */
            .sidebar {
                transform: translateX(-260px);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            /* Topbar Mobile */
            .app-header {
                left: 0;
            }

            /* Konten Utama Mobile */
            .main-content {
                margin-left: 0;
            }

            /* Tampilkan Tombol Toggle Mobile */
            .navbar-toggler-mobile {
                display: block;
            }

            /* Sembunyikan Tombol Toggle Desktop */
            #sidebarToggleDesktop {
                display: none !important;
            }

            /* Backdrop untuk Mobile */
            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1050;
                display: none;
            }

            .sidebar-backdrop.show {
                display: block;
            }

            /* Saat mobile, class toggled di body tidak berlaku */
            body.toggled .sidebar {
                width: 260px;
                transform: translateX(-260px);
            }

            body.toggled .app-header,
            body.toggled .main-content {
                left: 0;
                margin-left: 0;
            }

            /* Pastikan Submenu tetap terlihat di mobile */
            body.toggled .submenu {
                display: block !important;
            }
        }
    </style>
</head>

<body>

    <header class="app-header">
        <div class="header-inner">
            <div class="left d-flex align-items-center">

                {{-- Tombol Toggle Desktop (NEW) --}}
                <button class="btn btn-sm text-dark me-3 d-none d-lg-block" type="button" id="sidebarToggleDesktop"
                    title="Toggle Sidebar">
                    <i class="bi bi-arrow-bar-left fs-5"></i>
                </button>

                {{-- Tombol Toggle Mobile (EXISTING) --}}
                <button class="navbar-toggler-mobile" type="button" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <span class="app-title">{{ $title ?? 'SHE – PT AICC' }}</span>
            </div>

            <div class="right">
                {{-- Asumsi Anda menggunakan Auth --}}
                <div class="user-info d-flex align-items-center me-3">
                    <span class="d-none d-lg-block" style="font-size: 14px; color: #4b5563;">
                        Halo, {{ Auth::user()->nama ?? 'Pengguna' }}
                    </span>
                </div>

                <div class="user-menu" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle user-icon"></i>
                </div>

                {{-- Dropdown Sederhana --}}
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Pengaturan Akun</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form-topbar').submit();">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    {{-- Formulir Logout untuk Topbar --}}
    <form id="logout-form-topbar" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>


    <nav class="sidebar" id="sidebar">
        {{-- LOGO BRAND AREA --}}
        <div class="brand">
            <img src="{{ asset('template/logo/logo.png') }}" alt="AICC Logo" id="sidebarLogo" class="img-fluid">
        </div>

        <ul class="menu">
            {{-- ================= DASHBOARD ================= --}}
            <li>
                <a href="{{ url('/she/dashboard') }}" class="{{ request()->is('she/dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- ================= HYARI HATTO ================= --}}
            <li>
                <a href="{{ url('/she/hyari-hatto') }}" class="{{ request()->is('she/hyari-hatto*') ? 'active' : '' }}">
                    <i class="bi bi-journal-check"></i>
                    <span>Hyari Hatto</span>
                </a>
            </li>

            {{-- ================= INSIDEN ================= --}}
            <li>
                <a href="{{ url('/she/insiden') }}" class="{{ request()->is('she/insiden*') ? 'active' : '' }}">
                    <i class="bi bi-x-octagon"></i>
                    <span>Accident</span>
                </a>
            </li>

            {{-- ================= KOMITMEN K3 ================= --}}
            <li>
                <a href="{{ url('/she/komitmen-k3') }}" class="{{ request()->is('she/komitmen-k3*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-check"></i>
                    <span>Komitmen K3</span>
                </a>
            </li>

            {{-- ================= SAFETY RIDING ================= --}}
            <li>
                <a href="{{ url('/she/safety-riding') }}"
                    class="{{ request()->is('she/safety-riding*') ? 'active' : '' }}">
                    <i class="bi bi-bicycle"></i>
                    <span>Safety Riding</span>
                </a>
            </li>

            {{-- ================= SAFETY PATROL ================= --}}
            <li>
                <a href="{{ url('/she/safety-patrol') }}"
                    class="{{ request()->is('she/safety-patrol*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i>
                    <span>Safety Patrol</span>
                </a>
            </li>

            {{-- ================= DIVIDER ================= --}}
            <li>
                <hr class="dropdown-divider my-2 mx-3" style="border-color: rgba(255,255,255,0.2);">
            </li>

            {{-- ================= DATA MASTER (DROPDOWN) ================= --}}
            @php
                $dataMasterActive =
                    request()->is('she/master/pta*') ||
                    request()->is('she/master/kta*') ||
                    request()->is('she/master/pb*') ||
                    request()->is('she/master/pf*') ||
                    request()->is('she/master/pd*');
            @endphp

            <li class="menu-dropdown {{ $dataMasterActive ? 'open' : '' }}">
                <a href="#" class="dropdown-toggle {{ $dataMasterActive ? 'active' : '' }}" data-bs-toggle="collapse"
                    data-bs-target="#submenuDataMaster" aria-expanded="{{ $dataMasterActive ? 'true' : 'false' }}">
                    <i class="bi bi-database"></i>
                    <span>Data Master</span>
                    <i class="bi bi-chevron-right dropdown-toggle-icon"></i>
                </a>

                <ul class="submenu collapse {{ $dataMasterActive ? 'show' : '' }}" id="submenuDataMaster"
                    data-bs-parent=".menu">

                    <li>
                        <a href="{{ url('/she/master/pta') }}"
                            class="{{ request()->is('she/master/pta*') ? 'active' : '' }}">
                            <i class="bi bi-dot"></i>
                            <span>Hyari - PTA</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/she/master/kta') }}"
                            class="{{ request()->is('she/master/kta*') ? 'active' : '' }}">
                            <i class="bi bi-dot"></i>
                            <span>Hyari - KTA</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/she/master/pb') }}"
                            class="{{ request()->is('she/master/pb*') ? 'active' : '' }}">
                            <i class="bi bi-dot"></i>
                            <span>Hyari - PB</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/she/master/pf') }}"
                            class="{{ request()->is('she/master/pf*') ? 'active' : '' }}">
                            <i class="bi bi-dot"></i>
                            <span>Riding - PF</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/she/master/pd') }}"
                            class="{{ request()->is('she/master/pd*') ? 'active' : '' }}">
                            <i class="bi bi-dot"></i>
                            <span>Riding - PD</span>
                        </a>
                    </li>

                </ul>
            </li>
        </ul>


        <div class="logout">
            <button type="button" class="logout-btn"
                onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
            </button>
        </div>

        {{-- Formulir Logout untuk Sidebar --}}
        <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarToggleDesktop = document.getElementById('sidebarToggleDesktop');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const body = document.body;

        // Fungsi untuk Toggle Sidebar Mobile/Backdrop
        const toggleSidebarMobile = () => {
            const isShown = sidebar.classList.toggle('show');
            sidebarBackdrop.classList.toggle('show');
            body.style.overflow = isShown ? 'hidden' : 'auto';
        };

        // Fungsi untuk Toggle Sidebar Desktop (menyempitkan)
        const toggleSidebarDesktop = () => {
            body.classList.toggle('toggled');
            // Simpan status di localStorage agar tetap pada reload
            localStorage.setItem('sidebarToggled', body.classList.contains('toggled'));
        };

        // Memuat status toggle dari localStorage saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            const toggledState = localStorage.getItem('sidebarToggled');

            if (toggledState === 'true') {
                if (window.innerWidth >= 992) {
                    body.classList.add('toggled');
                } else {
                    body.classList.remove('toggled');
                }
            }

            // Inisialisasi status dropdown saat dimuat (untuk Bootstrap Collapse)
            const activeDropdowns = document.querySelectorAll('.menu-dropdown.open');
            activeDropdowns.forEach(activeDropdown => {
                const submenu = activeDropdown.querySelector('.submenu');
                if (submenu) {
                    submenu.classList.add('show');
                }
            });
        });

        // Event Listeners
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebarMobile);
        }

        if (sidebarToggleDesktop) {
            sidebarToggleDesktop.addEventListener('click', toggleSidebarDesktop);
        }

        // Tutup sidebar mobile saat backdrop diklik
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', () => {
                if (sidebar.classList.contains('show')) {
                    toggleSidebarMobile();
                }
            });
        }

        // Tutup sidebar saat menu diklik (hanya di mobile)
        const menuLinks = document.querySelectorAll('.menu a:not(.dropdown-toggle)');
        menuLinks.forEach(link => {
            link.addEventListener('click', () => {
                // Hanya tutup jika bukan link yang membuka collapse
                if (window.innerWidth < 992 && sidebar.classList.contains('show')) {
                    toggleSidebarMobile();
                }
            });
        });

        // Handle Toggle Dropdown Manual (menambahkan/menghapus class 'open' untuk CSS custom)
        document.querySelectorAll('.menu-dropdown .dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', function (e) {
                // Mencegah navigasi jika href="#"
                e.preventDefault();

                const parentLi = this.closest('.menu-dropdown');
                const submenu = parentLi.querySelector('.submenu');
                const isOpening = !parentLi.classList.contains('open');

                // Tutup dropdown lain (jika ingin single-open)
                document.querySelectorAll('.menu-dropdown.open').forEach(openLi => {
                    if (openLi !== parentLi) {
                        openLi.classList.remove('open');
                        const collapseElement = openLi.querySelector('.submenu');
                        if (collapseElement) {
                            new bootstrap.Collapse(collapseElement, { toggle: false }).hide();
                        }
                    }
                });

                // Toggle class 'open' pada parent <li>
                parentLi.classList.toggle('open', isOpening);

                // Toggle Bootstrap Collapse
                if (submenu) {
                    new bootstrap.Collapse(submenu, { toggle: false }).toggle();
                }
            });
        });


        // Handle resize: Menjaga konsistensi saat jendela diubah ukurannya
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                // Saat kembali ke desktop, pastikan mobile "show" hilang
                sidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
                document.body.style.overflow = 'auto';

                // Terapkan status toggle desktop dari localStorage
                if (localStorage.getItem('sidebarToggled') === 'true') {
                    body.classList.add('toggled');
                }
            } else {
                // Saat masuk mode mobile, hapus class 'toggled' desktop
                body.classList.remove('toggled');
            }
        });
    </script>
    @yield('modals')
</body>

</html>