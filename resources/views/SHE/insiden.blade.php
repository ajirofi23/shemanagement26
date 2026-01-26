@extends(($isPICAccess || $isManagerAccess) ? 'layout.picsidebar' : 'layout.sidebar')

@section('content')

<style>
    /* Modern Variable & Reset */
    :root {
        --primary-soft: #e0e7ff;
        --primary-bold: #4f46e5;
        --secondary-text: #64748b;
        --glass-bg: rgba(255, 255, 255, 0.9);
    }

    .content {
        background-color: #f8fafc;
        min-height: 100vh;
    }

    /* Header Styling */
    .page-header-title {
        letter-spacing: -0.5px;
    }

    /* Card & Shadow Improvements */
    .modern-card {
        border-radius: 12px !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
    }

    .filter-card {
        background: white;
        border-left: 4px solid var(--primary-bold) !important;
    }

    /* Table Improvements */
    .table-container {
        border-radius: 12px;
        overflow: hidden;
        background: white;
    }

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

    /* Status Badges - Modern Pill Style */
    .badge-pill-modern {
        padding: 0.5em 1em;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.7rem;
        display: inline-block;
    }

    .bg-soft-warning { background-color: #fef3c7; color: #92400e; }
    .bg-soft-primary { background-color: #e0e7ff; color: #3730a3; }
    .bg-soft-success { background-color: #dcfce7; color: #166534; }
    .bg-soft-danger { background-color: #fee2e2; color: #991b1b; }
    .bg-soft-info { background-color: #e0f2fe; color: #075985; }

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

    /* Filter Input focus */
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-bold);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }
</style>

<div class="content p-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 style="color:#1e293b;" class="fw-bold page-header-title mb-1">
                <i class="bi bi-shield-check me-2 text-primary"></i> Data Laporan Insiden
            </h3>
            <p class="text-muted small mb-0">Kelola dan pantau semua laporan insiden di satu tempat.</p>
        </div>
        
        @if(($permissions->can_add && $isSHEAccess))
        <a href="{{ url('/she/insiden/form') }}" class="btn btn-primary px-4 py-2 shadow-sm rounded-3">
            <i class="bi bi-plus-lg me-2"></i>Tambah Insiden
        </a>
        @endif
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Filter Section --}}
    <div class="card modern-card filter-card shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-funnel-fill me-2 text-primary"></i> Filter Data
                </h6>
                <div class="d-flex gap-2">
                    <button type="button" id="resetFilter" class="btn btn-sm btn-light border">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                    <button type="submit" form="filterForm" class="btn btn-sm btn-primary">
                        <i class="bi bi-filter me-1"></i> Terapkan
                    </button>
                </div>
            </div>
            
            <form action="{{ $currentUrl }}" method="GET" id="filterForm">
                <div class="row g-2">
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Tanggal</label>
                        <input type="date" id="tanggal" name="tanggal" value="{{ request('tanggal') }}" class="form-control form-control-sm" max="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Bulan</label>
                        <input type="month" id="bulan" name="bulan" value="{{ request('bulan') }}" class="form-control form-control-sm">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Section</label>
                        <select id="section_id" name="section_id" class="form-select form-select-sm">
                            <option value="">Semua Section</option>
                            @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->section }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Kategori</label>
                        <select id="kategori" name="kategori" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            @foreach($kategoriList ?? [] as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Status</label>
                        <select id="status" name="status" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="progress" {{ request('status') === 'progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-secondary mb-1">Pencarian Lokasi</label>
                        <div class="input-group input-group-sm">
                            <input type="text" id="search_lokasi" name="search_lokasi" value="{{ request('search_lokasi') }}" class="form-control" placeholder="Lokasi...">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Insiden --}}
    <div class="card modern-card shadow-sm">
        <div class="card-body p-0">
            @if($insidens->isEmpty())
            <div class="text-center py-5">
                <img src="https://illustrations.popsy.co/slate/empty-folder.svg" alt="Empty" style="width: 150px;" class="mb-4">
                <h5 class="text-secondary fw-bold">Tidak ada data ditemukan</h5>
                <p class="text-muted small">Cobalah menyesuaikan filter Anda untuk mendapatkan hasil yang berbeda.</p>
                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="document.getElementById('resetFilter').click()">Reset Filter</button>
            </div>
            @else
            <div class="table-responsive">
                <table class="table modern-table align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4" style="width: 60px;">No</th>
                            <th>Info Kejadian</th>
                            <th>Lokasi</th>
                            <th>Kategori</th>
                            <th>Unit / Dept</th>
                            <th>Status</th>
                            <th class="pe-4 text-end" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = ($insidens->currentPage() - 1) * $insidens->perPage() + 1; ?>
                        @foreach($insidens as $insiden)
                        <tr>
                            <td class="ps-4">
                                <span class="text-muted fw-bold">#{{ $no++ }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-dark">{{ \Carbon\Carbon::parse($insiden->tanggal)->format('d M Y') }}</span>
                                    <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($insiden->jam)->format('H:i') }} WIB</small>
                                </div>
                            </td>
                            <td>
                                <span class="text-secondary small">{{ Str::limit($insiden->lokasi, 30) }}</span>
                            </td>
                            <td>
                                <span class="badge-pill-modern bg-soft-info text-info">{{ $insiden->kategori }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold small text-dark">{{ $insiden->section_name ?? '-' }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $insiden->departemen }}</span>
                                </div>
                            </td>
                            <td>
                                @switch($insiden->status)
                                    @case('open')
                                        <span class="badge-pill-modern bg-soft-warning">Open</span> @break
                                    @case('progress')
                                        <span class="badge-pill-modern bg-soft-primary">In Progress</span> @break
                                    @case('closed')
                                        <span class="badge-pill-modern bg-soft-success">Closed</span> @break
                                    @case('rejected')
                                        <span class="badge-pill-modern bg-soft-danger">Rejected</span> @break
                                    @default
                                        <span class="badge-pill-modern bg-secondary">{{ $insiden->status }}</span>
                                @endswitch
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    {{-- Detail --}}
                                    @php
                                        $viewUrl = url('/she/insiden/detail/' . $insiden->id);
                                        if(strpos($currentUrl, '/manager/insiden') !== false) $viewUrl = url('/manager/insiden/detail/' . $insiden->id);
                                        elseif(strpos($currentUrl, '/pic/insiden') !== false) $viewUrl = url('/pic/insiden/detail/' . $insiden->id);
                                    @endphp
                                    <a href="{{ $viewUrl }}" class="btn-action bg-soft-primary text-primary" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    {{-- Edit --}}
                                    @if($permissions->can_edit)
                                        @php $showEdit = false; @endphp
                                        @if(strpos($currentUrl, '/manager/insiden') !== false && ($insiden->status === 'progress' || $insiden->status === 'open'))
                                            @php $editUrl = url('/manager/insiden/edit/' . $insiden->id); $showEdit = true; @endphp
                                        @elseif(strpos($currentUrl, '/pic/insiden') !== false && ($insiden->status === 'open' || $insiden->status === 'rejected'))
                                            @php $editUrl = url('/pic/insiden/edit/' . $insiden->id); $showEdit = true; @endphp
                                        @elseif(strpos($currentUrl, '/she') !== false && $insiden->status === 'progress')
                                            @php $editUrl = url('/she/insiden/edit/' . $insiden->id); $showEdit = true; @endphp
                                        @endif

                                        @if($showEdit)
                                        <a href="{{ $editUrl }}" class="btn-action bg-soft-warning text-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endif
                                    @endif

                                    {{-- Delete --}}
                                    @if($permissions->can_delete && $insiden->status == 'open')
                                        @php
                                            $deleteUrl = url('/she/insiden/delete/' . $insiden->id);
                                            if(strpos($currentUrl, '/manager/insiden') !== false) $deleteUrl = url('/manager/insiden/delete/' . $insiden->id);
                                            elseif(strpos($currentUrl, '/pic/insiden') !== false) $deleteUrl = url('/pic/insiden/delete/' . $insiden->id);
                                        @endphp
                                        <form action="{{ $deleteUrl }}" method="POST" class="d-inline" onsubmit="return confirmDelete(event)">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action bg-soft-danger text-danger border-0" title="Hapus">
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
            @endif
        </div>
    </div>

    {{-- Pagination --}}
    @if($insidens->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-4 px-2">
        <div class="text-muted small">
            Menampilkan {{ $insidens->firstItem() }} sampai {{ $insidens->lastItem() }} dari {{ $insidens->total() }} data
        </div>
        <div>
            {{ $insidens->appends(request()->except('page'))->links() }}
        </div>
    </div>
    @endif
</div>

{{-- Scripts tetap sama tapi dengan tambahan konfirmasi yang lebih manis --}}
<script>
    function confirmDelete(event) {
        event.preventDefault();
        const form = event.target.closest('form');
        if (confirm('Apakah Anda yakin ingin menghapus laporan ini secara permanen?')) {
            form.submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit logic dan reset filter tetap dipertahankan dari kode asli Anda
        const form = document.getElementById('filterForm');
        
        document.getElementById('resetFilter')?.addEventListener('click', function() {
            form.querySelectorAll('input, select').forEach(input => {
                input.value = '';
                if (input.type === 'select-one') input.selectedIndex = 0;
            });
            form.submit();
        });

        // Delay submit untuk input teks agar user selesai mengetik
        let searchTimeout;
        ['department', 'search_lokasi'].forEach(id => {
            document.getElementById(id)?.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => form.submit(), 800);
            });
        });

        // Langsung submit untuk select box & date
        ['section_id', 'kategori', 'status', 'bulan', 'tanggal'].forEach(id => {
            document.getElementById(id)?.addEventListener('change', () => form.submit());
        });
    });
</script>

@endsection