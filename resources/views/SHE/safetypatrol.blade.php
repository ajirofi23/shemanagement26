@extends('layout.sidebar')

@section('content')
    @php
        $statusColors = [
            'Open' => 'danger',
            'Progress' => 'warning',
            'Close' => 'success',
            'Rejected' => 'secondary'
        ];
    @endphp

    <style>
        :root {
            --primary-bold: #4f46e5;
            --secondary-text: #64748b;
        }

        .content {
            background-color: #f8fafc;
            min-height: 100vh;
        }

        .modern-card {
            border-radius: 12px;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
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
            border: none;
        }

        .btn-action:hover {
            opacity: 0.8;
            transform: translateY(-1px);
        }

        .badge-pill-modern {
            padding: 0.4em 0.8em;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.7rem;
            display: inline-block;
        }

        .bg-soft-info {
            background-color: #e0f2fe;
            color: #075985;
        }

        .bg-soft-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .bg-soft-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .bg-soft-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .bg-soft-secondary {
            background-color: #f1f5f9;
            color: #475569;
        }
    </style>

    <div class="content p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 style="color:#0f172a;" class="fw-bold m-0">
                    <i class="bi bi-shield-check me-2 text-primary"></i> Laporan Safety Patrol
                </h3>
                <p class="text-muted small mb-0">Manajemen temuan dan tindak lanjut patroli keselamatan kerja.</p>
            </div>

            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#addSafetyPatrolModal">
                <i class="bi bi-plus-circle me-1"></i> Buat Laporan
            </button>
        </div>

        <div class="card modern-card mb-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-search me-2 text-primary"></i> Filter & Pencarian
                    </h6>
                    <a href="{{ url('/she/safety-patrol/export') }}" class="btn btn-success btn-sm px-3">
                        <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
                    </a>
                </div>
                <form action="{{ url('/she/safety-patrol') }}" method="GET" class="row g-2">
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light text-muted border-end-0"><i
                                    class="bi bi-search"></i></span>
                            <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                                class="form-control border-start-0" placeholder="Cari E-PORTE atau Area...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Cari Data</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card modern-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4" style="width: 50px;">No</th>
                                <th style="width: 100px;">Tanggal</th>
                                <th style="width: 100px;">E-PORTE</th>
                                <th>Area & Problem</th>
                                <th style="width: 100px;">Section</th>
                                <th style="width: 100px;">Due Date</th>
                                <th class="text-center" style="width: 80px;">Foto</th>
                                <th class="text-center" style="width: 80px;">Status</th>
                                <th class="pe-4 text-end" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($safetypatrols as $laporan)
                                @php
                                    $statusBadge = $statusColors[$laporan->status] ?? 'secondary';
                                @endphp
                                <tr>
                                    <td class="ps-4 text-muted small fw-bold">#{{ $loop->iteration }}</td>
                                    <td class="small">{{ \Carbon\Carbon::parse($laporan->tanggal)->format('d/m/Y') }}</td>
                                    <td class="fw-bold small">{{ $laporan->eporte }}</td>
                                    <td>
                                        <div class="fw-bold text-dark small" title="{{ $laporan->area }}">
                                            {{ Str::limit($laporan->area, 30) }}</div>
                                        <div class="text-muted small" style="font-size: 0.7rem;"
                                            title="{{ $laporan->problem }}">{{ Str::limit($laporan->problem, 40) }}</div>
                                    </td>
                                    <td class="small">{{ $laporan->section->section ?? 'N/A' }}</td>
                                    <td class="small fw-semibold text-primary">
                                        {{ $laporan->due_date ? \Carbon\Carbon::parse($laporan->due_date)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            @if($laporan->foto_before) <i class="bi bi-image text-primary" title="Before"></i>
                                            @endif
                                            @if($laporan->foto_after) <i class="bi bi-image-fill text-success"
                                            title="After"></i> @endif
                                            @if(!$laporan->foto_before && !$laporan->foto_after) <span
                                            class="text-muted">-</span> @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = [
                                                'Open' => 'bg-soft-danger',
                                                'Progress' => 'bg-soft-warning',
                                                'Close' => 'bg-soft-success',
                                                'Rejected' => 'bg-soft-secondary'
                                            ][$laporan->status] ?? 'bg-soft-secondary';
                                        @endphp
                                        <span class="badge-pill-modern {{ $badgeClass }}">{{ $laporan->status }}</span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            @if(isset($permission) && $permission->can_edit && ($laporan->status === 'Progress' || $laporan->status === 'Open'))
                                                <button type="button" class="btn-action bg-soft-success text-success"
                                                    data-bs-toggle="modal" data-bs-target="#tindakLanjutModal{{ $laporan->id }}"
                                                    title="Tindak Lanjut">
                                                    <i class="bi bi-check2-circle"></i>
                                                </button>
                                            @endif

                                            @if(isset($permission) && $permission->can_edit && $laporan->status === 'Open')
                                                <button type="button" class="btn-action bg-soft-warning text-warning"
                                                    data-bs-toggle="modal" data-bs-target="#editSafetyPatrolModal{{ $laporan->id }}"
                                                    title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            @endif

                                            @if($laporan->foto_before || $laporan->foto_after)
                                                <button type="button" class="btn-action bg-soft-info text-info"
                                                    data-bs-toggle="modal" data-bs-target="#viewDetailModal{{ $laporan->id }}"
                                                    title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            @endif

                                            @if(isset($permission) && $permission->can_delete && $laporan->status === 'Open')
                                                <form action="{{ url('/she/safety-patrol/destroy/' . $laporan->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-action bg-soft-danger text-danger"
                                                        onclick="return confirm('Hapus laporan ini?')" title="Hapus">
                                                        <i class="bi bi-trash"></i>
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
            </div>
        </div>
    </div>

    <script>
        const placeholders = ["Cari E-PORTE", "Cari Area", "Filter"];
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

        // ============================================
        // FUNGSI UTAMA UNTUK PREVIEW GAMBAR
        // ============================================

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            if (!preview) {
                console.error(`Container #${previewId} not found!`);
                return;
            }

            preview.innerHTML = '';

            if (input.files && input.files.length > 0) {
                const file = input.files[0];
                const reader = new FileReader();

                reader.onload = function (e) {
                    const imgDiv = document.createElement('div');
                    imgDiv.className = 'col-md-6 mb-2';
                    imgDiv.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                            <div class="card-body p-2 text-center">
                                <small class="text-muted">${file.name}</small>
                            </div>
                        </div>
                    `;
                    preview.appendChild(imgDiv);
                }

                reader.onerror = function (e) {
                    console.error('Error reading file:', file.name, e);
                }

                reader.readAsDataURL(file);
            }
        }

        // ============================================
        // FUNGSI UNTUK TINDAK LANJUT
        // ============================================

        function validateTindakLanjut(formId) {
            const form = document.getElementById(formId);
            const statusSelect = form.querySelector('select[name="status"]');
            const catatan = form.querySelector('textarea[name="catatan"]');

            if (statusSelect.value === '') {
                alert('Harap pilih status tindak lanjut!');
                return false;
            }

            if (statusSelect.value === 'Rejected' && catatan.value.trim() === '') {
                alert('Harap isi catatan untuk status Reject!');
                catatan.focus();
                return false;
            }

            return true;
        }

        // ============================================
        // SETUP TOOLTIPS
        // ============================================

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection

@section('modals')
    <!-- Modal Add Laporan Baru -->
    <div class="modal fade" id="addSafetyPatrolModal" tabindex="-1" aria-labelledby="addSafetyPatrolModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="addSafetyPatrolModalLabel"><i class="bi bi-plus-circle-fill me-1"></i> Buat
                        Laporan Safety Patrol</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('/she/safety-patrol/store') }}" method="POST" enctype="multipart/form-data"
                        id="addSafetyPatrolForm">
                        @csrf

                        <h6 class="fw-bold text-primary mb-3">I. Data Safety Patrol</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control"
                                    value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="eporte" class="form-label">E-PORTE <span class="text-danger">*</span></label>
                                <input type="text" name="eporte" id="eporte" class="form-control"
                                    value="{{ old('eporte') }}" placeholder="Masukkan E-PORTE" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="area" class="form-label">Area <span class="text-danger">*</span></label>
                                <input type="text" name="area" id="area" class="form-control" value="{{ old('area') }}"
                                    placeholder="Contoh: Workshop, Kantor, Gudang" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="section_id" class="form-label">Section <span
                                        class="text-danger">*</span></label>
                                <select name="section_id" id="section_id" class="form-select" required>
                                    <option value="">Pilih Section</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                            {{ $section->section }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="problem" class="form-label">Problem <span class="text-danger">*</span></label>
                                <textarea name="problem" id="problem" class="form-control" rows="3"
                                    placeholder="Deskripsikan masalah yang ditemukan"
                                    required>{{ old('problem') }}</textarea>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="counter_measure" class="form-label">Counter Measure <span
                                        class="text-danger">*</span></label>
                                <textarea name="counter_measure" id="counter_measure" class="form-control" rows="3"
                                    placeholder="Tindakan yang diperlukan" required>{{ old('counter_measure') }}</textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="date" name="due_date" id="due_date" class="form-control"
                                    value="{{ old('due_date') }}" required>
                            </div>
                        </div>

                        <h6 class="fw-bold text-primary mt-4 mb-3">II. Foto Dokumentasi</h6>

                        <div class="mb-3">
                            <label for="foto_before" class="form-label fw-bold">Foto Before</label>
                            <small class="text-danger d-block mb-2">* Upload foto kondisi sebelum perbaikan</small>

                            <div id="fotoBeforePreview" class="row mb-2"></div>

                            <input type="file" name="foto_before" id="foto_before" class="form-control" accept="image/*"
                                onchange="previewImage(this, 'fotoBeforePreview')">
                            <small class="form-text text-muted">Upload satu foto sebelum perbaikan</small>
                        </div>

                        <input type="hidden" name="status" value="Open">
                        <input type="hidden" name="created_by" value="{{ auth()->id() }}">

                        <div class="modal-footer mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                    class="bi bi-x-lg me-1"></i> Batal</button>
                            <button type="submit" class="btn btn-info text-white"><i class="bi bi-save me-1"></i> Simpan
                                Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @foreach($safetypatrols as $laporan)
        <!-- ============================================ -->
        <!-- MODAL TINDAK LANJUT (UNTUK SHE) -->
        <!-- ============================================ -->
        @if(isset($permission) && $permission->can_edit && ($laporan->status === 'Progress' || $laporan->status === 'Open'))
            <div class="modal fade" id="tindakLanjutModal{{ $laporan->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Tindak Lanjut - Laporan #{{ $laporan->id }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ url('/she/safety-patrol/tindak-lanjut/' . $laporan->id) }}" method="POST"
                                id="tindakLanjutForm{{ $laporan->id }}" enctype="multipart/form-data"
                                onsubmit="return validateTindakLanjut('tindakLanjutForm{{ $laporan->id }}')">
                                @csrf
                                @method('PUT')

                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Informasi Laporan:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>E-PORTE: <strong>{{ $laporan->eporte }}</strong></li>
                                        <li>Area: {{ $laporan->area }}</li>
                                        <li>Section: {{ $laporan->section->section ?? 'N/A' }}</li>
                                        <li>Status Saat Ini: <span
                                                class="badge bg-{{ $statusColors[$laporan->status] ?? 'secondary' }}">{{ $laporan->status }}</span>
                                        </li>
                                        <li>Due Date:
                                            <strong>{{ \Carbon\Carbon::parse($laporan->due_date)->format('d-m-Y') }}</strong>
                                        </li>
                                    </ul>
                                </div>

                                <div class="row mb-4">
                                    <!-- Before Photos -->
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-primary">
                                            <i class="bi bi-camera me-1"></i> Foto Before
                                        </h6>
                                        @if($laporan->foto_before)
                                            <div class="row">
                                                <div class="col-12 mb-2">
                                                    <div class="card">
                                                        <a href="{{ asset('storage/' . $laporan->foto_before) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $laporan->foto_before) }}"
                                                                class="card-img-top" style="height: 150px; object-fit: cover;"
                                                                alt="Foto Before">
                                                        </a>
                                                        <div class="card-body p-1 text-center">
                                                            <small class="text-muted">Foto Before</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-muted small">Tidak ada foto before</p>
                                        @endif
                                    </div>

                                    <!-- After Photos -->
                                    <div class="col-md-6">
                                        <h6 class="fw-bold text-success">
                                            <i class="bi bi-check-circle me-1"></i> Foto After
                                        </h6>
                                        @if($laporan->foto_after)
                                            <div class="row">
                                                <div class="col-12 mb-2">
                                                    <div class="card">
                                                        <a href="{{ asset('storage/' . $laporan->foto_after) }}" target="_blank">
                                                            <img src="{{ asset('storage/' . $laporan->foto_after) }}"
                                                                class="card-img-top" style="height: 150px; object-fit: cover;"
                                                                alt="Foto After">
                                                        </a>
                                                        <div class="card-body p-1 text-center">
                                                            <small class="text-muted">Foto After</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-muted small">Belum ada foto after</p>
                                        @endif
                                    </div>
                                </div>



                                <!-- Status Selection -->
                                <div class="mb-4">
                                    <label for="status_tindak_lanjut_{{ $laporan->id }}" class="form-label fw-bold">
                                        <i class="bi bi-flag me-1"></i> Status Tindak Lanjut:
                                    </label>
                                    <select name="status" id="status_tindak_lanjut_{{ $laporan->id }}" class="form-select" required>
                                        <option value="">-- Pilih Status --</option>
                                        @if($laporan->status === 'Open')
                                            <option value="Progress">Progress</option>
                                        @endif
                                        @if($laporan->status === 'Progress')
                                            <option value="Close">Close</option>
                                        @endif
                                        <option value="Rejected">Reject</option>
                                    </select>
                                    <div class="form-text">
                                        <strong>Progress:</strong> Laporan dalam penanganan<br>
                                        <strong>Close:</strong> Laporan selesai (wajib upload foto after)<br>
                                        <strong>Reject:</strong> Laporan ditolak (wajib isi catatan)<br>
                                    </div>
                                </div>

                                <!-- Catatan -->
                                <div class="mb-4" id="catatan_container_{{ $laporan->id }}">
                                    <label for="catatan_{{ $laporan->id }}" class="form-label fw-bold">
                                        <i class="bi bi-chat-text me-1"></i> Catatan Tindak Lanjut:
                                    </label>
                                    <textarea name="catatan" id="catatan_{{ $laporan->id }}" class="form-control" rows="3"
                                        placeholder="{{ $laporan->status === 'Progress' ? 'Tambahkan catatan progress...' : 'Khusus untuk status Reject, harap isi alasan penolakan...' }}"></textarea>
                                    <div class="form-text">Wajib diisi jika memilih status Reject</div>
                                </div>

                                <div class="modal-footer mt-4">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-lg me-1"></i> Batal
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i> Simpan Tindak Lanjut
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Show/hide catatan berdasarkan status
                document.addEventListener('DOMContentLoaded', function () {
                    const statusSelect{{ $laporan->id }} = document.getElementById('status_tindak_lanjut_{{ $laporan->id }}');
                    const catatanContainer{{ $laporan->id }} = document.getElementById('catatan_container_{{ $laporan->id }}');

                    if (statusSelect{{ $laporan->id }}) {
                        statusSelect{{ $laporan->id }}.addEventListener('change', function () {
                            if (this.value === 'Rejected') {
                                catatanContainer{{ $laporan->id }}.style.display = 'block';
                            } else {
                                catatanContainer{{ $laporan->id }}.style.display = 'none';
                            }
                        });

                        // Set initial state
                        if (statusSelect{{ $laporan->id }}.value !== 'Rejected') {
                            catatanContainer{{ $laporan->id }}.style.display = 'none';
                        }
                    }
                });
            </script>
        @endif

        <!-- ============================================ -->
        <!-- MODAL VIEW DETAIL (GABUNGAN BEFORE & AFTER) -->
        <!-- ============================================ -->
        @if($laporan->foto_before || $laporan->foto_after)
            <div class="modal fade" id="viewDetailModal{{ $laporan->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">Detail Foto - Laporan #{{ $laporan->id }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Tabs untuk Before dan After -->
                            <ul class="nav nav-tabs mb-4" id="detailTab{{ $laporan->id }}" role="tablist">
                                @if($laporan->foto_before)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="before-tab-{{ $laporan->id }}" data-bs-toggle="tab"
                                            data-bs-target="#before-{{ $laporan->id }}" type="button" role="tab">
                                            <i class="bi bi-camera me-1"></i> Before
                                        </button>
                                    </li>
                                @endif
                                @if($laporan->foto_after)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ !$laporan->foto_before ? 'active' : '' }}"
                                            id="after-tab-{{ $laporan->id }}" data-bs-toggle="tab"
                                            data-bs-target="#after-{{ $laporan->id }}" type="button" role="tab">
                                            <i class="bi bi-check-circle me-1"></i> After
                                        </button>
                                    </li>
                                @endif
                            </ul>

                            <div class="tab-content" id="detailTabContent{{ $laporan->id }}">
                                <!-- Tab Before -->
                                @if($laporan->foto_before)
                                    <div class="tab-pane fade {{ $laporan->foto_before ? 'show active' : '' }}"
                                        id="before-{{ $laporan->id }}" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6 offset-md-3 mb-3">
                                                <div class="card">
                                                    <a href="{{ asset('storage/' . $laporan->foto_before) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $laporan->foto_before) }}" class="card-img-top"
                                                            style="height: 300px; object-fit: cover;">
                                                    </a>
                                                    <div class="card-body">
                                                        <h6 class="card-title">Foto Before</h6>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            <a href="{{ asset('storage/' . $laporan->foto_before) }}" target="_blank"
                                                                class="btn btn-sm btn-primary">
                                                                <i class="bi bi-eye"></i> Lihat
                                                            </a>
                                                            <a href="{{ asset('storage/' . $laporan->foto_before) }}" download
                                                                class="btn btn-sm btn-success">
                                                                <i class="bi bi-download"></i> Download
                                                            </a>
                                                            @if(isset($permission) && $permission->can_edit && $laporan->status === 'Open')
                                                                <form
                                                                    action="{{ url('/she/safety-patrol/' . $laporan->id . '/delete-image/before') }}"
                                                                    method="POST" class="d-inline"
                                                                    onsubmit="return confirmDeleteImage(event, 'before')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                                        <i class="bi bi-trash"></i> Hapus
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Tab After -->
                                @if($laporan->foto_after)
                                    <div class="tab-pane fade {{ !$laporan->foto_before ? 'show active' : '' }}"
                                        id="after-{{ $laporan->id }}" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6 offset-md-3 mb-3">
                                                <div class="card">
                                                    <a href="{{ asset('storage/' . $laporan->foto_after) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $laporan->foto_after) }}" class="card-img-top"
                                                            style="height: 300px; object-fit: cover;">
                                                    </a>
                                                    <div class="card-body">
                                                        <h6 class="card-title">Foto After</h6>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            <a href="{{ asset('storage/' . $laporan->foto_after) }}" target="_blank"
                                                                class="btn btn-sm btn-primary">
                                                                <i class="bi bi-eye"></i> Lihat
                                                            </a>
                                                            <a href="{{ asset('storage/' . $laporan->foto_after) }}" download
                                                                class="btn btn-sm btn-success">
                                                                <i class="bi bi-download"></i> Download
                                                            </a>
                                                            @if(isset($permission) && $permission->can_edit)
                                                                <form
                                                                    action="{{ url('/she/safety-patrol/' . $laporan->id . '/delete-image/after') }}"
                                                                    method="POST" class="d-inline"
                                                                    onsubmit="return confirmDeleteImage(event, 'after')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                                        <i class="bi bi-trash"></i> Hapus
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- ============================================ -->
        <!-- MODAL EDIT (UNTUK STATUS OPEN) -->
        <!-- ============================================ -->
        @if(isset($permission) && $permission->can_edit && $laporan->status === 'Open')
            <div class="modal fade" id="editSafetyPatrolModal{{ $laporan->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i> Edit Laporan Safety Patrol
                                #{{ $laporan->id }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ url('/she/safety-patrol/update/' . $laporan->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <h6 class="fw-bold text-primary mb-3">I. Data Safety Patrol</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="edit_tanggal_{{ $laporan->id }}" class="form-label">Tanggal <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="tanggal" id="edit_tanggal_{{ $laporan->id }}" class="form-control"
                                            value="{{ old('tanggal', \Carbon\Carbon::parse($laporan->tanggal)->format('Y-m-d')) }}"
                                            required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="edit_eporte_{{ $laporan->id }}" class="form-label">E-PORTE <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="eporte" id="edit_eporte_{{ $laporan->id }}" class="form-control"
                                            value="{{ old('eporte', $laporan->eporte) }}" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="edit_area_{{ $laporan->id }}" class="form-label">Area <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="area" id="edit_area_{{ $laporan->id }}" class="form-control"
                                            value="{{ old('area', $laporan->area) }}" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="edit_section_{{ $laporan->id }}" class="form-label">Section <span
                                                class="text-danger">*</span></label>
                                        <select name="section_id" id="edit_section_{{ $laporan->id }}" class="form-select" required>
                                            @foreach($sections as $section)
                                                <option value="{{ $section->id }}" {{ $laporan->section_id == $section->id ? 'selected' : '' }}>
                                                    {{ $section->section }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="edit_problem_{{ $laporan->id }}" class="form-label">Problem <span
                                                class="text-danger">*</span></label>
                                        <textarea name="problem" id="edit_problem_{{ $laporan->id }}" class="form-control" rows="3"
                                            required>{{ old('problem', $laporan->problem) }}</textarea>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="edit_counter_measure_{{ $laporan->id }}" class="form-label">Counter Measure
                                            <span class="text-danger">*</span></label>
                                        <textarea name="counter_measure" id="edit_counter_measure_{{ $laporan->id }}"
                                            class="form-control" rows="3"
                                            required>{{ old('counter_measure', $laporan->counter_measure) }}</textarea>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="edit_due_date_{{ $laporan->id }}" class="form-label">Due Date <span
                                                class="text-danger">*</span></label>
                                        <input type="date" name="due_date" id="edit_due_date_{{ $laporan->id }}"
                                            class="form-control"
                                            value="{{ old('due_date', \Carbon\Carbon::parse($laporan->due_date)->format('Y-m-d')) }}"
                                            required>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-primary mt-4 mb-3">II. Foto Dokumentasi</h6>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Foto Before Saat Ini:</label>
                                    @if($laporan->foto_before)
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="card">
                                                    <a href="{{ asset('storage/' . $laporan->foto_before) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $laporan->foto_before) }}" class="card-img-top"
                                                            style="height: 150px; object-fit: cover;">
                                                    </a>
                                                    <div class="card-body p-2 text-center">
                                                        <small class="text-muted">Foto Sebelumnya</small>
                                                        <br>
                                                        <!-- TAMBAHKAN CHECKBOX UNTUK HAPUS FOTO -->
                                                        <div class="form-check mt-2">
                                                            <input class="form-check-input" type="checkbox" name="hapus_foto_before"
                                                                id="hapus_foto_before_{{ $laporan->id }}" value="1">
                                                            <label class="form-check-label text-danger"
                                                                for="hapus_foto_before_{{ $laporan->id }}">
                                                                <i class="bi bi-trash"></i> Hapus foto ini
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted">Tidak ada foto before.</p>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label for="edit_foto_before_{{ $laporan->id }}" class="form-label fw-bold">
                                        @if($laporan->foto_before)
                                            Ganti Foto Before:
                                        @else
                                            Upload Foto Before:
                                        @endif
                                    </label>

                                    <div id="editFotoBeforePreview_{{ $laporan->id }}" class="row mb-2"></div>

                                    <input type="file" name="foto_before_baru" id="edit_foto_before_{{ $laporan->id }}"
                                        class="form-control" accept="image/*"
                                        onchange="previewImage(this, 'editFotoBeforePreview_{{ $laporan->id }}')">
                                    <small class="form-text text-muted">
                                        @if($laporan->foto_before)
                                            Upload foto baru untuk mengganti, atau centang "Hapus foto ini" untuk menghapus
                                        @else
                                            Upload foto kondisi sebelum perbaikan
                                        @endif
                                    </small>
                                </div>

                                <div class="modal-footer mt-4">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-lg me-1"></i> Batal
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i> Update Laporan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @endforeach

    </script>
@endsection