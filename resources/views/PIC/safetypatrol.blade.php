@extends('layout.picsidebar')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #64748b;
            --dark: #0f172a;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --glass: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #f8fafc, #eff6ff);
            color: #1e293b;
            min-height: 100vh;
        }

        .animate-in {
            animation: fadeInSlide 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        @keyframes fadeInSlide {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .bg-blur-dot {
            position: fixed;
            width: 400px;
            height: 400px;
            background: var(--primary);
            filter: blur(150px);
            opacity: 0.05;
            z-index: -1;
            top: -100px;
            right: -100px;
        }

        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .custom-table-wrapper {
            border-radius: 20px;
            background: white;
            padding: 10px;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .table thead th {
            background: transparent;
            color: var(--secondary);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            border: none;
            padding: 15px 20px;
        }

        .table tbody tr {
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            transform: translateY(-3px) scale(1.002);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            background: #f8faff !important;
        }

        .table td {
            border: none !important;
            padding: 18px 20px !important;
        }

        .table td:first-child {
            border-radius: 15px 0 0 15px;
        }

        .table td:last-child {
            border-radius: 0 15px 15px 0;
        }

        .btn-futuristic {
            border-radius: 14px;
            padding: 10px 20px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.4s ease;
            border: none;
        }

        .btn-export {
            background: #000;
            color: #fff;
        }

        .btn-export:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .btn-after {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        /* Filter & Search Styling */
        .filter-input {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 10px 15px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: #fff;
        }

        .filter-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .search-container {
            position: relative;
            flex: 1;
            min-width: 250px;
        }

        .search-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary);
        }

        .search-container input {
            padding-left: 45px !important;
        }

        .status-pill {
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .modal-content {
            border: none;
            border-radius: 35px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
        }
    </style>

    <div class="bg-blur-dot"></div>

    <div class="content p-4 animate-in">
        <div class="row align-items-center mb-5">
            <div class="col-md-7">

                <h1 class="display-5 fw-extrabold text-dark mb-2">Safety Patrol</h1>
                <p class="text-secondary fs-5">Pantau, perbaiki, dan tingkatkan standar keselamatan kerja.</p>
            </div>
            <div class="col-md-5 text-md-end">
                <a href="{{ url('/pic/safety-patrol/export') }}" class="btn btn-futuristic btn-export">
                    <i class="bi bi-box-arrow-up-right"></i> Export Excel
                </a>
            </div>
        </div>

        <div class="card glass-card border-0">
            <div class="card-body p-4">
                <form action="{{ url('/pic/safety-patrol') }}" method="GET" class="mb-4">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <div class="search-container">
                            <i class="bi bi-search"></i>
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                class="form-control filter-input" placeholder="Cari temuan...">
                        </div>

                        <div style="min-width: 180px;">
                            <select name="area" class="form-select filter-input" onchange="this.form.submit()">
                                <option value="">Semua Lokasi/Area</option>
                                @foreach($areas ?? [] as $a) {{-- Asumsi variabel $areas dikirim dari Controller --}}
                                    <option value="{{ $a }}" {{ request('area') == $a ? 'selected' : '' }}>{{ $a }}</option>
                                @endforeach
                                {{-- Jika data area hardcoded: --}}
                                <option value="Produksi" {{ request('area') == 'Produksi' ? 'selected' : '' }}>Produksi
                                </option>
                                <option value="Gudang" {{ request('area') == 'Gudang' ? 'selected' : '' }}>Gudang</option>
                                <option value="Office" {{ request('area') == 'Office' ? 'selected' : '' }}>Office</option>
                            </select>
                        </div>

                        <div style="min-width: 150px;">
                            <select name="status" class="form-select filter-input" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="Progress" {{ request('status') == 'Progress' ? 'selected' : '' }}>Progress
                                </option>
                                <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected
                                </option>
                                <option value="Close" {{ request('status') == 'Close' ? 'selected' : '' }}>Close</option>
                            </select>
                        </div>

                        @if(request('search') || request('area') || request('status'))
                            <a href="{{ url('/pic/safety-patrol') }}" class="btn btn-light rounded-3 text-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        @endif
                    </div>
                </form>

                <div class="custom-table-wrapper">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Timestamp</th>
                                    <th>Identitas</th>
                                    <th>Lokasi / Area</th>
                                    <th>Temuan Masalah</th>
                                    <th class="text-center">DueDate</th>
                                    <th class="text-center">Visual</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($safetypatrols as $laporan)
                                    @php $statusBadge = $statusColors[$laporan->status] ?? 'secondary'; @endphp
                                    <tr>
                                        <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span
                                                    class="text-dark fw-bold">{{ \Carbon\Carbon::parse($laporan->tanggal)->format('d/m/y') }}</span>
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($laporan->tanggal)->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="badge bg-dark mb-1"
                                                    style="width: fit-content;">{{ $laporan->eporte }}</span>
                                                <small
                                                    class="text-primary fw-bold">{{ $laporan->section->section ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="p-2 bg-light rounded-3 me-2">
                                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                                </div>
                                                <span class="fw-bold">{{ $laporan->area }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 150px; cursor: help;"
                                                title="{{ $laporan->problem }}">
                                                {{ $laporan->problem }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="fw-bold {{ \Carbon\Carbon::parse($laporan->due_date)->isPast() && $laporan->status != 'Close' ? 'text-danger' : 'text-muted' }}">
                                                {{ $laporan->due_date ? \Carbon\Carbon::parse($laporan->due_date)->format('d M') : '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <span
                                                    class="badge {{ $laporan->foto_before ? 'bg-info' : 'bg-light text-muted' }}"
                                                    title="Before">B</span>
                                                <span
                                                    class="badge {{ $laporan->foto_after ? 'bg-success' : 'bg-light text-muted' }}"
                                                    title="After">A</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="status-pill bg-{{ $statusBadge }} bg-opacity-10 text-{{ $statusBadge }}">
                                                ● {{ $laporan->status }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                @if(($laporan->status === 'Open' || $laporan->status === 'Rejected'))
                                                    <button type="button" class="btn btn-sm btn-after btn-futuristic"
                                                        data-bs-toggle="modal" data-bs-target="#uploadAfterModal{{ $laporan->id }}">
                                                        <i class="bi bi-camera-fill"></i> After
                                                    </button>
                                                @endif

                                                @if($laporan->foto_before || $laporan->foto_after)
                                                    <button type="button" class="btn btn-sm btn-outline-dark shadow-sm"
                                                        style="border-radius: 10px;" data-bs-toggle="modal"
                                                        data-bs-target="#viewDetailModal{{ $laporan->id }}">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">Data tidak ditemukan dengan filter
                                            tersebut.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
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
        type();

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            preview.innerHTML = '';
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.innerHTML = `
                                <div class="col-12 mt-3 animate-in">
                                    <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                                        <img src="${e.target.result}" style="height: 250px; object-fit: cover;">
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
        {{-- Kode Modals Tetap Sama Seperti Versi Sebelumnya --}}
        @if(($laporan->status === 'Open' || $laporan->status === 'Rejected'))
            <div class="modal fade" id="uploadAfterModal{{ $laporan->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header border-0 p-4">
                            <div>
                                <h3 class="fw-extrabold text-dark mb-0">Update Perbaikan</h3>
                                <p class="text-muted mb-0 small">ID: #{{ $laporan->eporte }}</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 pt-0">
                            <form action="{{ url('/pic/safety-patrol/upload-after/' . $laporan->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="Progress">
                                <div class="row g-4">
                                    <div class="col-md-5">
                                        <div class="p-4 bg-light rounded-4 border-dashed border-2">
                                            <h6 class="fw-bold mb-3">Detail Temuan</h6>
                                            <div class="mb-2"><small class="text-muted d-block">Masalah:</small>
                                                <strong>{{ $laporan->problem }}</strong>
                                            </div>
                                            <div class="mb-3"><small class="text-muted d-block">Target:</small>
                                                <strong>{{ $laporan->due_date }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <label class="form-label fw-bold">Foto Perbaikan <span class="text-danger">*</span></label>
                                        <input type="file" name="foto_after" class="form-control filter-input" accept="image/*"
                                            onchange="previewImage(this, 'fotoAfterPreview_{{ $laporan->id }}')" required>
                                        <div id="fotoAfterPreview_{{ $laporan->id }}" class="row px-2"></div>
                                    </div>
                                </div>
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-lg"
                                        style="border-radius: 12px;">Kirim Solusi</button>
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
                    <div class="modal-content overflow-hidden">
                        <div class="modal-header border-0 p-4">
                            <h4 class="fw-extrabold">Visual Evidence Comparison</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 pt-0">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="text-center mb-2"><span
                                            class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">BEFORE</span></div>
                                    <div class="rounded-4 overflow-hidden border" style="height: 300px;">
                                        @if($laporan->foto_before)
                                            <img src="{{ asset('storage/' . $laporan->foto_before) }}" class="w-100 h-100"
                                                style="object-fit: cover;">
                                        @else
                                            <div class="h-100 d-flex align-items-center justify-content-center bg-light text-muted">No
                                                Image</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-center mb-2"><span
                                            class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">AFTER</span></div>
                                    <div class="rounded-4 overflow-hidden border shadow-sm" style="height: 300px;">
                                        @if($laporan->foto_after)
                                            <img src="{{ asset('storage/' . $laporan->foto_after) }}" class="w-100 h-100"
                                                style="object-fit: cover;">
                                        @else
                                            <div
                                                class="h-100 d-flex align-items-center justify-content-center bg-light text-muted italic">
                                                Waiting for update...</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection