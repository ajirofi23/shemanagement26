@extends('layout.picsidebar')

@section('content')
<style>
    /* Import Font Modern */
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

    :root {
        --primary-gradient: linear-gradient(135deg, #198754 0%, #2fb380 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
    }

    .content {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
        min-height: 100vh;
        animation: fadeIn 0.8s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Modern Card */
    .card-modern {
        border: none;
        border-radius: 20px;
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.04);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Filter & Search Bar */
    .filter-wrapper {
        background: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }

    .form-control-modern {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 18px;
        transition: all 0.3s;
    }

    .form-control-modern:focus {
        border-color: #198754;
        box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.1);
        outline: none;
    }

    /* Table Styling */
    .table thead th {
        background: #f8fafc;
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 1px;
        color: #475569;
        padding: 20px 15px;
        border: none;
    }

    /* Staggered Row Animation - Menggunakan CSS Manual agar tidak error */
    .table tbody tr {
        animation: slideInUp 0.5s ease-out both;
        border-bottom: 1px solid #f1f5f9;
    }

    /* Memberikan delay unik untuk 10 baris pertama agar terlihat estetik saat load */
    .table tbody tr:nth-child(1) { animation-delay: 0.1s; }
    .table tbody tr:nth-child(2) { animation-delay: 0.15s; }
    .table tbody tr:nth-child(3) { animation-delay: 0.2s; }
    .table tbody tr:nth-child(4) { animation-delay: 0.25s; }
    .table tbody tr:nth-child(5) { animation-delay: 0.3s; }

    .table tbody tr:hover {
        background-color: #f8fafc !important;
        transform: scale(1.005) translateX(5px);
        transition: all 0.3s ease;
    }

    /* Badge Custom */
    .badge-modern {
        padding: 8px 14px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.7rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-grad {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 10px 24px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-grad:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(25, 135, 84, 0.3);
        color: white;
    }

    .timeline-box {
        background: #f1f5f9;
        padding: 6px 10px;
        border-radius: 8px;
        display: inline-block;
        min-width: 120px;
    }
</style>

<div class="content p-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color:#0f172a; letter-spacing: -1px;">
                <span class="text-success"><i class="bi bi-shield-lock-fill"></i> Program</span> Safety
            </h2>
            <p class="text-muted mb-0">Monitor aktivitas dan kepatuhan keselamatan kerja secara real-time.</p>
        </div>
        <div class="d-none d-md-block text-end">
            <span class="badge bg-white text-dark border p-2 px-3 rounded-pill shadow-sm">
                <i class="bi bi-clock-history me-1 text-success"></i> {{ date('d M Y') }} | {{ date('H:i') }}
            </span>
        </div>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="filter-wrapper">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted text-uppercase">Cari Program</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 shadow-sm" style="border-radius: 12px 0 0 12px; border: 1.5px solid #e2e8f0;">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" class="form-control form-control-modern border-start-0 shadow-sm" 
                           value="{{ request('search') }}" placeholder="Contoh: Inspeksi K3...">
                </div>
            </div>
            
            <div class="col-md-5">
                <label class="form-label small fw-bold text-muted text-uppercase">Filter Timeline</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 shadow-sm" style="border-radius: 12px 0 0 12px; border: 1.5px solid #e2e8f0;">
                        <i class="bi bi-calendar-range text-muted"></i>
                    </span>
                    <input type="month" name="timeline" class="form-control form-control-modern border-start-0 shadow-sm"
                           value="{{ request('timeline') }}">
                </div>
            </div>

            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button class="btn btn-grad w-100 shadow-sm" type="submit">
                        <i class="bi bi-filter-right me-1"></i> Terapkan
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-light border shadow-sm" style="border-radius: 12px; padding: 10px 15px;" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="card card-modern">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" width="60">No</th>
                            <th>Informasi Program</th>
                            <th>Department</th>
                            <th>Deskripsi & Aktivitas</th>
                            <th>Target</th>
                            <th>Budget</th>
                            <th>Timeline</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programs as $program)
                        <tr>
                            <td class="text-center">
                                <span class="fw-bold text-muted">{{ $loop->iteration }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">{{ $program->nama_program }}</div>
                                <small class="text-muted text-uppercase" style="font-size: 0.7rem;">ID: #{{ str_pad($program->id, 4, '0', STR_PAD_LEFT) }}</small>
                            </td>
                            <td>
                                <span class="badge-modern bg-light text-primary border">
                                    <i class="bi bi-building"></i> {{ $program->department }}
                                </span>
                            </td>
                            <td>
                                <div class="text-dark small fw-medium mb-1">{{ Str::limit($program->deskripsi, 40) }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    <i class="bi bi-arrow-right-short"></i> {{ Str::limit($program->aktivitas, 35) }}
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark small"><i class="bi bi-bullseye text-success me-1"></i>{{ $program->target }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark small">
                                    Rp {{ number_format($program->budget, 0, ',', '.') }}
                                </div>
                            </td>
                            <td>
                                <div class="timeline-box shadow-sm border">
                                    <div class="text-primary fw-bold" style="font-size: 0.7rem;">
                                        <i class="bi bi-play-fill me-1"></i>{{ $program->plan_date }}
                                    </div>
                                    <div class="text-danger fw-bold" style="font-size: 0.7rem;">
                                        <i class="bi bi-stop-fill me-1"></i>{{ $program->due_date }}
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusClass = [
                                        'Open' => 'bg-warning text-dark border-warning',
                                        'On Progress' => 'bg-info text-white border-info',
                                        'Completed' => 'bg-success text-white border-success'
                                    ][$program->status] ?? 'bg-secondary text-white';
                                    
                                    $statusIcon = [
                                        'Open' => 'bi-clock-history',
                                        'On Progress' => 'bi-arrow-repeat',
                                        'Completed' => 'bi-check2-all'
                                    ][$program->status] ?? 'bi-question-circle';
                                @endphp
                                <span class="badge-modern {{ $statusClass }} shadow-sm">
                                    <i class="bi {{ $statusIcon }}"></i> {{ $program->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-clipboard2-x display-1 text-light"></i>
                                    <h5 class="mt-3 text-muted fw-bold">Data Tidak Ditemukan</h5>
                                    <p class="text-muted small">Coba gunakan kata kunci lain atau periksa filter timeline Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection