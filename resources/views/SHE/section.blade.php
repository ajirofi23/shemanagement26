@extends('layout.sidebar')

@section('content')
    @include('layout.header')

    <style>
        /* ====== GLOBAL ANIMATION ====== */
        @keyframes fadeSlideUp {
            0% { opacity: 0; transform: translateY(14px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeSlideRight {
            0% { opacity:0; transform:translateX(-14px); }
            100% { opacity:1; transform:translateX(0); }
        }
        @keyframes softPop {
            0% { transform: scale(.9); opacity:0; }
            100% { transform: scale(1); opacity:1; }
        }

        /* ====== TITLE & SUBTITLE ====== */
        .k3-title {
            color:#0f172a;
            font-weight:800;
            letter-spacing:.4px;
            font-size:1.55rem;
            animation: fadeSlideUp .6s ease;
        }
        .k3-subtitle {
            color:#6b7280;
            font-size:.95rem;
            animation: fadeSlideUp .75s ease;
        }

        /* ====== CARD WRAPPER ====== */
        .card-k3 {
            border-radius:22px;
            background:#ffffffee;
            backdrop-filter:blur(10px);
            border:1px solid #e5e7eb;
            box-shadow:0 6px 16px rgba(0,0,0,0.06);
            padding-bottom:6px;
            animation: softPop .6s ease;
        }

        /* ====== PAGE WRAPPER ====== */
        .content-with-sidebar {
            margin-left:280px;
            display:flex;
            justify-content:center;
            padding-top:8vh;
            animation: fadeSlideRight .65s ease;
        }
        @media (max-width: 992px) {
            .content-with-sidebar { margin-left:0; }
        }

        /* ====== FILTER BUTTONS ====== */
        .filters-bar {
            display:flex;
            gap:12px;
            margin:12px 0 20px;
            animation: fadeSlideUp .7s ease;
        }
        @media (max-width: 768px) {
            .filters-bar { flex-direction:column; }
        }
        .filter-btn {
            display:flex;
            align-items:center;
            gap:8px;
            padding:8px 14px;
            border-radius:10px;
            border:1px solid #d1d5db;
            background:#f9fafb;
            color:#374151;
            font-weight:600;
            transition:.25s;
        }
        .filter-btn:hover {
            background:#eef2ff;
            border-color:#c7d2fe;
            transform: translateY(-2px);
            box-shadow:0 3px 10px rgba(0,0,0,0.08);
        }
        .filter-btn svg { width:18px; height:18px; }

        /* ====== TABLE ====== */
        .table-k3 {
            width:100%;
            border-collapse:separate;
            border-spacing:0;
            animation: fadeSlideUp .8s ease;
        }
        .table-k3 tbody tr {
            transition:.25s ease;
        }
        .table-k3 tbody tr:hover {
            background:#f3f4f6;
            transform:translateX(6px);
        }

        .table-k3 thead th {
            background:#f8fafc;
            color:#1f2937;
            font-weight:700;
            border-bottom:2px solid #e5e7eb;
            font-size:0.9rem;
        }
        .table-k3 th,
        .table-k3 td {
            padding:12px 14px;
            vertical-align:middle;
            border-bottom:1px solid #e5e7eb;
        }

        /* ====== BADGE ====== */
        .status-badge {
            padding:6px 12px;
            border-radius:999px;
            font-weight:700;
            font-size:.85rem;
        }
        .status-ok {
            background:#22c55e;
            color:#fff;
            box-shadow:0 0 6px rgba(34,197,94,0.4);
        }
        .status-warn {
            background:#f43f5e;
            color:#fff;
            box-shadow:0 0 6px rgba(244,63,94,0.4);
        }

        /* ====== ACTION BUTTON ====== */
        .action-btn {
            padding:6px;
            border-radius:50%;
            border:1px solid #c7d2fe;
            background:#eef2ff;
            color:#1d4ed8;
            width:36px;
            height:36px;
            display:flex;
            align-items:center;
            justify-content:center;
            transition:.25s ease;
            box-shadow:0 2px 5px rgba(0,0,0,0.06);
        }
        .action-btn:hover {
            background:#4f46e5;
            color:white;
            border-color:#4338ca;
            transform:scale(1.12);
            box-shadow:0 4px 12px rgba(79,70,229,0.35);
        }
        .action-btn svg {
            width:18px;
            height:18px;
            stroke-width:1.4;
        }

        .table-k3 td:last-child,
        .table-k3 th:last-child {
            text-align:center !important;
        }

            .filter-btn input[type="date"]::-webkit-calendar-picker-indicator {
        cursor:pointer;
        filter:invert(35%);
    }

    </style>



    <div class="content-with-sidebar">
        <div class="container-fluid" style="margin-top:16px;">
            <div class="card card-k3 shadow-sm">
                <div class="card-body">

                    <h5 class="k3-title mb-1">Detail Section: {{ $section }}</h5>
                    <p class="k3-subtitle mb-3">Summary of reports for section {{ $section }}</p>

                    <div class="alert alert-info mb-3" style="animation: fadeSlideUp .8s ease;">
                        This is the detail page for the section: <strong>{{ $section }}</strong>.
                    </div>

                    <!-- Filters -->
                                <div class="filters-bar">
                                <div class="filter-btn" style="padding: 6px 14px;">
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path d="M3 1a1 1 0 0 0-1 1v1H1.5a.5.5 0 0 0 0 1H15a.5.5 0 0 0 0-1H14V2a1 1 0 0 0-1-1h-1V0h-1v1H6V0H5v1H3zm11 4H2v8a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V5z"/>
                </svg>

                <input
                    type="date"
                    class="form-control"
                    style="border:none; background:transparent; font-weight:600; padding:0; width:150px; outline:none;"
                >
</div>

                        <button type="button" class="filter-btn" id="filter-belum">
                <svg viewBox="0 0 16 16" fill="currentColor">
                    <path d="M8 1a3 3 0 0 0-3 3v1H3.5a.5.5 0 0 0 0 1H5v2.5a3 3 0 0 0 3 3h0a3 3 0 0 0 3-3V6h1.5a.5.5 0 0 0 0-1H11V4a3 3 0 0 0-3-3z"/>
                </svg>
                <span>Filter: Belum Upload</span>
            </button>


                    </div>

                    <!-- TABLE -->
                    <div class="table-responsive">
                        <table class="table-k3 w-100">
                            <thead>
                                <tr>
                                    <th style="width:60px;">No</th>
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th>Section</th>
                                    <th>Departemen</th>
                                    <th>Status K3</th>
                                    <th style="width:90px;">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Aji</td>
                                    <td>43897</td>
                                    <td>{{ $section }}</td>
                                    <td>Engineering</td>
                                    <td><span class="status-badge status-ok">Sudah Upload</span></td>
                                    <td>
                                        <button type="button" class="action-btn" title="Detail">
                                            <svg viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M8 3C4.5 3 1.8 5.2.5 8c1.3 2.8 4 5 7.5 5s6.2-2.2 7.5-5C14.2 5.2 11.5 3 8 3zm0 8a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td>Nabil</td>
                                    <td>43898</td>
                                    <td>{{ $section }}</td>
                                    <td>Engineering</td>
                                    <td><span class="status-badge status-ok">Sudah Upload</span></td>
                                    <td>
                                        <button type="button" class="action-btn" title="Detail">
                                            <svg viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M8 3C4.5 3 1.8 5.2.5 8c1.3 2.8 4 5 7.5 5s6.2-2.2 7.5-5C14.2 5.2 11.5 3 8 3zm0 8a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>Tania</td>
                                    <td>43899</td>
                                    <td>{{ $section }}</td>
                                    <td>Engineering</td>
                                    <td><span class="status-badge status-ok">Sudah Upload</span></td>
                                    <td>
                                        <button type="button" class="action-btn" title="Detail">
                                            <svg viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M8 3C4.5 3 1.8 5.2.5 8c1.3 2.8 4 5 7.5 5s6.2-2.2 7.5-5C14.2 5.2 11.5 3 8 3zm0 8a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td>Titin</td>
                                    <td>43900</td>
                                    <td>{{ $section }}</td>
                                    <td>Engineering</td>
                                    <td><span class="status-badge status-warn">Belum Upload</span></td>
                                    <td>
                                        <button type="button" class="action-btn" title="Detail">
                                            <svg viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M8 3C4.5 3 1.8 5.2.5 8c1.3 2.8 4 5 7.5 5s6.2-2.2 7.5-5C14.2 5.2 11.5 3 8 3zm0 8a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                                <tr>
                                    <td>5</td>
                                    <td>Daryana</td>
                                    <td>43901</td>
                                    <td>{{ $section }}</td>
                                    <td>Engineering</td>
                                    <td><span class="status-badge status-warn">Belum Upload</span></td>
                                    <td>
                                        <button type="button" class="action-btn" title="Detail">
                                            <svg viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M8 3C4.5 3 1.8 5.2.5 8c1.3 2.8 4 5 7.5 5s6.2-2.2 7.5-5C14.2 5.2 11.5 3 8 3zm0 8a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                                <tr>
                                    <td>6</td>
                                    <td>Nafiz</td>
                                    <td>43902</td>
                                    <td>{{ $section }}</td>
                                    <td>Engineering</td>
                                    <td><span class="status-badge status-ok">Sudah Upload</span></td>
                                    <td>
                                        <button type="button" class="action-btn" title="Detail">
                                            <svg viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M8 3C4.5 3 1.8 5.2.5 8c1.3 2.8 4 5 7.5 5s6.2-2.2 7.5-5C14.2 5.2 11.5 3 8 3zm0 8a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <button class="btn btn-secondary btn-sm mt-3" onclick="history.back()" style="animation: fadeSlideUp .8s ease;">
                        Back
                    </button>

                </div>
            </div>
        </div>
    </div>

   <script>
document.addEventListener("DOMContentLoaded", function () {

    /* ================================
       1) FILTER BY DATE (placeholder)
    =================================*/
    const dateInput = document.querySelector('.filter-btn input[type="date"]');
    if (dateInput) {
        dateInput.addEventListener("change", function () {
            const selectedDate = this.value;
            const rows = document.querySelectorAll(".table-k3 tbody tr");

            rows.forEach(row => {
                // Sementara tidak filter apapun (karena belum ada kolom tanggal)
                row.style.display = "";
            });
        });
    }

    /* ================================
       2) FILTER: BELUM UPLOAD (TOGGLE)
       - Menggantikan dropdown section
       - Mencari .status-warn pada kolom status
    =================================*/
    const filterBtn = document.querySelector('#filter-belum-btn'); // ID baru tombol
    const tableRows = document.querySelectorAll(".table-k3 tbody tr");

    let filterActive = false;

    if (filterBtn) {
        filterBtn.addEventListener("click", function () {

            filterActive = !filterActive;

            // Efek visual tombol saat aktif
            if (filterActive) {
                filterBtn.style.background = "#fff1f2";
                filterBtn.style.borderColor = "#fda4af";
            } else {
                filterBtn.style.background = "";
                filterBtn.style.borderColor = "";
            }

            // Jalankan filter baris
            tableRows.forEach(row => {
                const statusCell = row.children[5]; // kolom status index ke-5
                if (!statusCell) return;

                const badge = statusCell.querySelector(".status-badge");
                const isBelum = badge && badge.classList.contains("status-warn");

                if (filterActive) {
                    row.style.display = isBelum ? "" : "none";
                } else {
                    row.style.display = "";
                }
            });
        });
    }

});
</script>


@endsection
