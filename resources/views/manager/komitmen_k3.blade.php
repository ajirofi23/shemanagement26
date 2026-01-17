@extends('layout.managersidebar')

@section('content')
<div class="content p-3">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold" style="color:#0f172a;">
            <i class="bi bi-clipboard-check me-2 text-success"></i> Komitmen K3 (Department)
        </h3>
    </div>

    <hr class="mb-4">

    <div class="card shadow-lg border-0">
        <div class="card-body p-3">
            <h5 class="card-title mb-3 text-secondary">
                <i class="bi bi-list-check me-1"></i> Daftar Komitmen K3
            </h5>

            @if($komitmenK3->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x fs-1 text-muted mb-3"></i>
                    <h6 class="text-muted">Belum ada data Komitmen K3</h6>
                </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama User</th>
                            <th>Section</th>
                            <th>Department</th>
                            <th>Komitmen</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($komitmenK3 as $index => $data)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $data->nama_user }}</td>
                            <td>{{ $data->section }}</td>
                            <td>{{ $data->department }}</td>
                            <td>{{ Str::limit($data->komitmen, 50) }}</td>
                            <td>
                                @if($data->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($data->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @else
                                    <span class="badge bg-secondary">{{ $data->status }}</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
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
