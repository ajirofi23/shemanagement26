@extends('layout.sidebar')

@section('content')

<?php
    // Pastikan permissions dari controller tersedia
    if(!isset($permissions)) {
        $permissions = (object)[
            'can_read' => false,
            'can_add' => false,
            'can_edit' => false,
            'can_delete' => false
        ];
    }
    
    // Cek URL untuk menentukan role
    $currentUrl = request()->path();
    $currentUrl = '/' . ltrim($currentUrl, '/');
    
    $isPIC = strpos($currentUrl, '/pic/insiden/edit') !== false;
    $isSHE = strpos($currentUrl, '/she/insiden/edit') !== false;
    $isManager = strpos($currentUrl, '/manager/insiden/edit') !== false;
    
    // Cek apakah user adalah pembuat laporan
    $isCreator = Auth::id() == $insiden->created_by;
    
    // Tentukan apa yang bisa diedit berdasarkan role dan status
    $canEditDataAwal = false; // Tanggal, jam, lokasi, kondisi luka
    $canEditKategori = false; // Kategori, tipe work accident, section, departemen
    $canEditKronologi = false; // Kronologi, foto, keterangan lain
    $canEditStatus = false; // Status (close, progress, reject)
    $canAddNote = false; // Catatan SHE
    $canViewOnly = true; // Default view only
    $canSelectUser = false; // Memilih data user/korban
    
    // Logika permission berdasarkan role dan status
    if ($permissions->can_edit) {
        // CREATOR: bisa edit data awal termasuk kategori ketika status open
        if ($isCreator && $insiden->status === 'open') {
            $canEditDataAwal = true;
            $canEditKategori = true; // Creator bisa edit kategori dan department
            $canEditStatus = false; // Creator tidak bisa ubah status
            $canViewOnly = false;
             $canSelectUser = true;
        }
        // PIC: hanya bisa isi setelah kondisi luka ketika status open
        elseif ($isPIC && $insiden->status === 'open'  || $isPIC && $insiden->status == 'rejected') {
            $canEditKronologi = true;
            $canViewOnly = false;
            $canEditStatus = true;
        }
        elseif ($isSHE && $insiden->status === 'progress') {
            $canEditStatus = true;
            $canAddNote = true;
            $canViewOnly = false;
        }
        elseif ($isManager && $permissions->can_edit) {
            $canEditStatus = true;
            $canViewOnly = false;
        }
    }
    
    // Proses foto untuk tampilan - HANDLE JSON DECODE
    $fotosArray = [];
    if (!empty($insiden->foto)) {
        if (is_string($insiden->foto)) {
            $decoded = json_decode($insiden->foto, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $fotosArray = $decoded;
            } else {
                $fotosArray = [$insiden->foto];
            }
        } elseif (is_array($insiden->foto)) {
            $fotosArray = $insiden->foto;
        }
    }
    
    // Pastikan existing_fotos adalah JSON string untuk input hidden
    $existingFotosJson = !empty($fotosArray) ? json_encode($fotosArray) : '[]';
    
    // Ambil data sections dari database
    $sections = App\Models\Section::all();
?>

<div class="content p-3">
    <div class="header-section mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-1">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>
                    @if($canEditDataAwal)
                        Edit Data Insiden - Creator Mode
                    @elseif($canEditKronologi)
                        Lengkapi Kronologi Insiden - PIC Mode
                    @elseif($canAddNote)
                        Tinjauan Insiden - SHE Mode
                    @elseif($canEditStatus && $isManager)
                        Update Status Insiden - Manager Mode
                    @else
                        Detail Laporan Insiden
                    @endif
                </h2>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <p class="text-muted mb-0">ID: {{ $insiden->id }}</p>
                    <span class="badge {{ $insiden->status === 'open' ? 'bg-warning' : ($insiden->status === 'progress' ? 'bg-primary' : ($insiden->status === 'closed' ? 'bg-success' : ($insiden->status === 'rejected' ? 'bg-danger' : 'bg-secondary'))) }}">
                        {{ ucfirst($insiden->status) }}
                    </span>
                    @if($insiden->status === 'open' && $isPIC)
                    <span class="badge bg-primary">
                        <i class="bi bi-clock-history me-1"></i>Menunggu Kronologi PIC
                    </span>
                    @elseif($insiden->status === 'progress' && $isSHE)
                    <span class="badge bg-info">
                        <i class="bi bi-shield-check me-1"></i>Menunggu Review SHE
                    </span>
                    @endif
                </div>
                
                <div class="d-flex flex-wrap gap-2">
                    @if($isManager)
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-person-vcard me-1"></i>Manager
                    </span>
                    @elseif($isPIC)
                    <span class="badge bg-info">
                        <i class="bi bi-person-badge me-1"></i>PIC
                    </span>
                    @elseif($isSHE)
                    <span class="badge bg-success">
                        <i class="bi bi-shield-check me-1"></i>SHE
                    </span>
                    @endif
                    
                    @if($isCreator)
                    <span class="badge bg-primary">
                        <i class="bi bi-person-fill-add me-1"></i>Pelapor/Creator
                    </span>
                    @endif
                    
                    @if(!$canViewOnly)
                    <span class="badge bg-success">
                        <i class="bi bi-pencil me-1"></i>Edit Mode
                    </span>
                    @else
                    <span class="badge bg-secondary">
                        <i class="bi bi-eye me-1"></i>View Only
                    </span>
                    @endif
                </div>
            </div>
            <a href="{{ url()->previous() ?: ($isPIC ? '/pic/insiden' : ($isManager ? '/manager/insiden' : ($isSHE ? '/she/insiden' : '/insiden'))) }}" 
               class="btn btn-outline-secondary btn-back">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
        <hr class="mt-3 mb-0">
    </div>

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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0 form-card">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-primary">
                    <i class="bi bi-clipboard2-data me-2"></i>Formulir Laporan Insiden
                </h5>
                <div class="d-flex gap-2">
                    @if($canViewOnly)
                    <span class="badge bg-secondary">
                        <i class="bi bi-eye me-1"></i>View Only
                    </span>
                    @endif
                    @if($canEditDataAwal)
                    <span class="badge bg-warning">
                        <i class="bi bi-pencil me-1"></i>Edit Data Awal
                    </span>
                    @endif
                    @if($canEditKategori)
                    <span class="badge bg-warning">
                        <i class="bi bi-tags me-1"></i>Edit Kategori
                    </span>
                    @endif
                    @if($canEditKronologi)
                    <span class="badge bg-info">
                        <i class="bi bi-journal-text me-1"></i>Edit Kronologi
                    </span>
                    @endif
                    @if($canAddNote)
                    <span class="badge bg-success">
                        <i class="bi bi-chat-square-text me-1"></i>Tinjau SHE
                    </span>
                    @endif
                    @if($canEditStatus)
                    <span class="badge bg-primary">
                        <i class="bi bi-arrow-repeat me-1"></i>Update Status
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @php
            // Tentukan URL update berdasarkan role
            $updateUrl = '';
            if ($isPIC) {
                $updateUrl = url('/pic/insiden/update/' . $insiden->id);
            } elseif ($isSHE) {
                $updateUrl = url('/she/insiden/update/' . $insiden->id);
            } elseif ($isManager) {
                $updateUrl = url('/manager/insiden/update/' . $insiden->id);
            } else {
                $updateUrl = url('/insiden/' . $insiden->id);
            }
        @endphp
        
        <div class="card-body p-4">
            <form id="insidenForm" action="{{ $updateUrl }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if($isPIC)
                <input type="hidden" name="redirect_to" value="pic">
                @elseif($isManager)
                <input type="hidden" name="redirect_to" value="manager">
                @elseif($isSHE)
                <input type="hidden" name="redirect_to" value="she">
                @elseif($isCreator)
                <input type="hidden" name="redirect_to" value="creator">
                @endif

                {{-- Bagian Data Awal (Hanya bisa diedit Creator saat status open) --}}
                <div class="form-section mb-4">
                    <div class="section-header mb-3">
                        <h6 class="section-title text-primary">
                            <i class="bi bi-info-circle me-2"></i>Data Insiden Awal
                            @if($canEditDataAwal)
                            <span class="badge bg-warning ms-2">Dapat Diedit oleh Creator</span>
                            @endif
                        </h6>
                        <div class="section-divider"></div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold mb-1">Tanggal</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-calendar text-muted"></i>
                                    </span>
                                    <input type="date" id="tanggal" name="tanggal" 
                                        class="form-control {{ !$canEditDataAwal ? 'bg-light' : '' }}" 
                                        value="{{ old('tanggal', \Carbon\Carbon::parse($insiden->tanggal)->format('Y-m-d')) }}" 
                                        {{ !$canEditDataAwal ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold mb-1">Jam</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-clock text-muted"></i>
                                    </span>
                                    <input type="time" id="jam" name="jam" 
                                        class="form-control {{ !$canEditDataAwal ? 'bg-light' : '' }}" 
                                        value="{{ old('jam', \Carbon\Carbon::parse($insiden->jam)->format('H:i')) }}" 
                                        {{ !$canEditDataAwal ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold mb-1">Lokasi Kejadian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-geo-alt text-muted"></i>
                                    </span>
                                    <input type="text" id="lokasi" name="lokasi" 
                                        class="form-control {{ !$canEditDataAwal ? 'bg-light' : '' }}" 
                                        value="{{ old('lokasi', $insiden->lokasi) }}" 
                                        {{ !$canEditDataAwal ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>

                        {{-- Bagian Kondisi Luka (Bisa diedit Creator) --}}
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold mb-1">Kondisi Luka</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-heart-pulse text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control {{ !$canEditDataAwal ? 'bg-light' : '' }}" 
                                        name="kondisi_luka"
                                        value="{{ old('kondisi_luka', $insiden->kondisi_luka) }}" 
                                        {{ !$canEditDataAwal ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>

                        {{-- Kategori Accident (Bisa diedit Creator) --}}
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold mb-1">Kategori Accident</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-tag text-muted"></i>
                                    </span>
                                    @if($canEditKategori)
                                    <select name="kategori" id="kategori" class="form-select" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="Work Accident" {{ old('kategori', $insiden->kategori) == 'Work Accident' ? 'selected' : '' }}>
                                            Work Accident
                                        </option>
                                        <option value="Traffic Accident" {{ old('kategori', $insiden->kategori) == 'Traffic Accident' ? 'selected' : '' }}>
                                            Traffic Accident
                                        </option>
                                        <option value="Fire Accident" {{ old('kategori', $insiden->kategori) == 'Fire Accident' ? 'selected' : '' }}>
                                            Fire Accident
                                        </option>
                                        <option value="Forklift Accident" {{ old('kategori', $insiden->kategori) == 'Forklift Accident' ? 'selected' : '' }}>
                                            Forklift Accident
                                        </option>
                                        <option value="Molten Spill Incident" {{ old('kategori', $insiden->kategori) == 'Molten Spill Incident' ? 'selected' : '' }}>
                                            Molten Spill Incident
                                        </option>
                                        <option value="Property Damage Incident" {{ old('kategori', $insiden->kategori) == 'Property Damage Incident' ? 'selected' : '' }}>
                                            Property Damage Incident
                                        </option>
                                    </select>
                                    @else
                                    <input type="text" class="form-control bg-light" 
                                        value="{{ $insiden->kategori }}" readonly>
                                    <input type="hidden" name="kategori" value="{{ $insiden->kategori }}">
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Tipe Work Accident (Bisa diedit Creator, akan ditampilkan/diatur oleh JavaScript) --}}
                        <div class="col-md-6 col-lg-4" id="workAccidentDiv" style="{{ ($insiden->kategori === 'Work Accident' || old('kategori') === 'Work Accident') ? '' : 'display: none;' }}">
                            <div class="form-group">
                                <label class="form-label fw-semibold mb-1">
                                    Tipe Work Accident
                                    @if($insiden->kategori === 'Work Accident' && $canEditKategori)
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-info-square text-muted"></i>
                                    </span>
                                    @if($canEditKategori)
                                    <select name="work_accident_type" id="workAccidentType" class="form-select" {{ $insiden->kategori === 'Work Accident' ? 'required' : '' }}>
                                        <option value="">Pilih Tipe</option>
                                        <option value="Loss Day" {{ old('work_accident_type', $insiden->work_accident_type) == 'Loss Day' ? 'selected' : '' }}>
                                            Loss Day
                                        </option>
                                        <option value="Light" {{ old('work_accident_type', $insiden->work_accident_type) == 'Light' ? 'selected' : '' }}>
                                            Light
                                        </option>
                                    </select>
                                    @else
                                    <input type="text" class="form-control bg-light" 
                                        value="{{ $insiden->work_accident_type }}" readonly>
                                    <input type="hidden" name="work_accident_type" value="{{ $insiden->work_accident_type }}">
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Section (Bisa diedit Creator) --}}
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold mb-1">Section</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-diagram-3 text-muted"></i>
                                    </span>
                                    @if($canEditKategori)
                                    <select name="section_id" id="section" class="form-select">
                                        <option value="">Pilih Section</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->id }}" 
                                                    data-department="{{ $section->department }}"
                                                    {{ old('section_id', $insiden->section_id) == $section->id ? 'selected' : '' }}>
                                                {{ $section->section }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @else
                                    <input type="text" class="form-control bg-light" 
                                        value="{{ $insiden->section->section ?? '-' }}" readonly>
                                    <input type="hidden" name="section_id" value="{{ $insiden->section_id }}">
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Departemen (Auto fill dari section) --}}
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold mb-1">Departemen</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-building text-muted"></i>
                                    </span>
                                    @if($canEditKategori)
                                    <input type="text" id="department" name="department" 
                                        class="form-control" 
                                        value="{{ old('department', $insiden->departemen) }}" 
                                        readonly>
                                    @else
                                    <input type="text" class="form-control bg-light" 
                                        value="{{ $insiden->departemen }}" readonly>
                                    <input type="hidden" name="department" value="{{ $insiden->departemen }}">
                                    @endif
                                </div>
                                @if($canEditKategori)
                                <small class="text-muted">Otomatis terisi berdasarkan section</small>
                                @endif
                            </div>
                        </div>

                        {{-- Bagian Status --}}
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold mb-1">Status</label>
                                <select id="status" name="status" class="form-select" 
                                    {{ !$canEditStatus ? 'disabled' : '' }}>
                                    @php
                                        $currentStatus = old('status', $insiden->status);
                                        
                                        // Tentukan opsi berdasarkan status saat ini
                                        if ($currentStatus === 'open') {
                                            // Jika status open, hanya boleh pilih progress
                                            $options = [
                                                ['value' => 'open', 'label' => 'Open', 'selected' => $currentStatus === 'open'],
                                                ['value' => 'progress', 'label' => 'In Progress', 'selected' => $currentStatus === 'progress']
                                            ];
                                        } elseif ($currentStatus === 'progress') {
                                            // Jika status progress, boleh pilih reject atau closed
                                            $options = [
                                                ['value' => 'progress', 'label' => 'In Progress', 'selected' => $currentStatus === 'progress'],
                                                ['value' => 'rejected', 'label' => 'Rejected', 'selected' => $currentStatus === 'rejected'],
                                                ['value' => 'closed', 'label' => 'Closed', 'selected' => $currentStatus === 'closed']
                                            ];
                                        } elseif ($currentStatus === 'rejected') {
                                            // Jika sudah rejected, tetap tampilkan status rejected
                                            $options = [
                                                ['value' => 'progress', 'label' => 'In Progress', 'selected' => true]
                                            ];
                                        } elseif ($currentStatus === 'closed') {
                                            // Jika sudah closed, tetap tampilkan status closed
                                            $options = [
                                                ['value' => 'closed', 'label' => 'Closed', 'selected' => true]
                                            ];
                                        } else {
                                            // Default semua opsi
                                            $options = [
                                                ['value' => 'open', 'label' => 'Open', 'selected' => $currentStatus === 'open'],
                                                ['value' => 'progress', 'label' => 'In Progress', 'selected' => $currentStatus === 'progress'],
                                                ['value' => 'rejected', 'label' => 'Rejected', 'selected' => $currentStatus === 'rejected'],
                                                ['value' => 'closed', 'label' => 'Closed', 'selected' => $currentStatus === 'closed']
                                            ];
                                        }
                                    @endphp
                                    
                                    @foreach($options as $option)
                                        <option value="{{ $option['value'] }}" 
                                            {{ isset($option['selected']) && $option['selected'] ? 'selected' : '' }}>
                                            {{ $option['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                @if(!$canEditStatus)
                                    <input type="hidden" name="status" value="{{ $insiden->status }}">
                                @endif
                                
                                {{-- Informasi tambahan --}}
                                @if($canEditStatus)
                                    <small class="form-text text-muted mt-1 d-block">
                                        @if($currentStatus === 'open')
                                            <i class="bi bi-info-circle me-1"></i> Status akan berubah dari <strong>Open</strong> ke <strong>In Progress</strong>
                                        @elseif($currentStatus === 'progress')
                                            <i class="bi bi-info-circle me-1"></i> Pilih <strong>Rejected</strong> untuk tolak atau <strong>Closed</strong> untuk selesaikan
                                        @endif
                                    </small>
                                @endif
                            </div>
                        </div>

                        {{-- Data Korban (Hanya PIC yang bisa pilih user) --}}
                        <div class="col-md-6 col-lg-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold mb-1">Data Korban</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-person-heart text-muted"></i>
                                    </span>
                                    @if($canEditKronologi)
                                        <select name="user_id" id="user_id" class="form-select">
                                            <option value="">Pilih Korban</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" 
                                                    {{ old('user_id', $insiden->user_id) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->nama }} - {{ $user->kode_user }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" class="form-control bg-light" 
                                            value="{{ $insiden->user->nama ?? '-' }} ({{ $insiden->user->kode_user ?? 'N/A' }})" 
                                            readonly>
                                        <input type="hidden" name="user_id" value="{{ $insiden->user_id }}">
                                    @endif
                                </div>
                                @if($canEditKronologi)
                                <small class="text-muted">Pilih data karyawan yang menjadi korban insiden</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>



                {{-- Garis pemisah: Bagian ini hanya bisa diedit PIC saat status open --}}
                @if($canEditKronologi)
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-badge fs-5 me-3"></i>
                        <div>
                            <strong>Bagian ini hanya dapat diisi oleh PIC</strong><br>
                            <small class="text-muted">Silakan lengkapi kronologi kejadian, foto, dan keterangan lain.</small>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Bagian Kronologi (Hanya bisa diedit PIC saat status open) --}}
                <div class="form-section mb-4">
                    <div class="section-header mb-3">
                        <h6 class="section-title text-primary">
                            <i class="bi bi-journal-text me-2"></i>Kronologi dan Detail Insiden
                            @if($canEditKronologi)
                            <span class="badge bg-info ms-2">Dapat Diedit oleh PIC</span>
                            @endif
                        </h6>
                        <div class="section-divider"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-1">
                            Kronologi <span class="text-danger">*</span>
                            @if($insiden->kronologi && !$canEditKronologi)
                            <span class="badge bg-success ms-2">Telah Diisi</span>
                            @elseif($canEditKronologi && !$insiden->kronologi)
                            <span class="badge bg-warning ms-2">Harus Diisi</span>
                            @elseif($canEditKronologi && $insiden->kronologi)
                            <span class="badge bg-warning ms-2">Dapat Diperbarui</span>
                            @endif
                        </label>
                        <div class="position-relative">
                            <textarea id="kronologi" name="kronologi" 
                                    class="form-control kronologi-textarea {{ !$canEditKronologi ? 'bg-light' : '' }}" 
                                    rows="5" placeholder="Jelaskan kronologi kejadian secara detail..." 
                                    maxlength="3000" {{ !$canEditKronologi ? 'readonly' : '' }} 
                                    {{ $canEditKronologi ? 'required' : '' }}>{{ old('kronologi', $insiden->kronologi) }}</textarea>
                            <div class="textarea-footer mt-1">
                                <small class="text-muted">
                                    @if($canEditKronologi)
                                    Jelaskan secara kronologis bagaimana insiden terjadi (wajib diisi oleh PIC)
                                    @elseif($insiden->kronologi)
                                    Kronologi telah diisi oleh PIC
                                    @else
                                    Belum ada kronologi
                                    @endif
                                </small>
                                <small id="kronologiCount" class="text-muted">0/3000</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian Foto & Dokumen Pendukung --}}
                <div class="form-section mb-4">
                    <div class="section-header mb-3">
                        <h6 class="section-title text-primary">
                            <i class="bi bi-images me-2"></i>Foto & Dokumen Pendukung
                            @if($canEditKronologi)
                            <span class="badge bg-info ms-2">Dapat Diedit oleh PIC</span>
                            @endif
                        </h6>
                        <div class="section-divider"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-1">
                            Upload Gambar Pendukung
                            @if(!$canEditKronologi)
                            <span class="badge bg-secondary ms-2">View Only</span>
                            @endif
                        </label>
                        
                        {{-- Tampilkan foto yang sudah ada --}}
                        @if(count($fotosArray) > 0)
                            <div class="preview-grid mt-3 mb-4">
                                <p class="text-muted mb-2"><small>Foto yang telah diupload:</small></p>
                                @foreach($fotosArray as $index => $foto)
                                    <div class="preview-item">
                                        <div class="preview-image-container">
                                            <img src="{{ asset('storage/' . $foto) }}" class="preview-image" 
                                                onerror="this.onerror=null; this.src='data:image/svg+xml;charset=UTF-8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"100\" height=\"100\" viewBox=\"0 0 100 100\"><rect width=\"100\" height=\"100\" fill=\"%23f8f9fa\"/><text x=\"50\" y=\"50\" font-family=\"Arial\" font-size=\"14\" fill=\"%236c757d\" text-anchor=\"middle\" dy=\".3em\">Gambar</text></svg>';">
                                            <div class="preview-overlay">
                                                <a href="{{ asset('storage/' . $foto) }}" target="_blank" class="btn btn-sm btn-info me-1">
                                                    <i class="bi bi-zoom-in"></i>
                                                </a>
                                                @if($canEditKronologi)
                                                <button type="button" class="btn btn-sm btn-danger btn-remove-existing" data-foto="{{ $foto }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                            <div class="preview-info">
                                                <small class="text-truncate d-block">Foto {{ $index + 1 }}</small>
                                                <small class="text-muted">Existing</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>Belum ada gambar yang diupload
                            </div>
                        @endif
                        
                        {{-- Input untuk upload foto baru --}}
                        @if($canEditKronologi)
                        <div class="file-upload-wrapper mt-3">
                            <p class="text-muted mb-2"><small>Upload foto tambahan:</small></p>
                            <div class="file-upload-area" id="uploadArea">
                                <div class="upload-placeholder">
                                    <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                    <div class="upload-text">
                                        <h6 class="mb-1">Klik untuk mengupload gambar tambahan</h6>
                                        <p class="text-muted small mb-0">Format: JPG, PNG, GIF, BMP (Maksimum: 10MB per file)</p>
                                        <p class="text-muted small">Dapat memilih beberapa file sekaligus</p>
                                    </div>
                                </div>
                                <input type="file" id="foto" name="foto[]" 
                                    class="form-control file-input" 
                                    accept=".jpg,.jpeg,.png,.gif,.bmp" multiple>
                            </div>
                            <div id="fotoPreview" class="preview-grid mt-3"></div>
                            <div id="fotoError" class="error-message mt-2" style="display: none;"></div>
                            
                            {{-- Input hidden untuk foto yang tetap dipertahankan --}}
                            <input type="hidden" name="existing_fotos" id="existingFotos" 
                                value="{{ $existingFotosJson }}">
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Bagian Keterangan Lain --}}
                <div class="form-section mb-4">
                    <div class="section-header mb-3">
                        <h6 class="section-title text-primary">
                            <i class="bi bi-chat-left-text me-2"></i>Keterangan Lain
                            @if($canEditKronologi)
                            <span class="badge bg-info ms-2">Dapat Diedit oleh PIC</span>
                            @endif
                        </h6>
                        <div class="section-divider"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-1">
                            Keterangan Tambahan
                            @if(!$canEditKronologi)
                            <span class="badge bg-secondary ms-2">View Only</span>
                            @endif
                        </label>
                        <textarea id="keterangan_lain" name="keterangan_lain" 
                                class="form-control {{ !$canEditKronologi ? 'bg-light' : '' }}" 
                                rows="3" placeholder="Tambahkan informasi lain yang relevan..." 
                                {{ !$canEditKronologi ? 'readonly' : '' }}>{{ old('keterangan_lain', $insiden->keterangan_lain) }}</textarea>
                        <small class="text-muted">Opsional: informasi tambahan yang belum tercakup di bagian sebelumnya</small>
                    </div>
                </div>

                {{-- Bagian Catatan SHE (Dinamis berdasarkan status) --}}
                @if($canAddNote)
                <div class="form-section mb-4">
                    <div class="section-header mb-3">
                        <h6 class="section-title text-primary">
                            <i class="bi bi-chat-dots me-2"></i>Tinjauan SHE
                            <span class="badge bg-success ms-2">Dapat Diisi oleh SHE</span>
                        </h6>
                        <div class="section-divider"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold mb-1">
                            Catatan SHE
                            <span class="badge bg-info ms-1" id="catatanBadge">Opsional</span>
                        </label>
                        <textarea id="catatan_she" name="catatan_she" 
                                class="form-control catatan-textarea" 
                                rows="3" 
                                placeholder="Tambahkan catatan atau tinjauan dari SHE...">{{ old('catatan_she', $insiden->catatan_she) }}</textarea>
                        <small class="text-muted" id="catatanHelpText">Catatan tambahan dari tim SHE (opsional)</small>
                        <div id="catatanError" class="text-danger mt-2" style="display: none;"></div>
                    </div>
                </div>
                @endif

                
                <div class="form-actions pt-4 border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a href="{{ url()->previous() ?: ($isPIC ? '/pic/insiden' : ($isManager ? '/manager/insiden' : ($isSHE ? '/she/insiden' : '/insiden'))) }}" 
                               class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                        </div>
                        
                        <div class="d-flex gap-2">
                            @if(!$canViewOnly && ($canEditDataAwal || $canEditKategori || $canEditKronologi || $canAddNote || $canEditStatus))
                            <button type="submit" id="submitBtn" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>
                                <span id="submitText">
                                    @if($canAddNote)
                                    Simpan Tinjauan SHE
                                    @elseif($canEditKronologi)
                                    Simpan Kronologi (PIC)
                                    @elseif($canEditDataAwal || $canEditKategori)
                                    Update Data Insiden (Creator)
                                    @elseif($canEditStatus && $isManager)
                                    Update Status
                                    @else
                                    Simpan Perubahan
                                    @endif
                                </span>
                                <span id="submitSpinner" class="spinner-border spinner-border-sm ms-2 d-none"></span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Counter untuk kronologi
    const kronologiTextarea = document.getElementById('kronologi');
    const kronologiCount = document.getElementById('kronologiCount');
    
    function updateKronologiCounter() {
        if (kronologiTextarea && kronologiCount) {
            const length = kronologiTextarea.value.length;
            kronologiCount.textContent = `${length}/3000`;
            
            if (length > 2700) {
                kronologiCount.classList.remove('text-muted');
                kronologiCount.classList.add('text-warning');
            } else if (length > 2850) {
                kronologiCount.classList.remove('text-warning');
                kronologiCount.classList.add('text-danger');
            } else {
                kronologiCount.classList.remove('text-warning', 'text-danger');
                kronologiCount.classList.add('text-muted');
            }
        }
    }
    
    if (kronologiTextarea) {
        kronologiTextarea.addEventListener('input', updateKronologiCounter);
        updateKronologiCounter();
    }
    
    // Validasi tanggal tidak lebih dari hari ini
    const tanggalInput = document.getElementById('tanggal');
    const today = new Date().toISOString().split('T')[0];
    if (tanggalInput) tanggalInput.max = today;
    
    // ============================================
    // AUTO-FILL DEPARTMENT BERDASARKAN SECTION
    // ============================================
    const sectionSelect = document.getElementById('section');
    const departmentInput = document.getElementById('department');
    
    function updateDepartment() {
        if (sectionSelect && departmentInput) {
            const selectedOption = sectionSelect.options[sectionSelect.selectedIndex];
            const department = selectedOption.getAttribute('data-department');
            
            if (department) {
                departmentInput.value = department; // Auto-fill department
            } else {
                departmentInput.value = '';
            }
        }
    }
    
    // Initialize on page load jika creator bisa edit
    const canEditKategori = {{ $canEditKategori ? 'true' : 'false' }};
    if (canEditKategori && sectionSelect && departmentInput) {
        if (sectionSelect.value) {
            updateDepartment();
        }
        
        // Update department when section changes
        sectionSelect.addEventListener('change', updateDepartment);
    }
    
    // ============================================
    // TOGGLE WORK ACCIDENT TYPE BERDASARKAN KATEGORI
    // ============================================
    const kategoriSelect = document.getElementById('kategori');
    const workAccidentType = document.getElementById('workAccidentType');
    const workAccidentDiv = document.getElementById('workAccidentDiv');
    
    function toggleWorkAccident() {
        if (kategoriSelect && workAccidentDiv) {
            if (kategoriSelect.value === 'Work Accident') {
                workAccidentDiv.style.display = 'block';
                if (workAccidentType) {
                    workAccidentType.required = true;
                    // Tambahkan required attribute ke label
                    const label = workAccidentDiv.querySelector('.form-label');
                    if (label) {
                        if (!label.querySelector('.text-danger')) {
                            label.innerHTML += ' <span class="text-danger">*</span>';
                        }
                    }
                }
            } else {
                workAccidentDiv.style.display = 'none';
                if (workAccidentType) {
                    workAccidentType.required = false;
                    workAccidentType.value = '';
                    // Hapus required attribute dari label
                    const label = workAccidentDiv.querySelector('.form-label');
                    if (label) {
                        const requiredSpan = label.querySelector('.text-danger');
                        if (requiredSpan) {
                            requiredSpan.remove();
                        }
                    }
                }
            }
        }
    }
    
    if (kategoriSelect && canEditKategori) {
        kategoriSelect.addEventListener('change', toggleWorkAccident);
        toggleWorkAccident();
    }
    
    // ============================================
    // DINAMIS CATATAN SHE BERDASARKAN STATUS
    // ============================================
    const statusSelect = document.getElementById('status');
    const catatanSheTextarea = document.getElementById('catatan_she');
    const catatanBadge = document.getElementById('catatanBadge');
    const catatanHelpText = document.getElementById('catatanHelpText');
    
    function updateCatatanRequirement() {
        if (statusSelect && catatanSheTextarea && catatanBadge && catatanHelpText) {
            const selectedStatus = statusSelect.value;
            
            if (selectedStatus === 'rejected') {
                // Jika status rejected, catatan SHE wajib diisi
                catatanSheTextarea.required = true;
                catatanBadge.innerHTML = 'Wajib <span class="text-danger">*</span>';
                catatanBadge.classList.remove('bg-info');
                catatanBadge.classList.add('bg-danger');
                
                // Update placeholder dan help text
                catatanSheTextarea.placeholder = 'Wajib diisi: Jelaskan alasan penolakan laporan...';
                catatanHelpText.innerHTML = '<span class="text-danger">Wajib diisi ketika menolak laporan. Jelaskan alasan penolakan secara detail.</span>';
                
                // Tambahkan border merah untuk emphasis
                catatanSheTextarea.classList.add('border-danger');
                catatanSheTextarea.classList.remove('border-primary');
                
                // Jika catatan kosong, tambahkan prefix otomatis
                if (!catatanSheTextarea.value.trim()) {
                    catatanSheTextarea.value = 'DITOLAK - Alasan: ';
                }
            } else if (selectedStatus === 'closed') {
                // Jika status closed, catatan SHE direkomendasikan
                catatanSheTextarea.required = false;
                catatanBadge.innerHTML = 'Direkomendasikan';
                catatanBadge.classList.remove('bg-danger', 'bg-info');
                catatanBadge.classList.add('bg-warning');
                
                // Update placeholder dan help text
                catatanSheTextarea.placeholder = 'Direkomendasikan: Tambahkan kesimpulan atau tindak lanjut...';
                catatanHelpText.innerHTML = 'Direkomendasikan: Tambahkan kesimpulan atau rekomendasi tindak lanjut untuk laporan yang ditutup.';
                
                // Hapus border merah
                catatanSheTextarea.classList.remove('border-danger', 'border-primary');
                
                // Jika catatan kosong, tambahkan prefix otomatis
                if (!catatanSheTextarea.value.trim()) {
                    catatanSheTextarea.value = 'KESIMPULAN - ';
                }
            } else {
                // Untuk status lainnya, catatan SHE opsional
                catatanSheTextarea.required = false;
                catatanBadge.innerHTML = 'Opsional';
                catatanBadge.classList.remove('bg-danger', 'bg-warning');
                catatanBadge.classList.add('bg-info');
                
                // Reset placeholder dan help text
                catatanSheTextarea.placeholder = 'Tambahkan catatan atau tinjauan dari SHE...';
                catatanHelpText.innerHTML = 'Catatan tambahan dari tim SHE (opsional)';
                
                // Hapus semua border
                catatanSheTextarea.classList.remove('border-danger', 'border-primary');
            }
        }
    }
    
    // Inisialisasi saat halaman dimuat
    if (statusSelect) {
        updateCatatanRequirement();
        statusSelect.addEventListener('change', updateCatatanRequirement);
    }
    
    // ============================================
    // VALIDASI FORM SAAT SUBMIT
    // ============================================
    const form = document.getElementById('insidenForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitSpinner = document.getElementById('submitSpinner');
    
    // Handle upload foto dengan preview
    const fotoInput = document.getElementById('foto');
    const fotoPreview = document.getElementById('fotoPreview');
    const existingFotosInput = document.getElementById('existingFotos');
    
    // Array untuk menyimpan foto yang akan dihapus
    let deletedFotos = [];
    
    if (fotoInput && fotoPreview) {
        fotoInput.addEventListener('change', function(e) {
            handleFiles(this.files);
        });
        
        function handleFiles(files) {
            if (!files.length) return;
            
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/bmp'];
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // Validasi ukuran
                if (file.size > maxSize) {
                    alert(`File ${file.name} melebihi ukuran maksimum 10MB`);
                    continue;
                }
                
                // Validasi tipe
                if (!allowedTypes.includes(file.type)) {
                    alert(`File ${file.name} harus berupa gambar (JPG, PNG, GIF, BMP)`);
                    continue;
                }
                
                // Preview gambar
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'preview-item';
                    div.innerHTML = `
                        <div class="preview-image-container">
                            <img src="${e.target.result}" class="preview-image">
                            <div class="preview-overlay">
                                <button type="button" class="btn btn-sm btn-danger btn-remove-preview">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="preview-info">
                                <small class="text-truncate d-block">${file.name}</small>
                                <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                            </div>
                        </div>
                    `;
                    fotoPreview.appendChild(div);
                    
                    // Tombol hapus untuk preview
                    div.querySelector('.btn-remove-preview').addEventListener('click', function() {
                        div.remove();
                        // Juga hapus dari file input
                        const dataTransfer = new DataTransfer();
                        const inputFiles = fotoInput.files;
                        for (let j = 0; j < inputFiles.length; j++) {
                            if (inputFiles[j].name !== file.name) {
                                dataTransfer.items.add(inputFiles[j]);
                            }
                        }
                        fotoInput.files = dataTransfer.files;
                    });
                };
                reader.readAsDataURL(file);
            }
        }
    }
    
    // Handle hapus foto yang sudah ada
    document.querySelectorAll('.btn-remove-existing').forEach(button => {
        button.addEventListener('click', function() {
            const foto = this.getAttribute('data-foto');
            
            if (existingFotosInput) {
                let currentFotos = [];
                try {
                    currentFotos = JSON.parse(existingFotosInput.value || '[]');
                    if (!Array.isArray(currentFotos)) {
                        currentFotos = [];
                    }
                } catch (e) {
                    currentFotos = [];
                }
                
                // Hapus foto dari array
                currentFotos = currentFotos.filter(f => f !== foto);
                existingFotosInput.value = JSON.stringify(currentFotos);
                
                // Tambahkan ke array foto yang dihapus
                if (!deletedFotos.includes(foto)) {
                    deletedFotos.push(foto);
                }
                
                // Buat input hidden untuk foto yang dihapus
                if (!document.getElementById('deletedFotosInput')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.id = 'deletedFotosInput';
                    input.name = 'deleted_fotos';
                    document.querySelector('form').appendChild(input);
                }
                document.getElementById('deletedFotosInput').value = JSON.stringify(deletedFotos);
                
                // Hapus elemen preview
                this.closest('.preview-item').remove();
                
                // Tampilkan pesan
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-warning mt-2';
                alertDiv.innerHTML = '<i class="bi bi-info-circle me-2"></i>Foto akan dihapus saat Anda menyimpan perubahan.';
                this.closest('.form-section').insertBefore(alertDiv, this.closest('.mb-3').nextSibling);
                
                setTimeout(() => alertDiv.remove(), 3000);
            }
        });
    });
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validasi field required
            const requiredFields = form.querySelectorAll('[required]:not([disabled])');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    
                    if (!field.nextElementSibling?.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback d-block';
                        errorDiv.textContent = 'Field ini wajib diisi';
                        field.parentNode.appendChild(errorDiv);
                    }
                    
                    // Scroll ke field yang error
                    if (isValid === false) {
                        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                } else {
                    field.classList.remove('is-invalid');
                    const errorDiv = field.parentNode.querySelector('.invalid-feedback');
                    if (errorDiv && errorDiv.textContent === 'Field ini wajib diisi') {
                        errorDiv.remove();
                    }
                }
            });
            
            // Validasi khusus untuk catatan SHE jika status rejected
            if (statusSelect && statusSelect.value === 'rejected' && catatanSheTextarea) {
                if (!catatanSheTextarea.value.trim()) {
                    isValid = false;
                    catatanSheTextarea.classList.add('is-invalid');
                    
                    if (!document.getElementById('catatanError')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.id = 'catatanError';
                        errorDiv.className = 'text-danger mt-2';
                        errorDiv.textContent = 'Catatan SHE wajib diisi ketika menolak laporan. Harap jelaskan alasan penolakan.';
                        catatanSheTextarea.parentNode.appendChild(errorDiv);
                    } else {
                        document.getElementById('catatanError').style.display = 'block';
                    }
                    
                    catatanSheTextarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    catatanSheTextarea.classList.remove('is-invalid');
                    const errorDiv = document.getElementById('catatanError');
                    if (errorDiv) {
                        errorDiv.style.display = 'none';
                    }
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                
                // Tampilkan alert khusus untuk status rejected
                if (statusSelect && statusSelect.value === 'rejected') {
                    alert('Catatan SHE wajib diisi ketika menolak laporan. Harap jelaskan alasan penolakan.');
                } else {
                    alert('Harap lengkapi semua field yang wajib diisi');
                }
                return;
            }
            
            // Tampilkan loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                if (submitText) submitText.textContent = 'Menyimpan...';
                if (submitSpinner) submitSpinner.classList.remove('d-none');
            }
            
            // Jika PIC mengisi kronologi, ubah status menjadi progress
            const statusSelect = document.getElementById('status');
            const isPICEditing = {{ $canEditKronologi ? 'true' : 'false' }};
            if (isPICEditing && statusSelect && statusSelect.value === 'open') {
                statusSelect.value = 'progress';
            }
        });
    }
    
    // Auto-hide alerts setelah 5 detik
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            setTimeout(() => bsAlert.close(), 5000);
        });
    }, 1000);
});
</script>

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

.form-card {
    border-radius: 12px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.form-card .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid var(--primary-color);
}

.form-section {
    padding: 1.5rem;
    background: var(--light-color);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
    margin-bottom: 1.5rem;
    position: relative;
}

.section-header {
    position: relative;
    margin-bottom: 1.5rem;
}

.section-title {
    color: var(--primary-color);
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.section-divider {
    height: 2px;
    background: linear-gradient(90deg, var(--primary-color) 0%, transparent 100%);
    opacity: 0.3;
}

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

.form-control.bg-light, .form-select.bg-light {
    background-color: #f8f9fa !important;
    border-color: #dee2e6 !important;
    cursor: not-allowed;
}

.input-group-text {
    background-color: var(--light-color);
    border-right: none;
    color: var(--primary-color);
}

.kronologi-textarea, .catatan-textarea {
    resize: vertical;
    min-height: 120px;
    max-height: 300px;
}

.catatan-textarea.border-danger {
    border-color: #dc3545 !important;
}

.catatan-textarea.border-primary {
    border-color: #0d6efd !important;
}

.textarea-footer {
    display: flex;
    justify-content: space-between;
    margin-top: 0.5rem;
    font-size: 0.85rem;
}

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

.preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
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
    box-shadow: var(--box-shadow);
    transition: transform 0.3s ease;
}

.preview-image-container:hover {
    transform: translateY(-5px);
    box-shadow: var(--box-shadow-hover);
}

.preview-image {
    width: 100%;
    height: 140px;
    object-fit: cover;
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
    gap: 5px;
}

.preview-image-container:hover .preview-overlay {
    opacity: 1;
}

.btn-remove-preview, .btn-remove-existing {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.preview-info {
    padding: 0.75rem;
    background: white;
    border-top: 1px solid var(--border-color);
}

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
    
    .form-actions .d-flex {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-actions .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

@media print {
    .btn-back, .form-actions, .file-upload-area {
        display: none !important;
    }
    
    .form-section {
        border: 1px solid #000;
        break-inside: avoid;
    }
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.35rem 0.65rem;
}

.form-group {
    margin-bottom: 1rem;
}

.bg-light {
    background-color: #f8f9fa !important;
}

/* Alert khusus untuk pemisah section */
.alert-info {
    background-color: #e7f1ff;
    border: 1px solid #0d6efd;
    border-left: 4px solid #0d6efd;
}
</style>
@endsection