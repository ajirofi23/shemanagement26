@extends('layout.itsidebar')

@section('content')

<style>
.card-modern {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,.08);
    border: 1px solid rgba(0,0,0,.02);
}
.user-avatar-placeholder {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 20px;
    border-radius: 12px;
}
.permission-check {
    width: 1.3em;
    height: 1.3em;
    cursor: pointer;
}
</style>

<div class="content p-4" style="background:#f8fafc;min-height:100vh">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1e293b">
                <i class="bi bi-shield-lock-fill text-primary me-2"></i>
                Management Akses
            </h4>
            <small class="text-muted">Kelola hak akses user terhadap menu sistem</small>
        </div>
        
        @if(request('user_id'))
        <a href="{{ url('/it/management-akses') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar User
        </a>
        @endif
    </div>

    {{-- ================= MODE 1: PILIH USER ================= --}}
    @if(!request('user_id'))
    <div class="card card-modern">
        <div class="card-body p-4">

            <div class="row mb-4 align-items-center">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-0">Daftar User</h5>
                    <p class="text-muted small mb-0">Pilih user untuk mengatur hak akses</p>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="position-relative">
                        <i class="bi bi-search position-absolute text-muted" style="top:10px; left:15px"></i>
                        <input type="text" name="search_user"
                            value="{{ request('search_user') }}"
                            class="form-control rounded-pill ps-5"
                            placeholder="Cari user berdasarkan nama atau username...">
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">User</th>
                            <th>Username</th>
                            <th>Departemen</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar-placeholder me-3">
                                        {{ substr($u->nama, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $u->nama }}</div>
                                        <small class="text-muted">ID: {{ $u->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $u->usr }}</span></td>
                            <td>{{ $u->departemen ?? '-' }}</td>
                            <td class="text-end pe-3">
                                <a href="{{ url('/it/management-akses?user_id='.$u->id) }}"
                                   class="btn btn-primary btn-sm rounded-pill px-3">
                                   Atur Akses <i class="bi bi-chevron-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                User tidak ditemukan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    @endif

    {{-- ================= MODE 2: ATUR PERMISSION ================= --}}
    @if(request('user_id') && isset($selectedUser))
    <div class="card card-modern">
        <div class="card-header bg-white border-bottom p-4">
            <div class="d-flex align-items-center">
                <div class="user-avatar-placeholder me-3" style="width:55px;height:55px;font-size:24px;">
                    {{ substr($selectedUser->nama, 0, 1) }}
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">{{ $selectedUser->nama }}</h5>
                    <div class="text-muted small">
                        Username: <strong>{{ $selectedUser->usr }}</strong> • 
                        ID: {{ $selectedUser->id }}
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <form method="POST" action="{{ url('/it/management-akses/store') }}">
                @csrf
                <input type="hidden" name="user_id" value="{{ request('user_id') }}">

                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0 text-center align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-start ps-4 py-3" style="width: 30%">Menu Aplikasi</th>
                                <th class="py-3" style="width: 8%">Akses</th>
                                <th class="py-3" style="width: 8%">Read</th>
                                <th class="py-3" style="width: 8%">Add</th>
                                <th class="py-3" style="width: 8%">Edit</th>
                                <th class="py-3" style="width: 8%">Delete</th>
                                <th colspan="4" class="py-3">Approval Level (1-4)</th>
                            </tr>
                            <tr>
                                <th class="bg-light"></th>
                                <th class="bg-light"></th>
                                <th class="bg-light"></th>
                                <th class="bg-light"></th>
                                <th class="bg-light"></th>
                                <th class="bg-light"></th>
                                <th class="small text-muted py-1">L1</th>
                                <th class="small text-muted py-1">L2</th>
                                <th class="small text-muted py-1">L3</th>
                                <th class="small text-muted py-1">L4</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $currentGroup = null; @endphp
                            @foreach($menus as $menu)
                                {{-- Group Separator if needed, assuming simple list for now --}}
                                @php $p = $permissions[$menu->id] ?? null; @endphp
                                
                                <tr>
                                    <td class="text-start ps-4">
                                        <div class="fw-bold text-dark">{{ $menu->menu_name }}</div>
                                        <div class="small text-muted" style="font-size:11px">{{ $menu->url }}</div>
                                    </td>

                                    {{-- Access Control --}}
                                    <td>
                                        <input type="hidden" name="permissions[{{ $menu->id }}][can_access]" value="0">
                                        <div class="form-check d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input permission-check border-secondary"
                                                name="permissions[{{ $menu->id }}][can_access]" value="1"
                                                {{ ($p?->can_access ?? 0) ? 'checked' : '' }}>
                                        </div>
                                    </td>

                                    {{-- CRUD Actions --}}
                                    @foreach(['can_read', 'can_add', 'can_edit', 'can_delete'] as $crud)
                                    <td>
                                        <input type="hidden" name="permissions[{{ $menu->id }}][{{ $crud }}]" value="0">
                                        <div class="form-check d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input permission-check"
                                                name="permissions[{{ $menu->id }}][{{ $crud }}]" value="1"
                                                {{ ($p?->$crud ?? 0) ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    @endforeach

                                    {{-- Approvals --}}
                                    @foreach(['can_approve1', 'can_approve2', 'can_approve3', 'can_approve4'] as $appr)
                                    <td style="width:5%">
                                        <input type="hidden" name="permissions[{{ $menu->id }}][{{ $appr }}]" value="0">
                                        <div class="form-check d-flex justify-content-center">
                                            <input type="checkbox" class="form-check-input permission-check"
                                                name="permissions[{{ $menu->id }}][{{ $appr }}]" value="1"
                                                {{ ($p?->$appr ?? 0) ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-white p-4 text-end sticky-bottom" style="bottom:0; z-index: 10; border-top: 1px solid #e2e8f0; box-shadow: 0 -4px 10px rgba(0,0,0,0.02)">
                    <button type="button" class="btn btn-light rounded-pill px-4 me-2" onclick="window.history.back()">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                        <i class="bi bi-save me-2"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
    @endif

</div>
@endsection
