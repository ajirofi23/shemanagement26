@extends('layout.sidebar')

@section('content')

@php
    // 🎯 TARGET KOMITMEN K3 (GLOBAL)
    $targetPersen = 70;
@endphp

<style>
    /* Modern Theme & Animations */
    :root {
        --primary-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        --accent-color: #3b82f6;
        --glass-bg: rgba(255, 255, 255, 0.95);
    }

    .content-wrapper {
        animation: fadeInUp 0.6s ease-out;
        background: #f8fafc;
        min-height: 100vh;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Card Styling */
    .custom-card {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        background: white;
    }

    /* Table Improvements */
    .modern-table {
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .modern-table thead th {
        background: #f1f5f9;
        color: #475569;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        border: none;
        padding: 1.25rem;
    }

    .modern-table tbody tr {
        background: white;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .modern-table tbody tr:hover {
        transform: scale(1.005);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07);
        background-color: #fff !important;
    }

    .modern-table td {
        padding: 1.25rem;
        border: none;
    }

    .modern-table td:first-child { border-top-left-radius: 1rem; border-bottom-left-radius: 1rem; }
    .modern-table td:last-child { border-top-right-radius: 1rem; border-bottom-right-radius: 1rem; }

    /* Progress Bar Modern */
    .progress-modern {
        height: 8px;
        border-radius: 10px;
        background-color: #e2e8f0;
        overflow: hidden;
    }

    .progress-bar-animated-slow {
        transition: width 1.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* Button Enhancements */
    .btn-modern-export {
        background: #10b981;
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 0.75rem;
        transition: all 0.3s;
    }

    .btn-modern-export:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        color: white;
    }

    .btn-detail-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        border: 1px solid #e2e8f0;
        color: #64748b;
    }

    .btn-detail-circle:hover {
        background: var(--accent-color);
        color: white;
        border-color: var(--accent-color);
        transform: rotate(15deg);
    }

    /* Filter Section Glassmorphism */
    .filter-section {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 1rem;
    }

    .badge-soft-primary { background: #e0e7ff; color: #4338ca; }
    .bg-orange { background-color: #f97316 !important; }
</style>

<div class="content p-4 content-wrapper">

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item small"><a href="#">Safety Health & Environment</a></li>
                    <li class="breadcrumb-item small active" aria-current="page">Monitoring</li>
                </ol>
            </nav>
            <h2 style="color:#0f172a;" class="fw-bold m-0">
                <i class="bi bi-shield-lock-fill me-2 text-primary"></i>Monitoring Komitmen K3
            </h2>
            @if(isset($totalSummary['periode']))
            <p class="text-muted mt-1 mb-0">
                <span class="badge badge-soft-primary px-3 py-2">
                    <i class="bi bi-calendar3 me-2"></i>Periode: <strong>{{ $totalSummary['periode'] }}</strong>
                </span>
            </p>
            @endif
        </div>
        
        <div class="d-flex gap-2">
            @php
                $exportParams = [];
                if ($bulan) $exportParams['bulan'] = $bulan;
                if ($tahun) $exportParams['tahun'] = $tahun;
                if ($monthYear) $exportParams['monthYear'] = $monthYear;
                if ($search) $exportParams['search'] = $search;
                $exportUrl = url('/she/komitmen/export') . '?' . http_build_query($exportParams);
            @endphp
            <a href="{{ $exportUrl }}" class="btn btn-modern-export shadow-sm">
                <i class="bi bi-file-earmark-arrow-down me-2"></i>Export Spreadsheet
            </a>
        </div>
    </div>

    <div class="card custom-card">
        <div class="card-body p-4">
            
            {{-- Filter Section --}}
            <div class="filter-section p-4 mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-funnel-fill text-primary"></i>
                    </div>
                    <h6 class="m-0 fw-bold text-dark">Data Optimization Filters</h6>
                </div>
                
                <form action="{{ url()->current() }}" method="GET" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label for="monthYear" class="form-label small fw-bold text-muted">Periode Monitoring</label>
                            <input type="month" id="monthYear" name="monthYear" 
                                   value="{{ $monthYear ?? '' }}" 
                                   class="form-control form-control-lg border-light bg-light"
                                   style="border-radius: 0.75rem; font-size: 0.9rem;">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="searchInput" class="form-label small fw-bold text-muted">Search Section</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light" style="border-radius: 0.75rem 0 0 0.75rem;">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" id="searchInput" name="search" 
                                       value="{{ $search ?? '' }}" 
                                       class="form-control form-control-lg border-0 bg-light" 
                                       placeholder="Ketik nama section..."
                                       style="border-radius: 0 0.75rem 0.75rem 0; font-size: 0.9rem;">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold" style="border-radius: 0.75rem; font-size: 0.9rem;">
                                    Apply Filter
                                </button>
                                <button type="button" id="resetFilter" class="btn btn-outline-secondary btn-lg px-3" style="border-radius: 0.75rem;">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table Area --}}
            @if(empty($summary) || count($summary) === 0)
            <div class="text-center py-5">
                <img src="https://illustrations.popsy.co/flat/no-data-found.svg" style="width: 200px;" class="mb-4">
                <h5 class="fw-bold">No commitment data found</h5>
                <p class="text-muted">Data for the selected period is currently unavailable.</p>
                <a href="{{ url()->current() }}" class="btn btn-primary rounded-pill px-4">
                    Refresh Data
                </a>
            </div>
            @else
            <div class="table-responsive">
                <table class="table modern-table align-middle" id="k3Table">
                    <thead>
                        <tr>
                            <th width="60px" class="text-center">No</th>
                            <th>Section Name</th>
                            <th class="text-center">Periode</th>
                            <th class="text-center">Target</th>
                            <th class="text-center">Aktual (%)</th>
                            <th class="text-center">User (Target/Aktual)</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                            $totalUserTarget = 0;
                            $totalUserAktual = 0;
                            $sectionCount = count($summary);
                        @endphp

                        @foreach($summary as $sectionData)
                            @php
                                $userTarget = (int) ($sectionData['total_user_target'] ?? 0);
                                $userAktual = (int) ($sectionData['total_user_aktual'] ?? 0);
                                $periode = $sectionData['periode'] ?? '';
                                $persentaseAktual = $userTarget > 0 ? ($userAktual / $userTarget) * 100 : 0;
                                
                                $totalUserTarget += $userTarget;
                                $totalUserAktual += $userAktual;

                                // Color Logic
                                $statusColor = '#dc3545';
                                $bgClass = 'bg-danger';
                                if($persentaseAktual >= $targetPersen) { $statusColor = '#10b981'; $bgClass = 'bg-success'; }
                                elseif($persentaseAktual >= ($targetPersen * 0.8)) { $statusColor = '#f59e0b'; $bgClass = 'bg-warning'; }
                                elseif($persentaseAktual >= ($targetPersen * 0.5)) { $statusColor = '#f97316'; $bgClass = 'bg-orange'; }
                            @endphp

                            <tr>
                                <td class="text-center text-muted fw-bold small">{{ $no++ }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $sectionData['section_name'] }}</div>
                                    <div class="small text-muted">ID: #{{ $sectionData['section_id'] }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border-0 px-3 py-2 rounded-pill small">
                                        {{ $periode }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-primary">{{ $targetPersen }}%</span>
                                </td>
                                <td class="text-center" style="min-width: 150px;">
                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        <div class="flex-grow-1" style="max-width: 100px;">
                                            <div class="progress progress-modern">
                                                <div class="progress-bar progress-bar-animated-slow {{ $bgClass }}" 
                                                     role="progressbar" 
                                                     style="width: {{ min($persentaseAktual, 100) }}%"></div>
                                            </div>
                                        </div>
                                        <span class="fw-bold" style="color: {{ $statusColor }}">
                                            {{ number_format($persentaseAktual, 1) }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <span class="badge bg-light text-muted px-2">{{ $userTarget }}</span>
                                        <i class="bi bi-arrow-right small text-muted"></i>
                                        <span class="badge bg-primary px-2">{{ $userAktual }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="/she/komitmen-k3/detail/{{ $sectionData['section_id'] }}" 
                                       class="btn-detail-circle" 
                                       data-bs-toggle="tooltip" title="View Details">
                                        <i class="bi bi-arrow-right-short fs-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $totalPersentase = $totalUserTarget > 0 ? ($totalUserAktual / $totalUserTarget) * 100 : 0;
                        @endphp
                        @if($sectionCount > 0)
                        <tr style="background: var(--primary-gradient) !important; color: white;">
                            <td colspan="3" class="text-end fw-bold py-4">AGGREGATE TOTAL</td>
                            <td class="text-center fw-bold py-4">{{ $targetPersen }}%</td>
                            <td class="text-center py-4">
                                <span class="fs-5 fw-bold">{{ number_format($totalPersentase, 1) }}%</span>
                            </td>
                            <td class="text-center py-4">
                                <div class="small opacity-75">Target: {{ $totalUserTarget }}</div>
                                <div class="fw-bold">Aktual: {{ $totalUserAktual }}</div>
                            </td>
                            <td class="text-center py-4 text-white-50">SUMMARY</td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>

            <div class="mt-4 p-3 rounded-4 bg-light d-flex justify-content-between align-items-center border-start border-primary border-4">
                <div class="small">
                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                    Found <strong>{{ $sectionCount }}</strong> sections for monitoring period.
                </div>
                <div class="small text-muted italic">
                    <i class="bi bi-clock-history me-1"></i>Last synced: {{ now()->format('H:i:s d M Y') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    const filterForm = document.getElementById("filterForm");
    const monthYearInput = document.getElementById("monthYear");

    // Reset Filter
    document.getElementById('resetFilter')?.addEventListener('click', function() {
        if (monthYearInput) monthYearInput.value = '';
        document.getElementById('searchInput').value = '';
        filterForm.submit();
    });

    // Auto-submit on date change with delay for better UX
    monthYearInput?.addEventListener('change', function() {
        this.style.opacity = "0.5";
        setTimeout(() => filterForm.submit(), 400);
    });
});
</script>

@endsection