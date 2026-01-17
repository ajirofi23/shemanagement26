@extends('layout.itsidebar')

@section('content')

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        --glass-bg: rgba(255, 255, 255, 0.95);
        --glass-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    }

    .card-modern {
        background: var(--glass-bg);
        box-shadow: var(--glass-shadow);
        border-radius: 16px;
    }
</style>

<div class="content p-4" style="background:#f8fafc; min-height:100vh;">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold">
                <i class="bi bi-people-fill text-primary me-2"></i>
                Manajemen Pengguna
            </h4>
            <small class="text-muted">Kelola data pengguna dan hak akses</small>
        </div>

        <button class="btn btn-primary rounded-pill"
            data-bs-toggle="modal"
            data-bs-target="#addUserModal">
            <i class="bi bi-plus-circle me-1"></i> Tambah User
        </button>
    </div>

    {{-- CARD --}}
    <div class="card card-modern border-0">
        <div class="card-body">

            {{-- SEARCH --}}
            <form class="mb-3" method="GET">
                <input type="text" name="search"
                    value="{{ request('search') }}"
                    class="form-control"
                    placeholder="Cari nama / username / email">
            </form>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Section</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Level</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <strong>{{ $user->nama }}</strong><br>
                                <small class="text-muted">{{ $user->usr }}</small>
                            </td>

                            {{-- SECTION (DISPLAY SAJA) --}}
                            <td>
                                @if($user->section_name)
                                    <span class="badge bg-light text-dark border">
                                        {{ $user->section_name }}
                                    </span>
                                @else
                                    <span class="text-muted">Tidak Ada</span>
                                @endif
                            </td>

                            <td>{{ $user->email }}</td>

                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ strtoupper($user->level) }}
                                </span>
                            </td>

                            <td class="text-end">
                                @if($permission?->can_edit)
                                <button class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUserModal{{ $user->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endif

                                @if($permission?->can_delete)
                                <form class="d-inline"
                                    method="POST"
                                    action="{{ url('/it/management-user/destroy/'.$user->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Hapus user ini?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Tidak ada data user
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

{{-- ========================= --}}
{{-- MODALS --}}
{{-- ========================= --}}
@section('modals')

{{-- ========================= --}}
{{-- MODAL TAMBAH USER --}}
{{-- ========================= --}}
<div class="modal fade" id="addUserModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form method="POST"
                action="{{ url('/it/management-user/store') }}"
                enctype="multipart/form-data">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah User</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label>Nama</label>
                        <input name="nama" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Username</label>
                        <input name="usr" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Section</label>
                        <select name="section_id" class="form-select">
                            <option value="">Tidak Ada</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}">
                                    {{ $section->section }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Level</label>
                        <select name="level" class="form-select">
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                            <option value="it">IT</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col">
                            <label>Status</label>
                            <select name="is_active" class="form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col">
                            <label>PC User</label>
                            <select name="is_user_computer" class="form-select">
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- ========================= --}}
{{-- MODAL EDIT USER --}}
{{-- ========================= --}}
@foreach($users as $user)
@if($permission?->can_edit)
<div class="modal fade" id="editUserModal{{ $user->id }}">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form method="POST"
                action="{{ url('/it/management-user/update/'.$user->id) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit User</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label>Nama</label>
                        <input name="nama" class="form-control" value="{{ $user->nama }}" required>
                    </div>

                    <div class="mb-2">
                        <label>Username</label>
                        <input name="usr" class="form-control" value="{{ $user->usr }}" required>
                    </div>

                    <div class="mb-2">
                        <label>Section</label>
                        <select name="section_id" class="form-select">
                            <option value="">Tidak Ada</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}"
                                    {{ $user->section_id == $section->id ? 'selected' : '' }}>
                                    {{ $section->section }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Update</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endif
@endforeach

@endsection
