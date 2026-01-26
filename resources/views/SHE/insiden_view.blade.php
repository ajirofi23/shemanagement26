@extends(($isPIC || $isSHE || $isManager) ? 'layout.picsidebar' : 'layout.sidebar')

@section('content')

    <?php
    $fotosArray = [];
    if (!empty($insiden->foto)) {
        if (is_string($insiden->foto)) {
            $decoded = json_decode($insiden->foto, true);
            $fotosArray = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [$insiden->foto];
        } elseif (is_array($insiden->foto)) {
            $fotosArray = $insiden->foto;
        }
    }
    ?>

    <style>
        :root {
            --primary-bold: #4f46e5;
            --primary-soft: #eef2ff;
            --secondary-text: #64748b;
        }

        .content {
            background-color: #f8fafc;
            min-height: 100vh;
        }

        .modern-card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: var(--secondary-text);
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: #1e293b;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .section-header {
            border-bottom: 2px solid #f1f5f9;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
        }

        .badge-status {
            padding: 0.5em 1em;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .detail-photo {
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.2s;
        }

        .detail-photo:hover {
            transform: scale(1.02);
        }

        .timeline-light {
            position: relative;
            padding-left: 20px;
        }

        .timeline-light::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-light-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-light-item::after {
            content: '';
            position: absolute;
            left: -24px;
            top: 4px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #cbd5e1;
            border: 2px solid #fff;
        }

        .timeline-light-item.active::after {
            background: var(--primary-bold);
        }
    </style>

    <div class="content p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ url('/she/insiden') }}" class="text-decoration-none">Laporan
                                Insiden</a></li>
                        <li class="breadcrumb-item active">Detail #{{ $insiden->id }}</li>
                    </ol>
                </nav>
                <h3 class="fw-bold text-dark mb-0">Detail Laporan</h3>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-light border btn-sm px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                @if($permissions->can_edit)
                    <a href="{{ url('/she/insiden/edit/' . $insiden->id) }}" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-pencil me-1"></i> Edit Laporan
                    </a>
                @endif
            </div>
        </div>

        <div class="row g-3">
            {{-- Kolom Kiri: Informasi Utama --}}
            <div class="col-lg-8">
                <div class="card modern-card mb-3">
                    <div class="card-body p-4">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <span class="section-title"><i class="bi bi-info-circle me-1"></i> Informasi Kejadian</span>
                            @php
                                $statusClass = [
                                    'open' => 'bg-soft-warning text-warning',
                                    'progress' => 'bg-soft-primary text-primary',
                                    'closed' => 'bg-soft-success text-success',
                                    'rejected' => 'bg-soft-danger text-danger'
                                ][$insiden->status] ?? 'bg-light text-dark';
                            @endphp
                            <span
                                class="badge-status {{ str_replace('bg-soft-', 'bg-light border border-', $statusClass) }}">
                                {{ strtoupper($insiden->status) }}
                            </span>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="info-label">Waktu Kejadian</div>
                                <div class="info-value">
                                    <i class="bi bi-calendar3 me-1 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($insiden->tanggal)->format('d M Y') }}<br>
                                    <i class="bi bi-clock me-1 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($insiden->jam)->format('H:i') }} WIB
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Kategori / Tipe</div>
                                <div class="info-value">
                                    <span
                                        class="badge bg-light text-primary border border-primary-subtle">{{ $insiden->kategori }}</span><br>
                                    <small class="text-muted">{{ $insiden->work_accident_type ?? 'N/A' }}</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Lokasi</div>
                                <div class="info-value text-truncate" title="{{ $insiden->lokasi }}">
                                    <i class="bi bi-geo-alt me-1 text-danger"></i> {{ $insiden->lokasi }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="info-label">Bagian / Unit</div>
                                <div class="info-value">
                                    <div>{{ $insiden->section->section ?? '-' }}</div>
                                    <small class="text-muted">{{ $insiden->departemen ?? '-' }}</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Korban</div>
                                <div class="info-value">
                                    <i class="bi bi-person me-1 text-muted"></i> {{ $insiden->user->nama ?? '-' }}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Pelapor</div>
                                <div class="info-value">
                                    {{ $insiden->creator->nama ?? 'System' }}<br>
                                    <small class="text-muted"
                                        style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($insiden->created_at)->format('d/m/y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card modern-card mb-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <span class="section-title"><i class="bi bi-journal-text me-1"></i> Kronologi Kejadian</span>
                        </div>
                        <div class="p-3 bg-light rounded text-dark"
                            style="white-space: pre-line; min-height: 100px; font-size: 0.95rem; line-height: 1.6;">
                            {{ $insiden->kronologi ?: 'Belum ada kronologi yang diunggah.' }}
                        </div>

                        @if($insiden->kondisi_luka)
                            <div class="mt-4">
                                <div class="info-label">Kondisi Luka / Dampak</div>
                                <div class="p-2 border-start border-3 border-danger bg-light-subtle rounded">
                                    {{ $insiden->kondisi_luka }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if(count($fotosArray) > 0)
                    <div class="card modern-card">
                        <div class="card-body p-4">
                            <div class="section-header">
                                <span class="section-title"><i class="bi bi-images me-1"></i> Dokumentasi Foto</span>
                            </div>
                            <div class="row g-2">
                                @foreach($fotosArray as $foto)
                                    <div class="col-md-4">
                                        <a href="{{ asset('storage/' . $foto) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $foto) }}" class="w-100 detail-photo shadow-sm border"
                                                alt="Foto Insiden">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Kolom Kanan: Status & Review --}}
            <div class="col-lg-4">
                <div class="card modern-card mb-3">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <span class="section-title"><i class="bi bi-shield-check me-1"></i> Status Follow Up</span>
                        </div>

                        <div class="timeline-light mt-3">
                            <div class="timeline-light-item active">
                                <div class="fw-bold small text-dark">Laporan Diterima</div>
                                <div class="text-muted" style="font-size: 0.8rem;">
                                    {{ \Carbon\Carbon::parse($insiden->created_at)->format('d M Y, H:i') }}</div>
                            </div>

                            <div
                                class="timeline-light-item {{ in_array($insiden->status, ['progress', 'closed', 'rejected']) ? 'active' : '' }}">
                                <div class="fw-bold small text-dark">Penanganan PIC</div>
                                <div class="text-muted" style="font-size: 0.8rem;">Status: {{ ucfirst($insiden->status) }}
                                </div>
                            </div>

                            <div
                                class="timeline-light-item {{ in_array($insiden->status, ['closed', 'rejected']) ? 'active' : '' }}">
                                <div class="fw-bold small text-dark">Review SHE / Closing</div>
                                <div class="text-muted" style="font-size: 0.8rem;">
                                    @if($insiden->status == 'closed')
                                        Telah Ditutup pada {{ \Carbon\Carbon::parse($insiden->updated_at)->format('d M Y') }}
                                    @elseif($insiden->status == 'rejected')
                                        Laporan Ditolak
                                    @else
                                        Menunggu Review Selesai
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($insiden->catatan_she)
                    <div class="card modern-card mb-3 border-start border-4 border-info">
                        <div class="card-body p-4">
                            <div class="section-header">
                                <span class="section-title text-info"><i class="bi bi-chat-left-dots me-1"></i> Review Tim
                                    SHE</span>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded text-dark small">
                                {{ $insiden->catatan_she }}
                            </div>
                        </div>
                    </div>
                @endif

                @if($insiden->catatan_reject || $insiden->catatan_close)
                    <div
                        class="card modern-card border-start border-4 {{ $insiden->status == 'rejected' ? 'border-danger' : 'border-success' }}">
                        <div class="card-body p-4">
                            <div class="section-header">
                                <span
                                    class="section-title {{ $insiden->status == 'rejected' ? 'text-danger' : 'text-success' }}">
                                    <i class="bi bi-sticky me-1"></i> Catatan Akhir
                                </span>
                            </div>
                            <div class="bg-light p-3 rounded text-dark small italic">
                                "{{ $insiden->catatan_reject ?? $insiden->catatan_close }}"
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection