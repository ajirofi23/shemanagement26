@extends('layout.managersidebar')

@section('content')
<div class="content p-3">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold" style="color:#0f172a;">
            <i class="bi bi-journal-check me-2 text-warning"></i>
            Laporan Hyari Hatto – Department {{ $hyarihattos->first()->department ?? '-' }}
        </h3>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-2">

            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Section</th>
                            <th>Deskripsi</th>
                            <th>Rekomendasi</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hyarihattos as $laporan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $laporan->section }}</td>
                            <td>{{ Str::limit($laporan->deskripsi, 60) }}</td>
                            <td>{{ $laporan->rekomendasi ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($laporan->created_at)->format('d-m-Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Tidak ada laporan Hyari Hatto untuk department ini
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
