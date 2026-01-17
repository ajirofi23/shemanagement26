@extends('layout.sidebar')

@section('content')

{{-- Tambahan Style Khusus untuk Modernisasi --}}
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }
    .content { animation: fadeIn 0.5s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    .card { border-radius: 15px; transition: all 0.3s ease; border: none !important; }
    .card:hover { transform: translateY(-2px); box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important; }
    
    .btn { border-radius: 8px; padding: 10px 20px; font-weight: 600; transition: all 0.2s; }
    .btn-primary { background: var(--primary-gradient); border: none; }
    .btn-primary:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4); }
    
    .table thead th { 
        background-color: #f8f9fc; 
        text-transform: uppercase; 
        font-size: 0.8rem; 
        letter-spacing: 0.05em; 
        color: #4e73df;
        border-bottom: 2px solid #e3e6f0;
    }
    .table-hover tbody tr:hover { background-color: #f8f9ff; transition: 0.3s; }
    
    .search-container .input-group { border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .search-container input { border: 1px solid #e3e6f0; padding-left: 20px; }
    .search-container input:focus { box-shadow: none; border-color: #4e73df; }
    
    .badge-id { background: #f1f5f9; color: #475569; padding: 5px 10px; border-radius: 6px; font-weight: bold; }
</style>

<div class="content p-4"> {{-- Kembali ke p-4 untuk spacing yang lebih lega/lux --}}

    {{-- Header Halaman --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h3 style="color:#0f172a;" class="fw-bold mb-1">
                <i class="bi bi-shield-lock-fill me-2 text-primary"></i> Data Master: PTA
            </h3>
            <p class="text-muted small mb-0">Manajemen daftar Perilaku Tidak Aman untuk standar SHE.</p>
        </div>
        
        <button type="button" class="btn btn-primary shadow-sm pulse-animation" data-bs-toggle="modal" data-bs-target="#addPtaModal">
            <i class="bi bi-plus-lg me-2"></i> Tambah Data Baru
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            {{-- Search & Export Row --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-8 search-container">
                    <form action="{{ url('/she/master/pta') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Cari data pta...">
                            <button type="submit" class="btn btn-primary px-4">Cari</button>
                        </div>
                    </form>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <a href="{{ url('/she/master/pta/export') }}" class="btn btn-outline-success w-100 w-md-auto">
                        <i class="bi bi-file-earmark-excel me-2"></i> Export Excel
                    </a>
                </div>
            </div>

            {{-- Tabel PTA --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 70px;">No</th>
                            <th>Deskripsi Perilaku Tidak Aman</th>
                            <th class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ptas as $pta)
                        <tr>
                            <td class="text-center">
                                <span class="badge-id">{{ $loop->iteration }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $pta->nama_pta }}</div>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    @if(isset($permission) && $permission->can_edit)
                                    <button type="button" class="btn btn-sm btn-light text-primary border me-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editPtaModal{{ $pta->id }}"
                                        title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    @endif

                                    @if(isset($permission) && $permission->can_delete)
                                    <form action="{{ url('/she/master/pta/destroy/' . $pta->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger border" onclick="return confirm('Hapus data ini?')" title="Hapus">
                                            <i class="bi bi-trash3-fill"></i>
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

            @if(method_exists($ptas, 'links'))
            <div class="mt-4">
                {{ $ptas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Logic Typing Placeholder tetap sama (Struktur tidak diubah)
const placeholders = ["Cari Perilaku Tidak Aman...", "Contoh: Tidak pakai helm", "Filter Data Master"];
let current = 0; let index = 0; let isDeleting = false;
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
        if (index > currentPlaceholder.length) { isDeleting = true; setTimeout(typePlaceholder, 1500); return; }
    }
    setTimeout(typePlaceholder, 100);
}
if (input && !input.value) typePlaceholder();
</script>
@endsection

@section('modals')
{{-- Modal Styling di dalam Section Modals --}}
<style>
    .modal-content { border-radius: 20px; border: none; overflow: hidden; }
    .modal-header { border-bottom: none; padding: 25px 30px 10px; }
    .modal-footer { border-top: none; padding: 10px 30px 25px; }
    .form-control:focus { box-shadow: 0 0 0 0.25 luxury rgba(13, 110, 253, 0.1); }
</style>

{{-- Modal Tambah PTA --}}
<div class="modal fade" id="addPtaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <div class="modal-content shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill text-primary me-2"></i>Tambah PTA</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/she/master/pta/store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Detail Perilaku</label>
                        <textarea name="nama_pta" class="form-control bg-light border-0" rows="4" placeholder="Masukkan deskripsi perilaku tidak aman..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit PTA --}}
@foreach($ptas as $pta)
@if(isset($permission) && $permission->can_edit)
<div class="modal fade" id="editPtaModal{{ $pta->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> 
        <div class="modal-content shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/she/master/pta/update/' . $pta->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Perbarui Deskripsi</label>
                        <textarea name="nama_pta" class="form-control bg-light border-0" rows="4" required>{{ $pta->nama_pta }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection