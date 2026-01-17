@extends('layout.sidebar')

@section('content')

    {{-- Custom CSS untuk UI Modern & Animasi --}}
    <style>
        /* Animasi Masuk Halaman */
        .page-entry {
            animation: fadeInBlur 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeInBlur {
            from {
                opacity: 0;
                filter: blur(10px);
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                filter: blur(0);
                transform: translateY(0);
            }
        }

        /* Card & Table Styling */
        .modern-card {
            border: none;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important;
        }

        .table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1.25rem 1rem;
            border: none;
        }

        .table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f1f5f9;
        }

        .table tbody tr:hover {
            background-color: #f8fbff !important;
            transform: scale(1.002);
        }

        /* Tombol & Input Custom */
        .btn-modern {
            border-radius: 10px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .search-container {
            position: relative;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 4px;
            transition: all 0.3s;
        }

        .search-container:focus-within {
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .search-container input {
            background: transparent !important;
            border: none !important;
        }

        .search-container input:focus {
            box-shadow: none !important;
        }

        /* Status Badge ID */
        .id-badge {
            background: #e0e7ff;
            color: #4338ca;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.85rem;
        }
    </style>

    <div class="content p-4 page-entry">

        {{-- Header Halaman --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h3 style="color:#0f172a;" class="fw-bold mb-1">
                    <i class="bi bi-shield-lock-fill me-2 text-primary"></i> Data Master: Pelanggaran Fisik (PF)
                </h3>
                <p class="text-muted small mb-0">Manajemen daftar jenis pelanggaran pemeriksaan fisik dalam sistem SHE.</p>
            </div>

            <button type="button" class="btn btn-primary btn-modern shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addPfModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Data Baru
            </button>
        </div>

        <div class="card modern-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                        <i class="bi bi-journal-text text-primary fs-4"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold text-dark">Daftar Pelanggaran Fisik</h5>
                </div>

                {{-- Search & Export --}}
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-8">
                        <form action="{{ url('/she/master/pf') }}" method="GET">
                            <div class="search-container d-flex align-items-center">
                                <span class="ms-3 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                    class="form-control" placeholder="">
                                <button type="submit" class="btn btn-primary btn-modern px-4 py-2">Cari</button>
                            </div>
                        </form>
                    </div>
                    <!-- <div class="col-12 col-md-4 text-md-end d-flex align-items-center justify-content-md-end">
                        <a href="{{ url('/she/master/pf/export') }}"
                            class="btn btn-outline-success btn-modern w-100 w-md-auto">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                        </a>
                    </div> -->
                </div>

                {{-- Tabel PF --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 80px;">No</th>
                                <th>Nama Pelanggaran Fisik</th>
                                <th class="text-center" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pfs as $pf)
                                <tr>
                                    <td class="text-center">
                                        <span class="id-badge">{{ $loop->iteration }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $pf->nama_pf }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            @if(isset($permission) && $permission->can_edit)
                                                <button type="button" class="btn btn-sm btn-outline-primary border-0"
                                                    data-bs-toggle="modal" data-bs-target="#editPfModal{{ $pf->id }}"
                                                    title="Edit Data" style="border-radius: 8px; padding: 6px 10px;">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            @endif

                                            @if(isset($permission) && $permission->can_delete)
                                                <form action="{{ url('/she/master/pf/destroy/' . $pf->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0"
                                                        onclick="return confirm('Yakin hapus data: {{ $pf->nama_pf }}?')"
                                                        title="Hapus Data" style="border-radius: 8px; padding: 6px 10px;">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paging --}}
                @if(method_exists($pfs, 'links'))
                    <div class="mt-4">
                        {{ $pfs->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Animasi Typing Script --}}
    <script>
        const placeholders = ["Cari Pelanggaran Fisik...", "Cth: Tanda Tangan...", "Cari Nama Pelanggaran..."];
        let current = 0; let index = 0; let isDeleting = false;
        const speed = 80; const delay = 2000;
        const input = document.getElementById("searchInput");

        function typePlaceholder() {
            const currentPlaceholder = placeholders[current];
            if (isDeleting) {
                input.placeholder = currentPlaceholder.substring(0, index);
                index--;
                if (index < 0) { isDeleting = false; current = (current + 1) % placeholders.length; }
            } else {
                input.placeholder = currentPlaceholder.substring(0, index);
                index++;
                if (index > currentPlaceholder.length) { isDeleting = true; setTimeout(typePlaceholder, delay); return; }
            }
            setTimeout(typePlaceholder, isDeleting ? 40 : speed);
        }

        if (!input.value) typePlaceholder();
    </script>

@endsection

@section('modals')

    <style>
        .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }

        .modal-header {
            border-bottom: none;
            padding: 1.5rem 1.5rem 0.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: none;
            padding: 0.5rem 1.5rem 1.5rem;
        }

        .custom-input:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            border-color: #2563eb;
        }
    </style>

    {{-- Modal Tambah PF --}}
    <div class="modal fade" id="addPfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill text-primary me-2"></i> Tambah
                        Pelanggaran Fisik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/she/master/pf/store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nama_pf" class="form-label fw-bold small">Nama Pelanggaran Fisik <span
                                    class="text-danger">*</span></label>
                            <textarea name="nama_pf" id="nama_pf" class="form-control custom-input" rows="4" required
                                placeholder="Masukkan keterangan pelanggaran..."></textarea>
                            <small class="form-text text-muted">Contoh: Deskripsi kesalahan fisik dokumen atau
                                personil.</small>
                        </div>

                        <div class="modal-footer px-0 pb-0 mt-3 d-flex gap-2">
                            <button type="button" class="btn btn-light btn-modern flex-grow-1"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-modern flex-grow-2"><i
                                    class="bi bi-save me-1"></i> Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit PF --}}
    @foreach($pfs as $pf)
        @if(isset($permission) && $permission->can_edit)
            <div class="modal fade" id="editPfModal{{ $pf->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow-lg">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i> Edit Data</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ url('/she/master/pf/update/' . $pf->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="edit_nama_pf{{ $pf->id }}" class="form-label fw-bold small">Nama Pelanggaran Fisik
                                        <span class="text-danger">*</span></label>
                                    <textarea name="nama_pf" id="edit_nama_pf{{ $pf->id }}" class="form-control custom-input"
                                        rows="4" required>{{ $pf->nama_pf }}</textarea>
                                </div>

                                <div class="modal-footer px-0 pb-0 mt-3 d-flex gap-2">
                                    <button type="button" class="btn btn-light btn-modern flex-grow-1"
                                        data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary btn-modern flex-grow-2"><i
                                            class="bi bi-check-circle me-1"></i> Update Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

@endsection