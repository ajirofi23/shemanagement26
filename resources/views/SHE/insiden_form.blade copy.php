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
                                       value="{{ old('tanggal') ?: date('Y-m-d') }}" 
                                       max="{{ date('Y-m-d') }}" required>
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
                                       value="{{ old('jam') ?: date('H:i') }}" required>
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
                {{-- BAGIAN UNTUK PELAPOR (NON-SHE) --}}
                {{-- ============================================================ --}}
                @if(!$sheOnly)
                {{-- Section 2: Kronologi --}}
                <div class="form-section mb-5">
                    <div class="section-header mb-4">
                        <h6 class="section-title">
                            <span class="section-icon">II</span>
                            <i class="bi bi-journal-text me-2"></i>Kronologi
                        </h6>
                        <div class="section-divider"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="kronologi" class="form-label fw-semibold">
                            Kronologi <span class="text-danger">*</span>
                        </label>
                        <div class="position-relative">
                            <textarea id="kronologi" name="kronologi" 
                                      class="form-control kronologi-textarea @error('kronologi') is-invalid @enderror" 
                                      rows="5" placeholder="Jelaskan kronologi kejadian secara detail..." 
                                      maxlength="3000" required>{{ old('kronologi') }}</textarea>
                            <div class="textarea-footer">
                                <small class="text-muted">Jelaskan secara kronologis bagaimana insiden terjadi</small>
                                <small id="kronologiCount" class="text-muted">0/3000</small>
                            </div>
                        </div>
                        @error('kronologi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Section 3: Foto & Dokumen --}}
                <div class="form-section mb-5">
                    <div class="section-header mb-4">
                        <h6 class="section-title">
                            <span class="section-icon">III</span>
                            <i class="bi bi-images me-2"></i>Foto & Dokumen
                        </h6>
                        <div class="section-divider"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload Gambar Pendukung</label>
                        <div class="file-upload-wrapper">
                            <div class="file-upload-area" id="uploadArea">
                                <div class="upload-placeholder">
                                    <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                    <div class="upload-text">
                                        <h6 class="mb-1">Klik untuk mengupload gambar</h6>
                                        <p class="text-muted small mb-0">Format: JPG, PNG (Maksimum: 10MB per file)</p>
                                        <p class="text-muted small">Dapat memilih beberapa file sekaligus</p>
                                    </div>
                                </div>
                                <input type="file" id="foto" name="foto[]" 
                                       class="form-control file-input @error('foto') is-invalid @enderror" 
                                       accept=".jpg,.jpeg,.png" multiple>
                            </div>
                            <div id="fotoPreview" class="preview-grid mt-4"></div>
                            <div id="fotoError" class="error-message mt-2" style="display: none;"></div>
                        </div>
                        @error('foto')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Section 4: Keterangan Lain --}}
                <div class="form-section mb-5">
                    <div class="section-header mb-4">
                        <h6 class="section-title">
                            <span class="section-icon">IV</span>
                            <i class="bi bi-chat-left-text me-2"></i>Keterangan Lain
                        </h6>
                        <div class="section-divider"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan_lain" class="form-label fw-semibold">
                            Keterangan Tambahan
                        </label>
                        <textarea id="keterangan_lain" name="keterangan_lain" 
                                  class="form-control @error('keterangan_lain') is-invalid @enderror" 
                                  rows="3" placeholder="Tambahkan informasi lain yang relevan...">{{ old('keterangan_lain') }}</textarea>
                        <small class="text-muted">Opsional: informasi tambahan yang belum tercakup di bagian sebelumnya</small>
                        @error('keterangan_lain')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @endif

                {{-- ============================================================ --}}
                {{-- BAGIAN KHUSUS UNTUK SHE (PIC INVESTIGASI) --}}
                {{-- ============================================================ --}}
                @if(!$sheOnly)
                {{-- Section 5: Kronologi & Investigasi (SHE) --}}
                <div class="form-section mb-5">
                    <div class="section-header mb-4">
                        <h6 class="section-title">
                            <span class="section-icon">II</span>
                            <i class="bi bi-journal-text me-2"></i>Kronologi & Investigasi
                            <span class="badge bg-success ms-2">Diisi oleh SHE</span>
                        </h6>
                        <div class="section-divider"></div>
                    </div>
                    
                    {{-- Kronologi Detail --}}
                    <div class="mb-4">
                        <label for="kronologi_detail" class="form-label fw-semibold">
                            Kronologi Detail <span class="text-danger">*</span>
                        </label>
                        <textarea id="kronologi_detail" name="kronologi_detail" 
                                  class="form-control kronologi-textarea @error('kronologi_detail') is-invalid @enderror" 
                                  rows="4" placeholder="Jelaskan kronologi kejadian secara detail berdasarkan investigasi..." 
                                  maxlength="3000" required>{{ old('kronologi_detail') }}</textarea>
                        <div class="textarea-footer">
                            <small class="text-muted">Kronologi berdasarkan hasil investigasi</small>
                            <small id="kronologiDetailCount" class="text-muted">0/3000</small>
                        </div>
                        @error('kronologi_detail')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Penyebab Langsung --}}
                    <div class="mb-4">
                        <label for="penyebab_langsung" class="form-label fw-semibold">
                            Penyebab Langsung <span class="text-danger">*</span>
                        </label>
                        <textarea id="penyebab_langsung" name="penyebab_langsung" 
                                  class="form-control @error('penyebab_langsung') is-invalid @enderror" 
                                  rows="3" placeholder="Identifikasi penyebab langsung insiden..." 
                                  maxlength="2000" required>{{ old('penyebab_langsung') }}</textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Maksimal 2000 karakter</small>
                            <small id="penyebabCount" class="text-muted">0/2000</small>
                        </div>
                        @error('penyebab_langsung')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Penyebab Dasar --}}
                    <div class="mb-4">
                        <label for="penyebab_dasar" class="form-label fw-semibold">
                            Penyebab Dasar <span class="text-danger">*</span>
                        </label>
                        <textarea id="penyebab_dasar" name="penyebab_dasar" 
                                  class="form-control @error('penyebab_dasar') is-invalid @enderror" 
                                  rows="3" placeholder="Identifikasi penyebab dasar/sistemik..." 
                                  maxlength="2000" required>{{ old('penyebab_dasar') }}</textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Maksimal 2000 karakter</small>
                            <small id="dasarCount" class="text-muted">0/2000</small>
                        </div>
                        @error('penyebab_dasar')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tindakan Perbaikan --}}
                    <div class="mb-4">
                        <label for="tindakan_perbaikan" class="form-label fw-semibold">
                            Tindakan Perbaikan <span class="text-danger">*</span>
                        </label>
                        <textarea id="tindakan_perbaikan" name="tindakan_perbaikan" 
                                  class="form-control @error('tindakan_perbaikan') is-invalid @enderror" 
                                  rows="3" placeholder="Tindakan perbaikan yang telah dilakukan..." 
                                  maxlength="2000" required>{{ old('tindakan_perbaikan') }}</textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Maksimal 2000 karakter</small>
                            <small id="perbaikanCount" class="text-muted">0/2000</small>
                        </div>
                        @error('tindakan_perbaikan')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tindakan Pencegahan --}}
                    <div class="mb-4">
                        <label for="tindakan_pencegahan" class="form-label fw-semibold">
                            Tindakan Pencegahan <span class="text-danger">*</span>
                        </label>
                        <textarea id="tindakan_pencegahan" name="tindakan_pencegahan" 
                                  class="form-control @error('tindakan_pencegahan') is-invalid @enderror" 
                                  rows="3" placeholder="Tindakan pencegahan untuk menghindari kejadian serupa..." 
                                  maxlength="2000" required>{{ old('tindakan_pencegahan') }}</textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Maksimal 2000 karakter</small>
                            <small id="pencegahanCount" class="text-muted">0/2000</small>
                        </div>
                        @error('tindakan_pencegahan')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Section 6: Foto & Dokumen Investigasi (SHE) --}}
                <div class="form-section mb-5">
                    <div class="section-header mb-4">
                        <h6 class="section-title">
                            <span class="section-icon">III</span>
                            <i class="bi bi-images me-2"></i>Foto & Dokumen Investigasi
                            <span class="badge bg-success ms-2">Diisi oleh SHE</span>
                        </h6>
                        <div class="section-divider"></div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Upload Foto Investigasi</label>
                        <div class="file-upload-wrapper">
                            <div class="file-upload-area" id="uploadAreaInvestigasi">
                                <div class="upload-placeholder">
                                    <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                    <div class="upload-text">
                                        <h6 class="mb-1">Klik untuk mengupload foto investigasi</h6>
                                        <p class="text-muted small mb-0">Format: JPG, PNG, PDF (Maksimum: 10MB per file)</p>
                                        <p class="text-muted small">Dokumen investigasi, diagram, bukti foto</p>
                                    </div>
                                </div>
                                <input type="file" id="foto_investigasi" name="foto_investigasi[]" 
                                       class="form-control file-input @error('foto_investigasi') is-invalid @enderror" 
                                       accept=".jpg,.jpeg,.png,.pdf" multiple>
                            </div>
                            <div id="fotoInvestigasiPreview" class="preview-grid mt-4"></div>
                            <div id="fotoInvestigasiError" class="error-message mt-2" style="display: none;"></div>
                        </div>
                        @error('foto_investigasi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Upload Dokumen Laporan --}}
                    <div class="mb-4">
                        <label for="dokumen_laporan" class="form-label fw-semibold">
                            Upload Laporan Investigasi (PDF)
                        </label>
                        <input type="file" id="dokumen_laporan" name="dokumen_laporan" 
                               class="form-control @error('dokumen_laporan') is-invalid @enderror" 
                               accept=".pdf">
                        <small class="text-muted">Opsional: Upload laporan investigasi lengkap dalam format PDF</small>
                        @error('dokumen_laporan')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Section 7: PIC & Tindak Lanjut (SHE) --}}
                <div class="form-section mb-5">
                    <div class="section-header mb-4">
                        <h6 class="section-title">
                            <span class="section-icon">IV</span>
                            <i class="bi bi-person-badge me-2"></i>PIC & Tindak Lanjut
                            <span class="badge bg-success ms-2">Diisi oleh SHE</span>
                        </h6>
                        <div class="section-divider"></div>
                    </div>
                    
                    <div class="row g-4">
                        {{-- PIC Investigasi --}}
                        <div class="col-md-6 col-lg-4">
                            <label for="pic_investigasi" class="form-label fw-semibold">
                                PIC Investigasi <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-person text-primary"></i>
                                </span>
                                <input type="text" id="pic_investigasi" name="pic_investigasi" 
                                       class="form-control @error('pic_investigasi') is-invalid @enderror" 
                                       placeholder="Nama PIC Investigasi" 
                                       value="{{ old('pic_investigasi', $user->name ?? '') }}" required>
                            </div>
                            @error('pic_investigasi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Investigasi --}}
                        <div class="col-md-6 col-lg-4">
                            <label for="tanggal_investigasi" class="form-label fw-semibold">
                                Tanggal Investigasi <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-calendar-check text-primary"></i>
                                </span>
                                <input type="date" id="tanggal_investigasi" name="tanggal_investigasi" 
                                       class="form-control @error('tanggal_investigasi') is-invalid @enderror" 
                                       value="{{ old('tanggal_investigasi', date('Y-m-d')) }}" required>
                            </div>
                            @error('tanggal_investigasi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status Investigasi --}}
                        <div class="col-md-6 col-lg-4">
                            <label for="status_investigasi" class="form-label fw-semibold">
                                Status Investigasi <span class="text-danger">*</span>
                            </label>
                            <select id="status_investigasi" name="status_investigasi" 
                                    class="form-select @error('status_investigasi') is-invalid @enderror" required>
                                <option value="">Pilih Status</option>
                                <option value="Draft" {{ old('status_investigasi') === 'Draft' ? 'selected' : '' }}>Draft</option>
                                <option value="Dalam Investigasi" {{ old('status_investigasi') === 'Dalam Investigasi' ? 'selected' : '' }}>Dalam Investigasi</option>
                                <option value="Selesai" {{ old('status_investigasi') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Ditutup" {{ old('status_investigasi') === 'Ditutup' ? 'selected' : '' }}>Ditutup</option>
                            </select>
                            @error('status_investigasi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Target Penyelesaian --}}
                        <div class="col-md-6 col-lg-4">
                            <label for="target_penyelesaian" class="form-label fw-semibold">
                                Target Penyelesaian
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-calendar-event text-primary"></i>
                                </span>
                                <input type="date" id="target_penyelesaian" name="target_penyelesaian" 
                                       class="form-control @error('target_penyelesaian') is-invalid @enderror" 
                                       value="{{ old('target_penyelesaian') }}">
                            </div>
                            @error('target_penyelesaian')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PIC Tindak Lanjut --}}
                        <div class="col-md-6 col-lg-4">
                            <label for="pic_tindak_lanjut" class="form-label fw-semibold">
                                PIC Tindak Lanjut
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-person-check text-primary"></i>
                                </span>
                                <input type="text" id="pic_tindak_lanjut" name="pic_tindak_lanjut" 
                                       class="form-control @error('pic_tindak_lanjut') is-invalid @enderror" 
                                       placeholder="Nama PIC Tindak Lanjut" 
                                       value="{{ old('pic_tindak_lanjut') }}">
                            </div>
                            @error('pic_tindak_lanjut')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Prioritas --}}
                        <div class="col-md-6 col-lg-4">
                            <label for="prioritas" class="form-label fw-semibold">
                                Prioritas
                            </label>
                            <select id="prioritas" name="prioritas" 
                                    class="form-select @error('prioritas') is-invalid @enderror">
                                <option value="">Pilih Prioritas</option>
                                <option value="Rendah" {{ old('prioritas') === 'Rendah' ? 'selected' : '' }}>Rendah</option>
                                <option value="Sedang" {{ old('prioritas') === 'Sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="Tinggi" {{ old('prioritas') === 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
                                <option value="Kritis" {{ old('prioritas') === 'Kritis' ? 'selected' : '' }}>Kritis</option>
                            </select>
                            @error('prioritas')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Catatan Tindak Lanjut --}}
                    <div class="mb-3 mt-4">
                        <label for="catatan_tindak_lanjut" class="form-label fw-semibold">
                            Catatan Tindak Lanjut
                        </label>
                        <textarea id="catatan_tindak_lanjut" name="catatan_tindak_lanjut" 
                                  class="form-control @error('catatan_tindak_lanjut') is-invalid @enderror" 
                                  rows="3" placeholder="Catatan tambahan untuk tindak lanjut...">{{ old('catatan_tindak_lanjut') }}</textarea>
                        <small class="text-muted">Catatan khusus untuk tindak lanjut investigasi</small>
                        @error('catatan_tindak_lanjut')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-section mb-5">
                    <div class="section-header mb-4">
                        <h6 class="section-title">
                            <span class="section-icon">{{ $sheOnly ? 'V' : 'V' }}</span>
                            <i class="bi bi-chat-left-text me-2"></i>Keterangan Lain
                        </h6>
                        <div class="section-divider"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan_lain" class="form-label fw-semibold">
                            Keterangan Tambahan
                        </label>
                        <textarea id="keterangan_lain" name="keterangan_lain" 
                                  class="form-control @error('keterangan_lain') is-invalid @enderror" 
                                  rows="3" placeholder="Tambahkan informasi lain yang relevan...">{{ old('keterangan_lain') }}</textarea>
                        <small class="text-muted">Opsional: informasi tambahan yang belum tercakup di bagian sebelumnya</small>
                        @error('keterangan_lain')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @endif


                {{-- ============================================================ --}}
                {{-- ACTION BUTTONS --}}
                {{-- ============================================================ --}}
                {{-- Action Buttons --}}
                <div class="form-actions pt-4 border-top">
                    <div class="d-flex justify-content-between">
                        <a href="{{ url('she.insiden') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </a>
                        
                        <div>
                            @if($sheOnly)
                                <button type="submit" name="action" value="submit" id="submitBtn" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send-check me-2"></i>
                                    <span id="submitText">Kirim Laporan</span>
                                    <span id="submitSpinner" class="spinner-border spinner-border-sm ms-2 d-none"></span>
                                </button>
                            @else
                                <button type="submit" id="submitBtn" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send-check me-2"></i>
                                    <span id="submitText">Kirim Laporan</span>
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
    
    // Set max date for semua date inputs
    const today = new Date().toISOString().split('T')[0];
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