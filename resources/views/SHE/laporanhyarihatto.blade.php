@extends('layout.sidebar')

@section('content')
    <style>
        /* Custom Modern UI Enhancements */
        :root {
            --primary-dark: #0f172a;
            --accent-warning: #f59e0b;
            --soft-bg: #f8fafc;
        }

        .content-wrapper {
            animation: fadeInUp 0.6s ease-out;
            background-color: var(--soft-bg);
            min-height: 100vh;
        }

        .custom-card {
            border: none;
            border-radius: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }

        .custom-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .table-container {
            border-radius: 12px;
            overflow-x: auto;
        }

        .table-container table {
            white-space: nowrap;
        }

        .table thead {
            background: var(--primary-dark);
            color: white;
        }

        .badge-pill {
            border-radius: 50px;
            padding: 5px 12px;
            font-weight: 500;
        }

        .btn-modern {
            border-radius: 10px;
            padding: 8px 20px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-modern:hover {
            filter: brightness(1.1);
            transform: scale(1.02);
        }

        .search-group .form-control {
            border-radius: 10px 0 0 10px;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
        }

        .search-group .btn {
            border-radius: 0 10px 10px 0;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pulse-warning {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            border-radius: 20px 20px 0 0;
        }
    </style>

    <div class="content p-4 content-wrapper">
        {{-- Header Section --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 style="color: var(--primary-dark);" class="fw-bold mb-1">
                    <i class="bi bi-exclamation-triangle-fill text-warning me-2 pulse-warning"></i>
                    Laporan Hyari Hatto
                </h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none text-muted">SHE</a></li>
                        <li class="breadcrumb-item active fw-medium" style="color: var(--accent-warning)">Near Miss</li>
                    </ol>
                </nav>
            </div>
            <div>
                {{-- Jika ada tombol tambah di header, bisa diletakkan di sini --}}
            </div>
        </div>

        <hr class="mb-4 opacity-25">

        <div class="card custom-card shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-light p-2 rounded-3 me-3">
                        <i class="bi bi-list-columns-reverse fs-4 text-primary"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold text-dark">Daftar Laporan Hyari Hatto</h5>
                </div>

                {{-- Search & Export --}}
                <div class="row g-3 justify-content-between align-items-center mb-4">
                    <div class="col-md-6 col-lg-4">
                        <form action="{{ url('/she/laporanhyarihatto') }}" method="GET">
                            <div class="input-group search-group">
                                <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                    class="form-control border-end-0" placeholder="Cari data...">
                                <button type="submit" class="btn btn-warning px-4">
                                    <i class="bi bi-search me-1"></i> Cari
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-auto">
                        <a href="{{ url('/she/hyari-hatto/export/excel') }}"
                            class="btn btn-success btn-modern shadow-sm text-white">
                            <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
                        </a>
                    </div>
                </div>

                {{-- Tabel Hyari Hatto --}}
                <div class="table-responsive table-container border shadow-sm">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark py-3">
                            <tr>
                                <th class="ps-3" style="width: 50px;">No</th>
                                <th style="width: 15%;">Perilaku Tidak Aman</th>
                                <th style="width: 15%;">Kondisi Tidak Aman</th>
                                <th style="width: 10%;">Potensi Bahaya</th>
                                <th style="width: 20%;">Deskripsi</th>
                                <th style="width: 15%;">Rekomendasi P2K3</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hyarihattos as $laporan)
                                <tr>
                                    <td class="ps-3 fw-bold text-secondary">{{ $loop->iteration }}</td>
                                    <td>
                                        @if($laporan->ptas->count() > 0)
                                            @foreach($laporan->ptas as $pta)
                                                <span
                                                    class="badge bg-light text-dark border badge-pill mb-1">{{ $pta->nama_pta }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($laporan->ktas->count() > 0)
                                            @foreach($laporan->ktas as $kta)
                                                <span
                                                    class="badge bg-light text-dark border badge-pill mb-1">{{ $kta->nama_kta }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($laporan->pbs->count() > 0)
                                            @foreach($laporan->pbs as $pb)
                                                <span
                                                    class="badge bg-danger-subtle text-danger border border-danger badge-pill mb-1">{{ $pb->nama_pb }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $laporan->deskripsi }}">
                                            {{ Str::limit($laporan->deskripsi, 50) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 150px;">
                                            {{ $laporan->rekomendasi ? Str::limit($laporan->rekomendasi, 50) : '-' }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            @if(isset($permission) && $permission->can_edit)
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-circle me-1"
                                                    data-bs-toggle="modal" data-bs-target="#editHyariHattoModal{{ $laporan->id }}"
                                                    title="Edit/Lihat Detail">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                            @endif

                                            <a href="{{ url('/she/laporanhyarihatto/download/' . $laporan->id) }}"
                                                class="btn btn-sm btn-outline-success rounded-circle me-1"
                                                title="Download Laporan">
                                                <i class="bi bi-download"></i>
                                            </a>

                                            @if($laporan->bukti)
                                                <a href="{{ asset('storage/' . $laporan->bukti) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-info rounded-circle" title="Download Bukti">
                                                    <i class="bi bi-image"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(method_exists($hyarihattos, 'links'))
                    <div class="mt-4">
                        {{ $hyarihattos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Logic placeholder animasi tetap dipertahankan
        const placeholders = ["Cari Perilaku Tidak Aman", "Cari Data", "Filter"];
        let current = 0;
        let index = 0;
        let isDeleting = false;
        const speed = 100;
        const delay = 1500;
        const input = document.getElementById("searchInput");

        function typePlaceholder() {
            const currentPlaceholder = placeholders[current];
            if (isDeleting) {
                input.placeholder = currentPlaceholder.substring(0, index);
                index--;
                if (index < 0) {
                    isDeleting = false;
                    current = (current + 1) % placeholders.length;
                }
            } else {
                input.placeholder = currentPlaceholder.substring(0, index);
                index++;
                if (index > currentPlaceholder.length) {
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

@endsection

{{---------------------------------------------------------------------------------}}
{{--- SECTION MODALS: Modal untuk Laporan Hyari Hatto --}}
{{---------------------------------------------------------------------------------}}
@section('modals')

    {{-- Modal Edit/Detail Hyari Hatto --}}
    @foreach($hyarihattos as $laporan)
        @if(isset($permission) && $permission->can_edit)
            <div class="modal fade" id="editHyariHattoModal{{ $laporan->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-dark text-white border-0 py-3">
                            <h5 class="modal-title fw-bold">
                                <i class="bi bi-pencil-square me-2 text-warning"></i> Tindak Lanjut Laporan #{{ $laporan->id }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 bg-light">
                            <form action="{{ url('/she/laporanhyarihatto/update/' . $laporan->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row g-4">
                                    {{-- Sisi Kiri: Informasi Laporan --}}
                                    <div class="col-lg-7">
                                        <div class="card border-0 shadow-sm rounded-4 mb-3">
                                            <div class="card-header bg-white border-bottom py-3">
                                                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-info"></i>Detail
                                                    Temuan</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-4">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="small text-muted text-uppercase fw-bold">Perilaku Tidak
                                                            Aman</label>
                                                        <div class="mt-2">
                                                            @php $selectedPtas = $laporan->ptas->pluck('id')->toArray(); @endphp
                                                            @foreach($masterPtas as $pta)
                                                                @if(in_array($pta->id, $selectedPtas))
                                                                    <span
                                                                        class="badge bg-secondary-subtle text-dark border me-1 mb-1">{{ $pta->nama_pta }}</span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="small text-muted text-uppercase fw-bold">Kondisi Tidak
                                                            Aman</label>
                                                        <div class="mt-2">
                                                            @php $selectedKtas = $laporan->ktas->pluck('id')->toArray(); @endphp
                                                            @foreach($masterKtas as $kta)
                                                                @if(in_array($kta->id, $selectedKtas))
                                                                    <span
                                                                        class="badge bg-secondary-subtle text-dark border me-1 mb-1">{{ $kta->nama_kta }}</span>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="small text-muted text-uppercase fw-bold">Deskripsi
                                                        Kejadian</label>
                                                    <div class="p-3 bg-light rounded-3 mt-2 border">
                                                        {{ $laporan->deskripsi }}
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="small text-muted text-uppercase fw-bold">Usulan
                                                        Countermeasure</label>
                                                    <div class="p-3 bg-light rounded-3 mt-2 border">
                                                        {{ $laporan->usulan }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Sisi Kanan: Bukti & Form Edit --}}
                                    <div class="col-lg-5">
                                        {{-- Preview Bukti --}}
                                        <div class="card border-0 shadow-sm rounded-4 mb-4">
                                            <div class="card-body p-2">
                                                @if($laporan->bukti)
                                                    <img src="{{ asset('storage/' . $laporan->bukti) }}"
                                                        class="img-fluid rounded-3 w-100 shadow-sm" alt="Bukti Visual">
                                                @else
                                                    <div class="text-center py-5 bg-light rounded-3">
                                                        <i class="bi bi-image text-muted fs-1"></i>
                                                        <p class="text-muted small mt-2">Tidak ada bukti foto</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Form Tindak Lanjut --}}
                                        <div class="card border-warning shadow-sm rounded-4">
                                            <div class="card-header bg-warning text-dark border-0 py-3">
                                                <h6 class="mb-0 fw-bold"><i class="bi bi-clipboard-check me-2"></i>Rekomendasi P2K3
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                {{-- Hidden Inputs --}}
                                                @foreach($selectedPtas as $pta_id) <input type="hidden" name="pta_id[]"
                                                value="{{ $pta_id }}"> @endforeach
                                                @foreach($selectedKtas as $kta_id) <input type="hidden" name="kta_id[]"
                                                value="{{ $kta_id }}"> @endforeach
                                                @php $selectedPbs = $laporan->pbs->pluck('id')->toArray(); @endphp
                                                @foreach($selectedPbs as $pb_id) <input type="hidden" name="pb_id[]"
                                                value="{{ $pb_id }}"> @endforeach
                                                <input type="hidden" name="deskripsi" value="{{ $laporan->deskripsi }}">
                                                <input type="hidden" name="usulan" value="{{ $laporan->usulan }}">

                                                <div class="mb-0">
                                                    <label for="edit_rekomendasi_{{ $laporan->id }}"
                                                        class="form-label fw-bold small">Masukan Rekomendasi <span
                                                            class="text-danger">*</span></label>
                                                    <textarea name="rekomendasi" id="edit_rekomendasi_{{ $laporan->id }}"
                                                        class="form-control border-warning shadow-sm" rows="5" required
                                                        placeholder="Tuliskan tindakan korektif di sini...">{{ old('rekomendasi', $laporan->rekomendasi) }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer border-0 px-0 mt-3">
                                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">
                                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection