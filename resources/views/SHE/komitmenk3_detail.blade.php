@extends('layout.sidebar')

@section('content')
<style>
    /* Modern UI Enhancements */
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
    }

    .content {
        background-color: #f8fafc;
        min-height: 100vh;
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Breadcrumb Styling */
    .breadcrumb-item a {
        color: #64748b;
        transition: color 0.3s;
    }
    .breadcrumb-item a:hover { color: #4338ca; }

    /* Modern Cards */
    .summary-card {
        border: none;
        border-radius: 16px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        background: white;
    }

    .summary-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
    }

    .icon-shape {
        width: 54px;
        height: 54px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 24px;
    }

    /* Table Styling */
    .custom-table-card {
        border-radius: 20px;
        overflow: hidden;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    #komitmenTable thead {
        background: #0f172a;
        color: white;
    }

    #komitmenTable th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1.2rem 1rem;
        border: none;
    }

    #komitmenTable tbody tr {
        transition: all 0.2s;
    }

    #komitmenTable tbody tr:hover {
        background-color: #f1f5f9 !important;
        transform: scale(1.002);
    }

    /* Badge Styling */
    .badge-modern {
        padding: 0.6em 1.2em;
        border-radius: 8px;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* Image Preview Styling */
    .img-preview {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
        cursor: pointer;
        transition: transform 0.3s;
    }

    .img-preview:hover {
        transform: scale(1.1);
    }

    /* Filter Section */
    .filter-wrapper {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 0.6rem 1rem;
        border-color: #e2e8f0;
    }

    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        border-color: #6366f1;
    }

    /* CSS FIX UNTUK PAGINATION TUMPANG TINDIH */
    .pagination-container {
        display: block !important; /* Menghindari konflik flex parent */
        width: 100%;
    }

    .modern-pagination nav {
        display: flex !important;
        justify-content: flex-end !important;
        margin: 0;
    }

    .modern-pagination .pagination {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Memastikan info data di kiri tidak terganggu */
    .info-data {
        display: flex;
        align-items: center;
        height: 100%;
    }
</style>

<div class="content p-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item">
                <a href="{{ url('/she/komitmen-k3') }}" class="text-decoration-none">
                    <i class="bi bi-grid-1x2-fill me-1"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ url('/she/komitmen-k3') }}" class="text-decoration-none">Komitmen K3</a>
            </li>
            <li class="breadcrumb-item active fw-medium" aria-current="page">Detail Section</li>
        </ol>
    </nav>

    {{-- Header Halaman --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 style="color:#1e293b;" class="fw-bold mb-1">
                <span class="text-primary">|</span> Detail Section: {{ $sectionName['section'] }}
            </h2>
            <p class="text-muted mb-0">
                <i class="bi bi-calendar3-event me-1 text-primary"></i>
                @php
                    $months = [
                        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                    ];
                @endphp
                <span class="fw-medium">Periode:</span> 
                @if($filter['monthYear'])
                    {{ date('F Y', strtotime($filter['monthYear'] . '-01')) }}
                @elseif($filter['bulan'] && $filter['tahun'])
                    {{ $months[$filter['bulan']] ?? $filter['bulan'] }} {{ $filter['tahun'] }}
                @elseif($filter['bulan'])
                    Bulan {{ $months[$filter['bulan']] ?? $filter['bulan'] }}
                @elseif($filter['tahun'])
                    Tahun {{ $filter['tahun'] }}
                @else
                    Semua Periode
                @endif
            </p>
        </div>
        
        <div>
            <a href="{{ url('/she/komitmen-k3') . '?' . http_build_query($filter) }}" 
               class="btn btn-white shadow-sm border-0 px-4 py-2 fw-semibold" style="border-radius: 12px; transition: 0.3s">
                <i class="bi bi-chevron-left me-2"></i>Kembali
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card shadow-sm border-start border-primary border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <p class="text-sm text-uppercase fw-bold text-muted mb-1" style="font-size: 11px;">Total Target</p>
                            <h3 class="mb-0 fw-bold" style="color: #1e293b;">{{ $totalUserTarget }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card shadow-sm border-start border-success border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <p class="text-sm text-uppercase fw-bold text-muted mb-1" style="font-size: 11px;">Selesai</p>
                            <h3 class="mb-0 fw-bold" style="color: #1e293b;">{{ $totalUserAktual }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card shadow-sm border-start border-warning border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <p class="text-sm text-uppercase fw-bold text-muted mb-1" style="font-size: 11px;">Menunggu</p>
                            <h3 class="mb-0 fw-bold" style="color: #1e293b;">{{ $totalUserTarget - $totalUserAktual }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card summary-card shadow-sm border-start border-info border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <p class="text-sm text-uppercase fw-bold text-muted mb-1" style="font-size: 11px;">Pencapaian</p>
                            <h3 class="mb-0 fw-bold" style="color: #1e293b;">{{ $persentaseAktual }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="filter-wrapper p-4 mb-4 shadow-sm">
        <form method="GET" action="{{ url()->current() }}">
            <div class="row g-3">
                <div class="col-lg-5 col-md-12">
                    <label class="form-label small fw-bold text-muted">Cari Nama / NIP</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="searchUser" name="search" value="{{ $filter['search'] ?? '' }}"
                               class="form-control bg-light border-start-0" placeholder="Masukkan kata kunci...">
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted">Bulan</label>
                    <select name="bulan" class="form-select bg-light">
                        <option value="">-- Semua Bulan --</option>
                        @foreach($months as $key => $label)
                            <option value="{{ $key }}" {{ ($filter['bulan'] ?? '') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted">Tahun</label>
                    <select name="tahun" class="form-select bg-light">
                        <option value="">-- Tahun --</option>
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ ($filter['tahun'] ?? '') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-lg-3 col-md-4 d-flex align-items-end gap-2">
                    <button class="btn btn-primary w-100 fw-semibold py-2" style="border-radius: 10px;">
                        <i class="bi bi-sliders me-2"></i> Terapkan
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary w-50 py-2" style="border-radius: 10px;">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabel Komitmen --}}
    <div class="card custom-table-card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
                <h5 class="card-title mb-0 fw-bold text-dark">
                    <i class="bi bi-list-stars me-2 text-primary"></i>Daftar Record Komitmen
                </h5>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                        Filter Status
                    </button>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="filterByStatus('all')">Semua</a></li>
                        <li><a class="dropdown-item text-success" href="javascript:void(0)" onclick="filterByStatus('sudah')">Sudah Komitmen</a></li>
                        <li><a class="dropdown-item text-warning" href="javascript:void(0)" onclick="filterByStatus('belum')">Belum Komitmen</a></li>
                    </ul>
                </div>
            </div>

            @if($komitmens->isEmpty())
                <div class="text-center py-5">
                    <img src="https://illustrations.popsy.co/gray/data-analysis.svg" alt="No data" style="width: 200px;" class="mb-4">
                    <h5 class="text-dark fw-bold">Data tidak ditemukan</h5>
                    <p class="text-muted small">Coba ubah parameter filter atau pencarian Anda.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="komitmenTable">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Informasi User</th>
                                <th class="text-center">Section / Dept</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Waktu Komitmen</th>
                                <th class="text-center">Evidence</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = $komitmens->firstItem() ?: 1; @endphp
                            @foreach($komitmens as $komitmen)
                            @php $user = $komitmen->user; @endphp
                            <tr>
                                <td class="text-center text-muted fw-medium">{{ $no++ }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-soft bg-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #eef2ff;">
                                            <span class="text-primary fw-bold small">{{ substr($user->nama ?? 'N', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $user->nama ?? 'N/A' }}</div>
                                            <div class="text-muted small" style="font-size: 11px;">NIP: {{ $user->kode_user ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="d-block fw-medium text-dark">{{ $user->section->section ?? 'N/A' }}</span>
                                    <span class="text-muted small">{{ $user->section->department ?? 'N/A' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($komitmen->status === 'Sudah Upload')
                                        <span class="badge-modern bg-success bg-opacity-10 text-success small">
                                            <i class="bi bi-check2-all me-1"></i> Terverifikasi
                                        </span>
                                    @else
                                        <span class="badge-modern bg-warning bg-opacity-10 text-warning small">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="small fw-medium text-dark">
                                        {{ $komitmen->created_at ? \Carbon\Carbon::parse($komitmen->created_at)->format('d M Y') : '-' }}
                                    </div>
                                    <div class="text-muted small" style="font-size: 10px;">
                                        {{ $komitmen->created_at ? \Carbon\Carbon::parse($komitmen->created_at)->format('H:i') . ' WIB' : '' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($komitmen->bukti)
                                        <img src="{{ asset('storage/' . $komitmen->bukti) }}" class="img-preview shadow-sm" alt="Bukti" data-bs-toggle="tooltip" title="Klik untuk perbesar">
                                    @else
                                        <span class="text-muted small italic">No Image</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($komitmen->bukti)
                                    <a href="{{ asset('storage/'.$komitmen->bukti) }}" 
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm"
                                       target="_blank">
                                         <i class="bi bi-eye-fill me-1"></i> View
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination & Footer - FIX VERSION --}}
                <div class="p-4 border-top pagination-container">
                    <div class="row w-100 m-0 align-items-center">
                        <div class="col-12 col-md-6 p-0 text-center text-md-start mb-3 mb-md-0">
                            <div class="text-muted small info-data">
                                Menampilkan <strong>{{ $komitmens->count() }}</strong> data dari <strong>{{ $totalUserTarget }}</strong> total user
                            </div>
                        </div>
                        <div class="col-12 col-md-6 p-0">
                            <div class="modern-pagination">
                                {{ $komitmens->appends(request()->except('page'))->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    {{-- Last Updated Info --}}
    <div class="text-center mt-4">
        <span class="badge rounded-pill bg-white text-muted border px-3 py-2 fw-normal">
            <i class="bi bi-clock-history me-1 text-primary"></i> Data sinkron terakhir: {{ now()->format('d/m/Y H:i:s') }}
        </span>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Tooltip initialization
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    const searchInput = document.getElementById('searchUser');
    const komitmenRows = document.querySelectorAll('#komitmenTable tbody tr');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            
            komitmenRows.forEach(row => {
                const textContent = row.textContent.toLowerCase();
                if (textContent.includes(searchTerm)) {
                    row.style.display = '';
                    row.style.animation = 'fadeIn 0.4s';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    window.filterByStatus = function(status) {
        komitmenRows.forEach(row => {
            const statusCell = row.cells[3].textContent.toLowerCase().trim();
            
            if (status === 'all') {
                row.style.display = '';
            } else if (status === 'sudah' && statusCell.includes('terverifikasi')) {
                row.style.display = '';
            } else if (status === 'belum' && statusCell.includes('pending')) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    };
});
</script>
@endsection