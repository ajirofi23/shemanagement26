@extends('layout.managersidebar')

@section('content')
@php
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
        <h3 class="fw-bold" style="color:#0f172a;">
            <i class="bi bi-shield-check me-2 text-info"></i>
            Laporan Safety Patrol
        </h3>
    </div>

    <hr class="mb-4">

    {{-- CARD --}}
    <div class="card shadow-lg border-0">
        <div class="card-body p-3">

            <h5 class="card-title mb-3 fs-5 text-secondary">
                <i class="bi bi-list-columns-reverse me-1"></i>
                Daftar Laporan Safety Patrol (View Only)
            </h5>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-hover table-striped mt-3 align-middle table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:40px;">No</th>
                            <th style="width:110px;">Tanggal</th>
                            <th style="width:120px;">E-PORTE</th>
                            <th>Area</th>
                            <th>Problem</th>
                            <th>Counter Measure</th>
                            <th style="width:120px;">Section</th>
                            <th style="width:110px;">Due Date</th>
                            <th style="width:90px;">Status</th>
                            <th style="width:90px;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($safetypatrols as $laporan)
                        @php
                            $statusBadge = $statusColors[$laporan->status] ?? 'secondary';
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($laporan->tanggal)->format('d-m-Y') }}</td>
                            <td>{{ $laporan->eporte }}</td>
                            <td>{{ $laporan->area }}</td>
                            <td>{{ Str::limit($laporan->problem, 40) }}</td>
                            <td>{{ Str::limit($laporan->counter_measure, 40) }}</td>
                            <td>{{ $laporan->section->section ?? '-' }}</td>
                            <td>
                                {{ $laporan->due_date
                                    ? \Carbon\Carbon::parse($laporan->due_date)->format('d-m-Y')
                                    : '-' }}
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusBadge }}">
                                    {{ $laporan->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($laporan->foto_before || $laporan->foto_after)
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
                            <td colspan="10" class="text-center text-muted py-4">
                                Tidak ada data Safety Patrol
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- ============================ --}}
{{-- MODAL DETAIL (VIEW ONLY) --}}
{{-- ============================ --}}
@foreach($safetypatrols as $laporan)
@if($laporan->foto_before || $laporan->foto_after)
<div class="modal fade" id="detailModal{{ $laporan->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    Detail Safety Patrol – #{{ $laporan->id }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <ul class="nav nav-tabs mb-4">
                    @if($laporan->foto_before)
                    <li class="nav-item">
                        <button class="nav-link active"
                            data-bs-toggle="tab"
                            data-bs-target="#before{{ $laporan->id }}">
                            <i class="bi bi-camera me-1"></i> Before
                        </button>
                    </li>
                    @endif
                    @if($laporan->foto_after)
                    <li class="nav-item">
                        <button class="nav-link {{ !$laporan->foto_before ? 'active' : '' }}"
                            data-bs-toggle="tab"
                            data-bs-target="#after{{ $laporan->id }}">
                            <i class="bi bi-check-circle me-1"></i> After
                        </button>
                    </li>
                    @endif
                </ul>

                <div class="tab-content">

                    {{-- BEFORE --}}
                    @if($laporan->foto_before)
                    <div class="tab-pane fade show active" id="before{{ $laporan->id }}">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card">
                                    <a href="{{ asset('storage/'.$laporan->foto_before) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$laporan->foto_before) }}"
                                             class="card-img-top"
                                             style="height:300px;object-fit:cover;">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- AFTER --}}
                    @if($laporan->foto_after)
                    <div class="tab-pane fade {{ !$laporan->foto_before ? 'show active' : '' }}" id="after{{ $laporan->id }}">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card">
                                    <a href="{{ asset('storage/'.$laporan->foto_after) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$laporan->foto_after) }}"
                                             class="card-img-top"
                                             style="height:300px;object-fit:cover;">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
@endif
@endforeach
@endsection
