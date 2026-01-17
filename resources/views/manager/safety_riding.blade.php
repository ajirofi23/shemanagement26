@extends('layout.managersidebar')

@section('content')
@php
    /**
     * Helper ambil array bukti
     */
    function getBuktiArray($bukti) {
        if (is_array($bukti)) return $bukti;
        if (is_string($bukti) && !empty($bukti)) {
            $decoded = json_decode($bukti, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    $statusColors = [
        'Open'     => 'danger',
        'Progress' => 'warning',
        'Close'    => 'success',
        'Rejected' => 'secondary'
    ];
@endphp

<div class="content p-3">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 style="color:#0f172a;" class="fw-bold">
            <i class="bi bi-person-bounding-box me-2 text-info"></i>
            Laporan Safety Riding
        </h3>
    </div>

    <hr class="mb-4">

    {{-- CARD --}}
    <div class="card shadow-lg border-0">
        <div class="card-body p-3">

            <h5 class="card-title mb-3 fs-5 text-secondary">
                <i class="bi bi-list-columns-reverse me-1"></i>
                Daftar Laporan Safety Riding (View Only)
            </h5>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-hover table-striped mt-3 align-middle table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:40px;">No</th>
                            <th style="width:140px;">Waktu Kejadian</th>
                            <th style="width:120px;">Section</th>
                            <th>Nama Pelanggar</th>
                            <th style="width:120px;">Tipe Kendaraan</th>
                            <th style="width:110px;">NOPOL</th>
                            <th style="width:90px;">Total</th>
                            <th style="width:90px;">Status</th>
                            <th style="width:100px;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($safetyridings as $laporan)
                        @php
                            $totalPelanggaran = $laporan->pds->count() + $laporan->pfs->count();
                            $statusBadge = $statusColors[$laporan->status] ?? 'secondary';

                            $buktiBefore = getBuktiArray($laporan->bukti);
                            $buktiAfter  = getBuktiArray($laporan->bukti_after);
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($laporan->waktu_kejadian)->format('d-m-Y H:i') }}
                            </td>
                            <td>
                                {{ $laporan->user->section->section ?? '-' }}
                            </td>
                            <td>
                                {{ $laporan->user->nama ?? '-' }}
                            </td>
                            <td>{{ $laporan->type_kendaraan }}</td>
                            <td>{{ $laporan->nopol }}</td>
                            <td class="text-center">
                                <span class="badge bg-dark">{{ $totalPelanggaran }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusBadge }}">
                                    {{ $laporan->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if(count($buktiBefore) || count($buktiAfter))
                                    <button class="btn btn-sm btn-info text-white"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detailModal{{ $laporan->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Tidak ada data Safety Riding
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- ========================= --}}
{{-- MODAL DETAIL (VIEW ONLY) --}}
{{-- ========================= --}}
@foreach($safetyridings as $laporan)
@php
    $buktiBefore = getBuktiArray($laporan->bukti);
    $buktiAfter  = getBuktiArray($laporan->bukti_after);
@endphp

@if(count($buktiBefore) || count($buktiAfter))
<div class="modal fade" id="detailModal{{ $laporan->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    Detail Bukti – Laporan #{{ $laporan->id }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab"
                            data-bs-target="#before{{ $laporan->id }}">
                            Before
                            <span class="badge bg-info">{{ count($buktiBefore) }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab"
                            data-bs-target="#after{{ $laporan->id }}">
                            After
                            <span class="badge bg-success">{{ count($buktiAfter) }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- BEFORE --}}
                    <div class="tab-pane fade show active" id="before{{ $laporan->id }}">
                        <div class="row">
                            @forelse($buktiBefore as $path)
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <a href="{{ asset('storage/'.$path) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$path) }}"
                                             class="card-img-top"
                                             style="height:150px;object-fit:cover;">
                                    </a>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">Tidak ada bukti before</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- AFTER --}}
                    <div class="tab-pane fade" id="after{{ $laporan->id }}">
                        <div class="row">
                            @forelse($buktiAfter as $path)
                            <div class="col-md-3 mb-3">
                                <div class="card">
                                    <a href="{{ asset('storage/'.$path) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$path) }}"
                                             class="card-img-top"
                                             style="height:150px;object-fit:cover;">
                                    </a>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">Belum ada bukti after</p>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>
@endif
@endforeach
@endsection
