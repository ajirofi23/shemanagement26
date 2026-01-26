@extends('layout.sidebar')

@section('content')

@php
    $months = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
@endphp

<style>
    :root {
        --primary-bold: #4f46e5;
        --secondary-text: #64748b;
    }

    .content { background-color: #f8fafc; min-height: 100vh; }
    
    .modern-card {
        border-radius: 12px;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    /* Summary Card Mini */
    .mini-card {
        border-radius: 10px;
        padding: 1rem;
        background: white;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .mini-card .icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Table Improvements */
    .modern-table thead th {
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.05em;
        font-weight: 700;
        padding: 12px 15px;
        vertical-align: middle;
    }

    .modern-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.2s;
    }

    .modern-table tbody tr:hover {
        background-color: #f8fafc !important;
    }

    /* Action Buttons */
    .btn-action {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-action:hover {
        opacity: 0.8;
        transform: translateY(-1px);
    }

    .img-thumb {
        width: 45px;
        height: 45px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }

    .badge-status {
        padding: 0.4em 0.8em;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.7rem;
    }
</style>

<div class="content p-4">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item small"><a href="{{ url('/she/komitmen-k3') }}" class="text-decoration-none">Komitmen K3</a></li>
                    <li class="breadcrumb-item small active">Detail Section</li>
                </ol>
            </nav>
            <h3 style="color:#0f172a;" class="fw-bold m-0">
                Detail: {{ $sectionName['section'] ?? $sectionName }}
            </h3>
            <p class="text-muted small mb-0">
                <i class="bi bi-calendar3 me-1"></i>
                Periode: <strong>
                @if($filter['monthYear'])
                    {{ date('F Y', strtotime($filter['monthYear'] . '-01')) }}
                @elseif($filter['bulan'] && $filter['tahun'])
                    {{ $months[$filter['bulan']] ?? $filter['bulan'] }} {{ $filter['tahun'] }}
                @else
                    Semua Periode
                @endif
                </strong>
            </p>
        </div>
        
        <div>
            <a href="{{ url('/she/komitmen-k3') . '?' . http_build_query($filter) }}" class="btn btn-light border btn-sm px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Summary Cards Mini --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="mini-card shadow-sm border-start border-primary border-4">
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people"></i></div>
                <div>
                    <div class="text-muted small fw-bold">TOTAL TARGET</div>
                    <div class="h5 mb-0 fw-bold">{{ $totalUserTarget }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mini-card shadow-sm border-start border-success border-4">
                <div class="icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="text-muted small fw-bold">SELESAI</div>
                    <div class="h5 mb-0 fw-bold">{{ $totalUserAktual }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mini-card shadow-sm border-start border-warning border-4">
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="text-muted small fw-bold">MENUNGGU</div>
                    <div class="h5 mb-0 fw-bold">{{ $totalUserTarget - $totalUserAktual }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mini-card shadow-sm border-start border-info border-4">
                <div class="icon bg-info bg-opacity-10 text-info"><i class="bi bi-graph-up"></i></div>
                <div>
                    <div class="text-muted small fw-bold">PENCAPAIAN</div>
                    <div class="h5 mb-0 fw-bold">{{ $persentaseAktual }}%</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="card modern-card mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ url()->current() }}" id="filterForm">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary mb-1">Cari Karyawan</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" value="{{ $filter['search'] ?? '' }}" class="form-control border-start-0" placeholder="Nama atau NIP...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Bulan</label>
                        <select name="bulan" class="form-select form-select-sm auto-submit">
                            @foreach($months as $key => $label)
                                <option value="{{ $key }}" {{ ($filter['bulan'] ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Tahun</label>
                        <select name="tahun" class="form-select form-select-sm auto-submit">
                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" {{ ($filter['tahun'] ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button class="btn btn-primary btn-sm flex-grow-1"><i class="bi bi-filter me-1"></i> Terapkan</button>
                        <a href="{{ url()->current() }}" class="btn btn-light border btn-sm">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Komitmen --}}
    <div class="card modern-card">
        <div class="card-body p-0">
            @if($komitmens->isEmpty())
                <div class="text-center py-5">
                    <img src="https://illustrations.popsy.co/slate/empty-folder.svg" alt="No data" style="width: 150px;" class="mb-4">
                    <h5 class="text-secondary fw-bold">Data tidak ditemukan</h5>
                    <p class="text-muted small">Tidak ada record komitmen untuk kriteria yang dipilih.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0" id="komitmenTable">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4" width="60px">No</th>
                                <th>Informasi Karyawan</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Waktu Komitmen</th>
                                <th class="text-center">Bukti</th>
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($komitmens as $index => $komitmen)
                            <tr>
                                <td class="ps-4 text-muted small fw-bold">#{{ $komitmens->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $komitmen->user->nama ?? 'N/A' }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">NIP: {{ $komitmen->user->kode_user ?? 'N/A' }}</div>
                                </td>
                                <td class="text-center">
                                    @if($komitmen->status === 'Sudah Upload')
                                        <span class="badge-status bg-light border border-success text-success">
                                            <i class="bi bi-check2-circle me-1"></i> SELESAI
                                        </span>
                                    @else
                                        <span class="badge-status bg-light border border-warning text-warning">
                                            <i class="bi bi-clock-history me-1"></i> PENDING
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="small fw-semibold">{{ $komitmen->created_at ? \Carbon\Carbon::parse($komitmen->created_at)->format('d/m/Y') : '-' }}</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">{{ $komitmen->created_at ? \Carbon\Carbon::parse($komitmen->created_at)->format('H:i') . ' WIB' : '' }}</div>
                                </td>
                                <td class="text-center">
                                    @if($komitmen->bukti)
                                        <a href="{{ asset('storage/' . $komitmen->bukti) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $komitmen->bukti) }}" class="img-thumb shadow-sm" alt="Bukti">
                                        </a>
                                    @else
                                        <span class="text-muted small italic" style="font-size: 0.7rem;">Tidak ada foto</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    @if($komitmen->bukti)
                                    <a href="{{ asset('storage/'.$komitmen->bukti) }}" class="btn-action bg-light border text-primary" target="_blank" title="Lihat Foto">
                                         <i class="bi bi-eye"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        Menampilkan {{ $komitmens->firstItem() }} - {{ $komitmens->lastItem() }} dari {{ $komitmens->total() }} data
                    </div>
                    <div>
                        {{ $komitmens->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const filterForm = document.getElementById("filterForm");
    const autoSubmits = document.querySelectorAll(".auto-submit");

    autoSubmits.forEach(select => {
        select.addEventListener("change", () => {
            filterForm.submit();
        });
    });
});
</script>

@endsection