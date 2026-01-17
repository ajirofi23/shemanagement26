@extends('layout.itsidebar')

@section('content')

{{-- CUSTOM CSS UNTUK UI MODERN --}}
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-shadow: 0 8px 32px rgba(31, 38, 135, 0.07);
        --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }

    /* Animasi Halaman */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    /* Card & Container Style */
    .content-modern {
        background-color: #f8f9fc;
        min-height: 100vh;
    }

    .card-custom {
        background: var(--glass-bg);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: var(--glass-shadow);
        border-radius: 15px;
        transition: all 0.3s ease;
    }

    /* Table Style */
    .table-modern thead th {
        background-color: #f1f4f8;
        color: #495057;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        font-weight: 700;
        border: none;
        padding: 15px;
    }

    .table-modern tbody tr {
        transition: transform 0.2s ease;
    }

    .table-modern tbody tr:hover {
        background-color: #f8f9ff !important;
        transform: scale(1.002);
    }

    /* Input & Button Style */
    .form-control-modern {
        border-radius: 10px;
        border: 1.5px solid #e3e6f0;
        padding: 10px 15px;
        transition: all 0.2s;
    }

    .form-control-modern:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25 margin rgba(78, 115, 223, 0.1);
    }

    .btn-gradient {
        background: var(--primary-gradient);
        border: none;
        color: white;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        color: white;
    }

    /* Badge Style */
    .badge-soft-info {
        background-color: #e3f2fd;
        color: #0d6efd;
        border: 1px solid #bbdefb;
    }

    .icon-preview {
        width: 35px;
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fc;
        border-radius: 8px;
    }
</style>

<div class="content content-modern p-4">

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4 animate-up">
        <div>
            <h3 style="color:#1e293b;" class="fw-bold mb-1">
                <i class="bi bi-layers-fill me-2 text-primary"></i> Manajemen Menu
            </h3>
            <p class="text-muted small mb-0">Kelola hierarki dan pengaturan akses navigasi aplikasi</p>
        </div>
        
        <button type="button" class="btn btn-gradient shadow-sm" data-bs-toggle="modal" data-bs-target="#addMenuModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah Menu Baru
        </button>
    </div>
    
    <div class="card card-custom border-0 animate-up">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                    <i class="bi bi-diagram-3 text-primary fs-5"></i>
                </div>
                <h5 class="card-title mb-0 fw-bold" style="color: #334155;">Struktur Navigasi Menu</h5>
            </div>

            {{-- Filter & Search Section --}}
            <div class="row g-3 mb-4 align-items-end">
                <div class="col-md-5">
                    <form action="{{ url('/it/management-menu') }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 rounded-start-3">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" 
                                   class="form-control form-control-modern border-start-0" 
                                   placeholder="">
                            <button type="submit" class="btn btn-primary rounded-end-3 px-4">Cari</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-7 text-md-end">
                    <div class="p-2 bg-light rounded-3 d-inline-block border">
                        <span class="text-muted small">Total Menu Terdaftar: </span>
                        <span class="badge bg-primary rounded-pill ms-1">{{ $menus->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Tabel Menu --}}
            <div class="table-responsive">
                <table class="table table-modern align-middle">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Urutan</th>
                            <th>Identitas Menu</th>
                            <th>Routing (URL)</th>
                            <th class="text-center">Parent</th>
                            <th>Icon</th>
                            <th>Group</th>
                            <th class="text-center">Extra Perm</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $menu)
                        <tr>
                            <td class="text-center text-muted">{{ $loop->iteration }}</td>
                            <td class="text-center">
                                <div class="fw-bold text-primary bg-primary bg-opacity-10 rounded p-1">{{ $menu->urutan_menu }}</div>
                            </td>
                            <td>
                                <span class="fw-bold d-block text-dark">{{ $menu->menu_name }}</span>
                                <small class="text-muted">ID: #{{ $menu->id }}</small>
                            </td>
                            <td>
                                <code class="px-2 py-1 bg-light rounded text-danger small" style="border: 1px solid #eee;">{{ $menu->url }}</code>
                            </td>
                            <td class="text-center">
                                @if($menu->parent_id)
                                    <span class="badge bg-secondary rounded-pill">{{ $menu->parent_id }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="icon-preview shadow-sm border">
                                    <i class="{{ $menu->icon }} text-primary"></i>
                                </div>
                                <span class="ms-2 small text-muted font-monospace">{{ str_replace('bi bi-', '', $menu->icon) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-soft-info rounded-pill px-3">{{ strtoupper($menu->apps_group) }}</span>
                            </td>
                            <td class="text-center">
                                @if($menu->has_extra_permissions)
                                    <div class="text-success"><i class="bi bi-check-circle-fill me-1"></i><small>Yes</small></div>
                                @else
                                    <div class="text-muted"><i class="bi bi-dash-circle me-1"></i><small>No</small></div>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm rounded">
                                    <button type="button" class="btn btn-sm btn-white border" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editMenuModal{{ $menu->id }}">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </button>

                                    <form action="{{ url('/it/management-menu/destroy/' . $menu->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-white border" onclick="return confirm('Hapus menu {{ $menu->menu_name }}?')">
                                            <i class="bi bi-trash3 text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/gray/box.svg" style="width: 150px;" class="mb-3">
                                <p class="text-muted fw-bold">Ops! Data menu masih kosong.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
const placeholders = ["Cari Nama Menu...", "Contoh: /it/dashboard", "Filter by Group...", "bi bi-gear"];
let current = 0; let index = 0; let isDeleting = false;
const speed = 100; const delay = 1500;
const input = document.getElementById("searchInput");

function typePlaceholder() {
    const currentPlaceholder = placeholders[current];
    if (isDeleting) {
        input.placeholder = currentPlaceholder.substring(0, index);
        index--;
        if (index < 0) { isDeleting = false; current = (current + 1) % placeholders.length; }
    } else {
        input.placeholder = currentPlaceholder.substring(0, index);
        index++;
        if (index > currentPlaceholder.length) { isDeleting = true; setTimeout(typePlaceholder, delay); return; }
    }
    setTimeout(typePlaceholder, speed);
}
if (input && !input.value) typePlaceholder();
</script>

@endsection

@section('modals')

<style>
    .modal-content-modern {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }
    .modal-header-modern {
        background: var(--primary-gradient);
        color: white;
        padding: 20px 25px;
    }
    .form-label-modern {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 6px;
        font-weight: 600;
    }
</style>

{{-- Modal Tambah Menu --}}
<div class="modal fade" id="addMenuModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"> 
        <div class="modal-content modal-content-modern shadow-lg">
            <div class="modal-header modal-header-modern">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill me-2"></i> Konfigurasi Menu Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/it/management-menu/store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light bg-opacity-50">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card p-3 border-0 shadow-sm rounded-4">
                                <label class="form-label form-label-modern">Visual & Label</label>
                                <div class="mb-3">
                                    <label class="small text-muted mb-1">Nama Tampilan</label>
                                    <input type="text" name="menu_name" class="form-control form-control-modern" required placeholder="User Management">
                                </div>
                                <div class="mb-3">
                                    <label class="small text-muted mb-1">Bootstrap Icon Class</label>
                                    <input type="text" name="icon" class="form-control form-control-modern" placeholder="bi bi-person">
                                </div>
                                <div class="mb-0">
                                    <label class="small text-muted mb-1">Target URL</label>
                                    <input type="text" name="url" class="form-control form-control-modern" required placeholder="/it/user-management">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 border-0 shadow-sm rounded-4">
                                <label class="form-label form-label-modern">Struktur & Grouping</label>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="small text-muted mb-1">No. Urut</label>
                                        <input type="number" name="urutan_menu" class="form-control form-control-modern" required placeholder="1">
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted mb-1">Parent ID</label>
                                        <input type="number" name="parent_id" class="form-control form-control-modern" placeholder="0">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="small text-muted mb-1">Apps Group Tag</label>
                                    <input type="text" name="apps_group" class="form-control form-control-modern" required placeholder="Contoh: IT">
                                </div>
                                <div class="mb-0">
                                    <label class="small text-muted mb-1">Fitur Tambahan</label>
                                    <select name="has_extra_permissions" class="form-select form-control-modern">
                                        <option value="0">Basic Permission</option>
                                        <option value="1">Extra Permission Control</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white p-3 border-0 shadow-sm">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow">Simpan Data Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Menu --}}
@foreach($menus as $menu)
<div class="modal fade" id="editMenuModal{{ $menu->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"> 
        <div class="modal-content modal-content-modern shadow-lg">
            <div class="modal-header modal-header-modern bg-primary">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Update Menu Konfigurasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ url('/it/management-menu/update/' . $menu->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 bg-light">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card p-3 border-0 shadow-sm rounded-4">
                                <div class="mb-3">
                                    <label class="form-label form-label-modern">Nama Menu</label>
                                    <input type="text" name="menu_name" class="form-control form-control-modern" value="{{ $menu->menu_name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label form-label-modern">URL Routing</label>
                                    <input type="text" name="url" class="form-control form-control-modern" value="{{ $menu->url }}" required>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label form-label-modern">Icon (BI Class)</label>
                                    <input type="text" name="icon" class="form-control form-control-modern" value="{{ $menu->icon }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card p-3 border-0 shadow-sm rounded-4">
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <label class="form-label form-label-modern">Sort Order</label>
                                        <input type="number" name="urutan_menu" class="form-control form-control-modern" value="{{ $menu->urutan_menu }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label form-label-modern">Parent ID</label>
                                        <input type="number" name="parent_id" class="form-control form-control-modern" value="{{ $menu->parent_id }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label form-label-modern">Apps Group</label>
                                    <input type="text" name="apps_group" class="form-control form-control-modern" value="{{ $menu->apps_group }}" required>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label form-label-modern">Extra Perm?</label>
                                    <select name="has_extra_permissions" class="form-select form-control-modern">
                                        <option value="0" {{ $menu->has_extra_permissions == 0 ? 'selected' : '' }}>No</option>
                                        <option value="1" {{ $menu->has_extra_permissions == 1 ? 'selected' : '' }}>Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-primary rounded-pill shadow px-4">Update Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection