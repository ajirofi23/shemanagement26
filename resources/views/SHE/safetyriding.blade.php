@extends('layout.sidebar')

@section('content')
@php
    // DEKLARASI FUNGSI DI LUAR LOOP (HANYA SEKALI)
    function getBuktiArray($bukti) {
        if (is_array($bukti)) {
            return $bukti;
        }
        if (is_string($bukti) && !empty($bukti)) {
            $decoded = json_decode($bukti, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }
    
    // Status badge colors
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
        
        <button type="button" class="btn btn-info shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#addSafetyRidingModal">
            <i class="bi bi-plus-circle me-1"></i> Buat Laporan Baru
        </button>
    </div>
    
    <hr class="mb-4">

    <div class="card shadow-lg border-0">
        <div class="card-body p-3">
            <h5 class="card-title mb-3 fs-5 text-secondary">
                <i class="bi bi-list-columns-reverse me-1"></i> Daftar Laporan Safety Riding
            </h5>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <form action="{{ url('/she/safety-riding') }}" method="GET" class="d-flex flex-grow-1 me-3">
                    <div class="input-group">
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" class="form-control border-info" style="max-width: 300px;" placeholder="">
                        <button type="submit" class="btn btn-info text-white">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </form>

                <a href="{{ url('/she/safety-riding/export') }}" class="btn btn-success shadow-sm">
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
                            <th style="width: 180px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($safetyridings as $laporan)
                        @php
                            // GUNAKAN FUNGSI YANG SUDAH DIDEKLARASIKAN DI ATAS
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
                                    <!-- Tombol Tindak Lanjut untuk SHE -->
                                    @if(isset($permission) && $permission->can_edit && ($laporan->status === 'Progress' || $laporan->status === 'Open'))
                                    <button type="button" class="btn btn-sm btn-success" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#tindakLanjutModal{{ $laporan->id }}"
                                        title="Tindak Lanjut">
                                        <i class="bi bi-check-circle"></i> Tindak Lanjut
                                    </button>
                                    @endif

                                    @if(isset($permission) && $permission->can_edit && $laporan->status === 'Open')
                                    <button type="button" class="btn btn-sm btn-primary" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editSafetyRidingModal{{ $laporan->id }}"
                                        title="Edit Data">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @endif

                                    <!-- Tombol Lihat Detail (Gabungan Before & After) -->
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

                                    @if(isset($permission) && $permission->can_delete && $laporan->status === 'Open')
                                    <form action="{{ url('/she/safety-riding/destroy/' . $laporan->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus Laporan Safety Riding ini?')" title="Hapus Data">
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
        </div>
    </div>
</div>

<script>
const placeholders = ["Cari Nama Pelanggar", "Cari NOPOL", "Filter"];
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

// ============================================
// FUNGSI UTAMA UNTUK PREVIEW GAMBAR
// ============================================

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) {
        console.error(`Container #${previewId} not found!`);
        return;
    }
    
    preview.innerHTML = '';
    
    if (input.files && input.files.length > 0) {
        console.log(`Found ${input.files.length} files for preview`);
        
        for (let i = 0; i < input.files.length; i++) {
            const reader = new FileReader();
            const file = input.files[i];
            
            reader.onload = function(e) {
                const imgDiv = document.createElement('div');
                imgDiv.className = 'col-md-3 mb-2';
                imgDiv.innerHTML = `
                    <div class="card">
                        <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;">
                        <div class="card-body p-2 text-center">
                            <small class="text-muted">${file.name}</small>
                        </div>
                    </div>
                `;
                preview.appendChild(imgDiv);
            }
            
            reader.onerror = function(e) {
                console.error('Error reading file:', file.name, e);
            }
            
            reader.readAsDataURL(file);
        }
    }
}

// ============================================
// FUNGSI UNTUK DELETE IMAGE
// ============================================

function deleteImage(laporanId, imageIndex) {
    if (confirm('Yakin ingin menghapus gambar ini?')) {
        fetch(`/she/safety-riding/${laporanId}/delete-image/${imageIndex}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Content-Type': 'application/json'
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
                alert('Gambar berhasil dihapus!');
                location.reload();
            } else {
                alert('Gagal menghapus gambar: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }
}

// ============================================
// FUNGSI UNTUK TINDAK LANJUT
// ============================================

function validateTindakLanjut(formId) {
    const form = document.getElementById(formId);
    const statusSelect = form.querySelector('select[name="status"]');
    const catatan = form.querySelector('textarea[name="catatan"]');
    
    if (statusSelect.value === '') {
        alert('Harap pilih status tindak lanjut!');
        return false;
    }
    
    if (statusSelect.value === 'Rejected' && catatan.value.trim() === '') {
        alert('Harap isi catatan untuk status Reject!');
        catatan.focus();
        return false;
    }
    
    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Setup untuk modal add
    const addModal = document.getElementById('addSafetyRidingModal');
    if (addModal) {
        const checkboxes = addModal.querySelectorAll('.pelanggaran-checkbox');
        const totalInput = addModal.querySelector('#total_pelanggaran');
        const totalUploadInput = addModal.querySelector('#total_upload_files');

        function updateTotal() {
            let count = 0;
            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    count++;
                }
            });
            totalInput.value = count;
            totalUploadInput.value = count;
            generateUploadFields(count, 'add');
        }

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateTotal);
        });

        updateTotal();

        const sectionSelect = addModal.querySelector('#section_id');
        const userSelect = addModal.querySelector('#user_id');

        if (sectionSelect && userSelect) {
            sectionSelect.addEventListener('change', function() {
                const sectionId = this.value;
                userSelect.innerHTML = '<option value="">Pilih Nama Pelanggar</option>';
                
                if (sectionId) {
                    fetch(`/she/safety-riding/get-users-by-section/${sectionId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.users.forEach(function(user) {
                                const option = document.createElement('option');
                                option.value = user.id;
                                option.textContent = user.nama;
                                userSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        }
    }
});

function generateUploadFields(count, type, modalId = '') {
    let containerId = type === 'add' ? 'dynamicUploadContainer' : `dynamicUploadContainerEdit_${modalId}`;
    let container = document.getElementById(containerId);
    
    if (!container) {
        console.error(`Container #${containerId} not found!`);
        return;
    }
    
    container.innerHTML = '';
    
    if (count > 0) {
        for (let i = 1; i <= count; i++) {
            const fieldDiv = document.createElement('div');
            fieldDiv.className = 'mb-2';
            if (type === 'add') {
                fieldDiv.innerHTML = `
                    <label class="form-label">Bukti Pelanggaran ${i} <span class="text-danger">*</span></label>
                    <input type="file" name="bukti[]" class="form-control" accept="image/*" required>
                    <small class="form-text text-muted">Upload bukti untuk pelanggaran ${i}</small>
                `;
            } else {
                fieldDiv.innerHTML = `
                    <label class="form-label">Bukti Baru ${i} (Opsional)</label>
                    <input type="file" name="bukti_baru[]" class="form-control" accept="image/*">
                `;
            }
            container.appendChild(fieldDiv);
        }
    } else {
        container.innerHTML = '<p class="text-muted">Tidak ada pelanggaran yang dipilih.</p>';
    }
}
</script>
@endsection

@section('modals')
<!-- Modal Add Laporan Baru -->
<div class="modal fade" id="addSafetyRidingModal" tabindex="-1" aria-labelledby="addSafetyRidingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="addSafetyRidingModalLabel"><i class="bi bi-plus-circle-fill me-1"></i> Buat Laporan Safety Riding</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ url('/she/safety-riding/store') }}" method="POST" enctype="multipart/form-data" id="addSafetyRidingForm">
                    @csrf
                    
                    <h6 class="fw-bold text-primary mb-3">I. Data Temuan Safety Riding</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="waktu_kejadian" class="form-label">Waktu Kejadian <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="waktu_kejadian" id="waktu_kejadian" class="form-control" value="{{ old('waktu_kejadian') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="section_id" class="form-label">Section <span class="text-danger">*</span></label>
                            <select name="section_id" id="section_id" class="form-select" required>
                                <option value="">Pilih Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->section }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">Nama Pelanggar <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">Pilih Nama Pelanggar</option>
                                @if(old('section_id'))
                                    @php
                                        $selectedSectionUsers = \App\Models\User::where('section_id', old('section_id'))->get();
                                    @endphp
                                    @foreach($selectedSectionUsers as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->nama }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="type_kendaraan" class="form-label">Tipe Kendaraan <span class="text-danger">*</span></label>
                            <input type="text" name="type_kendaraan" id="type_kendaraan" class="form-control" value="{{ old('type_kendaraan') }}" placeholder="Contoh: Sepeda Motor, Mobil Box" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nopol" class="form-label">Nomor Polisi (NOPOL) <span class="text-danger">*</span></label>
                            <input type="text" name="nopol" id="nopol" class="form-control" value="{{ old('nopol') }}" placeholder="Contoh: T1234ABC" required>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold text-primary mt-4 mb-3">II. Jenis Pelanggaran</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block fw-bold">Pelanggaran Dokumen (PD)</label>
                            <div class="form-check-group border p-2 rounded" style="max-height: 200px; overflow-y: auto;">
                                @foreach($masterPds as $pd)
                                    <div class="form-check">
                                        <input class="form-check-input pelanggaran-checkbox" type="checkbox" name="pd_id[]" value="{{ $pd->id }}" id="pd_check_{{ $pd->id }}" {{ in_array($pd->id, old('pd_id', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pd_check_{{ $pd->id }}">
                                            {{ $pd->nama_pd }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('pd_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block fw-bold">Pelanggaran Fisik (PF)</label>
                            <div class="form-check-group border p-2 rounded" style="max-height: 200px; overflow-y: auto;">
                                @foreach($masterPfs as $pf)
                                    <div class="form-check">
                                        <input class="form-check-input pelanggaran-checkbox" type="checkbox" name="pf_id[]" value="{{ $pf->id }}" id="pf_check_{{ $pf->id }}" {{ in_array($pf->id, old('pf_id', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pf_check_{{ $pf->id }}">
                                            {{ $pf->nama_pf }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('pf_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="total_pelanggaran" class="form-label fw-bold">Total Pelanggaran</label>
                        <input type="text" name="total_pelanggaran" id="total_pelanggaran" class="form-control" value="0" readonly>
                        <input type="hidden" name="total_upload_files" id="total_upload_files" value="0">
                        <small class="form-text text-muted">Angka ini menentukan jumlah file bukti yang perlu diupload.</small>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan_pelanggaran" class="form-label">Keterangan Pelanggaran <span class="text-danger">*</span></label>
                        <textarea name="keterangan_pelanggaran" id="keterangan_pelanggaran" class="form-control" rows="3" required>{{ old('keterangan_pelanggaran') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Upload Bukti Foto</label>
                        <small class="text-danger d-block mb-2">* Upload sesuai jumlah pelanggaran yang dipilih di atas</small>
                        
                        <div id="imagePreviewContainer" class="row mb-2"></div>
                        
                        <div id="dynamicUploadContainer">
                            <p class="text-muted">Pilih pelanggaran terlebih dahulu untuk menampilkan form upload.</p>
                        </div>
                    </div>

                    <input type="hidden" name="status" value="Open">
                    
                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i> Batal</button>
                        <button type="submit" class="btn btn-info text-white"><i class="bi bi-save me-1"></i> Simpan Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@foreach($safetyridings as $laporan)
@php
    // GUNAKAN FUNGSI YANG SUDAH DIDEKLARASIKAN DI ATAS
    $buktiArray = getBuktiArray($laporan->bukti);
    $buktiAfterArray = getBuktiArray($laporan->bukti_after);
    $totalPelanggaran = $laporan->pds->count() + $laporan->pfs->count();
    $editBuktiArray = getBuktiArray($laporan->bukti);
    $editTotalPelanggaran = $laporan->pds->count() + $laporan->pfs->count();
    $selectedPds = $laporan->pds->pluck('id')->toArray();
    $selectedPfs = $laporan->pfs->pluck('id')->toArray();
@endphp

<!-- ============================================ -->
<!-- MODAL TINDAK LANJUT (UNTUK SHE) -->
<!-- ============================================ -->
@if(isset($permission) && $permission->can_edit && ($laporan->status === 'Progress' || $laporan->status === 'Open'))
<div class="modal fade" id="tindakLanjutModal{{ $laporan->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Tindak Lanjut - Laporan #{{ $laporan->id }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ url('/she/safety-riding/tindak-lanjut/' . $laporan->id) }}" 
                      method="POST" 
                      id="tindakLanjutForm{{ $laporan->id }}"
                      onsubmit="return validateTindakLanjut('tindakLanjutForm{{ $laporan->id }}')">
                    @csrf
                    @method('PUT')
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Informasi Laporan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Pelapor: <strong>{{ $laporan->user->nama ?? 'N/A' }}</strong></li>
                            <li>Section: {{ $laporan->user->section->section ?? 'N/A' }}</li>
                            <li>Total Pelanggaran: <strong>{{ $totalPelanggaran }}</strong></li>
                            <li>Status Saat Ini: <span class="badge bg-{{ $statusColors[$laporan->status] ?? 'secondary' }}">{{ $laporan->status }}</span></li>
                        </ul>
                    </div>

                    <div class="row mb-4">
                        <!-- Before Photos -->
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">
                                <i class="bi bi-camera me-1"></i> Bukti Before
                                <span class="badge bg-info">{{ count($buktiArray) }} foto</span>
                            </h6>
                            @if(count($buktiArray) > 0)
                                <div class="row">
                                    @foreach($buktiArray as $index => $path)
                                    <div class="col-6 mb-2">
                                        <div class="card">
                                            <a href="{{ asset('storage/' . $path) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $path) }}" 
                                                     class="card-img-top" 
                                                     style="height: 80px; object-fit: cover;"
                                                     alt="Before {{ $index + 1 }}">
                                            </a>
                                            <div class="card-body p-1 text-center">
                                                <small class="text-muted">Before {{ $index + 1 }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted small">Tidak ada bukti before</p>
                            @endif
                        </div>
                        
                        <!-- After Photos -->
                        <div class="col-md-6">
                            <h6 class="fw-bold text-success">
                                <i class="bi bi-check-circle me-1"></i> Bukti After
                                <span class="badge bg-success">{{ count($buktiAfterArray) }} foto</span>
                            </h6>
                            @if(count($buktiAfterArray) > 0)
                                <div class="row">
                                    @foreach($buktiAfterArray as $index => $path)
                                    <div class="col-6 mb-2">
                                        <div class="card">
                                            <a href="{{ asset('storage/' . $path) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $path) }}" 
                                                     class="card-img-top" 
                                                     style="height: 80px; object-fit: cover;"
                                                     alt="After {{ $index + 1 }}">
                                            </a>
                                            <div class="card-body p-1 text-center">
                                                <small class="text-muted">After {{ $index + 1 }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted small">Belum ada bukti after</p>
                            @endif
                        </div>
                    </div>

                    <!-- Status Selection -->
                    <div class="mb-4">
                        <label for="status_tindak_lanjut_{{ $laporan->id }}" class="form-label fw-bold">
                            <i class="bi bi-flag me-1"></i> Status Tindak Lanjut:
                        </label>
                        <select name="status" id="status_tindak_lanjut_{{ $laporan->id }}" class="form-select" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Close">Close</option>
                            <option value="Rejected">Reject</option>
                        </select>
                        <div class="form-text">
                            <strong>Close:</strong> Laporan selesai dan diterima<br>
                            <strong>Reject:</strong> Laporan ditolak (wajib isi catatan)<br>
                        </div>
                    </div>

                    <!-- Catatan untuk Reject -->
                    <div class="mb-4" id="catatan_reject_container_{{ $laporan->id }}">
                        <label for="catatan_{{ $laporan->id }}" class="form-label fw-bold">
                            <i class="bi bi-chat-text me-1"></i> Catatan Tindak Lanjut:
                        </label>
                        <textarea name="catatan" 
                                  id="catatan_{{ $laporan->id }}" 
                                  class="form-control" 
                                  rows="3"
                                  placeholder="Khusus untuk status Reject, harap isi alasan penolakan..."></textarea>
                        <div class="form-text">Catatan wajib diisi jika memilih status Reject</div>
                    </div>

                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Simpan Tindak Lanjut
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide catatan berdasarkan status
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect{{ $laporan->id }} = document.getElementById('status_tindak_lanjut_{{ $laporan->id }}');
    const catatanContainer{{ $laporan->id }} = document.getElementById('catatan_reject_container_{{ $laporan->id }}');
    
    if (statusSelect{{ $laporan->id }}) {
        statusSelect{{ $laporan->id }}.addEventListener('change', function() {
            if (this.value === 'Rejected') {
                catatanContainer{{ $laporan->id }}.style.display = 'block';
            } else {
                catatanContainer{{ $laporan->id }}.style.display = 'none';
            }
        });
        
        // Set initial state
        if (statusSelect{{ $laporan->id }}.value !== 'Rejected') {
            catatanContainer{{ $laporan->id }}.style.display = 'none';
        }
    }
});
</script>
@endif

<!-- MODAL VIEW DETAIL (GABUNGAN BEFORE & AFTER) -->
 
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

<!-- ============================================ -->
<!-- MODAL EDIT (UNTUK STATUS OPEN) -->
<!-- ============================================ -->
@if(isset($permission) && $permission->can_edit && $laporan->status === 'Open')
<div class="modal fade" id="editSafetyRidingModal{{ $laporan->id }}" tabindex="-1" aria-labelledby="editSafetyRidingModalLabel{{ $laporan->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editSafetyRidingModalLabel{{ $laporan->id }}"><i class="bi bi-pencil-square me-1"></i> Detail/Edit Laporan Safety Riding #{{ $laporan->id }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ url('/she/safety-riding/update/' . $laporan->id) }}" method="POST" enctype="multipart/form-data" id="editForm{{ $laporan->id }}">
                    @csrf
                    @method('PUT')
                    
                    <h6 class="fw-bold text-primary mb-3">I. Data Temuan Safety Riding</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_waktu_kejadian_{{ $laporan->id }}" class="form-label">Waktu Kejadian <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="waktu_kejadian" id="edit_waktu_kejadian_{{ $laporan->id }}" class="form-control" value="{{ old('waktu_kejadian', \Carbon\Carbon::parse($laporan->waktu_kejadian)->format('Y-m-d\TH:i')) }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_section_{{ $laporan->id }}" class="form-label">Section <span class="text-danger">*</span></label>
                            <select name="section_id" id="edit_section_{{ $laporan->id }}" class="form-select" required>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ $laporan->section_id == $section->id ? 'selected' : '' }}>
                                        {{ $section->section }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_nama_{{ $laporan->id }}" class="form-label">Nama Pelanggar <span class="text-danger">*</span></label>
                            <select name="user_id" id="edit_nama_{{ $laporan->id }}" class="form-select" required>
                                @php
                                    $sectionUsers = \App\Models\User::where('section_id', $laporan->section_id)->get();
                                @endphp
                                @foreach($sectionUsers as $user)
                                    <option value="{{ $user->id }}" {{ $laporan->user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="edit_type_kendaraan_{{ $laporan->id }}" class="form-label">Tipe Kendaraan <span class="text-danger">*</span></label>
                            <input type="text" name="type_kendaraan" id="edit_type_kendaraan_{{ $laporan->id }}" class="form-control" value="{{ old('type_kendaraan', $laporan->type_kendaraan) }}" placeholder="Contoh: Sepeda Motor, Mobil Box" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_nopol_{{ $laporan->id }}" class="form-label">Nomor Polisi (NOPOL) <span class="text-danger">*</span></label>
                            <input type="text" name="nopol" id="edit_nopol_{{ $laporan->id }}" class="form-control" value="{{ old('nopol', $laporan->nopol) }}" placeholder="Contoh: T1234ABC" required>
                        </div>
                    </div>
                    
                    <h6 class="fw-bold text-primary mt-4 mb-3">II. Jenis Pelanggaran</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block fw-bold">Pelanggaran Dokumen (PD)</label>
                            <div class="form-check-group border p-2 rounded" style="max-height: 200px; overflow-y: auto;">
                                @php
                                    $selectedPds = $laporan->pds->pluck('id')->toArray();
                                @endphp
                                @foreach($masterPds as $pd)
                                    <div class="form-check">
                                        <input class="form-check-input pelanggaran-checkbox-edit" type="checkbox" name="pd_id[]" value="{{ $pd->id }}" id="edit_pd_check_{{ $laporan->id }}_{{ $pd->id }}" {{ in_array($pd->id, old('pd_id', $selectedPds)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_pd_check_{{ $laporan->id }}_{{ $pd->id }}">
                                            {{ $pd->nama_pd }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block fw-bold">Pelanggaran Fisik (PF)</label>
                            <div class="form-check-group border p-2 rounded" style="max-height: 200px; overflow-y: auto;">
                                @php
                                    $selectedPfs = $laporan->pfs->pluck('id')->toArray();
                                @endphp
                                @foreach($masterPfs as $pf)
                                    <div class="form-check">
                                        <input class="form-check-input pelanggaran-checkbox-edit" type="checkbox" name="pf_id[]" value="{{ $pf->id }}" id="edit_pf_check_{{ $laporan->id }}_{{ $pf->id }}" {{ in_array($pf->id, old('pf_id', $selectedPfs)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_pf_check_{{ $laporan->id }}_{{ $pf->id }}">
                                            {{ $pf->nama_pf }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_total_pelanggaran_{{ $laporan->id }}" class="form-label fw-bold">Total Pelanggaran</label>
                        <input type="text" name="total_pelanggaran" id="edit_total_pelanggaran_{{ $laporan->id }}" class="form-control" value="{{ $editTotalPelanggaran }}" readonly>
                        <input type="hidden" name="total_upload_files" id="edit_total_upload_files_{{ $laporan->id }}" value="{{ $editTotalPelanggaran }}">
                    </div>

                    <div class="mb-3">
                        <label for="edit_keterangan_pelanggaran_{{ $laporan->id }}" class="form-label">Keterangan Pelanggaran <span class="text-danger">*</span></label>
                        <textarea name="keterangan_pelanggaran" id="edit_keterangan_pelanggaran_{{ $laporan->id }}" class="form-control" rows="3" required>{{ old('keterangan_pelanggaran', $laporan->keterangan_pelanggaran) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Bukti Foto Saat Ini:</label>
                        @if(count($editBuktiArray) > 0)
                            <div class="row">
                                @foreach($editBuktiArray as $index => $path)
                                <div class="col-md-3 mb-2">
                                    <div class="card">
                                        <img src="{{ asset('storage/' . $path) }}" class="card-img-top" style="height: 100px; object-fit: cover;">
                                        <div class="card-body p-2 text-center">
                                            <small class="text-muted">Foto {{ $index + 1 }}</small>
                                            <br>
                                            <button type="button" class="btn btn-sm btn-danger mt-1" onclick="deleteImage({{ $laporan->id }}, {{ $index }})">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Tidak ada bukti foto.</p>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tambah Bukti Foto Baru:</label>
                        <small class="text-danger d-block mb-2">* Upload sesuai jumlah pelanggaran yang dipilih</small>
                        
                        <div id="imagePreviewContainerEdit_{{ $laporan->id }}" class="row mb-2"></div>
                        
                        <div id="dynamicUploadContainerEdit_{{ $laporan->id }}">
                            <!-- Dynamic fields akan digenerate oleh JavaScript -->
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_status_{{ $laporan->id }}" class="form-label fw-bold text-success">Status</label>
                        <select name="status" id="edit_status_{{ $laporan->id }}" class="form-select" disabled>
                            <option value="Open" {{ old('status', $laporan->status) == 'Open' ? 'selected' : '' }}>Open</option>
                            <option value="Progress" {{ old('status', $laporan->status) == 'Progress' ? 'selected' : '' }}>Progress</option>
                            <option value="Close" {{ old('status', $laporan->status) == 'Close' ? 'selected' : '' }}>Close</option>
                            <option value="Rejected" {{ old('status', $laporan->status) == 'Rejected' ? 'selected' : '' }}>Reject</option>
                        </select>
                    </div>
                    
                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i> Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Update Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@endforeach
@endsection