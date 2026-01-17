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
    
    $isPIC = strpos($currentUrl, '/pic/insiden/detail') !== false;
    $isSHE = strpos($currentUrl, '/she/insiden/detail') !== false;
    $isManager = strpos($currentUrl, '/manager/insiden/detail') !== false;
    
    // Cek apakah user adalah pembuat laporan
    $isCreator = Auth::id() == $insiden->created_by;
    
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
?>

<div class="content p-3">
    <div class="header-section mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title mb-1">
                    <i class="bi bi-eye me-2 text-primary"></i>
                    Detail Laporan Insiden
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
                    
                    <span class="badge bg-secondary">
                        <i class="bi bi-eye me-1"></i>View Only
                    </span>
                </div>
            </div>
            <a href="{{ url()->previous() ?: ($isPIC ? '/pic/insiden' : ($isManager ? '/manager/insiden' : ($isSHE ? '/she/insiden' : '/insiden'))) }}" 
               class="btn btn-outline-secondary btn-back">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
        <hr class="mt-3 mb-0">
    </div>

    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-primary">
                    <i class="bi bi-clipboard2-data me-2"></i>Detail Laporan Insiden
                </h5>
                <span class="badge bg-secondary">
                    <i class="bi bi-eye me-1"></i>View Only
                </span>
            </div>
        </div>
        
        <div class="card-body p-4">
            {{-- Bagian Data Awal --}}
            <div class="detail-section mb-5">
                <h6 class="section-title text-primary mb-3">
                    <i class="bi bi-info-circle me-2"></i>Data Insiden Awal
                </h6>
                
                <div class="table-responsive">
                    <table class="table table-bordered detail-table">
                        <tbody>
                            <tr>
                                <th style="width: 25%">Tanggal</th>
                                <td>{{ \Carbon\Carbon::parse($insiden->tanggal)->format('d F Y') }}</td>
                                <th style="width: 25%">Jam</th>
                                <td>{{ \Carbon\Carbon::parse($insiden->jam)->format('H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Lokasi Kejadian</th>
                                <td colspan="3">{{ $insiden->lokasi }}</td>
                            </tr>
                            <tr>
                                <th>Kondisi Luka</th>
                                <td colspan="3">{{ $insiden->kondisi_luka ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Kategori Accident</th>
                                <td>{{ $insiden->kategori }}</td>
                                @if($insiden->kategori === 'Work Accident')
                                <th>Tipe Work Accident</th>
                                <td>{{ $insiden->work_accident_type ?? '-' }}</td>
                                @else
                                <th colspan="2"></th>
                                @endif
                            </tr>
                            <tr>
                                <th>Section</th>
                                <td>{{ $insiden->section->section ?? '-' }}</td>
                                <th>Departemen</th>
                                <td>{{ $insiden->departemen ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td colspan="3">
                                    <span class="badge {{ $insiden->status === 'open' ? 'bg-warning' : ($insiden->status === 'progress' ? 'bg-primary' : ($insiden->status === 'closed' ? 'bg-success' : ($insiden->status === 'rejected' ? 'bg-danger' : 'bg-secondary'))) }}">
                                        {{ ucfirst($insiden->status) }}
                                    </span>
                                </td>
                            </tr>
                            @if ($insiden->status == 'closed')
                                 <tr>
                                    <th>Catatan</th>
                                    <td colspan="3">
                                       {{ isset($insiden->catatan_close) ? $insiden->catatan_close : '-' }}
                                    </td>
                                </tr>
                            @endif
                            @if ($insiden->status == 'rejected')
                                 <tr>
                                    <th>Catatan</th>
                                    <td colspan="3">
                                       {{ isset($insiden->catatan_reject) ? $insiden->catatan_reject : '-' }}
                                    </td>
                                </tr>
                            @endif
                            {{-- @if ($insiden->user_id) --}}
                                 <tr>
                                    <th>Korban</th>
                                    <td colspan="3">
                                      {{ $insiden->user->nama ?? '-' }}
                                    </td>
                                </tr>
                            {{-- @endif --}}
                            <tr>
                                <th>Dilaporkan Oleh</th>
                                <td>{{ $insiden->creator->nama ?? 'Tidak diketahui' }}</td>
                                <th>Waktu Laporan</th>
                                <td>{{ \Carbon\Carbon::parse($insiden->created_at)->format('d F Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Bagian Kronologi --}}
            <div class="detail-section mb-5">
                <h6 class="section-title text-primary mb-3">
                    <i class="bi bi-journal-text me-2"></i>Kronologi dan Detail Insiden
                </h6>
                
                <div class="card bg-light border-0">
                    <div class="card-body">
                        @if($insiden->kronologi)
                            <p class="mb-0" style="white-space: pre-line">{{ $insiden->kronologi }}</p>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                                <p class="text-muted mb-0">Belum ada kronologi yang diisi</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                @if($insiden->updated_at && $insiden->kronologi)
                <div class="text-end mt-2">
                    <small class="text-muted">
                        <i class="bi bi-clock-history me-1"></i>
                        Diperbarui: {{ \Carbon\Carbon::parse($insiden->updated_at)->format('d F Y H:i') }}
                    </small>
                </div>
                @endif
            </div>

            {{-- Bagian Foto & Dokumen Pendukung --}}
            @if(count($fotosArray) > 0)
            <div class="detail-section mb-5">
                <h6 class="section-title text-primary mb-3">
                    <i class="bi bi-images me-2"></i>Foto & Dokumen Pendukung
                </h6>
                
                <div class="row g-3">
                    @foreach($fotosArray as $index => $foto)
                    <div class="col-md-4 col-lg-3">
                        <div class="card border-0 shadow-sm">
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $foto) }}" 
                                     class="card-img-top detail-image" 
                                     alt="Foto insiden {{ $index + 1 }}"
                                     onerror="this.onerror=null; this.src='data:image/svg+xml;charset=UTF-8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"100%25\" height=\"200\" viewBox=\"0 0 400 300\"><rect width=\"400\" height=\"300\" fill=\"%23f8f9fa\"/><text x=\"200\" y=\"150\" font-family=\"Arial\" font-size=\"16\" fill=\"%236c757d\" text-anchor=\"middle\" dy=\".3em\">Gambar tidak tersedia</text></svg>';">
                                <div class="position-absolute top-0 end-0 p-2">
                                    <span class="badge bg-dark">{{ $index + 1 }}</span>
                                </div>
                            </div>
                            <div class="card-body p-3 text-center">
                                <small class="text-muted">Foto {{ $index + 1 }}</small>
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $foto) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-zoom-in me-1"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Bagian Keterangan Lain --}}
            @if($insiden->keterangan_lain)
            <div class="detail-section mb-5">
                <h6 class="section-title text-primary mb-3">
                    <i class="bi bi-chat-left-text me-2"></i>Keterangan Lain
                </h6>
                
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <p class="mb-0" style="white-space: pre-line">{{ $insiden->keterangan_lain }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Bagian Catatan SHE --}}
            @if($insiden->catatan_she)
            <div class="detail-section mb-5">
                <h6 class="section-title text-primary mb-3">
                    <i class="bi bi-chat-square-text me-2"></i>Catatan SHE
                </h6>
                
                <div class="card border-primary border-start">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-shield-check text-primary fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-0">Tinjauan Tim SHE</h6>
                                @if($insiden->she_updated_at)
                                <small class="text-muted">
                                    Diperbarui: {{ \Carbon\Carbon::parse($insiden->she_updated_at)->format('d F Y H:i') }}
                                </small>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <p class="mb-0" style="white-space: pre-line">{{ $insiden->catatan_she }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Timeline Status --}}
            <div class="detail-section">
                <h6 class="section-title text-primary mb-3">
                    <i class="bi bi-clock-history me-2"></i>Timeline Status
                </h6>
                
                <div class="timeline">
                    <div class="timeline-item {{ $insiden->status === 'open' ? 'active' : '' }}">
                        <div class="timeline-marker bg-warning"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Open</h6>
                            <small class="text-muted">Laporan dibuat</small>
                            <p class="mb-0 small">{{ \Carbon\Carbon::parse($insiden->created_at)->format('d F Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($insiden->status === 'progress' || $insiden->status === 'closed' || $insiden->status === 'rejected')
                    <div class="timeline-item {{ $insiden->status === 'progress' ? 'active' : '' }}">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">In Progress</h6>
                            <small class="text-muted">Ditangani oleh PIC</small>
                            @if($insiden->kronologi)
                            <p class="mb-0 small">Kronologi telah diisi</p>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if($insiden->status === 'closed' || $insiden->status === 'rejected')
                    <div class="timeline-item {{ $insiden->status === 'closed' ? 'active' : 'active-danger' }}">
                        <div class="timeline-marker {{ $insiden->status === 'closed' ? 'bg-success' : 'bg-danger' }}"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">{{ ucfirst($insiden->status) }}</h6>
                            <small class="text-muted">Selesai diproses</small>
                            @if($insiden->updated_at)
                            <p class="mb-0 small">{{ \Carbon\Carbon::parse($insiden->updated_at)->format('d F Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Terakhir diperbarui: {{ \Carbon\Carbon::parse($insiden->updated_at)->format('d F Y H:i') }}
                    </small>
                </div>
                <div>
                    <a href="{{ url()->previous() ?: ($isPIC ? '/pic/insiden' : ($isManager ? '/manager/insiden' : ($isSHE ? '/she/insiden' : '/insiden'))) }}" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                    
                    @if($permissions->can_edit)
                        @php
                            $editUrl = '';
                            if ($isPIC) {
                                $editUrl = url('/pic/insiden/edit/' . $insiden->id);
                            } elseif ($isSHE) {
                                $editUrl = url('/she/insiden/edit/' . $insiden->id);
                            } elseif ($isManager) {
                                $editUrl = url('/manager/insiden/edit/' . $insiden->id);
                            } else {
                                $editUrl = url('/insiden/' . $insiden->id . '/edit');
                            }
                        @endphp
                        
                        <a href="{{ $editUrl }}" class="btn btn-primary ms-2">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

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

.card {
    border-radius: 12px;
    border: 1px solid var(--border-color);
    overflow: hidden;
}

.detail-section {
    padding: 1.5rem;
    background: var(--light-color);
    border-radius: var(--border-radius);
    border: 1px solid var(--border-color);
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

.detail-table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: var(--box-shadow);
    margin-bottom: 0;
}

.detail-table th {
    background-color: var(--primary-light);
    color: var(--primary-color);
    font-weight: 600;
    border-color: var(--border-color);
    padding: 1rem;
}

.detail-table td {
    padding: 1rem;
    border-color: var(--border-color);
    vertical-align: middle;
}

.detail-table tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.detail-image {
    height: 200px;
    object-fit: cover;
    border-radius: 6px 6px 0 0;
}

/* Timeline Styles */
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: var(--border-color);
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: flex-start;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-item.active .timeline-content h6 {
    color: var(--primary-color);
    font-weight: 600;
}

.timeline-item.active-danger .timeline-content h6 {
    color: var(--danger-color);
    font-weight: 600;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: var(--box-shadow);
    z-index: 1;
}

.timeline-content {
    flex: 1;
    padding-left: 1rem;
}

.timeline-content h6 {
    margin-bottom: 0.25rem;
    color: var(--secondary-color);
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
    padding: 0.35rem 0.65rem;
}

.btn {
    border-radius: 6px;
    padding: 0.5rem 1.25rem;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--box-shadow);
}

@media (max-width: 768px) {
    .content {
        padding: 1rem !important;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
    
    .detail-section {
        padding: 1rem;
    }
    
    .detail-table th,
    .detail-table td {
        padding: 0.75rem;
        font-size: 0.9rem;
    }
    
    .timeline {
        padding-left: 1.5rem;
    }
    
    .timeline::before {
        left: 0.75rem;
    }
    
    .timeline-marker {
        left: -1.75rem;
        width: 1.25rem;
        height: 1.25rem;
    }
}

@media print {
    .btn-back, .card-footer {
        display: none !important;
    }
    
    .detail-section {
        border: 1px solid #000;
        break-inside: avoid;
    }
    
    .detail-table {
        border: 1px solid #000;
    }
    
    .detail-table th,
    .detail-table td {
        border: 1px solid #000;
    }
}
</style>

@endsection