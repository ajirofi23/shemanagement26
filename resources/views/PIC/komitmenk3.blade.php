@extends('layout.picsidebar')

@section('content')

{{-- Tambahkan Custom CSS untuk visual yang lebih premium --}}
<style>
    .content { animation: fadeIn 0.6s ease-out; }
    .card { border-radius: 15px; transition: all 0.3s ease; }
    .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .btn { border-radius: 8px; transition: all 0.3s ease; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
    .table thead th { background-color: #1e293b; border: none; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
    .badge { padding: 0.5em 0.8em; border-radius: 6px; }
    .input-group .form-control:focus { box-shadow: none; border-color: #198754; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="content p-3">

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 style="color:#0f172a;" class="fw-bold mb-0">
                <i class="bi bi-shield-check me-2 text-success"></i> Data Komitmen K3
            </h3>
            <p class="text-muted small mb-0">Kelola dan pantau kepatuhan komitmen keselamatan kerja karyawan.</p>
        </div>
        
        @if(!isset($isUploaded) || !$isUploaded)
            {{-- Tombol disembunyikan sesuai logic awal --}}
        @else
        <button type="button" class="btn btn-primary shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#editKomitmenK3Modal{{ $userKomitmen->id ?? $user->id }}" title="Lihat/Edit Komitmen K3 Anda">
            <i class="bi bi-pencil-square me-1"></i> Lihat/Edit Komitmen
        </button>
        @endif
    </div>
    
    <hr class="mb-4 opacity-10">

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-light p-2 rounded-3 me-3">
                    <i class="bi bi-list-columns-reverse text-primary fs-4"></i>
                </div>
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark">Daftar Komitmen K3</h5>
                    <small class="text-muted">Section: <span class="badge bg-info text-dark bg-opacity-10">{{ $user->section->section ?? 'N/A' }}</span></small>
                </div>
            </div>
            
            @if(session('sync_message'))
                <div class="alert alert-{{ session('sync_status') ?? 'success' }} alert-dismissible fade show border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('sync_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Toolbar: Sync, Filter, Search, Export --}}
            <div class="row g-3 mb-4">
                {{-- 1. Sync Button --}}
                <div class="col-auto">
                    <form action="{{ url('/pic/komitmenk3/sync') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning shadow-sm fw-semibold" 
                            @if(isset($canSync) && !$canSync) disabled @endif
                            title="{{ (isset($canSync) && !$canSync) ? 'Sudah disinkronkan bulan ini' : 'Tarik data karyawan terbaru' }}">
                            <i class="bi bi-arrow-clockwise me-1"></i> Tarik Data
                        </button>
                    </form>
                    @if(isset($lastSyncDate))
                        <div class="text-muted" style="font-size: 0.7rem; margin-top: 4px;">Terakhir: {{ $lastSyncDate }}</div>
                    @endif
                </div>

                {{-- 2. Filter --}}
                <div class="col-auto">
                    <form action="{{ url('/pic/komitmenk3') }}" method="GET" class="d-flex gap-2">
                        <select name="bulan" class="form-select shadow-sm" style="width: 130px; border-radius: 8px;">
                            <option value="">Bulan</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                            @endfor
                        </select>
                        <select name="tahun" class="form-select shadow-sm" style="width: 110px; border-radius: 8px;">
                            <option value="">Tahun</option>
                            @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-dark shadow-sm">
                            <i class="bi bi-filter"></i>
                        </button>
                    </form>
                </div>

                {{-- 3. Search & Export --}}
                <div class="col ms-md-auto">
                    <div class="d-flex gap-2 justify-content-md-end">
                        <form action="{{ url('/pic/komitmenk3') }}" method="GET" class="d-flex flex-grow-1" style="max-width: 350px;">
                            <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                                <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                                <input type="text" id="searchInput" name="search" value="{{ request('search') }}" class="form-control border-0 bg-light" placeholder="Cari...">
                                <button type="submit" class="btn btn-success border-0 px-3">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                        <a href="{{ url('/pic/komitmenk3/export') }}?bulan={{ request('bulan') }}&tahun={{ request('tahun') }}" class="btn btn-outline-primary shadow-sm fw-semibold px-3">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export
                        </a>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="table-responsive rounded-3 overflow-hidden border">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-white">
                            <th class="py-3 ps-3">No</th>
                            <th class="py-3">Karyawan</th>
                            <th class="py-3">NIP</th>
                            <th class="py-3">Unit Kerja</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Ringkasan Komitmen</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($komitmens as $data)
                        <tr style="transition: all 0.2s;">
                            <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $data->user->nama ?? 'N/A' }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $data->user->nip ?? 'N/A' }}</span></td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $data->user->section->section ?? 'N/A' }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $data->user->section->department ?? 'N/A' }}</div>
                            </td>
                            <td>
                                @if($data->bukti)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                        <i class="bi bi-check2-circle me-1"></i> Terunggah
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                        <i class="bi bi-exclamation-circle me-1"></i> Belum
                                    </span>
                                @endif
                            </td>
                            <td>
                                <p class="mb-0 text-muted small italic" title="{{ $data->komitmen }}">
                                    {{ Str::limit($data->komitmen ?? 'Menunggu input komitmen...', 40) }}
                                </p>
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm">
                                    @if(isset($user) && $data->user && $data->user->section_id === $user->section_id)
                                    <button type="button" class="btn btn-sm btn-white border"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editKomitmenK3Modal"
                                        data-id="{{ $data->id }}"
                                        data-user-id="{{ $data->user_id }}"
                                        data-komitmen="{{ $data->komitmen }}">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </button>
                                    @endif

                                    @if($data->bukti)
                                    <button type="button" class="btn btn-sm btn-white border" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viewBuktiModal{{ $data->id }}"
                                        title="Lihat Bukti">
                                        <i class="bi bi-eye text-info"></i>
                                    </button>
                                    @else
                                    <button class="btn btn-sm btn-white border disabled text-light">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/gray/data-report.svg" alt="Empty" style="height: 120px;" class="mb-3">
                                <p class="text-muted">Tidak ada data Komitmen K3 untuk periode ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if(isset($komitmens) && method_exists($komitmens, 'links'))
            <div class="d-flex justify-content-between align-items-center mt-4">
                <small class="text-muted">Menampilkan {{ $komitmens->count() }} data</small>
                <div>{{ $komitmens->links() }}</div>
            </div> 
            @endif
        </div>
    </div>
</div>

{{-- Script Placeholder (Tetap Sama) --}}
<script>
const placeholders = ["Cari Nama Karyawan", "Cari NIP", "Cari Komitmen K3"];
let current = 0, index = 0, isDeleting = false;
const speed = 100, delay = 1500;
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
if (input && !input.value) typePlaceholder();
</script>

@endsection

@section('modals')
    {{-- Modal Logic (Tetap Sama agar tidak merusak fungsionalitas) --}}
    @php
        $targetKomitmen = $userKomitmen ?? (object)['id' => $user->id, 'komitmen' => '', 'bukti' => null, 'user_id' => $user->id];
    @endphp

    <div class="modal fade" id="editKomitmenK3Modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> 
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-success text-white border-0" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Update Komitmen</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ url('/pic/komitmenk3/' . ($userKomitmen ? 'update/' . $userKomitmen->id : 'store')) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($userKomitmen) @method('PUT') @endif
                        
                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Isi Komitmen K3 <span class="text-danger">*</span></label>
                            <textarea name="komitmen" id="komitmen" class="form-control border-light-subtle bg-light" rows="4" required placeholder="Contoh: Saya berkomitmen menggunakan APD lengkap setiap saat...">{{ old('komitmen', $targetKomitmen->komitmen) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Bukti Komitmen (Gambar) <span class="text-danger">*</span></label>
                            @if($targetKomitmen->bukti)
                                <div class="mb-3 p-2 bg-light rounded text-center">
                                    <img src="{{ asset('storage/' . $targetKomitmen->bukti) }}" class="img-thumbnail shadow-sm" style="max-height: 120px;">
                                </div>
                            @endif
                            <input type="file" name="bukti" class="form-control" accept="image/*" {{ $targetKomitmen->bukti ? '' : 'required' }}>
                        </div>
                        
                        <div class="modal-footer border-0 px-0 pb-0 mt-4">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success px-4 fw-bold text-white shadow-sm">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @foreach($komitmens as $data)
    @if($data->bukti)
    <div class="modal fade" id="viewBuktiModal{{ $data->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg"> 
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-dark text-white border-0" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title"><i class="bi bi-image me-2"></i> Bukti: {{ $data->user->nama }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 text-center bg-light">
                    <img src="{{ asset('storage/' . $data->bukti) }}" class="img-fluid w-100">
                    <div class="p-4 bg-white text-start">
                        <div class="badge bg-primary mb-2">Pesan Komitmen</div>
                        <p class="text-dark fs-5 italic">"{{ $data->komitmen }}"</p>
                        <hr class="opacity-10">
                        <div class="small text-muted"><i class="bi bi-clock me-1"></i> Diunggah pada: {{ $data->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <a href="{{ asset('storage/' . $data->bukti) }}" target="_blank" class="btn btn-outline-dark"><i class="bi bi-download me-1"></i> Download</a>
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach

<script>
// Logic edit modal dynamic data (Tetap Sama)
const editModal = document.getElementById('editKomitmenK3Modal');
editModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    if (!button || button.id === 'editKomitmenK3Modal{{ $userKomitmen->id ?? $user->id }}') return; 

    const id = button.getAttribute('data-id');
    const userId = button.getAttribute('data-user-id');
    const komitmen = button.getAttribute('data-komitmen') || '';

    const form = editModal.querySelector('form');
    form.action = `/pic/komitmenk3/update/${id}`;
    form.querySelector('input[name="user_id"]').value = userId;
    form.querySelector('textarea[name="komitmen"]').value = komitmen;
});
</script>
@endsection