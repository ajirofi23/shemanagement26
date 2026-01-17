@extends('layout.itsidebar')

@section('content')

{{-- CUSTOM CSS UNTUK UI MODERN & ANIMASI --}}
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    }

    /* Animasi Fade In Up */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-fade-in {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    /* Card Modern */
    .card-modern {
        background: var(--glass-bg);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: var(--glass-shadow);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease;
    }

    /* Input Styling */
    .form-control-modern {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 10px 15px;
        transition: all 0.3s;
    }
    .form-control-modern:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    /* Table Styling */
    .table-modern thead th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
        padding: 15px;
    }
    .table-modern tbody tr {
        transition: all 0.2s ease-in-out;
    }
    .table-modern tbody tr:hover {
        background-color: #f1f5f9;
        transform: scale(1.005);
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        z-index: 10;
        position: relative;
    }
    .table-modern td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    /* Badge Soft Styling */
    .badge-soft-success { background-color: #dcfce7; color: #166534; padding: 6px 12px; border-radius: 20px; }
    .badge-soft-danger { background-color: #fee2e2; color: #991b1b; padding: 6px 12px; border-radius: 20px; }
    .badge-soft-info { background-color: #e0f2fe; color: #075985; padding: 6px 12px; border-radius: 20px; }
    
    /* Button Hover Effects */
    .btn-hover-scale {
        transition: transform 0.2s;
    }
    .btn-hover-scale:hover {
        transform: translateY(-2px);
    }
    
    /* Modal Modern */
    .modal-content-modern {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    }
    .modal-header-modern {
        background: var(--primary-gradient);
        color: white;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }
</style>

{{-- UBAH p-4 MENJADI p-3 pada div content --}}
<div class="content p-4 animate-fade-in" style="background-color: #f8fafc; min-height: 100vh;">

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 style="color:#1e293b;" class="fw-bold mb-1">
                <i class="bi bi-people-fill me-2 text-primary"></i> Manajemen Pengguna
            </h3>
            <p class="text-muted mb-0 ms-1 small">Kelola data pengguna, akses, dan perizinan sistem.</p>
        </div>
        
        {{-- Tombol Tambah User --}}
        <button type="button" class="btn btn-primary shadow-lg btn-hover-scale rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#addUserModal" style="background: var(--primary-gradient); border:none;">
            <i class="bi bi-plus-circle me-2"></i> Tambah User Baru
        </button>
    </div>
    
    <div class="card card-modern border-0">
        {{-- UBAH p-4 MENJADI p-3 pada card-body --}}
        <div class="card-body p-4">
            
            {{-- Search & Export --}}
            <div class="row g-3 justify-content-between align-items-center mb-4">
                <div class="col-md-6">
                     <h5 class="card-title fs-5 text-secondary fw-bold mb-0">
                        <i class="bi bi-list-columns-reverse me-2"></i> Daftar User Aktif
                    </h5>
                </div>
                <div class="col-md-6 d-flex justify-content-md-end gap-2">
                     {{-- Search --}}
                    <form action="{{ url('/it/management-user') }}" method="GET" class="d-flex flex-grow-1" style="max-width: 350px;">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" class="form-control border-start-0 rounded-end-pill py-2" placeholder="Cari data..." style="border-color: #dee2e6;">
                        </div>
                    </form>

                    {{-- Export --}}
                    <a href="#" class="btn btn-success text-white shadow-sm rounded-pill px-3 d-flex align-items-center btn-hover-scale" style="background: #10b981; border:none;">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i> CSV
                    </a>
                </div>
            </div>

            {{-- Tabel User --}}
            <div class="table-responsive rounded-3">
                <table class="table table-modern table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center" width="5%">#</th>
                            <th scope="col">Profil User</th>
                            <th scope="col">Section</th>
                            <th scope="col">Kontak</th>
                            <th scope="col">Status</th>
                            <th scope="col">Level</th>
                            <th scope="col" class="text-center">PC?</th>
                            <th scope="col" class="text-center">Sign</th>
                            <th scope="col" class="text-end" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold me-3" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                        {{ substr($user->nama, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->nama }}</div>
                                        <div class="small text-muted"><i class="bi bi-person me-1"></i>{{ $user->usr }} <span class="mx-1">•</span> <i class="bi bi-upc-scan me-1"></i>{{ $user->kode_user }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->section_id == 1) <span class="badge bg-light text-dark border">IT</span>
                                @elseif($user->section_id == 2) <span class="badge bg-light text-dark border">HRD</span>
                                @elseif($user->section_id == 3) <span class="badge bg-light text-dark border">PROD</span>
                                @else <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="small text-muted">{{ $user->email }}</div>
                                <div class="small text-muted">{{ $user->no_hp }}</div>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge badge-soft-success"><i class="bi bi-check-circle-fill me-1"></i> Aktif</span>
                                @else
                                    <span class="badge badge-soft-danger"><i class="bi bi-x-circle-fill me-1"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td><span class="badge badge-soft-info">{{ strtoupper($user->level) }}</span></td>
                            <td class="text-center">
                                @if($user->is_user_computer)
                                    <i class="bi bi-pc-display text-primary fs-5" title="User Komputer"></i>
                                @else
                                    <i class="bi bi-dash-lg text-muted"></i>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($user->image_sign)
                                    <img src="{{ asset('images/sign/' . $user->image_sign) }}" width="35" height="35" class="rounded-circle border shadow-sm" style="object-fit: cover;">
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    {{-- Tombol Edit --}}
                                    @if(isset($permission) && $permission->can_edit)
                                    <button type="button" class="btn btn-sm btn-outline-primary border-0 rounded-circle me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editUserModal{{ $user->id }}"
                                        title="Edit User" style="width: 32px; height: 32px; padding: 0;">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    @endif

                                    {{-- Tombol Delete --}}
                                    @if(isset($permission) && $permission->can_delete)
                                    <form action="{{ url('/it/management-user/destroy/' . $user->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle" 
                                            onclick="return confirm('Yakin hapus user {{ $user->nama }}?')" 
                                            title="Hapus User" style="width: 32px; height: 32px; padding: 0;">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Empty State (Optional Visual) --}}
            @if(count($users) == 0)
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2">Belum ada data user.</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Animasi Placeholder (Tidak diubah logikanya, hanya target input disesuaikan) --}}
<script>
const placeholders = ["Cari Nama...", "Cari Username...", "Cari Kode User...", "Cari Email..."];
let current = 0;
let index = 0;
let isDeleting = false;
const speed = 100;
const delay = 1500;
const input = document.getElementById("searchInput");

function typePlaceholder() {
    const currentPlaceholder = placeholders[current];
    if (isDeleting) {
        input.placeholder = currentPlaceholder.substring(0, index);
        index--;
        if (index < 0) {
            isDeleting = false;
            current = (current + 1) % placeholders.length;
        }
    } else {
        input.placeholder = currentPlaceholder.substring(0, index);
        index++;
        if (index > currentPlaceholder.length) {
            isDeleting = true;
            setTimeout(typePlaceholder, delay);
            return;
        }
    }
    setTimeout(typePlaceholder, speed);
}

if (input && !input.value) {
    typePlaceholder();
}
</script>

@endsection

{{---------------------------------------------------------------------------------}}
{{--- SECTION MODALS ---}}
{{---------------------------------------------------------------------------------}}
@section('modals')

{{-- Modal Tambah User --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"> 
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern px-4 py-3">
                <h5 class="modal-title fw-bold" id="addUserModalLabel"><i class="bi bi-person-plus-fill me-2"></i> Tambah User Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ url('/it/management-user/store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        {{-- Kiri --}}
                        <div class="col-12 col-md-6">
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">Informasi Akun</h6>
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control form-control-modern" placeholder="Cth: John Doe" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">Username <span class="text-danger">*</span></label>
                                <input type="text" name="usr" class="form-control form-control-modern" placeholder="johndoe" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control form-control-modern" placeholder="••••••••" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control form-control-modern border-start-0 ps-0" placeholder="email@example.com">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">No HP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="no_hp" class="form-control form-control-modern border-start-0 ps-0" placeholder="0812...">
                                </div>
                            </div>
                        </div>
                        
                        {{-- Kanan --}}
                        <div class="col-12 col-md-6">
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">Akses & Detail</h6>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label small text-muted fw-bold">Section</label>
                                    <select name="section_id" class="form-select form-control-modern">
                                        <option value="">Tidak Ada</option>
                                        <option value="1">IT</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-muted fw-bold">Level</label>
                                    <select name="level" class="form-select form-control-modern">
                                        <option value="">Tidak Ada</option>
                                        <option value="admin">Admin</option>
                                        <option value="user">User</option>
                                        <option value="it">IT</option>
                                    </select>
                                </div>
                            </div>

                            <div class="card bg-light border-0 mb-3 p-3 rounded-3">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label small text-muted fw-bold">Status Aktif <span class="text-danger">*</span></label>
                                        <select name="is_active" class="form-select form-control-modern border-0 shadow-sm" required>
                                            <option value="1">Aktif</option>
                                            <option value="0">Nonaktif</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-muted fw-bold">Komputer? <span class="text-danger">*</span></label>
                                        <select name="is_user_computer" class="form-select form-control-modern border-0 shadow-sm" required>
                                            <option value="0">Tidak</option>
                                            <option value="1">Ya</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">Kode User</label>
                                <input type="text" name="kode_user" class="form-control form-control-modern" placeholder="EX: USR001">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">Image Sign</label>
                                <input type="file" name="image_sign" class="form-control form-control-modern" accept="image/*">
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-0 pt-4 px-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="bi bi-save me-1"></i> Simpan User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- Modal Edit User --}}
@foreach($users as $user)
@if(isset($permission) && $permission->can_edit)
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered"> 
        <div class="modal-content modal-content-modern">
            <div class="modal-header modal-header-modern px-4 py-3">
                <h5 class="modal-title fw-bold" id="editUserModalLabel{{ $user->id }}">
                    <i class="bi bi-pencil-square me-2"></i> Edit: {{ $user->nama }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ url('/it/management-user/update/' . $user->id) }}" method="POST" enctype="multipart/form-data">
                     @csrf
                     @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">Informasi Akun</h6>
                            <div class="mb-3">
                                <label for="nama{{ $user->id }}" class="form-label small text-muted fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama" id="nama{{ $user->id }}" class="form-control form-control-modern" value="{{ $user->nama }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="usr{{ $user->id }}" class="form-label small text-muted fw-bold">Username <span class="text-danger">*</span></label>
                                <input type="text" name="usr" id="usr{{ $user->id }}" class="form-control form-control-modern" value="{{ $user->usr }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="password{{ $user->id }}" class="form-label small text-muted fw-bold">Password <span class="text-secondary fw-normal">(Opsional)</span></label>
                                <input type="password" name="password" id="password{{ $user->id }}" class="form-control form-control-modern" placeholder="•••••••">
                            </div>
                            <div class="mb-3">
                                <label for="email{{ $user->id }}" class="form-label small text-muted fw-bold">Email</label>
                                <input type="email" name="email" id="email{{ $user->id }}" class="form-control form-control-modern" value="{{ $user->email }}">
                            </div>
                            <div class="mb-3">
                                <label for="no_hp{{ $user->id }}" class="form-label small text-muted fw-bold">No HP</label>
                                <input type="text" name="no_hp" id="no_hp{{ $user->id }}" class="form-control form-control-modern" value="{{ $user->no_hp }}">
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                             <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">Akses & Detail</h6>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="section_id{{ $user->id }}" class="form-label small text-muted fw-bold">Section</label>
                                    <select name="section_id" id="section_id{{ $user->id }}" class="form-select form-control-modern">
                                        <option value="" {{ is_null($user->section_id) || $user->section_id == '' ? 'selected' : '' }}>Tidak Ada</option>
                                        <option value="1" {{ $user->section_id == 1 ? 'selected' : '' }}>IT</option>
                                        <option value="2" {{ $user->section_id == 2 ? 'selected' : '' }}>HRD</option>
                                        <option value="3" {{ $user->section_id == 3 ? 'selected' : '' }}>PRODUKSI</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="level{{ $user->id }}" class="form-label small text-muted fw-bold">Level</label>
                                    <select name="level" id="level{{ $user->id }}" class="form-select form-control-modern">
                                        <option value="" {{ is_null($user->level) || $user->level == '' ? 'selected' : '' }}>Tidak Ada</option>
                                        <option value="admin" {{ $user->level == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="user" {{ $user->level == 'user' ? 'selected' : '' }}>User</option>
                                        <option value="it" {{ $user->level == 'it' ? 'selected' : '' }}>IT</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="card bg-light border-0 mb-3 p-3 rounded-3">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="is_active{{ $user->id }}" class="form-label small text-muted fw-bold">Status Aktif <span class="text-danger">*</span></label>
                                        <select name="is_active" id="is_active{{ $user->id }}" class="form-select form-control-modern border-0 shadow-sm" required>
                                            <option value="1" {{ $user->is_active == 1 ? 'selected' : '' }}>Aktif</option>
                                            <option value="0" {{ $user->is_active == 0 ? 'selected' : '' }}>Nonaktif</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="is_user_computer{{ $user->id }}" class="form-label small text-muted fw-bold">PC User? <span class="text-danger">*</span></label>
                                        <select name="is_user_computer" id="is_user_computer{{ $user->id }}" class="form-select form-control-modern border-0 shadow-sm" required>
                                            <option value="1" {{ $user->is_user_computer == 1 ? 'selected' : '' }}>Ya</option>
                                            <option value="0" {{ $user->is_user_computer == 0 ? 'selected' : '' }}>Tidak</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="kode_user{{ $user->id }}" class="form-label small text-muted fw-bold">Kode User</label>
                                <input type="text" name="kode_user" id="kode_user{{ $user->id }}" class="form-control form-control-modern" value="{{ $user->kode_user }}">
                            </div>
                            <div class="mb-3">
                                <label for="image_sign{{ $user->id }}" class="form-label small text-muted fw-bold">Image Sign</label>
                                <input type="file" name="image_sign" id="image_sign{{ $user->id }}" class="form-control form-control-modern" accept="image/*">
                                @if($user->image_sign)
                                    <div class="mt-2 p-2 bg-light border rounded d-flex align-items-center">
                                        <img src="{{ asset('images/sign/' . $user->image_sign) }}" height="30" class="me-2">
                                        <small class="text-muted">Terlampir</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-0 pt-4 px-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="bi bi-cloud-arrow-up me-1"></i> Update Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection