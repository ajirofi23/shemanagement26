@extends('layout.managersidebar')

@section('content')
<div class="content p-3">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold" style="color:#0f172a;">
            <i class="bi bi-shield-check me-2 text-success"></i> Program Safety
        </h3>

        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProgramModal">
            <i class="bi bi-plus-circle"></i> Tambah Program
        </button>
    </div>

    {{-- SEARCH --}}
    <form method="GET" class="mb-3">
        <div class="input-group" style="max-width: 350px;">
            <input type="text" name="search" class="form-control"
                   value="{{ request('search') }}" placeholder="Cari program...">
            <button class="btn btn-success">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    {{-- TABLE --}}
    <div class="card shadow-sm">
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-sm align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Program</th>
                            <th>Department</th>
                            <th>Deskripsi</th>
                            <th>Aktivitas</th>
                            <th>Target</th>
                            <th>Budget</th>
                            <th>Plan Date</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programs as $program)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $program->nama_program }}</td>
                            <td>{{ $program->department }}</td>
                            <td>{{ Str::limit($program->deskripsi, 40) }}</td>
                            <td>{{ Str::limit($program->aktivitas, 40) }}</td>
                            <td>{{ $program->target }}</td>
                            <td>Rp {{ number_format($program->budget,0,',','.') }}</td>
                            <td>{{ $program->plan_date }}</td>
                            <td>{{ $program->due_date }}</td>
                            <td>
                                <span class="badge
                                    {{ $program->status == 'Open' ? 'bg-warning text-dark' :
                                       ($program->status == 'On Progress' ? 'bg-info' : 'bg-success') }}">
                                    {{ $program->status }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $program->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form method="POST"
                                      action="{{ url('/manager/programsafety/'.$program->id) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Yakin hapus data?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


{{-- ===================== --}}
{{-- MODAL EDIT (DI LUAR TABLE) --}}
{{-- ===================== --}}
@foreach($programs as $program)
<div class="modal fade" id="editModal{{ $program->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ url('/manager/programsafety/'.$program->id) }}">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Program Safety</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label>Nama Program</label>
                        <input type="text" name="nama_program" class="form-control"
                               value="{{ $program->nama_program }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Target</label>
                        <input type="text" name="target" class="form-control"
                               value="{{ $program->target }}" readonly>
                    </div>

                   {{-- Budget Tampilan --}}
                    <input type="text" class="form-control bg-light"
                        value="Rp {{ number_format($program->budget, 0, ',', '.') }}"
                        readonly>

                    {{-- Budget Asli (Dikirim ke Server) --}}
                    <input type="hidden" name="budget" value="{{ $program->budget }}">

                    <div class="col-md-6">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="Open" {{ $program->status=='Open'?'selected':'' }}>Open</option>
                            <option value="On Progress" {{ $program->status=='On Progress'?'selected':'' }}>On Progress</option>
                            <option value="Closed" {{ $program->status=='Closed'?'selected':'' }}>Closed</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Plan Date</label>
                        <input type="date" name="plan_date" class="form-control"
                               value="{{ $program->plan_date }}">
                    </div>

                    <div class="col-md-6">
                        <label>Due Date</label>
                        <input type="date" name="due_date" class="form-control"
                               value="{{ $program->due_date }}">
                    </div>

                    <div class="col-md-12">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control">{{ $program->deskripsi }}</textarea>
                    </div>

                    <div class="col-md-12">
                        <label>Aktivitas</label>
                        <textarea name="aktivitas" class="form-control">{{ $program->aktivitas }}</textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">
                        <i class="bi bi-save"></i> Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach


{{-- ===================== --}}
{{-- MODAL TAMBAH (SATU KALI SAJA) --}}
{{-- ===================== --}}
<div class="modal fade" id="addProgramModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ url('/manager/programsafety') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Tambah Program Safety</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label>Nama Program</label>
                        <input type="text" name="nama_program" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label>Target</label>
                        <input type="text" name="target" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>Budget</label>
                        <input type="number" name="budget" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>Plan Date</label>
                        <input type="date" name="plan_date" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label>Due Date</label>
                        <input type="date" name="due_date" class="form-control">
                    </div>

                    <div class="col-md-12">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" required></textarea>
                    </div>

                    <div class="col-md-12">
                        <label>Aktivitas</label>
                        <textarea name="aktivitas" class="form-control" required></textarea>
                    </div>

                    <div class="col-md-12">
                        <label>Remark</label>
                        <textarea name="remark" class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-success">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
