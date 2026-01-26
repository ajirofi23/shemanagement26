@extends('layout.picsidebar')

@section('content')

    <style>
        :root {
            --primary-bold: #4f46e5;
            --secondary-text: #64748b;
        }

        .content {
            background: #f8fafc;
            min-height: 100vh;
            animation: fadeIn 0.6s ease-out;
        }

        .modern-card {
            border-radius: 12px;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .modern-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
        }

        /* Table Improvements */
        .modern-table thead th {
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 0.05em;
            font-weight: 700;
            padding: 12px 15px;
            vertical-align: middle;
        }

        .modern-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.2s;
        }

        .modern-table tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Action Buttons */
        .btn-action {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .btn-action:hover {
            opacity: 0.8;
            transform: translateY(-1px);
            background: #f8fafc;
        }

        .badge-soft-primary {
            background: #e0e7ff;
            color: #4338ca;
        }

        .badge-soft-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-soft-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="content p-4">
        {{-- Header Halaman --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 style="color:#0f172a;" class="fw-bold m-0">
                    <i class="bi bi-shield-check me-2 text-success"></i>Data Komitmen K3
                </h3>
                <p class="text-muted small mb-0">Kelola dan pantau kepatuhan komitmen keselamatan kerja karyawan di section <strong>{{ $user->section->section ?? 'N/A' }}</strong>.</p>
            </div>

            <div class="d-flex gap-2">
                @if(isset($isUploaded) && $isUploaded)
                    <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#editKomitmenK3Modal{{ $userKomitmen->id ?? $user->id }}">
                        <i class="bi bi-pencil-square me-1"></i> Edit Komitmen Saya
                    </button>
                @endif
                <a href="{{ url('/pic/komitmenk3/export') }}?bulan={{ request('bulan', date('n')) }}&tahun={{ request('tahun', date('Y')) }}" class="btn btn-success btn-sm px-3 shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
                </a>
            </div>
        </div>

        {{-- Filter & Sync Toolbar --}}
        <div class="card modern-card mb-4">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-auto">
                        <form action="{{ url('/pic/komitmenk3/sync') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm fw-bold shadow-sm px-3" 
                                @if(isset($canSync) && !$canSync) disabled @endif
                                title="{{ (isset($canSync) && !$canSync) ? 'Sudah disinkronkan bulan ini' : 'Tarik data karyawan terbaru' }}">
                                <i class="bi bi-arrow-clockwise me-1"></i> Tarik Data Karyawan
                            </button>
                            @if(isset($lastSyncDate))
                                <div class="text-muted mt-1" style="font-size: 0.65rem;">Terakhir: {{ $lastSyncDate }}</div>
                            @endif
                        </form>
                    </div>

                    <div class="col-md-auto">
                        <form action="{{ url('/pic/komitmenk3') }}" method="GET" id="filterForm" class="d-flex gap-2 align-items-end">
                            <div style="min-width: 130px;">
                                <label class="form-label small fw-bold text-secondary mb-1">Bulan</label>
                                <select name="bulan" class="form-select form-select-sm shadow-sm" onchange="this.form.submit()">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ request('bulan', date('n')) == $m ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div style="min-width: 100px;">
                                <label class="form-label small fw-bold text-secondary mb-1">Tahun</label>
                                <select name="tahun" class="form-select form-select-sm shadow-sm" onchange="this.form.submit()">
                                    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                                        <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </form>
                    </div>

                    <div class="col-md">
                        <form action="{{ url()->current() }}" method="GET">
                            <input type="hidden" name="bulan" value="{{ request('bulan', date('n')) }}">
                            <input type="hidden" name="tahun" value="{{ request('tahun', date('Y')) }}">
                            <label class="form-label small fw-bold text-secondary mb-1">Cari Karyawan / NIP / Status</label>
                            <div class="input-group input-group-sm shadow-sm">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchInput" name="search" value="{{ request('search') }}" 
                                    class="form-control border-start-0 ps-0" placeholder="">
                                <button type="submit" class="btn btn-primary px-3">Cari</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(session('sync_message'))
            <div class="alert alert-{{ session('sync_status') ?? 'success' }} alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> {{ session('sync_message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Table Area --}}
        <div class="card modern-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th width="60px" class="ps-4">No</th>
                                <th>Karyawan</th>
                                <th>NIP</th>
                                <th>Status</th>
                                <th>Rangkuman Komitmen</th>
                                <th width="120px" class="pe-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($komitmens as $data)
                                <tr>
                                    <td class="ps-4 text-muted small fw-bold">#{{ ($komitmens->currentPage()-1) * $komitmens->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $data->user->nama ?? 'N/A' }}</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ $data->user->section->section ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <code class="text-primary fw-bold" style="background:#f1f5f9; padding:2px 6px; border-radius:4px;">{{ $data->user->kode_user ?? 'N/A' }}</code>
                                    </td>
                                    <td>
                                        @if($data->bukti)
                                            <span class="badge badge-soft-success rounded-pill px-3">
                                                <i class="bi bi-check-circle-fill me-1"></i> Terunggah
                                            </span>
                                        @else
                                            <span class="badge badge-soft-danger rounded-pill px-3">
                                                <i class="bi bi-clock-history me-1"></i> Belum
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <p class="mb-0 text-muted small italic" title="{{ $data->komitmen }}">
                                            {{ Str::limit($data->komitmen ?? 'Menunggu input komitmen...', 60) }}
                                        </p>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn-action text-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editKomitmenK3Modal"
                                                    data-id="{{ $data->id }}"
                                                    data-user-id="{{ $data->user_id }}"
                                                    data-komitmen="{{ $data->komitmen }}"
                                                    title="Edit Komitmen">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                            @if($data->bukti)
                                                <button type="button" class="btn-action text-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewBuktiModal{{ $data->id }}"
                                                    title="Lihat Detail">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            @else
                                                <button class="btn-action text-muted opacity-50" disabled>
                                                    <i class="bi bi-eye-slash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <img src="https://illustrations.popsy.co/slate/empty-folder.svg" style="width: 150px;" class="mb-4">
                                        <h5 class="fw-bold text-secondary">Tidak ada data ditemukan</h5>
                                        <p class="text-muted small">Belum ada data komitmen K3 untuk periode ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($komitmens->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4 px-2">
                <small class="text-muted">Menampilkan {{ $komitmens->firstItem() }} s/d {{ $komitmens->lastItem() }} dari {{ $komitmens->total() }} data</small>
                <div>{{ $komitmens->links() }}</div>
            </div> 
        @endif
    </div>

@endsection

@section('modals')
    {{-- Modal Update (Internal) --}}
    @php
        $targetKomitmen = $userKomitmen ?? (object)['id' => $user->id, 'komitmen' => '', 'bukti' => null, 'user_id' => $user->id];
    @endphp

    <div class="modal fade" id="editKomitmenK3Modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> 
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-success text-white border-0" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> Update Komitmen</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ url('/pic/komitmenk3/' . ($userKomitmen ? 'update/' . $userKomitmen->id : 'store')) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($userKomitmen) @method('PUT') @endif
                        
                        <input type="hidden" name="user_id" value="{{ $user->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Isi Komitmen K3 <span class="text-danger">*</span></label>
                            <textarea name="komitmen" id="komitmen" class="form-control border-light-subtle bg-light" rows="4" required placeholder="Contoh: Saya berkomitmen menggunakan APD lengkap setiap saat...">{{ old('komitmen', $targetKomitmen->komitmen) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Bukti Komitmen (Gambar) <span class="text-danger">*</span></label>
                            @if($targetKomitmen->bukti)
                                <div class="mb-3 p-2 bg-light rounded text-center">
                                    <img src="{{ asset('storage/' . $targetKomitmen->bukti) }}" class="img-thumbnail shadow-sm" style="max-height: 120px;">
                                </div>
                            @endif
                            <input type="file" name="bukti" class="form-control" accept="image/*" {{ $targetKomitmen->bukti ? '' : 'required' }}>
                        </div>
                        
                        <div class="modal-footer border-0 px-0 pb-0 mt-4">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success px-4 fw-bold text-white shadow-sm">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @foreach($komitmens as $data)
        @if($data->bukti)
            <div class="modal fade" id="viewBuktiModal{{ $data->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg"> 
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                        <div class="modal-header bg-dark text-white border-0" style="border-radius: 15px 15px 0 0;">
                            <h5 class="modal-title"><i class="bi bi-image me-2"></i> Bukti: {{ $data->user->nama ?? 'N/A' }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-0 text-center bg-light">
                            <img src="{{ asset('storage/' . $data->bukti) }}" class="img-fluid w-100">
                            <div class="p-4 bg-white text-start">
                                <span class="badge bg-primary mb-2">Pesan Komitmen</span>
                                <p class="text-dark fs-5 italic">"{{ $data->komitmen }}"</p>
                                <hr class="opacity-10">
                                <div class="small text-muted"><i class="bi bi-clock me-1"></i> Diunggah pada: {{ $data->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <a href="{{ asset('storage/' . $data->bukti) }}" target="_blank" class="btn btn-outline-dark btn-sm"><i class="bi bi-download me-1"></i> Download</a>
                            <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-modal="hide" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Typing Placeholder Effect
            const placeholders = ["Cari Nama...", "Cari NIP...", "Cari Status...", "Cari Rangkuman..."];
            let current = 0, index = 0, isDeleting = false;
            const input = document.getElementById("searchInput");

            function type() {
                let currentText = placeholders[current];
                if (isDeleting) {
                    input.placeholder = currentText.substring(0, index--);
                } else {
                    input.placeholder = currentText.substring(0, index++);
                }

                if (!isDeleting && index === currentText.length) {
                    isDeleting = true;
                    setTimeout(type, 2000);
                } else if (isDeleting && index === 0) {
                    isDeleting = false;
                    current = (current + 1) % placeholders.length;
                    setTimeout(type, 500);
                } else if (input) {
                    setTimeout(type, isDeleting ? 50 : 100);
                }
            }
            if(input) type();

            const editModal = document.getElementById('editKomitmenK3Modal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (button.tagName === 'BUTTON' && button.hasAttribute('data-bs-target') && button.getAttribute('data-bs-target') !== '#editKomitmenK3Modal') return;

                    const id = button.getAttribute('data-id');
                    const userId = button.getAttribute('data-user-id');
                    const komitmen = button.getAttribute('data-komitmen') || '';

                    if (id && userId) {
                        const form = editModal.querySelector('form');
                        form.action = `/pic/komitmenk3/update/${id}`;
                        form.querySelector('input[name="user_id"]').value = userId;
                        form.querySelector('textarea[name="komitmen"]').value = komitmen;
                    }
                });
            }
        });
    </script>
@endsection
