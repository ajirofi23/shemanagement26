@extends('layout.managersidebar')

@section('content')
<div class="content p-3">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold" style="color:#0f172a;">
            <i class="bi bi-clipboard-data me-2 text-primary"></i> Laporan Insiden (Department)
        </h3>
    </div>

    <hr class="mb-4">

    {{-- TABLE --}}
    <div class="card shadow-lg border-0">
        <div class="card-body p-3">
            <h5 class="card-title mb-3 text-secondary">
                <i class="bi bi-list-columns me-1"></i> Daftar Insiden
            </h5>

            @if($insidens->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x fs-1 text-muted mb-3"></i>
                    <h6 class="text-muted">Tidak ada laporan insiden</h6>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Lokasi</th>
                            <th>Kategori</th>
                            <th>Section</th>
                            <th>Departemen</th>
                            <th>Status</th>
                            <th style="width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($insidens as $index => $insiden)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($insiden->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($insiden->jam)->format('H:i') }}</td>
                            <td>{{ Str::limit($insiden->lokasi, 25) }}</td>
                            <td>
                                <span class="badge bg-info">{{ $insiden->kategori }}</span>
                            </td>
                            <td>{{ $insiden->section }}</td>
                            <td>{{ $insiden->department }}</td>
                            <td>
                                @switch($insiden->status)
                                    @case('open')
                                        <span class="badge bg-warning text-dark">Open</span>
                                        @break
                                    @case('progress')
                                        <span class="badge bg-primary">In Progress</span>
                                        @break
                                    @case('closed')
                                        <span class="badge bg-success">Closed</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $insiden->status }}</span>
                                @endswitch
                            </td>
                            <td>
                                <a href="{{ url('/manager/insiden/detail/'.$insiden->id) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Lihat Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
