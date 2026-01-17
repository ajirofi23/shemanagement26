@extends('layout.sidebar')

@section('content')

    {{-- Kustomisasi CSS Modern --}}
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0f172a 0%, #2563eb 100%);
        }

        .content-wrapper {
            animation: slideInUp 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
        }

        /* Card Styling */
        .custom-card {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
            overflow: hidden;
        }

        /* Table Styling */
        .table thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            padding: 1.25rem 1rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8fbff !important;
            transform: scale(1.002);
        }

        /* Buttons Modern */
        .btn {
            border-radius: 12px;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-action {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 10px;
            background: #f1f5f9;
            color: #475569;
            border: none;
        }

        .btn-action:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* Search Input Modern */
        .search-wrapper {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 4px;
            transition: all 0.3s ease;
        }

        .search-wrapper:focus-within {
            background: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .search-wrapper input {
            background: transparent;
            border: none;
            padding: 0.6rem 1rem;
        }

        .search-wrapper input:focus {
            box-shadow: none;
            background: transparent;
        }

        /* Badge ID */
        .badge-no {
            background: #e0e7ff;
            color: #4338ca;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.8rem;
        }

        /* Animations */
        @keyframes slideInUp {
            0% {
                transform: translateY(30px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .empty-state i {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }
    </style>

    <div class="content p-4 content-wrapper">

        {{-- Header Halaman --}}
        <div class="row align-items-center mb-4 g-3">
            <div class="col-md-auto col-12">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3">
                        <i class="bi bi-database-fill-gear fs-3 text-primary"></i>
                    </div>
                    <div>
                        <h3 style="color:#0f172a;" class="fw-bold mb-0">Data Master</h3>
                        <p class="text-muted small mb-0">Manajemen Potensi Bahaya (PB)</p>
                    </div>
                </div>
            </div>

            <div class="col-md col-12 text-md-end">
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addPbModal">
                    <i class="bi bi-plus-lg me-2"></i> Tambah Data Baru
                </button>
            </div>
        </div>

        <div class="card custom-card">
            <div class="card-body p-4">

                {{-- Toolbar --}}
                <div class="row g-3 mb-4 justify-content-between">
                    <div class="col-12 col-lg-5">
                        <form action="{{ url('/she/master/pb') }}" method="GET">
                            <div class="search-wrapper d-flex align-items-center">
                                <i class="bi bi-search ms-3 text-muted"></i>
                                <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                    class="form-control" placeholder="">
                                <button type="submit" class="btn btn-primary px-4 ms-2">Cari</button>
                            </div>
                        </form>
                    </div>
                    <!-- <div class="col-12 col-lg-auto text-end">
                        <a href="{{ url('/she/master/pb/export') }}" class="btn btn-outline-success border-2">
                            <i class="bi bi-file-earmark-spreadsheet-fill me-2"></i>
                        </a>
                    </div> -->
                </div>

                {{-- Tabel PB --}}
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 80px;">No</th>
                                <th>Deskripsi Potensi Bahaya</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pbs as $pb)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge-no">{{ $loop->iteration }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="text-dark fw-bold">{{ $pb->nama_pb }}</div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            @if(isset($permission) && $permission->can_edit)
                                                <button type="button" class="btn-action shadow-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editPbModal{{ $pb->id }}" title="Edit">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            @endif

                                            @if(isset($permission) && $permission->can_delete)
                                                <form action="{{ url('/she/master/pb/destroy/' . $pb->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action text-danger shadow-sm"
                                                        onclick="return confirm('Yakin ingin menghapus data ini?')" title="Hapus">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="empty-state text-muted">
                                            <i class="bi bi-database-exclamation fs-1 d-block mb-3 text-primary opacity-25"></i>
                                            <h6 class="fw-bold">Data tidak ditemukan</h6>
                                            <p class="small">Belum ada daftar potensi bahaya yang tersimpan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paging --}}
                @if(method_exists($pbs, 'links'))
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $pbs->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    <script>
        // Typing Placeholder Logic (Struktur tetap sama)
        const placeholders = ["Kabel terkelupas...", "Lantai licin...", "Plafon retak...", "Cari Potensi Bahaya"];
        let current = 0; let index = 0; let isDeleting = false;
        const input = document.getElementById("searchInput");

        function typePlaceholder() {
            if (!input) return;
            const currentPlaceholder = placeholders[current];
            if (isDeleting) {
                input.placeholder = currentPlaceholder.substring(0, index);
                index--;
                if (index < 0) { isDeleting = false; current = (current + 1) % placeholders.length; }
            } else {
                input.placeholder = currentPlaceholder.substring(0, index);
                index++;
                if (index > currentPlaceholder.length) { isDeleting = true; setTimeout(typePlaceholder, 2000); return; }
            }
            setTimeout(typePlaceholder, isDeleting ? 40 : 80);
        }
        if (input && !input.value) typePlaceholder();
    </script>

@endsection

@section('modals')

    <style>
        .modal-content {
            border-radius: 24px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            border-bottom: none;
            padding: 2rem 2rem 1rem;
        }

        .modal-footer {
            border-top: none;
            padding: 1rem 2rem 2rem;
        }

        .modal-body {
            padding: 1rem 2rem;
        }
    </style>

    {{-- Modal Tambah PB --}}
    <div class="modal fade" id="addPbModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-2xl">
                <div class="modal-header d-block">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h4 class="modal-title fw-bold text-dark">Tambah Data</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <p class="text-muted small mb-0">Masukkan detail potensi bahaya baru di bawah ini.</p>
                </div>
                <form action="{{ url('/she/master/pb/store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_pb" class="form-label fw-bold text-dark">Deskripsi Potensi Bahaya</label>
                            <textarea name="nama_pb" id="nama_pb" class="form-control bg-light border-0" rows="4" required
                                placeholder="Contoh: Pencahayaan kurang di lorong gudang A..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer d-flex gap-2">
                        <button type="button" class="btn btn-light flex-grow-1" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary flex-grow-1 px-4">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit PB --}}
    @foreach($pbs as $pb)
        @if(isset($permission) && $permission->can_edit)
            <div class="modal fade" id="editPbModal{{ $pb->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-2xl">
                        <div class="modal-header d-block">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h4 class="modal-title fw-bold text-dark">Edit Data</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <p class="text-muted small mb-0">Ubah deskripsi potensi bahaya yang telah dipilih.</p>
                        </div>
                        <form action="{{ url('/she/master/pb/update/' . $pb->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="edit_nama_pb{{ $pb->id }}" class="form-label fw-bold text-dark">Deskripsi Potensi
                                        Bahaya</label>
                                    <textarea name="nama_pb" id="edit_nama_pb{{ $pb->id }}" class="form-control bg-light border-0"
                                        rows="4" required>{{ $pb->nama_pb }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer d-flex gap-2">
                                <button type="button" class="btn btn-light flex-grow-1" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary flex-grow-1 px-4">Update Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

@endsection