@extends('layout.picsidebar')

@section('content')
@php
    // DEKLARASI FUNGSI DI LUAR LOOP (HANYA SEKALI)
    function getBuktiArray($bukti) {
        if (is_array($bukti)) return $bukti;
        if (is_string($bukti) && !empty($bukti)) {
            $decoded = json_decode($bukti, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }
    
    // Status badge colors konsisten dengan SHE
    $statusColors = [
        'Open' => 'danger',
        'Progress' => 'warning', 
        'Close' => 'success',
        'Rejected' => 'secondary'
    ];
@endphp

<div class="content p-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 style="color:#0f172a;" class="fw-bold">
            <i class="bi bi-person-bounding-box me-2 text-info"></i> Laporan Safety Riding
        </h3>
    </div>
    
    <hr class="mb-4">

    <div class="card shadow-lg border-0">
        <div class="card-body p-3">
            <h5 class="card-title mb-3 fs-5 text-secondary">
                <i class="bi bi-list-columns-reverse me-1"></i> Daftar Laporan Safety Riding
            </h5>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <form action="{{ url('/pic/safety-riding') }}" method="GET" class="d-flex flex-grow-1 me-3">
                    <div class="input-group">
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" class="form-control border-info" style="max-width: 300px;" placeholder="Cari...">
                        <button type="submit" class="btn btn-info text-white">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </form>

                <a href="{{ url('/pic/safety-riding/export') }}" class="btn btn-success shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped mt-3 align-middle table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 30px;">No</th>
                            <th style="width: 120px;">Waktu Kejadian</th>
                            <th style="width: 80px;">Section</th>
                            <th>Nama Pelanggar</th>
                            <th style="width: 80px;">Tipe Kendaraan</th>
                            <th style="width: 100px;">NOPOL</th>
                            <th style="width: 10%;">Pelanggaran Dokumen</th>
                            <th style="width: 10%;">Pelanggaran Fisik</th>
                            <th style="width: 80px;">Total Pelanggaran</th>
                            <th>Keterangan Pelanggaran</th>
                            <th style="width: 70px;">Status</th>
                            <th style="width: 160px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($safetyridings as $laporan)
                        @php
                            $totalPelanggaran = $laporan->pds->count() + $laporan->pfs->count();
                            $statusBadge = $statusColors[$laporan->status] ?? 'secondary';
                            $buktiArray = getBuktiArray($laporan->bukti);
                            $buktiCount = count($buktiArray);
                            $buktiAfterArray = getBuktiArray($laporan->bukti_after);
                            $buktiAfterCount = count($buktiAfterArray);
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($laporan->waktu_kejadian)->format('d-m-Y H:i') }}</td>
                            <td>{{ $laporan->user->section->section ?? 'N/A' }}</td>
                            <td>{{ $laporan->user->nama ?? 'N/A' }}</td>
                            <td>{{ $laporan->type_kendaraan }}</td>
                            <td>{{ $laporan->nopol }}</td>
                            <td>
                                @if($laporan->pds->count() > 0)
                                    <div class="pelanggaran-list">
                                        @foreach($laporan->pds as $pd)
                                            <span class="badge bg-danger mb-1 d-block text-start" 
                                                  data-bs-toggle="tooltip" 
                                                  title="{{ $pd->nama_pd }}">
                                                {{ $pd->nama_pd }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="badge bg-success">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                @if($laporan->pfs->count() > 0)
                                    <div class="pelanggaran-list">
                                        @foreach($laporan->pfs as $pf)
                                            <span class="badge bg-warning mb-1 d-block text-start" 
                                                  data-bs-toggle="tooltip" 
                                                  title="{{ $pf->nama_pf }}">
                                                {{ $pf->nama_pf }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="badge bg-success">Tidak ada</span>
                                @endif
                            </td>
                            <td class="fw-bold text-center">
                                <span class="badge bg-dark">{{ $totalPelanggaran }}</span>
                            </td>
                            <td>{{ Str::limit($laporan->keterangan_pelanggaran, 30) }}</td>
                            <td>
                                <span class="badge bg-{{ $statusBadge }}">{{ $laporan->status }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <!-- Tombol Upload After untuk status yang diperbolehkan -->
                                    @if($laporan->status === 'Open' || $laporan->status === 'Progress' || $laporan->status === 'Rejected')
                                    <button type="button" class="btn btn-sm btn-warning" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#uploadAfterModal{{ $laporan->id }}"
                                        title="Upload Foto After">
                                        <i class="bi bi-camera"></i> After
                                    </button>
                                    @endif

                                    <!-- Tombol Lihat Detail (Gabungan) -->
                                    @if($buktiCount > 0 || $buktiAfterCount > 0)
                                    <button type="button" class="btn btn-sm btn-info" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#viewDetailModal{{ $laporan->id }}"
                                        title="Lihat Detail">
                                        <i class="bi bi-eye"></i> Detail
                                        @if($buktiCount > 0)
                                        <span class="badge bg-light text-dark ms-1">{{ $buktiCount }}</span>
                                        @endif
                                        @if($buktiAfterCount > 0)
                                        <span class="badge bg-success text-white ms-1">{{ $buktiAfterCount }}</span>
                                        @endif
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================
// FUNGSI UNTUK PREVIEW DAN VALIDASI
// ============================================

function previewAfterImage(input, laporanId, index) {
    const previewContainer = document.getElementById(`after_preview_${laporanId}_${index}`);
    if (!previewContainer) return;
    
    previewContainer.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        // Validasi ukuran file (maks 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert(`File ${file.name} terlalu besar! Maksimal 5MB.`);
            input.value = '';
            return;
        }
        
        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!allowedTypes.includes(file.type)) {
            alert(`Tipe file ${file.name} tidak diizinkan! Hanya JPG, PNG.`);
            input.value = '';
            return;
        }
        
        reader.onload = function(e) {
            previewContainer.innerHTML = `
                <div class="card" style="width: 150px;">
                    <img src="${e.target.result}" 
                         class="card-img-top" 
                         style="height: 100px; object-fit: cover;"
                         alt="Preview ${file.name}">
                    <div class="card-body p-2 text-center">
                        <small class="text-muted d-block">${file.name}</small>
                        <small class="text-success">${(file.size / 1024).toFixed(2)} KB</small>
                    </div>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
}

function viewExistingAfter(laporanId, index, path) {
    // Buka gambar dalam tab baru
    window.open(`{{ asset('storage/') }}/${path}`, '_blank');
}

function deleteAfterImage(laporanId, index) {
    if (confirm('Apakah Anda yakin ingin menghapus foto after ini?')) {
        fetch(`/pic/safety-riding/delete-after/${laporanId}/${index}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Foto berhasil dihapus!');
                location.reload();
            } else {
                alert('Gagal menghapus foto: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus foto');
        });
    }
}

// ============================================
// SETUP TOOLTIPS DAN VALIDASI
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Validasi form upload after
    @foreach($safetyridings as $laporan)
    @if($laporan->status === 'Open' || $laporan->status === 'Progress' || $laporan->status === 'Rejected')
    const form{{ $laporan->id }} = document.getElementById('uploadAfterForm{{ $laporan->id }}');
    if (form{{ $laporan->id }}) {
        form{{ $laporan->id }}.addEventListener('submit', function(e) {
            const fileInputs = this.querySelectorAll('.after-file-input');
            let hasFile = false;
            let hasExisting = false;
            
            // Cek apakah ada file baru yang diupload
            fileInputs.forEach(input => {
                if (input.files && input.files.length > 0) {
                    hasFile = true;
                }
            });
            
            // Cek existing files
            const existingInputs = this.querySelectorAll('input[name="existing_after[]"]');
            if (existingInputs.length > 0) {
                hasExisting = true;
            }
            
            // Jika tidak ada file baru dan tidak ada existing files
            if (!hasFile && !hasExisting) {
                e.preventDefault();
                alert('Harap upload minimal satu foto after!');
                return false;
            }
            
            return true;
        });
    }
    @endif
    @endforeach
});
</script>
@endsection

@section('modals')
@foreach($safetyridings as $laporan)
@php
    // GUNAKAN FUNGSI YANG SUDAH DIDEKLARASIKAN DI ATAS
    $buktiArray = getBuktiArray($laporan->bukti);
    $buktiAfterArray = getBuktiArray($laporan->bukti_after);
    $totalPelanggaran = $laporan->pds->count() + $laporan->pfs->count();
@endphp

<!-- ============================================ -->
<!-- MODAL UPLOAD AFTER (UNTUK PIC) -->
<!-- ============================================ -->
@if($laporan->status === 'Open' || $laporan->status === 'Progress' || $laporan->status === 'Rejected')
<div class="modal fade" id="uploadAfterModal{{ $laporan->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">
                    <i class="bi bi-camera me-1"></i> Upload Foto After - Laporan #{{ $laporan->id }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ url('/pic/safety-riding/upload-after/' . $laporan->id) }}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      id="uploadAfterForm{{ $laporan->id }}">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="total_pelanggaran" value="{{ $totalPelanggaran }}">
                    <input type="hidden" name="status" value="Progress">
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Informasi Laporan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Pelapor: <strong>{{ $laporan->user->nama ?? 'N/A' }}</strong></li>
                            <li>Total Pelanggaran: <strong>{{ $totalPelanggaran }}</strong></li>
                            <li>Status Saat Ini: <span class="badge bg-{{ $statusColors[$laporan->status] ?? 'secondary' }}">{{ $laporan->status }}</span></li>
                            @if($laporan->status === 'Rejected' && $laporan->catatan_tindak_lanjut)
                            <li>Catatan Reject: <span class="text-danger">{{ $laporan->catatan_tindak_lanjut }}</span></li>
                            @endif
                        </ul>
                    </div>
                    
                    <!-- Catatan khusus untuk status Reject -->
                    @if($laporan->status === 'Rejected')
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Laporan ini ditolak oleh SHE. Harap perbaiki dan upload foto after yang benar.</strong>
                        @if($laporan->catatan_tindak_lanjut)
                        <div class="mt-2">
                            <strong>Catatan Reject:</strong> {{ $laporan->catatan_tindak_lanjut }}
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Before Photos -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-camera me-1"></i> Bukti Foto Before (Sebelum):
                            <span class="badge bg-info">{{ count($buktiArray) }} foto</span>
                        </label>
                        @if(count($buktiArray) > 0)
                            <div class="row">
                                @foreach($buktiArray as $index => $path)
                                <div class="col-md-3 mb-2">
                                    <div class="card border-primary">
                                        <a href="{{ asset('storage/' . $path) }}" target="_blank" class="text-decoration-none">
                                            <img src="{{ asset('storage/' . $path) }}" 
                                                 class="card-img-top" 
                                                 style="height: 100px; object-fit: cover;"
                                                 alt="Before {{ $index + 1 }}">
                                            <div class="card-body p-2 text-center">
                                                <small class="text-primary fw-bold">Before {{ $index + 1 }}</small>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning py-2">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Tidak ada bukti foto before.
                            </div>
                        @endif
                    </div>

                    <!-- Dynamic After File Inputs -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-cloud-upload me-1"></i> Upload Foto After (Sesudah):
                            <span class="badge bg-success">{{ count($buktiAfterArray) }} foto</span>
                        </label>
                        
                        @if($totalPelanggaran > 0)
                            <div class="row">
                                @for($i = 1; $i <= $totalPelanggaran; $i++)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header py-2 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong>After {{ $i }}</strong>
                                                @if(isset($buktiAfterArray[$i-1]))
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i> Sudah ada
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @if(isset($buktiAfterArray[$i-1]))
                                            <div class="mb-2">
                                                <div class="alert alert-success py-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>
                                                            <i class="bi bi-file-earmark-image me-1"></i>
                                                            Foto sudah diupload
                                                        </span>
                                                        <div>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-primary"
                                                                    onclick="viewExistingAfter({{ $laporan->id }}, {{ $i }}, '{{ $buktiAfterArray[$i-1] }}')">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    onclick="deleteAfterImage({{ $laporan->id }}, {{ $i-1 }})">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" 
                                                       name="existing_after[]" 
                                                       value="{{ $buktiAfterArray[$i-1] }}">
                                            </div>
                                            @endif
                                            
                                            <div class="mb-2">
                                                <label class="form-label small">
                                                    @if(isset($buktiAfterArray[$i-1]))
                                                    Ganti foto (opsional):
                                                    @else
                                                    Upload foto baru:
                                                    @endif
                                                </label>
                                                <input type="file" 
                                                       name="bukti_after[]" 
                                                       class="form-control form-control-sm after-file-input" 
                                                       id="after_file_{{ $laporan->id }}_{{ $i }}"
                                                       accept="image/*"
                                                       onchange="previewAfterImage(this, {{ $laporan->id }}, {{ $i }})">
                                            </div>
                                            
                                            <div id="after_preview_{{ $laporan->id }}_{{ $i }}"></div>
                                        </div>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Tidak ada pelanggaran ditemukan.
                            </div>
                        @endif
                        
                        <div class="alert alert-secondary mt-3 py-2">
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>Catatan:</strong>
                            <ul class="mb-0 mt-1">
                                <li>Upload foto setelah perbaikan untuk setiap pelanggaran</li>
                                <li>Format yang diterima: JPG, PNG (maks. 5MB per file)</li>
                                <li>Status akan otomatis berubah menjadi <strong>"Progress"</strong></li>
                                @if($laporan->status === 'Rejected')
                                <li><strong>Perhatian:</strong> Laporan ini ditolak, pastikan foto after sesuai dengan catatan reject</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    
                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-warning text-white">
                            <i class="bi bi-upload me-1"></i> Simpan Foto After
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- ============================================ -->
<!-- MODAL VIEW DETAIL (GABUNGAN BEFORE & AFTER) -->
<!-- ============================================ -->
@if(count($buktiArray) > 0 || count($buktiAfterArray) > 0)
<div class="modal fade" id="viewDetailModal{{ $laporan->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detail Bukti - Laporan #{{ $laporan->id }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Tabs untuk Before dan After -->
                <ul class="nav nav-tabs mb-4" id="detailTab{{ $laporan->id }}" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="before-tab-{{ $laporan->id }}" data-bs-toggle="tab" data-bs-target="#before-{{ $laporan->id }}" type="button" role="tab">
                            <i class="bi bi-camera me-1"></i> Before
                            <span class="badge bg-info">{{ count($buktiArray) }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="after-tab-{{ $laporan->id }}" data-bs-toggle="tab" data-bs-target="#after-{{ $laporan->id }}" type="button" role="tab">
                            <i class="bi bi-check-circle me-1"></i> After
                            <span class="badge bg-success">{{ count($buktiAfterArray) }}</span>
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content" id="detailTabContent{{ $laporan->id }}">
                    <!-- Tab Before -->
                    <div class="tab-pane fade show active" id="before-{{ $laporan->id }}" role="tabpanel">
                        @if(count($buktiArray) > 0)
                            <div class="row">
                                @foreach($buktiArray as $index => $path)
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <a href="{{ asset('storage/' . $path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $path) }}" 
                                                 class="card-img-top" 
                                                 style="height: 150px; object-fit: cover;">
                                        </a>
                                        <div class="card-body">
                                            <h6 class="card-title">Before {{ $index + 1 }}</h6>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </a>
                                                <a href="{{ asset('storage/' . $path) }}" download class="btn btn-sm btn-success">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Tidak ada bukti foto before.
                            </div>
                        @endif
                    </div>
                    
                    <!-- Tab After -->
                    <div class="tab-pane fade" id="after-{{ $laporan->id }}" role="tabpanel">
                        @if(count($buktiAfterArray) > 0)
                            <div class="row">
                                @foreach($buktiAfterArray as $index => $path)
                                <div class="col-md-3 mb-3">
                                    <div class="card">
                                        <a href="{{ asset('storage/' . $path) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $path) }}" 
                                                 class="card-img-top" 
                                                 style="height: 150px; object-fit: cover;">
                                        </a>
                                        <div class="card-body">
                                            <h6 class="card-title">After {{ $index + 1 }}</h6>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="{{ asset('storage/' . $path) }}" target="_blank" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> Lihat
                                                </a>
                                                <a href="{{ asset('storage/' . $path) }}" download class="btn btn-sm btn-success">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                                @if($laporan->status === 'Open' || $laporan->status === 'Progress' || $laporan->status === 'Rejected')
                                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteAfterImage({{ $laporan->id }}, {{ $index }})">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Belum ada bukti foto after.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection