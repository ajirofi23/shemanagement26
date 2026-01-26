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
            font-size: 0.75rem;
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
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
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

        .btn-after {
            background: #f59e0b;
            color: white;
            border: none;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-soft-primary { background: #e0e7ff; color: #4338ca; }
        .badge-soft-success { background-color: #dcfce7; color: #166534; }
        .badge-soft-warning { background-color: #fef3c7; color: #92400e; }
        .badge-soft-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-soft-info { background-color: #e0f2fe; color: #0369a1; }

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
                    <i class="bi bi-shield-shaded me-2 text-primary"></i>Safety Patrol Reporting
                </h3>
                <p class="text-muted small mb-0">Kelola temuan dan perbaikan standar keselamatan kerja di area Anda.</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ url('/pic/safety-patrol/export') }}" class="btn btn-success btn-sm px-3 shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
                </a>
            </div>
        </div>

        {{-- Filter & Search Toolbar --}}
        <div class="card modern-card mb-4">
            <div class="card-body p-3">
                <form action="{{ url('/pic/safety-patrol') }}" method="GET" id="filterForm">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary mb-1">Cari Temuan</label>
                            <div class="input-group input-group-sm shadow-sm">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchInput" name="search" value="{{ request('search') }}" 
                                    class="form-control border-start-0 ps-0" placeholder="">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary mb-1">Lokasi / Area</label>
                            <select name="area" class="form-select form-select-sm shadow-sm" onchange="this.form.submit()">
                                <option value="">Semua Lokasi</option>
                                @foreach($areas ?? ['Produksi', 'Gudang', 'Office'] as $a)
                                    <option value="{{ $a }}" {{ request('area') == $a ? 'selected' : '' }}>{{ $a }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-secondary mb-1">Status</label>
                            <select name="status" class="form-select form-select-sm shadow-sm" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="Progress" {{ request('status') == 'Progress' ? 'selected' : '' }}>Progress</option>
                                <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="Close" {{ request('status') == 'Close' ? 'selected' : '' }}>Close</option>
                            </select>
                        </div>

                        @if(request()->hasAny(['search', 'area', 'status']))
                        <div class="col-md-3 d-flex align-items-end">
                            <a href="{{ url('/pic/safety-patrol') }}" class="btn btn-light btn-sm w-100 border shadow-sm">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reset Filter
                            </a>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Area --}}
        <div class="card modern-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th width="50" class="ps-4 text-center">#</th>
                                <th>Timestamp</th>
                                <th>Lokasi / Area</th>
                                <th>Temuan Masalah</th>
                                <th class="text-center">Due Date</th>
                                <th class="text-center">Evidence</th>
                                <th class="text-center">Status</th>
                                <th width="150" class="pe-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($safetypatrols as $laporan)
                                @php 
                                    $statusBadge = [
                                        'Open' => 'badge-soft-danger',
                                        'Progress' => 'badge-soft-warning',
                                        'Rejected' => 'badge-soft-danger',
                                        'Close' => 'badge-soft-success'
                                    ][$laporan->status] ?? 'badge-soft-secondary';
                                @endphp
                                <tr>
                                    <td class="ps-4 text-center text-muted small fw-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($laporan->tanggal)->format('d M Y') }}</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($laporan->tanggal)->format('H:i') }} - #{{ $laporan->eporte }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                            <span class="fw-bold text-dark">{{ $laporan->area }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-muted small" style="max-width: 200px;" title="{{ $laporan->problem }}">
                                            {{ Str::limit($laporan->problem, 50) }}
                                        </p>
                                    </td>
                                    <td class="text-center">
                                        <span class="small fw-bold {{ \Carbon\Carbon::parse($laporan->due_date)->isPast() && $laporan->status != 'Close' ? 'text-danger' : 'text-muted' }}">
                                            {{ $laporan->due_date ? \Carbon\Carbon::parse($laporan->due_date)->format('d M y') : '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <span class="badge {{ $laporan->foto_before ? 'badge-soft-info' : 'bg-light text-muted' }}" title="Before">B</span>
                                            <span class="badge {{ $laporan->foto_after ? 'badge-soft-success' : 'bg-light text-muted' }}" title="After">A</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $statusBadge }} rounded-pill px-3">
                                            {{ $laporan->status }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            @if(($laporan->status === 'Open' || $laporan->status === 'Rejected'))
                                                <button type="button" class="btn-after shadow-sm"
                                                    data-bs-toggle="modal" data-bs-target="#uploadAfterModal{{ $laporan->id }}">
                                                    <i class="bi bi-camera"></i> After
                                                </button>
                                            @endif

                                            @if($laporan->foto_before || $laporan->foto_after)
                                                <button type="button" class="btn-action text-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewDetailModal{{ $laporan->id }}"
                                                    title="Lihat Visual">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <img src="https://illustrations.popsy.co/slate/empty-folder.svg" style="width: 150px;" class="mb-4">
                                        <h5 class="fw-bold text-secondary">Tidak ada temuan ditemukan</h5>
                                        <p class="text-muted small">Data temuan safety patrol untuk periode ini belum tersedia.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(method_exists($safetypatrols, 'links'))
            <div class="d-flex justify-content-between align-items-center mt-4 px-2">
                <small class="text-muted">Menampilkan {{ $safetypatrols->count() }} data</small>
                <div>{{ $safetypatrols->links() }}</div>
            </div> 
        @endif
    </div>

    <script>
        // Typing Placeholder Effect
        const placeholders = ["Cari nomor E-PORTE...", "Cari Lokasi Area...", "Cari Problem Temuan..."];
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
            } else {
                setTimeout(type, isDeleting ? 50 : 100);
            }
        }
        if(input) type();

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            preview.innerHTML = '';
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.innerHTML = `
                        <div class="col-12 mt-3 bounceIn">
                            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px;">
                                <img src="${e.target.result}" style="height: 200px; object-fit: cover;">
                            </div>
                        </div>`;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection

@section('modals')
    @foreach($safetypatrols as $laporan)
        @if(($laporan->status === 'Open' || $laporan->status === 'Rejected'))
            <div class="modal fade" id="uploadAfterModal{{ $laporan->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                        <div class="modal-header bg-warning text-white border-0" style="border-radius: 15px 15px 0 0;">
                            <h5 class="modal-title fw-bold"><i class="bi bi-camera me-2"></i> Update Perbaikan (After)</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form action="{{ url('/pic/safety-patrol/upload-after/' . $laporan->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="Progress">
                                <div class="row g-4">
                                    <div class="col-md-5">
                                        <div class="p-3 bg-light rounded-3 border">
                                            <h6 class="fw-bold mb-3 text-dark">Detail Temuan</h6>
                                            <div class="mb-2 small"><span class="text-muted d-block">Masalah:</span>
                                                <strong>{{ $laporan->problem }}</strong>
                                            </div>
                                            <div class="small"><span class="text-muted d-block">Due Date:</span>
                                                <strong class="text-danger">{{ $laporan->due_date }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label fw-bold small text-dark">Foto Perbaikan <span class="text-danger">*</span></label>
                                        <input type="file" name="foto_after" class="form-control form-control-sm" accept="image/*"
                                            onchange="previewImage(this, 'fotoAfterPreview_{{ $laporan->id }}')" required>
                                        <div id="fotoAfterPreview_{{ $laporan->id }}" class="row px-2"></div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 px-0 pb-0 mt-4">
                                    <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-warning btn-sm px-4 fw-bold text-white shadow-sm">Kirim Perbaikan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($laporan->foto_before || $laporan->foto_after)
            <div class="modal fade" id="viewDetailModal{{ $laporan->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                        <div class="modal-header bg-dark text-white border-0" style="border-radius: 15px 15px 0 0;">
                            <h5 class="modal-title fw-bold"><i class="bi bi-images me-2"></i> Perbandingan Visual</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6 border-end">
                                    <div class="text-center mb-2"><span class="badge badge-soft-danger rounded-pill px-3">BEFORE</span></div>
                                    <div class="rounded-3 overflow-hidden border shadow-sm" style="height: 250px;">
                                        @if($laporan->foto_before)
                                            <img src="{{ asset('storage/' . $laporan->foto_before) }}" class="w-100 h-100" style="object-fit: cover;">
                                        @else
                                            <div class="h-100 d-flex align-items-center justify-content-center bg-light text-muted">Tidak Ada Foto</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-center mb-2"><span class="badge badge-soft-success rounded-pill px-3">AFTER</span></div>
                                    <div class="rounded-3 overflow-hidden border shadow-sm" style="height: 250px;">
                                        @if($laporan->foto_after)
                                            <img src="{{ asset('storage/' . $laporan->foto_after) }}" class="w-100 h-100" style="object-fit: cover;">
                                        @else
                                            <div class="h-100 d-flex align-items-center justify-content-center bg-light text-muted italic small">Menunggu Perbaikan...</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light-subtle" style="border-radius: 0 0 15px 15px;">
                            <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
