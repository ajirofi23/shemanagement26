<header class="app-header">
    <div class="header-inner">
        <div class="left">
            <span class="app-title">{{ $title ?? 'SHE – PT AICC' }}</span>
        </div>

        <div class="right">
            <div class="user-menu" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle user-icon"></i>
            </div>
            
            {{-- Tambahkan Dropdown Menu sederhana jika diperlukan --}}
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Pengaturan Akun</a></li>
                <li><hr class="dropdown-divider"></li>
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

{{-- Formulir Logout Tersembunyi untuk Topbar Dropdown --}}
<form id="logout-form-topbar" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

<style>
    /* ------------------------------------------------------------- */
    /* --- PENTING: Penyesuaian Z-Index untuk Modal Fix --- */
    /* ------------------------------------------------------------- */
    /* Z-Index Bootstrap Modal Backdrop default = 1040. Kita naikkan modal di atasnya. */
    .modal-backdrop {
        z-index: 1059 !important; /* Ditingkatkan */
    }
    .modal {
        z-index: 1060 !important; /* Ditingkatkan, harus paling tinggi */
    }

    /* ------------------------------------------------------------- */
    /* --- Topbar Style --- */
    /* ------------------------------------------------------------- */
    .app-header {
        position: fixed;
        top: 0;
        left: 260px; 
        right: 0;
        height: 64px;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        /* Z-Index Header harus di bawah Modal (1060) dan Backdrop (1059) */
        z-index: 1020; /* Nilai yang aman, di atas konten normal */
        font-family: 'Poppins', sans-serif;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); /* Tambah shadow agar terlihat terangkat */
    }

    .header-inner {
        height: 64px;
        padding: 0 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .right {
        /* Memastikan ikon dan dropdown berjalan dengan baik */
        display: flex; 
        align-items: center;
    }

    .app-title {
        font-weight: 600;
        font-size: 18px;
        color: #111827;
        letter-spacing: 0.2px;
    }

    .user-menu {
        cursor: pointer;
        display: flex;
        align-items: center;
    }

    .user-icon {
        font-size: 28px;
        color: #4b5563;
        transition: 0.2s ease;
    }

    .user-icon:hover {
        color: #111827;
        transform: scale(1.08);
    }

    /* ------------------------------------------------------------- */
    /* --- PENTING: Pastikan Konten Utama memiliki padding-top --- */
    /* ------------------------------------------------------------- */
    /* Jika .main-content ada di file layout terpisah, pastikan CSS ini diterapkan di sana: */
    /*
    .main-content {
        padding-top: calc(2rem + 64px) !important; 
    }
    */
</style>