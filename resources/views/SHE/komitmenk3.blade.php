@extends('layout.sidebar')

@section('content')

    @php
        $targetPersen = 70;
    @endphp

    <style>
        :root {
            --primary-bold: #4f46e5;
            --secondary-text: #64748b;
        }

        .content {
            background: #f8fafc;
            min-height: 100vh;
        }

        .modern-card {
            border-radius: 12px;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
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

        /* Progress Bar Modern */
        .progress-modern {
            height: 8px;
            border-radius: 10px;
            background-color: #e2e8f0;
            overflow: hidden;
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

        .badge-soft-primary {
            background: #e0e7ff;
            color: #4338ca;
        }

        .bg-soft-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .bg-soft-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .bg-soft-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .bg-soft-orange {
            background-color: #fff7ed;
            color: #ea580c;
        }
    </style>

    <div class="content p-4">
        {{-- Header Halaman --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 style="color:#0f172a;" class="fw-bold m-0">
                    <i class="bi bi-shield-lock-fill me-2 text-primary"></i>Monitoring Komitmen K3
                </h3>
                <p class="text-muted small mb-0">Pemantauan persentase penyelesaian komitmen K3 antar section.</p>
            </div>

            <div class="d-flex gap-2">
                @php
                    $exportParams = [];
                    if ($bulan)
                        $exportParams['bulan'] = $bulan;
                    if ($tahun)
                        $exportParams['tahun'] = $tahun;
                    if ($monthYear)
                        $exportParams['monthYear'] = $monthYear;
                    if ($search)
                        $exportParams['search'] = $search;
                    $exportUrl = url('/she/komitmen-k3/export') . '?' . http_build_query($exportParams);
                @endphp
                <a href="{{ $exportUrl }}" class="btn btn-success btn-sm px-3 shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
                </a>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="card modern-card mb-4">
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

                <form action="{{ url()->current() }}" method="GET" id="filterForm">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary mb-1">Periode Monitoring</label>
                            <input type="month" id="monthYear" name="monthYear" value="{{ $monthYear ?? '' }}"
                                class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-secondary mb-1">Cari Section</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchInput" name="search" value="{{ $search ?? '' }}"
                                    class="form-control" placeholder="Nama section...">
                            </div>
                        </div>

                        @if(isset($totalSummary['periode']))
                            <div class="col-md-5 d-flex align-items-end justify-content-end">
                                <span class="badge badge-soft-primary px-3 py-2 rounded-3">
                                    <i class="bi bi-info-circle me-1"></i> Menampilkan data periode:
                                    <strong>{{ $totalSummary['periode'] }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Area --}}
        <div class="card modern-card">
            <div class="card-body p-0">
                @if(empty($summary) || count($summary) === 0)
                    <div class="text-center py-5">
                        <img src="https://illustrations.popsy.co/slate/empty-folder.svg" style="width: 150px;" class="mb-4">
                        <h5 class="fw-bold text-secondary">Tidak ada data ditemukan</h5>
                        <p class="text-muted small">Data untuk periode yang dipilih saat ini tidak tersedia.</p>
                        <a href="{{ url()->current() }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">Refresh
                            Halaman</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table modern-table align-middle mb-0" id="k3Table">
                            <thead class="table-dark">
                                <tr>
                                    <th width="60px" class="ps-4">No</th>
                                    <th>Nama Section</th>
                                    <th class="text-center">Target</th>
                                    <th class="text-center">Aktual (%)</th>
                                    <th class="text-center">Karyawan (Aktual/Total)</th>
                                    <th width="100px" class="pe-4 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalUserTarget = 0;
                                    $totalUserAktual = 0;
                                    $sectionCount = count($summary);
                                @endphp

                                @foreach($summary as $index => $sectionData)
                                    @php
                                        $userTarget = (int) ($sectionData['total_user_target'] ?? 0);
                                        $userAktual = (int) ($sectionData['total_user_aktual'] ?? 0);
                                        $persentaseAktual = $userTarget > 0 ? ($userAktual / $userTarget) * 100 : 0;

                                        $totalUserTarget += $userTarget;
                                        $totalUserAktual += $userAktual;

                                        // Progress Color & Style
                                        if ($persentaseAktual >= $targetPersen) {
                                            $bgClass = 'bg-success';
                                            $textClass = 'text-success';
                                        } elseif ($persentaseAktual >= ($targetPersen * 0.8)) {
                                            $bgClass = 'bg-warning';
                                            $textClass = 'text-warning';
                                        } else {
                                            $bgClass = 'bg-danger';
                                            $textClass = 'text-danger';
                                        }
                                    @endphp

                                    <tr>
                                        <td class="ps-4 text-muted small fw-bold">#{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $sectionData['section_name'] }}</div>
                                            <small class="text-muted" style="font-size: 0.7rem;">Periode:
                                                {{ $sectionData['periode'] ?? '' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold text-primary small">{{ $targetPersen }}%</span>
                                        </td>
                                        <td class="text-center" style="min-width: 140px;">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <div class="flex-grow-1" style="max-width: 80px;">
                                                    <div class="progress progress-modern">
                                                        <div class="progress-bar {{ $bgClass }}"
                                                            style="width: {{ min($persentaseAktual, 100) }}%"></div>
                                                    </div>
                                                </div>
                                                <span
                                                    class="fw-bold small {{ $textClass }}">{{ number_format($persentaseAktual, 1) }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="small fw-semibold">
                                                <span class="text-primary">{{ $userAktual }}</span> <span
                                                    class="text-muted">/</span> {{ $userTarget }}
                                            </div>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="/she/komitmen-k3/detail/{{ $sectionData['section_id'] }}"
                                                class="btn-action bg-soft-primary text-primary" title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            @if($sectionCount > 0)
                                <tfoot class="table-light">
                                    @php
                                        $totalPersentase = $totalUserTarget > 0 ? ($totalUserAktual / $totalUserTarget) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td colspan="3" class="ps-4 fw-bold py-3">AGGREGATE TOTAL</td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-2">
                                                <div class="progress progress-modern flex-grow-1" style="max-width: 80px;">
                                                    <div class="progress-bar {{ $totalPersentase >= $targetPersen ? 'bg-success' : 'bg-danger' }}"
                                                        style="width: {{ min($totalPersentase, 100) }}%"></div>
                                                </div>
                                                <span
                                                    class="fw-bold {{ $totalPersentase >= $targetPersen ? 'text-success' : 'text-danger' }}">{{ number_format($totalPersentase, 1) }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-center py-3">
                                            <div class="small">
                                                <span class="fw-bold text-primary">{{ $totalUserAktual }}</span> /
                                                {{ $totalUserTarget }}
                                            </div>
                                        </td>
                                        <td class="pe-4 text-end text-muted small">SUMMARY</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const filterForm = document.getElementById("filterForm");
            const monthYearInput = document.getElementById("monthYear");

            document.getElementById('resetFilter').addEventListener('click', function () {
                if (monthYearInput) monthYearInput.value = '';
                document.getElementById('searchInput').value = '';
                filterForm.submit();
            });

            monthYearInput?.addEventListener('change', () => filterForm.submit());
        });
    </script>

@endsection