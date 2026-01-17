@extends('layout.picsidebar')

@section('content')

<div class="content p-3">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">
            <i class="bi bi-person-check-fill me-2 text-success"></i>
            Data Komitmen K3
        </h3>
    </div>

    <hr>

    <div class="card shadow border-0">
        <div class="card-body p-3">

            <h5 class="mb-3 text-secondary">
                <i class="bi bi-list-columns-reverse me-1"></i>
                Daftar Komitmen K3 Seluruh Karyawan
                (Section: <strong>{{ $user->section->section ?? 'N/A' }}</strong>)
            </h5>

            {{-- ALERT --}}
            @if(session('sync_message'))
                <div class="alert alert-{{ session('sync_status') }} alert-dismissible fade show">
                    {{ session('sync_message') }}
                    <button class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- TOOLBAR --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2">

                {{-- SYNC --}}
                <form action="{{ url('/pic/komitmenk3/sync') }}" method="POST">
                    @csrf
                    <button class="btn btn-warning"
                        @if(isset($canSync) && !$canSync) disabled @endif>
                        <i class="bi bi-arrow-clockwise"></i> Tarik Data
                    </button>
                </form>

                {{-- FILTER --}}
                <form method="GET" action="{{ url('/pic/komitmenk3') }}" class="d-flex gap-2">
                    <select name="bulan" class="form-select">
                        <option value="">-- Bulan --</option>
                        @for($i=1;$i<=12;$i++)
                            <option value="{{ $i }}" {{ request('bulan')==$i?'selected':'' }}>
                                {{ date('F', mktime(0,0,0,$i,1)) }}
                            </option>
                        @endfor
                    </select>

                    <select name="tahun" class="form-select">
                        <option value="">-- Tahun --</option>
                        @for($y=date('Y');$y>=date('Y')-5;$y--)
                            <option value="{{ $y }}" {{ request('tahun')==$y?'selected':'' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>

                    <button class="btn btn-secondary">
                        <i class="bi bi-funnel"></i>
                    </button>
                </form>

                {{-- SEARCH --}}
                <form method="GET" action="{{ url('/pic/komitmenk3') }}" class="d-flex">
                    <input type="hidden" name="bulan" value="{{ request('bulan') }}">
                    <input type="hidden" name="tahun" value="{{ request('tahun') }}">

                    <input type="text"
                           id="searchInput"
                           name="search"
                           class="form-control me-2"
                           placeholder=""
                           value="{{ request('search') }}">

                    <button class="btn btn-success">
                        <i class="bi bi-search"></i>
                    </button>
                </form>

                {{-- EXPORT --}}
                <a href="{{ url('/pic/komitmenk3/export') }}
                    ?bulan={{ request('bulan') }}
                    &tahun={{ request('tahun') }}
                    &search={{ request('search') }}"
                   class="btn btn-primary">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                </a>

            </div>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Section</th>
                            <th>Departemen</th>
                            <th>Status</th>
                            <th>Komitmen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($komitmens as $data)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $data->user->nama ?? 'N/A' }}</td>
                            <td>{{ $data->user->nip ?? 'N/A' }}</td>
                            <td>{{ $data->user->section->section ?? 'N/A' }}</td>
                            <td>{{ $data->user->section->department ?? 'N/A' }}</td>
                            <td>
                                @if($data->bukti)
                                    <span class="badge bg-success">Sudah Upload</span>
                                @else
                                    <span class="badge bg-danger">Belum Upload</span>
                                @endif
                            </td>
                            <td>
                                {{ Str::limit($data->komitmen ?? 'Data ditarik, menunggu komitmen', 40) }}
                            </td>
                            <td>

                                {{-- EDIT --}}
                                @if($data->user && $data->user->section_id === $user->section_id)
                                <button class="btn btn-sm btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editKomitmenModal"
                                    data-id="{{ $data->id }}"
                                    data-user-id="{{ $data->user_id }}"
                                    data-komitmen="{{ $data->komitmen }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endif

                                {{-- VIEW --}}
                                @if($data->bukti)
                                <button class="btn btn-sm btn-info text-white"
                                    data-bs-toggle="modal"
                                    data-bs-target="#viewBuktiModal{{ $data->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @else
                                    <span class="text-muted small">No Proof</span>
                                @endif

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $komitmens->links() }}

        </div>
    </div>
</div>
@endsection


{{-- ================= MODALS ================= --}}
@section('modals')

{{-- EDIT MODAL --}}
<div class="modal fade" id="editKomitmenModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Edit Komitmen K3</h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="user_id" id="modalUserId">

                    <label class="fw-bold">Komitmen</label>
                    <textarea id="modalKomitmen" name="komitmen"
                        class="form-control mb-3" rows="4" required></textarea>

                    <label class="fw-bold">Upload Bukti (Opsional)</label>
                    <input type="file" name="bukti" class="form-control">
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- VIEW BUKTI --}}
@foreach($komitmens as $data)
@if($data->bukti)
<div class="modal fade" id="viewBuktiModal{{ $data->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Bukti - {{ $data->user->nama }}</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ asset('storage/'.$data->bukti) }}" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>
@endif
@endforeach
@endsection


{{-- ================= SCRIPT ================= --}}
@push('scripts')
<script>
/* EDIT MODAL */
document.getElementById('editKomitmenModal')
?.addEventListener('show.bs.modal', function (e) {

    const btn = e.relatedTarget;
    if (!btn) return;

    const id       = btn.getAttribute('data-id');
    const userId   = btn.getAttribute('data-user-id');
    const komitmen = btn.getAttribute('data-komitmen') || '';

    document.getElementById('editForm').action =
        `/pic/komitmenk3/update/${id}`;

    document.getElementById('modalUserId').value = userId;
    document.getElementById('modalKomitmen').value = komitmen;
});

/* ANIMASI PLACEHOLDER SEARCH */
const placeholders = [
    "Cari Nama Karyawan",
    "Cari NIP",
    "Cari Komitmen K3"
];

let current = 0;
let index = 0;
let isDeleting = false;
const speed = 100;
const delay = 1500;
const input = document.getElementById("searchInput");

function typePlaceholder() {
    if (!input || input.value) return;

    const text = placeholders[current];

    if (isDeleting) {
        input.placeholder = text.substring(0, index--);
        if (index < 0) {
            isDeleting = false;
            current = (current + 1) % placeholders.length;
        }
    } else {
        input.placeholder = text.substring(0, index++);
        if (index > text.length) {
            isDeleting = true;
            setTimeout(typePlaceholder, delay);
            return;
        }
    }
    setTimeout(typePlaceholder, speed);
}

if (input && !input.value) {
    typePlaceholder();
}
</script>
@endpush
