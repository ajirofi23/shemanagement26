@extends('layout.sidebar')

@section('content')

{{-- Kustomisasi CSS Modern & Animasi --}}
<style>
    /* Animasi Halaman */
    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Card & Table Styling */
    .modern-card {
        border: none;
        border-radius: 15px;
        background: #ffffff;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important;
        overflow: hidden;
    }

    .table thead th {
        background-color: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #64748b;
        border-top: none;
        padding: 15px;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.03) !important;
    }

    /* Input & Search Styling */
    .input-group-modern {
        background: #f1f5f9;
        border-radius: 10px;
        padding: 5px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .input-group-modern:focus-within {
        background: #fff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .input-group-modern input {
        background: transparent !important;
        border: none !important;
    }

    /* Button Styling */
    .btn-modern {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .btn-action {
        width: 35px;
        height: 35px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
</style>

<div class="content p-3 fade-in-up">

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 style="color:#1e293b;" class="fw-bold mb-1">
                <i class="bi bi-file-earmark-check-fill me-2 text-primary"></i> Data Master: Pelanggaran Dokumen (PD)
            </h3>
            <p class="text-muted small mb-0">Kelola daftar standar ketidaksesuaian dokumen dalam sistem SHE.</p>
        </div>
        
        <button type="button" class="btn btn-primary btn-modern shadow-sm" data-bs-toggle="modal" data-bs-target="#addPdModal">
            <i class="bi bi-plus-lg me-1"></i> Tambah Data Baru
        </button>
    </div>
    
    <hr class="mb-4 opacity-50">

    <div class="card modern-card shadow-lg border-0">
        <div class="card-body p-4">
            <h5 class="card-title mb-4 fs-5 text-dark fw-bold">
                <i class="bi bi-list-ul me-2 text-primary"></i>Daftar Pelanggaran Dokumen
            </h5>

            {{-- Search & Export --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-8">
                    <form action="{{ url('/she/master/pd') }}" method="GET">
                        <div class="input-group input-group-modern">
                            <span class="input-group-text bg-transparent border-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" 
                                   class="form-control" placeholder="">
                            <button type="submit" class="btn btn-primary btn-modern px-4">Cari</button>
                        </div>
                    </form>
                </div>

                <div class="col-12 col-md-4 text-md-end">
                    <a href="{{ url('/she/master/pd/export') }}" class="btn btn-success btn-modern w-100 w-md-auto">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
                    </a>
                </div>
            </div>

            {{-- Tabel PD --}}
            <div class="table-responsive">
                <table class="table align-middle mt-2">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 70px;">No</th>
                            <th>Nama Pelanggaran Dokumen</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pds as $pd)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border p-2">{{ $loop->iteration }}</span>
                            </td>
                            <td class="fw-semibold text-dark">{{ $pd->nama_pd }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    @if(isset($permission) && $permission->can_edit)
                                    <button type="button" class="btn btn-primary btn-action" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editPdModal{{ $pd->id }}"
                                        title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    @endif

                                    @if(isset($permission) && $permission->can_delete)
                                    <form action="{{ url('/she/master/pd/destroy/' . $pd->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-action" onclick="return confirm('Yakin hapus data: {{ $pd->nama_pd }}?')" title="Hapus Data">
                                            <i class="bi bi-trash"></i>
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

            @if(method_exists($pds, 'links'))
            <div class="mt-4">
                {{ $pds->links() }}
            </div>
            @endif

        </div>
    </div>
</div>

<script>
const placeholders = ["Cari Pelanggaran Dokumen...", "Cari Nama Data...", "Filter Pelanggaran..."];
let current = 0;
let index = 0;
let isDeleting = false;
const speed = 100;
const delay = 1500;
const input = document.getElementById("searchInput");

function typePlaceholder() {
    if(!input) return;
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

@section('modals')

<style>
    .modal-content { border-radius: 20px; border: none; overflow: hidden; }
    .modal-header { border-bottom: none; padding: 25px 25px 10px; }
    .modal-body { padding: 10px 25px 25px; }
    .modal-footer { border-top: none; padding: 10px 25px 25px; }
    .form-control-modern { border-radius: 12px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; }
    .form-control-modern:focus { background: #fff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); border-color: #3b82f6; }
</style>

{{-- Modal Tambah PD --}}
<div class="modal fade" id="addPdModal" tabindex="-1" aria-labelledby="addPdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <div class="modal-content shadow-2xl">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark" id="addPdModalLabel"><i class="bi bi-file-earmark-plus-fill text-primary me-2"></i>Tambah Pelanggaran Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/she/master/pd/store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_pd" class="form-label fw-bold">Deskripsi Pelanggaran <span class="text-danger">*</span></label>
                        <textarea name="nama_pd" id="nama_pd" class="form-control form-control-modern" rows="4" required placeholder="Sebutkan jenis pelanggaran..."></textarea>
                        <div class="form-text mt-2"><i class="bi bi-info-circle me-1"></i> Contoh: Tanggal tidak diisi, Nama tidak jelas.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-modern px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-modern px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit PD --}}
@foreach($pds as $pd)
@if(isset($permission) && $permission->can_edit)
<div class="modal fade" id="editPdModal{{ $pd->id }}" tabindex="-1" aria-labelledby="editPdModalLabel{{ $pd->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <div class="modal-content shadow-2xl">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark" id="editPdModalLabel{{ $pd->id }}"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/she/master/pd/update/' . $pd->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_pd{{ $pd->id }}" class="form-label fw-bold">Deskripsi Pelanggaran <span class="text-danger">*</span></label>
                        <textarea name="nama_pd" id="edit_nama_pd{{ $pd->id }}" class="form-control form-control-modern" rows="4" required>{{ $pd->nama_pd }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-modern px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-modern px-4">Update Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection