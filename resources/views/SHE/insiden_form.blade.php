@extends('layout.sidebar')

@section('content')

<?php
    $readonly = '';
    $sheOnly = false;
    if($namaSection === "SHE"){
        $readonly = "readonly";
        $sheOnly = true;
    }
?>

<div class="content p-3">
    {{-- Header Section --}}
    <div class="header-section mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-1">
                    <i class="bi bi-clipboard2-plus me-2 text-primary"></i>Form Laporan Insiden
                </h2>
                <p class="text-muted mb-0">Lengkapi formulir berikut untuk melaporkan insiden yang terjadi</p>
                @if($sheOnly)
                    <span class="badge bg-success mt-2">
                        <i class="bi bi-shield-check me-1"></i>Mode SHE - Data Insiden diisi oleh pelapor
                    </span>
                @endif
            </div>
            <a href="{{ url('/she/insiden') }}" class="btn btn-outline-secondary btn-back">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
        <hr class="mt-3 mb-0">
    </div>

    {{-- Alert Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div>
                    <h6 class="alert-heading mb-1">Terjadi Kesalahan</h6>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main Form Card --}}
    <div class="card shadow border-0 form-card">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 text-primary">
                <i class="bi bi-clipboard2-data me-2"></i>Formulir Pelaporan Insiden
            </h5>
        </div>
        
        <div class="card-body p-4">
            <form id="insidenForm" action="{{ url('/she/insiden/store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Section 1: Data Insiden --}}
                <div class="form-section mb-5">
                    <div class="section-header mb-4">
                        <h6 class="section-title">
                            <span class="section-icon">I</span>
                            <i class="bi bi-info-circle me-2"></i>Data Insiden
                            @if($sheOnly)
                                <span class="badge bg-secondary ms-2">Diisi oleh Pelapor</span>
                            @endif
                        </h6>
                        <div class="section-divider"></div>
                    </div>
                    
                    <div class="row g-4">
                        {{-- Tanggal --}}
                        <div class="col-md-6 col-lg-3">
                            <label for="tanggal" class="form-label fw-semibold">
                                Tanggal <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-calendar text-primary"></i>
                                </span>
                                <input type="date" id="tanggal" name="tanggal" 
                                    class="form-control @error('tanggal') is-invalid @enderror" 
                                    value="{{ old('tanggal') ?: \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}" 
                                    max="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}" required>
                            </div>
                            @error('tanggal')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jam --}}
                        <div class="col-md-6 col-lg-3">
                            <label for="jam" class="form-label fw-semibold">
                                Jam <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-clock text-primary"></i>
                                </span>
                                <input type="time" id="jam" name="jam" 
                                    class="form-control @error('jam') is-invalid @enderror" 
                                    value="{{ old('jam') ?: \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }}" required>
                            </div>
                            @error('jam')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Lokasi --}}
                        <div class="col-md-6 col-lg-3">
                            <label for="lokasi" class="form-label fw-semibold">
                                Lokasi Kejadian <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-geo-alt text-primary"></i>
                                </span>
                                <input type="text" id="lokasi" name="lokasi" 
                                    class="form-control @error('lokasi') is-invalid @enderror" 
                                    placeholder="Contoh: Area Produksi Line 1" 
                                    value="{{ old('lokasi') }}" required>
                            </div>
                            @error('lokasi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <div class="col-md-6 col-lg-3">
                            <label for="kategori" class="form-label fw-semibold">
                                Kategori Accident <span class="text-danger">*</span>
                            </label>
                            <select id="kategori" name="kategori" 
                                    class="form-select @error('kategori') is-invalid @enderror" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Work Accident" {{ old('kategori') === 'Work Accident' ? 'selected' : '' }}>Work Accident</option>
                                <option value="Traffic Accident" {{ old('kategori') === 'Traffic Accident' ? 'selected' : '' }}>Traffic Accident</option>
                                <option value="Fire Accident" {{ old('kategori') === 'Fire Accident' ? 'selected' : '' }}>Fire Accident</option>
                                <option value="Forklift Accident" {{ old('kategori') === 'Forklift Accident' ? 'selected' : '' }}>Forklift Accident</option>
                                <option value="Molten Spill Incident" {{ old('kategori') === 'Molten Spill Incident' ? 'selected' : '' }}>Molten Spill Incident</option>
                                <option value="Property Damage Incident" {{ old('kategori') === 'Property Damage Incident' ? 'selected' : '' }}>Property Damage Incident</option>
                            </select>
                            @error('kategori')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tipe Work Accident (conditional) --}}
                        <div class="col-md-6 col-lg-3" id="workAccidentDiv" style="display:none;">
                            <label for="work_accident_type" class="form-label fw-semibold">
                                Tipe Work Accident <span class="text-danger">*</span>
                            </label>
                            <select id="work_accident_type" name="work_accident_type" 
                                    class="form-select @error('work_accident_type') is-invalid @enderror">
                                <option value="">Pilih Tipe</option>
                                <option value="Loss Day" {{ old('work_accident_type') === 'Loss Day' ? 'selected' : '' }}>Loss Day</option>
                                <option value="Light" {{ old('work_accident_type') === 'Light' ? 'selected' : '' }}>Light</option>
                            </select>
                            @error('work_accident_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Section --}}
                        <div class="col-md-6 col-lg-3">
                            <label for="section_id" class="form-label fw-semibold">
                                Section <span class="text-danger">*</span>
                            </label>
                            <select id="section_id" name="section_id" 
                                    class="form-select @error('section_id') is-invalid @enderror" required>
                                <option value="">Pilih Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" 
                                            data-department="{{ $section->department }}"
                                            {{ old('section_id', $user->section_id) == $section->id ? 'selected' : '' }}>
                                        {{ $section->section }}
                                    </option>
                                @endforeach
                            </select>
                            @error('section_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Departemen (Auto fill dari section) --}}
                        <div class="col-md-6 col-lg-3">
                            <label for="department" class="form-label fw-semibold">
                                Departemen <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-building text-primary"></i>
                                </span>
                                <input type="text" id="department" name="department" 
                                    class="form-control @error('department') is-invalid @enderror" 
                                    readonly required>
                            </div>
                            <small class="text-muted">Otomatis terisi berdasarkan section</small>
                            @error('department')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kondisi Luka --}}
                        <div class="col-md-6 col-lg-3">
                            <label for="kondisi_luka" class="form-label fw-semibold">
                                Kondisi Luka
                            </label>
                            <input type="text" id="kondisi_luka" name="kondisi_luka" 
                                class="form-control @error('kondisi_luka') is-invalid @enderror" 
                                placeholder="Contoh: Luka ringan pada tangan" 
                                value="{{ old('kondisi_luka') }}">
                            @error('kondisi_luka')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- ACTION BUTTONS --}}
                {{-- ============================================================ --}}
                <div class="form-actions pt-4 border-top">
                    <div class="d-flex justify-content-between">
                        <a href="{{ url('/she/insiden') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </a>
                        
                        <div>
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-lg">
                                <i class="bi bi-send-check me-2"></i>
                                <span id="submitText">Kirim Laporan</span>
                                <span id="submitSpinner" class="spinner-border spinner-border-sm ms-2 d-none"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript LENGKAP --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================
    // 1. AUTO-FILL DEPARTMENT BERDASARKAN SECTION
    // ============================================
    const sectionSelect = document.getElementById('section_id');
    const departmentInput = document.getElementById('department');
    
    function updateDepartment() {
        const selectedOption = sectionSelect.options[sectionSelect.selectedIndex];
        const department = selectedOption.getAttribute('data-department');
        
        if (department) {
            departmentInput.value = department; // Auto-fill department
        } else {
            departmentInput.value = '';
        }
    }
    
    // Initialize on page load
    if (sectionSelect && sectionSelect.value) {
        updateDepartment();
    }
    
    // Update department when section changes
    if (sectionSelect) {
        sectionSelect.addEventListener('change', updateDepartment);
    }
    
    // Jika ada old value dari form validation
    const oldDepartment = "{{ old('department') }}";
    if (oldDepartment && departmentInput) {
        departmentInput.value = oldDepartment;
    }
    
    // Jika user SHE, auto-select section SHE
    const namaSection = "{{ $namaSection }}";
    const sheOnly = {{ $sheOnly ? 'true' : 'false' }};
    
    if (sheOnly && sectionSelect) {
        const userSectionId = "{{ $user->section_id }}";
        if (userSectionId) {
            sectionSelect.value = userSectionId;
            updateDepartment();
        }
    }
    
    // ============================================
    // 2. TOGGLE WORK ACCIDENT TYPE
    // ============================================
    const kategoriSelect = document.getElementById('kategori');
    const workAccidentDiv = document.getElementById('workAccidentDiv');
    const workAccidentType = document.getElementById('work_accident_type');
    
    function toggleWorkAccident() {
        if (kategoriSelect && kategoriSelect.value === 'Work Accident') {
            if (workAccidentDiv) workAccidentDiv.style.display = 'block';
            if (workAccidentType) workAccidentType.required = true;
        } else {
            if (workAccidentDiv) workAccidentDiv.style.display = 'none';
            if (workAccidentType) {
                workAccidentType.required = false;
                workAccidentType.value = '';
            }
        }
    }
    
    if (kategoriSelect) {
        kategoriSelect.addEventListener('change', toggleWorkAccident);
        toggleWorkAccident();
    }
    
    // ============================================
    // 3. CHARACTER COUNTERS UNTUK SEMUA TEXTAREA
    // ============================================
    function setupCharacterCounter(textareaId, counterId, maxLength) {
        const textarea = document.getElementById(textareaId);
        const counter = document.getElementById(counterId);
        
        if (!textarea || !counter) return;
        
        function updateCounter() {
            const length = textarea.value.length;
            counter.textContent = `${length}/${maxLength}`;
            
            if (length > maxLength * 0.9) {
                counter.classList.remove('text-muted');
                counter.classList.add('text-warning');
            } else if (length > maxLength * 0.95) {
                counter.classList.remove('text-warning');
                counter.classList.add('text-danger');
            } else {
                counter.classList.remove('text-warning', 'text-danger');
                counter.classList.add('text-muted');
            }
        }
        
        textarea.addEventListener('input', updateCounter);
        updateCounter();
    }
    
    // Setup counters untuk semua textarea
    setupCharacterCounter('kronologi', 'kronologiCount', 3000);
    setupCharacterCounter('kronologi_detail', 'kronologiDetailCount', 3000);
    setupCharacterCounter('penyebab_langsung', 'penyebabCount', 2000);
    setupCharacterCounter('penyebab_dasar', 'dasarCount', 2000);
    setupCharacterCounter('tindakan_perbaikan', 'perbaikanCount', 2000);
    setupCharacterCounter('tindakan_pencegahan', 'pencegahanCount', 2000);
    
    // ============================================
    // 4. FILE UPLOAD UNTUK PELAPOR (NON-SHE)
    // ============================================
    const fotoInput = document.getElementById('foto');
    const uploadArea = document.getElementById('uploadArea');
    const fotoPreview = document.getElementById('fotoPreview');
    const fotoError = document.getElementById('fotoError');
    
    if (fotoInput && uploadArea) {
        const MAX_SIZE = 10 * 1024 * 1024;
        const ALLOWED_TYPES = ['image/jpeg', 'image/png'];
        
        // Drag and drop functionality
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            uploadArea.classList.add('highlight');
        }
        
        function unhighlight() {
            uploadArea.classList.remove('highlight');
        }
        
        uploadArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fotoInput.files = files;
            handleFiles(files);
        }
        
        function handleFiles(files) {
            clearPreview();
            let hasError = false;
            const validFiles = [];
            
            Array.from(files).forEach((file, index) => {
                if (!ALLOWED_TYPES.includes(file.type)) {
                    hasError = true;
                    showError(`File "${file.name}" tidak didukung. Hanya JPG/PNG yang diperbolehkan.`);
                    return;
                }
                
                if (file.size > MAX_SIZE) {
                    hasError = true;
                    showError(`File "${file.name}" melebihi 10MB.`);
                    return;
                }
                
                validFiles.push(file);
                
                // Preview image
                const reader = new FileReader();
                reader.onload = (e) => {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    previewItem.innerHTML = `
                        <div class="preview-image-container">
                            <img src="${e.target.result}" class="preview-image">
                            <div class="preview-overlay">
                                <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removePelaporFile(${index})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="preview-info">
                                <small class="text-truncate d-block">${file.name}</small>
                                <small class="text-muted">${formatBytes(file.size)}</small>
                            </div>
                        </div>
                    `;
                    fotoPreview.appendChild(previewItem);
                };
                reader.readAsDataURL(file);
            });
            
            if (hasError) {
                const dt = new DataTransfer();
                validFiles.forEach(file => dt.items.add(file));
                fotoInput.files = dt.files;
            }
        }
        
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
        
        function showError(message) {
            if (fotoError) {
                fotoError.textContent = message;
                fotoError.style.display = 'block';
                fotoError.classList.add('show');
                
                setTimeout(() => {
                    fotoError.classList.remove('show');
                    setTimeout(() => {
                        fotoError.style.display = 'none';
                    }, 300);
                }, 5000);
            }
        }
        
        function clearPreview() {
            if (fotoPreview) fotoPreview.innerHTML = '';
            if (fotoError) {
                fotoError.style.display = 'none';
                fotoError.classList.remove('show');
            }
        }
        
        fotoInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleFiles(this.files);
            }
        });
        
        // Remove file function untuk pelapor
        window.removePelaporFile = function(index) {
            const dt = new DataTransfer();
            const files = Array.from(fotoInput.files);
            files.splice(index, 1);
            
            files.forEach(file => dt.items.add(file));
            fotoInput.files = dt.files;
            
            clearPreview();
            if (fotoInput.files.length > 0) {
                handleFiles(fotoInput.files);
            }
        };
    }
    
    // ============================================
    // 5. FILE UPLOAD UNTUK SHE (INVESTIGASI)
    // ============================================
    const fotoInvestigasiInput = document.getElementById('foto_investigasi');
    const uploadAreaInvestigasi = document.getElementById('uploadAreaInvestigasi');
    const fotoInvestigasiPreview = document.getElementById('fotoInvestigasiPreview');
    const fotoInvestigasiError = document.getElementById('fotoInvestigasiError');
    
    if (fotoInvestigasiInput && uploadAreaInvestigasi) {
        const MAX_SIZE = 10 * 1024 * 1024;
        const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'application/pdf'];
        
        // Drag and drop functionality
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadAreaInvestigasi.addEventListener(eventName, preventDefaultsInvestigasi, false);
        });
        
        function preventDefaultsInvestigasi(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadAreaInvestigasi.addEventListener(eventName, highlightInvestigasi, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadAreaInvestigasi.addEventListener(eventName, unhighlightInvestigasi, false);
        });
        
        function highlightInvestigasi() {
            uploadAreaInvestigasi.classList.add('highlight');
        }
        
        function unhighlightInvestigasi() {
            uploadAreaInvestigasi.classList.remove('highlight');
        }
        
        uploadAreaInvestigasi.addEventListener('drop', handleDropInvestigasi, false);
        
        function handleDropInvestigasi(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fotoInvestigasiInput.files = files;
            handleFilesInvestigasi(files);
        }
        
        function handleFilesInvestigasi(files) {
            clearPreviewInvestigasi();
            let hasError = false;
            const validFiles = [];
            
            Array.from(files).forEach((file, index) => {
                if (!ALLOWED_TYPES.includes(file.type)) {
                    hasError = true;
                    showErrorInvestigasi(`File "${file.name}" tidak didukung. Hanya JPG, PNG, PDF yang diperbolehkan.`);
                    return;
                }
                
                if (file.size > MAX_SIZE) {
                    hasError = true;
                    showErrorInvestigasi(`File "${file.name}" melebihi 10MB.`);
                    return;
                }
                
                validFiles.push(file);
                
                // Preview file
                const reader = new FileReader();
                reader.onload = (e) => {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    
                    let previewContent = '';
                    if (file.type.includes('image')) {
                        previewContent = `<img src="${e.target.result}" class="preview-image">`;
                    } else {
                        previewContent = `
                            <div class="preview-document">
                                <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
                            </div>
                        `;
                    }
                    
                    previewItem.innerHTML = `
                        <div class="preview-image-container">
                            ${previewContent}
                            <div class="preview-overlay">
                                <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeInvestigasiFile(${index})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="preview-info">
                                <small class="text-truncate d-block">${file.name}</small>
                                <small class="text-muted">${formatBytes(file.size)}</small>
                            </div>
                        </div>
                    `;
                    fotoInvestigasiPreview.appendChild(previewItem);
                };
                
                if (file.type.includes('image')) {
                    reader.readAsDataURL(file);
                } else {
                    // For PDF, create preview without reading file
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    previewItem.innerHTML = `
                        <div class="preview-image-container">
                            <div class="preview-document">
                                <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
                            </div>
                            <div class="preview-overlay">
                                <button type="button" class="btn btn-sm btn-danger btn-remove" onclick="removeInvestigasiFile(${index})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="preview-info">
                                <small class="text-truncate d-block">${file.name}</small>
                                <small class="text-muted">${formatBytes(file.size)}</small>
                            </div>
                        </div>
                    `;
                    fotoInvestigasiPreview.appendChild(previewItem);
                }
            });
            
            if (hasError) {
                const dt = new DataTransfer();
                validFiles.forEach(file => dt.items.add(file));
                fotoInvestigasiInput.files = dt.files;
            }
        }
        
        function showErrorInvestigasi(message) {
            if (fotoInvestigasiError) {
                fotoInvestigasiError.textContent = message;
                fotoInvestigasiError.style.display = 'block';
                fotoInvestigasiError.classList.add('show');
                
                setTimeout(() => {
                    fotoInvestigasiError.classList.remove('show');
                    setTimeout(() => {
                        fotoInvestigasiError.style.display = 'none';
                    }, 300);
                }, 5000);
            }
        }
        
        function clearPreviewInvestigasi() {
            if (fotoInvestigasiPreview) fotoInvestigasiPreview.innerHTML = '';
            if (fotoInvestigasiError) {
                fotoInvestigasiError.style.display = 'none';
                fotoInvestigasiError.classList.remove('show');
            }
        }
        
        fotoInvestigasiInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                handleFilesInvestigasi(this.files);
            }
        });
        
        // Remove file function untuk investigasi
        window.removeInvestigasiFile = function(index) {
            const dt = new DataTransfer();
            const files = Array.from(fotoInvestigasiInput.files);
            files.splice(index, 1);
            
            files.forEach(file => dt.items.add(file));
            fotoInvestigasiInput.files = dt.files;
            
            clearPreviewInvestigasi();
            if (fotoInvestigasiInput.files.length > 0) {
                handleFilesInvestigasi(fotoInvestigasiInput.files);
            }
        };
    }
    
    // ============================================
    // 6. DATE VALIDATION
    // ============================================
    const tanggalInput = document.getElementById('tanggal');
    const tanggalInvestigasiInput = document.getElementById('tanggal_investigasi');
    const targetPenyelesaianInput = document.getElementById('target_penyelesaian');
    
    // Set max date for semua date inputs (gunakan waktu lokal, bukan UTC)
    const now = new Date();
    const today = now.getFullYear() + '-' + 
                  String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                  String(now.getDate()).padStart(2, '0');
    if (tanggalInput) tanggalInput.max = today;
    
    // Update min date for investigasi ketika tanggal berubah
    if (tanggalInput && tanggalInvestigasiInput) {
        tanggalInput.addEventListener('change', function() {
            tanggalInvestigasiInput.min = this.value;
        });
    }
    
    // Set min date for target penyelesaian
    if (tanggalInvestigasiInput && targetPenyelesaianInput) {
        if (tanggalInvestigasiInput.value) {
            targetPenyelesaianInput.min = tanggalInvestigasiInput.value;
        }
        
        tanggalInvestigasiInput.addEventListener('change', function() {
            targetPenyelesaianInput.min = this.value;
        });
    }
    
    // ============================================
    // 7. FORM SUBMISSION
    // ============================================
    const form = document.getElementById('insidenForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Client-side validation
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    
                    // Add validation message
                    if (!field.nextElementSibling?.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback d-block';
                        errorDiv.textContent = 'Field ini wajib diisi';
                        field.parentNode.appendChild(errorDiv);
                    }
                } else {
                    field.classList.remove('is-invalid');
                    const errorDiv = field.parentNode.querySelector('.invalid-feedback');
                    if (errorDiv && errorDiv.textContent === 'Field ini wajib diisi') {
                        errorDiv.remove();
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Harap lengkapi semua field yang wajib diisi');
                return;
            }
            
            // Show loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                if (submitText) submitText.textContent = 'Mengirim...';
                if (submitSpinner) submitSpinner.classList.remove('d-none');
            }
        });
    }
    
    // ============================================
    // 8. AUTO-HIDE ALERTS
    // ============================================
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            setTimeout(() => bsAlert.close(), 5000);
        });
    }, 1000);
});
</script>

{{-- CSS Styles LENGKAP --}}
<style>
:root {
    --primary-color: #0d6efd;
    --primary-light: #e7f1ff;
    --secondary-color: #6c757d;
    --success-color: #198754;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --light-color: #f8f9fa;
    --border-color: #dee2e6;
    --border-radius: 8px;
    --box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    --box-shadow-hover: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* Header Styling */
.page-title {
    color: #0f172a;
    font-weight: 700;
    font-size: 1.75rem;
}

.btn-back {
    border-radius: 6px;
    padding: 8px 16px;
    transition: all 0.3s ease;
}

.btn-back:hover {
    transform: translateX(-2px);
}

/* Form Card */
.form-card {
    border-radius: 12px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.form-card .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid var(--primary-color);
}

/* Section Styling */
.form-section {
    padding: 1.5rem;
    background: var(--light-color);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    margin-bottom: 1.5rem;
}

.section-header {
    position: relative;
}

.section-title {
    color: var(--primary-color);
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.section-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    font-size: 0.875rem;
    font-weight: 600;
    margin-right: 12px;
}

.section-divider {
    height: 2px;
    background: linear-gradient(90deg, var(--primary-color) 0%, transparent 100%);
    opacity: 0.3;
}

/* Form Controls */
.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #495057;
}

.form-control, .form-select {
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    transform: translateY(-1px);
}

.input-group-text {
    background-color: var(--light-color);
    border-right: none;
    color: var(--primary-color);
}

.kronologi-textarea {
    resize: vertical;
    min-height: 120px;
    max-height: 300px;
}

.textarea-footer {
    display: flex;
    justify-content: space-between;
    margin-top: 0.5rem;
    font-size: 0.85rem;
}

/* File Upload */
.file-upload-wrapper {
    position: relative;
}

.file-upload-area {
    border: 2px dashed var(--border-color);
    border-radius: var(--border-radius);
    background-color: var(--light-color);
    padding: 2.5rem 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.file-upload-area:hover, .file-upload-area.highlight {
    border-color: var(--primary-color);
    background-color: var(--primary-light);
    transform: translateY(-2px);
    box-shadow: var(--box-shadow);
}

.upload-placeholder {
    pointer-events: none;
}

.upload-icon {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
    opacity: 0.7;
}

.upload-text h6 {
    color: var(--primary-color);
    font-weight: 600;
}

.file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 10;
}

/* Preview Grid */
.preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 1rem;
}

.preview-item {
    position: relative;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.preview-image-container {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid var(--border-color);
    background: white;
}

.preview-image {
    width: 100%;
    height: 140px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.preview-image-container:hover .preview-image {
    transform: scale(1.05);
}

.preview-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.preview-image-container:hover .preview-overlay {
    opacity: 1;
}

.btn-remove {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.preview-info {
    padding: 0.5rem;
    background: white;
    border-top: 1px solid var(--border-color);
}

.preview-document {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    padding: 1rem;
}

/* Error Message */
.error-message {
    color: var(--danger-color);
    font-size: 0.875rem;
    padding: 0.75rem 1rem;
    background: #fff5f5;
    border: 1px solid #f5c2c7;
    border-radius: 6px;
    display: none;
    animation: slideIn 0.3s ease;
}

.error-message.show {
    display: block;
}

@keyframes slideIn {
    from { 
        opacity: 0;
        transform: translateY(-10px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}

/* Form Actions */
.form-actions {
    margin-top: 2rem;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, #0b5ed7 100%);
    border: none;
    box-shadow: var(--box-shadow);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--box-shadow-hover);
}

.btn-primary:disabled {
    opacity: 0.7;
    transform: none;
    box-shadow: var(--box-shadow);
}

.btn-warning {
    background: linear-gradient(135deg, var(--warning-color) 0%, #e0a800 100%);
    border: none;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: var(--box-shadow-hover);
}

/* Readonly styling */
input[readonly], select[readonly], textarea[readonly] {
    background-color: #f8f9fa !important;
    border-color: #dee2e6 !important;
    cursor: not-allowed;
    opacity: 0.8;
}

/* Department input styling */
#department {
    background-color: #f8f9fa;
}

/* Badge styling */
.badge {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.25rem 0.5rem;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .content {
        padding: 1rem !important;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .form-card .card-body {
        padding: 1.5rem !important;
    }
    
    .form-section {
        padding: 1rem;
    }
    
    .preview-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
    
    .form-actions .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .form-actions .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
    
    .section-title {
        flex-wrap: wrap;
    }
    
    .section-title .badge {
        margin-top: 0.25rem;
        margin-left: 0 !important;
    }
}

/* Accessibility */
.form-control:focus-visible,
.form-select:focus-visible,
.btn:focus-visible {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

/* Print Styles */
@media print {
    .btn-back, .form-actions, .file-upload-area {
        display: none !important;
    }
    
    .form-section {
        border: 1px solid #000;
        break-inside: avoid;
    }
}

/* Highlight untuk section SHE */
.form-section:nth-child(n+5) {
    border-left: 4px solid var(--success-color);
}
</style>
@endsection