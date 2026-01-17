@extends('layout.sidebar')

@section('content')

{{-- KUSTOMISASI CSS UNTUK TAMPILAN MODERN --}}
<style>
    /* Animasi Masuk Halaman */
    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Styling Card & Table */
    .custom-card {
        border-radius: 15px;
        border: none;
        background: #ffffff;
        transition: all 0.3s ease;
    }

    .table thead th {
        background-color: #f8f9fa;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        border-top: none;
    }

    .table-hover tbody tr {
        transition: background-color 0.2s ease;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f5f9 !important;
    }

    /* Efek Tombol */
    .btn {
        border-radius: 8px;
        transition: all 0.2s ease;
        font-weight: 500;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #0f172a 0%, #2563eb 100%);
        border: none;
    }

    /* Styling Search Input */
    .input-group-modern {
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-radius: 10px;
        overflow: hidden;
    }

    .input-group-modern .form-control {
        border: 1px solid #e2e8f0;
        padding: 10px 15px;
    }

    .input-group-modern .form-control:focus {
        border-color: #3b82f6;
        box-shadow: none;
    }
</style>

<div class="content p-4 fade-in-up">

    {{-- Header Halaman --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 style="color:#0f172a;" class="fw-bold mb-1">
                <i class="bi bi-shield-exclamation me-2 text-primary"></i> Data Master: KTA
            </h3>
            <p class="text-muted small mb-0">Manajemen daftar kondisi lingkungan kerja yang tidak aman (KTA).</p>
        </div>
        
        <button type="button" class="btn btn-primary shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#addKtaModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Data Baru
        </button>
    </div>

    <div class="card shadow-lg custom-card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                    <i class="bi bi-list-ul text-primary fs-4"></i>
                </div>
                <h5 class="card-title mb-0 fw-bold text-dark">Daftar Kondisi Tidak Aman</h5>
            </div>

            {{-- Search & Export --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-8">
                    <form action="{{ url('/she/master/kta') }}" method="GET">
                        <div class="input-group input-group-modern">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="">
                            <button type="submit" class="btn btn-primary px-4">Cari</button>
                        </div>
                    </form>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <a href="{{ url('/she/master/kta/export') }}" class="btn btn-outline-success w-100 w-md-auto shadow-sm">
                        <i class="bi bi-file-earmark-spreadsheet me-2"></i> Export Data
                    </a>
                </div>
            </div>

            {{-- Tabel KTA --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 60px;">#</th>
                            <th>Kondisi Tidak Aman</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ktas as $kta)
                        <tr>
                            <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $kta->nama_kta }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @if(isset($permission) && $permission->can_edit)
                                    <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editKtaModal{{ $kta->id }}"
                                        title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    @endif

                                    @if(isset($permission) && $permission->can_delete)
                                    <form action="{{ url('/she/master/kta/destroy/' . $kta->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm" onclick="return confirm('Yakin hapus data: {{ $kta->nama_kta }}?')" title="Hapus Data">
                                            <i class="bi bi-trash3"></i>
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
            
            @if(method_exists($ktas, 'links'))
            <div class="mt-4">
                {{ $ktas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Logic Typing Placeholder (Tetap Sesuai Struktur)
const placeholders = ["Cari Kondisi Tidak Aman...", "Contoh: Lantai Licin", "Cari Data..."];
let current = 0; let index = 0; let isDeleting = false;
const speed = 100; const delay = 1500;
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
if (!input.value) typePlaceholder();
</script>

@endsection

@section('modals')

{{-- Modal Styling Khusus --}}
<style>
    .modal-content { border-radius: 15px; border: none; }
    .modal-header { border-bottom: 1px solid #f1f5f9; padding: 20px 25px; }
    .modal-footer { border-top: 1px solid #f1f5f9; padding: 20px 25px; }
    .modal-body { padding: 25px; }
</style>

{{-- Modal Tambah KTA --}}
<div class="modal fade" id="addKtaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <div class="modal-content shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle-fill text-primary me-2"></i> Tambah KTA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ url('/she/master/kta/store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_kta" class="form-label fw-bold small">Deskripsi Kondisi Tidak Aman <span class="text-danger">*</span></label>
                        <textarea name="nama_kta" id="nama_kta" class="form-control border-light-subtle bg-light" rows="4" placeholder="Jelaskan kondisi tidak aman..." required></textarea>
                        <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Contoh: Kabel terkelupas di area produksi.</div>
                    </div>
                    
                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-2"></i>Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit KTA --}}
@foreach($ktas as $kta)
@if(isset($permission) && $permission->can_edit)
<div class="modal fade" id="editKtaModal{{ $kta->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <div class="modal-content shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-primary me-2"></i> Edit Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ url('/she/master/kta/update/' . $kta->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_nama_kta{{ $kta->id }}" class="form-label fw-bold small">Deskripsi Kondisi Tidak Aman <span class="text-danger">*</span></label>
                        <textarea name="nama_kta" id="edit_nama_kta{{ $kta->id }}" class="form-control border-light-subtle bg-light" rows="4" required>{{ $kta->nama_kta }}</textarea>
                    </div>
                    
                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Update Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection