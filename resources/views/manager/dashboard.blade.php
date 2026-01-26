@extends('layout.managersidebar')

@section('content')

    <style>
        /* ----------------------------------------------------- */
        /* 1. VARIABEL & BASE STYLES */
        /* ----------------------------------------------------- */
        :root {
            --sidebar-width: 10px;
            --card-bg: #f8fafc;
            --card-border: #e2e8f0;
            --content-bg: #ffffff;
            --text-dark: #1f2937;
            --muted: #6b7280;
            --accent: #ef4b64;
            --gap-size: 10px;
            --card-padding-y: 12px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: var(--text-dark);
            background: var(--content-bg);
            overflow-x: hidden;
        }

        /* ----------------------------------------------------- */
        /* 2. LAYOUT: TOPBAR & CONTENT */
        /* ----------------------------------------------------- */

        .content {
            margin-left: var(--sidebar-width);
            padding: 16px;
            padding-top: 40px;
            min-height: 100vh;
            min-height: calc(100vh - 0px);
        }

        .content-inner {
            max-width: 1300px;
            margin: 0 auto;
            padding-bottom: 16px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 2px;
            color: #0f172a;
        }

        .page-subtitle {
            color: var(--muted);
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        /* Filter Section Compact */
        .filter-section {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .filter-form-row {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 150px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .filter-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .filter-input,
        .filter-select {
            padding: 7px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #1e293b;
            background: #f8fafc;
            width: 100%;
            transition: all 0.2s;
        }

        .filter-input:focus,
        .filter-select:focus {
            outline: none;
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 8px;
        }

        .filter-btn {
            height: 38px;
            padding: 0 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-apply {
            background: #3b82f6;
            color: white;
            border: none;
        }

        .btn-apply:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-reset {
            background: #fff;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .btn-reset:hover {
            background: #f8fafc;
            color: #1e293b;
        }

        /* Top bar */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: 56px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding: 0 18px;
            z-index: 5;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .user-avatar {
            display: inline-block;
            width: 24px;
            height: 24px;
            line-height: 24px;
            text-align: center;
            border-radius: 50%;
            background: #4b5563;
            color: #fff;
            margin-right: 8px;
            font-size: 12px;
        }

        .user-name {
            font-weight: 600;
        }

        /* ----------------------------------------------------- */
        /* 3. CARD & GRID STYLES */
        /* ----------------------------------------------------- */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.06);
        }

        .card.no-incident {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-color: #bbf7d0;
        }

        .card.has-incident {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-color: #fecaca;
        }

        .card .card-header {
            padding: 8px 14px;
            font-weight: 700;
            border-bottom: 1px solid var(--card-border);
            background: #f1f5f9;
            font-size: 0.9rem;
            line-height: 1.2;
            position: relative;
        }

        .reset-badge {
            position: absolute;
            top: 4px;
            right: 8px;
            background: #f59e0b;
            color: white;
            font-size: 0.65rem;
            padding: 1px 6px;
            border-radius: 10px;
            font-weight: 600;
        }

        .reset-info {
            display: block;
            font-size: 0.7rem;
            font-weight: normal;
            margin-top: 4px;
            color: #6b7280;
        }

        .card .card-body {
            padding: var(--card-padding-y) 14px;
            font-size: 30px;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
            position: relative;
        }

        .card-label {
            position: absolute;
            bottom: 4px;
            right: 8px;
            font-size: 0.7rem;
            color: #6b7280;
            font-weight: normal;
        }

        .card-total {
            grid-column: 1 / -1;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-color: #bfdbfe;
        }

        .card-total .card-body {
            font-size: 40px;
        }

        .today-warning {
            background: #fef2f2 !important;
            border-color: #fecaca !important;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }

            100% {
                opacity: 1;
            }
        }

        .dashboard-grid-container {
            display: grid;
            grid-template-rows: 1fr 1fr 1fr;
            gap: var(--gap-size);
            height: calc(100vh - 200px - 16px - 16px - 40px);
            max-height: 800px;
        }

        .grid-tiles {
            grid-column: 1 / -1;
            grid-row: 2 / span 2;
            display: grid;
            gap: var(--gap-size);
            grid-template-columns: repeat(4, 1fr);
        }

        .card-total-section {
            grid-row: 1 / 2;
            display: contents;
        }

        .grid-tiles .card:nth-child(7) {
            grid-column: span 1;
        }

        .update-time {
            text-align: center;
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 10px;
            padding: 5px;
            background: #f9fafb;
            border-radius: 4px;
        }

        /* ----------------------------------------------------- */
        /* 4. RESPONSIVENESS */
        /* ----------------------------------------------------- */

        @media (min-width: 1024px) {
            @media (min-width: 1400px) {
                .grid-tiles {
                    grid-template-columns: repeat(4, 1fr);
                }
            }
        }

        @media (max-width: 767px) {
            .content {
                margin-left: 0;
                padding: 16px;
                padding-top: 60px;
            }

            .topbar {
                left: 0;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                flex-direction: column;
            }

            .filter-btn {
                width: 100%;
            }

            .dashboard-grid-container {
                height: auto;
                grid-template-rows: auto;
                display: block;
            }

            .grid-tiles {
                margin-top: var(--gap-size);
                grid-template-columns: 1fr;
            }

            .card .card-header {
                font-size: 1rem;
            }

            .card .card-body {
                font-size: 32px;
                padding: 18px 16px;
            }

            .card-total .card-body {
                font-size: 40px;
            }

            .reset-badge {
                font-size: 0.7rem;
                padding: 2px 8px;
            }
        }

        /* ----------------------------------------------------- */
        /* 5. ANIMASI */
        /* ----------------------------------------------------- */
        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* ----------------------------------------------------- */
        /* 6. SUMMARY CARDS STYLES */
        /* ----------------------------------------------------- */
        .summary-section {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }

        .summary-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            border-color: #c7d2fe;
        }

        .summary-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e5e7eb;
        }

        .summary-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .summary-title h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #1f2937;
        }

        .summary-subtitle {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .summary-card-body {
            padding: 16px;
            flex-grow: 1;
        }

        .summary-stats-row {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .summary-stat {
            text-align: center;
            flex: 1;
            padding: 8px 4px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .stat-value {
            display: block;
            font-size: 1.5rem;
            font-weight: 800;
            line-height: 1.2;
        }

        .stat-label {
            display: block;
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 2px;
        }

        .stat-open {
            color: #ef4444;
        }

        .stat-closed {
            color: #10b981;
        }

        .stat-progress {
            color: #3b82f6;
        }

        .stat-total {
            color: #6366f1;
        }

        .progress-bar-container {
            margin-top: 12px;
        }

        .progress-bar-bg {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .progress-text {
            display: block;
            text-align: right;
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 4px;
        }

        .summary-card-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: #f8fafc;
            border-top: 1px solid #e5e7eb;
            color: #4f46e5;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .summary-card-link:hover {
            background: #eef2ff;
            color: #4338ca;
        }

        .summary-card-link i {
            transition: transform 0.2s ease;
        }

        .summary-card-link:hover i {
            transform: translateX(4px);
        }

        /* Komitmen K3 Percentage Circle */
        .komitmen-percentage {
            display: flex;
            justify-content: center;
            margin-bottom: 8px;
        }

        .percentage-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: conic-gradient(#6366f1 calc(var(--percentage) * 3.6deg),
                    #e5e7eb calc(var(--percentage) * 3.6deg));
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .percentage-circle::before {
            content: '';
            width: 60px;
            height: 60px;
            background: white;
            border-radius: 50%;
            position: absolute;
        }

        .percentage-value {
            position: relative;
            z-index: 1;
            font-size: 1.1rem;
            font-weight: 800;
            color: #6366f1;
        }

        /* Violation Badge */
        .violation-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 12px;
            padding: 8px 12px;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fecaca;
            border-radius: 8px;
            color: #dc2626;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Program Safety Overall Stats */
        .overall-stats {
            display: flex;
            align-items: center;
            justify-content: space-around;
            margin-bottom: 12px;
        }

        .overall-stat-big {
            text-align: center;
        }

        .big-number {
            display: block;
            font-size: 2.5rem;
            font-weight: 800;
            color: #10b981;
            line-height: 1;
        }

        .big-label {
            display: block;
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 4px;
        }

        .overall-completion {
            text-align: center;
        }

        .completion-ring {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: conic-gradient(#10b981 calc(var(--progress) * 3.6deg),
                    #e5e7eb calc(var(--progress) * 3.6deg));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
        }

        .completion-ring::before {
            content: '';
            width: 48px;
            height: 48px;
            background: white;
            border-radius: 50%;
            position: absolute;
        }

        .completion-ring span {
            position: relative;
            z-index: 1;
            font-size: 0.85rem;
            font-weight: 700;
            color: #10b981;
        }

        .completion-label {
            display: block;
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 4px;
        }

        .mini-stats-row {
            display: flex;
            justify-content: center;
            gap: 24px;
        }

        .mini-stat {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            color: #4b5563;
        }

        /* Responsive for Summary Cards */
        @media (max-width: 767px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }

            .summary-section {
                padding: 12px;
            }

            .stat-value {
                font-size: 1.25rem;
            }

            .big-number {
                font-size: 2rem;
            }
        }
    </style>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <main class="content">
        <div class="content-inner">

            <div class="page-title fade-up">DASHBOARD MONITORING</div>
            <div class="page-subtitle fade-up">Summary of safety performance AICC</div>

            <!-- Filter Section Compact -->
            <div class="filter-section fade-up">
                <form id="filterForm" method="GET" action="{{ url('/manager/dashboard') }}">
                    <div class="filter-form-row">
                        <div class="filter-group">
                            <label class="filter-label" for="start_date">Mulai</label>
                            <input type="date" id="start_date" name="start_date" class="filter-input"
                                value="{{ request('start_date', date('Y-m-d', strtotime('-30 days'))) }}">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label" for="end_date">Sampai</label>
                            <input type="date" id="end_date" name="end_date" class="filter-input"
                                value="{{ request('end_date', date('Y-m-d')) }}">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label" for="section">Section</label>
                            <select id="section" name="section" class="filter-select">
                                @php
                                    $selectedSectionId = request('section');
                                @endphp
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ $selectedSectionId == $section->id ? 'selected' : '' }}>
                                        {{ $section->section }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="filter-btn btn-apply">
                                <i class="fas fa-filter"></i> Terapkan
                            </button>
                            <button type="button" class="filter-btn btn-reset" onclick="resetFilters()">
                                <i class="fas fa-undo"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Filter Info Display -->
            @if(request()->hasAny(['start_date', 'end_date', 'section', 'status']))
                <div class="filter-info fade-up"
                    style="margin-bottom: 15px; padding: 10px; background: #f0f9ff; border-radius: 6px; border: 1px solid #bae6fd;">
                    <strong style="color: #0369a1;">
                        <i class="fas fa-info-circle"></i> Filter Aktif:
                    </strong>
                    <span style="font-size: 0.9rem; color: #0c4a6e;">
                        @php
                            $filterText = [];
                            if (request('start_date'))
                                $filterText[] = 'Dari: ' . date('d M Y', strtotime(request('start_date')));
                            if (request('end_date'))
                                $filterText[] = 'Sampai: ' . date('d M Y', strtotime(request('end_date')));
                            if (request('section')) {
                                $selectedSection = $sections->firstWhere('id', request('section'));
                                $filterText[] = 'Section: ' . ($selectedSection->section ?? 'Unknown');
                            }
                            if (request('status')) {
                                $statusLabels = [
                                    'open' => 'Open',
                                    'closed' => 'Closed',
                                    'progress' => 'In Progress'
                                ];
                                $filterText[] = 'Status: ' . ($statusLabels[request('status')] ?? request('status'));
                            }
                        @endphp
                        {{ implode(' | ', $filterText) }}
                        <a href="{{ url('/manager/dashboard') }}"
                            style="margin-left: 10px; color: #dc2626; text-decoration: none;">
                            <i class="fas fa-times"></i> Hapus Filter
                        </a>
                    </span>
                </div>
            @endif

            <div class="dashboard-grid-container">

                <section class="grid card-total-section">
                    <div class="card card-total fade-up {{ $dashboardData['total_safety_days'] == 0 ? 'today-warning' : '' }}"
                        id="total-safety-card">
                        <div class="card-header">
                            Total Safety Work Day
                            @if($dashboardData['last_reset_date'])
                                <span class="reset-badge">RESET</span>
                                <small class="reset-info" id="last-reset-date">
                                    Terakhir: {{ $dashboardData['last_reset_date'] }}
                                </small>
                            @endif
                        </div>
                        <div class="card-body">
                            <span id="total-safety-days">{{ $dashboardData['total_safety_days'] }}</span>
                            @if($dashboardData['total_safety_days'] == 0)
                                <span class="card-label">Hari ini ada loss day</span>
                            @else
                                <span class="card-label">Streak: {{ $dashboardData['current_streak_days'] }} hari</span>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="grid grid-tiles">
                    @php
                        $categories = [
                            'Work Accident (Loss day)' => ['id' => 'work-accident-loss-day', 'name' => 'Work Accident (Loss day)'],
                            'Work Accident (Light)' => ['id' => 'work-accident-light', 'name' => 'Work Accident (Light)'],
                            'Traffic Accident' => ['id' => 'traffic-accident', 'name' => 'Traffic Accident'],
                            'Fire Accident' => ['id' => 'fire-accident', 'name' => 'Fire Accident'],
                            'Forklift Accident' => ['id' => 'forklift-accident', 'name' => 'Forklift Accident'],
                            'Molten Spill Incident' => ['id' => 'molten-spill-incident', 'name' => 'Molten Spill Incident'],
                            'Property Damage Incident' => ['id' => 'property-damage-incident', 'name' => 'Property Damage Incident']
                        ];

                        $delay = 0.1;
                    @endphp

                    @foreach($categories as $key => $categoryInfo)
                        @php
                            $count = $dashboardData['incident_counts'][$key] ?? 0;
                            $cardClass = $count > 0 ? 'has-incident' : 'no-incident';
                            $cardId = 'card-' . $categoryInfo['id'];
                        @endphp

                        <div class="card fade-up {{ $cardClass }}" style="transition-delay: {{ $delay }}s;" id="{{ $cardId }}">
                            <div class="card-header">{{ $categoryInfo['name'] }}</div>
                            <div class="card-body">
                                <span class="incident-count" data-category="{{ $key }}">
                                    {{ $count }}
                                </span>
                            </div>
                        </div>
                        @php $delay += 0.1; @endphp
                    @endforeach
                </section>

            </div>

            <!-- Summary Cards Section -->
            <div class="summary-section fade-up" style="margin-top: 24px;">
                {{-- <h4
                    style="font-size: 1.1rem; font-weight: 700; color: #1f2937; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-chart-pie" style="color: #6366f1;"></i>
                    Program Safety Summary
                </h4> --}}

                <div class="summary-grid">
                    <!-- Hyari Hatto Summary Card -->
                    <div class="summary-card hyari-hatto-card fade-up">
                        <div class="summary-card-header">
                            <div class="summary-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="summary-title">
                                <h5>Hyari Hatto</h5>
                                <span class="summary-subtitle">Near Miss Reports</span>
                            </div>
                        </div>
                        <div class="summary-card-body">
                            <div class="summary-stats-row">
                                <div class="summary-stat">
                                    <span
                                        class="stat-value stat-open">{{ $dashboardData['hyari_hatto']['open'] ?? 0 }}</span>
                                    <span class="stat-label">Open</span>
                                </div>
                                <div class="summary-stat">
                                    <span
                                        class="stat-value stat-closed">{{ $dashboardData['hyari_hatto']['closed'] ?? 0 }}</span>
                                    <span class="stat-label">Closed</span>
                                </div>
                                <div class="summary-stat">
                                    <span
                                        class="stat-value stat-total">{{ $dashboardData['hyari_hatto']['total'] ?? 0 }}</span>
                                    <span class="stat-label">Total</span>
                                </div>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill"
                                        style="width: {{ $dashboardData['hyari_hatto']['percentage_closed'] ?? 0 }}%; background: linear-gradient(90deg, #10b981, #059669);">
                                    </div>
                                </div>
                                <span class="progress-text">{{ $dashboardData['hyari_hatto']['percentage_closed'] ?? 0 }}%
                                    Closed</span>
                            </div>
                        </div>
                        <a href="{{ url('/manager/hyari-hatto') }}" class="summary-card-link">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Komitmen K3 Summary Card -->
                    <div class="summary-card komitmen-k3-card fade-up" style="transition-delay: 0.1s;">
                        <div class="summary-card-header">
                            <div class="summary-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div class="summary-title">
                                <h5>Komitmen K3</h5>
                                <span class="summary-subtitle">Safety Commitment</span>
                            </div>
                        </div>
                        <div class="summary-card-body">
                            <div class="komitmen-percentage">
                                <div class="percentage-circle"
                                    style="--percentage: {{ $dashboardData['komitmen_k3']['percentage'] ?? 0 }};">
                                    <span
                                        class="percentage-value">{{ $dashboardData['komitmen_k3']['percentage'] ?? 0 }}%</span>
                                </div>
                            </div>
                            <div class="summary-stats-row" style="margin-top: 12px;">
                                <div class="summary-stat">
                                    <span
                                        class="stat-value stat-closed">{{ $dashboardData['komitmen_k3']['sudah_upload'] ?? 0 }}</span>
                                    <span class="stat-label">Sudah</span>
                                </div>
                                <div class="summary-stat">
                                    <span
                                        class="stat-value stat-open">{{ $dashboardData['komitmen_k3']['belum_upload'] ?? 0 }}</span>
                                    <span class="stat-label">Belum</span>
                                </div>
                                <div class="summary-stat">
                                    <span
                                        class="stat-value stat-total">{{ $dashboardData['komitmen_k3']['total_users'] ?? 0 }}</span>
                                    <span class="stat-label">Total User</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ url('/manager/komitmen-k3') }}" class="summary-card-link">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Safety Patrol Summary Card -->
                    <div class="summary-card safety-patrol-card fade-up" style="transition-delay: 0.2s;">
                        <div class="summary-card-header">
                            <div class="summary-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="summary-title">
                                <h5>Safety Patrol</h5>
                                <span class="summary-subtitle">Inspection Reports</span>
                            </div>
                        </div>
                        <div class="summary-card-body">
                            <div class="summary-stats-row">
                                <div class="summary-stat">
                                    <span
                                        class="stat-value stat-open">{{ $dashboardData['safety_patrol']['open'] ?? 0 }}</span>
                                    <span class="stat-label">Open</span>
                                </div>
                                <div class="summary-stat">
                                    <span
                                        class="stat-value stat-progress">{{ $dashboardData['safety_patrol']['progress'] ?? 0 }}</span>
                                    <span class="stat-label">Progress</span>
                                </div>
                                <div class="summary-stat">
                                    <span
                                        class="stat-value stat-closed">{{ $dashboardData['safety_patrol']['closed'] ?? 0 }}</span>
                                    <span class="stat-label">Closed</span>
                                </div>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar-bg">
                                    <div class="progress-bar-fill"
                                        style="width: {{ $dashboardData['safety_patrol']['percentage_closed'] ?? 0 }}%; background: linear-gradient(90deg, #3b82f6, #2563eb);">
                                    </div>
                                </div>
                                <span class="progress-text">{{ $dashboardData['safety_patrol']['percentage_closed'] ?? 0 }}%
                                    Closed</span>
                            </div>
                        </div>
                        <a href="{{ url('/manager/safety-patrol') }}" class="summary-card-link">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Safety Riding Summary Card -->
                    <div class="summary-card safety-riding-card fade-up" style="transition-delay: 0.3s;">
                        <div class="summary-card-header">
                            <div class="summary-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                            <div class="summary-title">
                                <h5>Safety Riding</h5>
                                <span class="summary-subtitle">Vehicle Violations</span>
                            </div>
                        </div>
                        <div class="summary-card-body">
                            <div class="summary-stats-row">
                                <div class="summary-stat">
                                    <span
                                        class="stat-value stat-open">{{ $dashboardData['safety_riding']['open'] ?? 0 }}</span>
                                    <span class="stat-label">Open</span>
                                </div>
                                <div class="summary-stat">
                                    <span
                                        class="stat-value stat-progress">{{ $dashboardData['safety_riding']['progress'] ?? 0 }}</span>
                                    <span class="stat-label">Progress</span>
                                </div>
                                <div class="summary-stat">
                                    <span
                                        class="stat-value stat-closed">{{ $dashboardData['safety_riding']['closed'] ?? 0 }}</span>
                                    <span class="stat-label">Closed</span>
                                </div>
                            </div>
                            <div class="violation-badge">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>{{ $dashboardData['safety_riding']['total_violations'] ?? 0 }} Total
                                    Pelanggaran</span>
                            </div>
                        </div>
                        <a href="{{ url('/manager/safety-riding') }}" class="summary-card-link">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <!-- Program Safety Overall Summary Card -->
                    <div class="summary-card program-safety-card fade-up" style="transition-delay: 0.4s;">

                        <div class="summary-card-header">
                            <div class="summary-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="summary-title">
                                <h5>Program Safety</h5>
                                <span class="summary-subtitle">Overall Summary</span>
                            </div>
                        </div>

                        <div class="summary-card-body">
                            <div class="overall-stats">
                                <div class="overall-stat-big">
                                    <span
                                        class="big-number">{{ $dashboardData['program_safety']['total_activities'] ?? 0 }}</span>
                                    <span class="big-label">Total Program</span>
                                </div>
                                <div class="overall-completion">
                                    <div class="completion-ring"
                                        style="--progress: {{ $dashboardData['program_safety']['completion_rate'] ?? 0 }};">
                                        <span>{{ $dashboardData['program_safety']['completion_rate'] ?? 0 }}%</span>
                                    </div>
                                    <span class="completion-label">Completion</span>
                                </div>
                            </div>

                            <div class="mini-stats-row">
                                <div class="mini-stat">
                                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                    <span>{{ $dashboardData['program_safety']['total_completed'] ?? 0 }} Selesai</span>
                                </div>
                                <div class="mini-stat">
                                    <i class="fas fa-clock" style="color: #f59e0b;"></i>
                                    <span>{{ $dashboardData['program_safety']['total_pending'] ?? 0 }} Pending</span>
                                </div>
                            </div>
                        </div>

                        <a href="/manager/programsafety" class="summary-card-link">
                            <span>Lihat Detail</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>

                    </div>

                </div>

            </div>
        </div>






        <div class="update-time fade-up">
            <span id="last-update">
                Data periode:
                @if(request()->hasAny(['start_date', 'end_date']))
                    {{ date('d M Y', strtotime(request('start_date', date('Y-m-d', strtotime('-30 days'))))) }}
                    -
                    {{ date('d M Y', strtotime(request('end_date', date('Y-m-d')))) }}
                @else
                    {{ now()->format('d M Y') }}
                @endif
                | Terakhir diperbarui: {{ now()->format('H:i:s') }}
            </span> <button onclick="refreshDashboard()" style="margin-left: 10px; padding: 2px 8px; font-size: 0.8rem; background: #3b82f6; color: white;
                                    border: none; border-radius: 4px; cursor: pointer;">
                Refresh
            </button>
        </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: "0px 0px -60px 0px"
            });

            document.querySelectorAll('.fade-up').forEach(el => {
                observer.observe(el);
            });

            // Set tanggal akhir default ke hari ini
            const endDateInput = document.getElementById('end_date');
            const startDateInput = document.getElementById('start_date');

            if (!endDateInput.value) {
                endDateInput.value = new Date().toISOString().split('T')[0];
            }

            // Set tanggal awal default ke 30 hari sebelumnya
            if (!startDateInput.value) {
                const thirtyDaysAgo = new Date();
                thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
                startDateInput.value = thirtyDaysAgo.toISOString().split('T')[0];
            }

            // Validasi tanggal
            startDateInput.addEventListener('change', function () {
                if (endDateInput.value && new Date(this.value) > new Date(endDateInput.value)) {
                    alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
                    this.value = endDateInput.value;
                }
            });

            endDateInput.addEventListener('change', function () {
                if (startDateInput.value && new Date(this.value) < new Date(startDateInput.value)) {
                    alert('Tanggal akhir tidak boleh lebih kecil dari tanggal awal');
                    this.value = startDateInput.value;
                }
            });

            // Auto-refresh setiap 5 menit
            setInterval(refreshDashboard, 300000);

            // Cek apakah hari ini ada loss day
            checkTodayLossDay();
        });

        function resetFilters() {
            const filterForm = document.getElementById('filterForm');
            if (filterForm) filterForm.reset();

            // Set nilai default
            const today = new Date().toISOString().split('T')[0];
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

            const endDateEl = document.getElementById('end_date');
            const startDateEl = document.getElementById('start_date');
            const sectionEl = document.getElementById('section');
            const statusEl = document.getElementById('status');

            if (endDateEl) endDateEl.value = today;
            if (startDateEl) startDateEl.value = thirtyDaysAgo.toISOString().split('T')[0];
            if (sectionEl) sectionEl.selectedIndex = 0;
            if (statusEl) statusEl.value = '';

            // Re-apply filter automatically after reset
            if (filterForm) filterForm.submit();
        }


        // function resetFilters() {
        //     document.getElementById('filterForm').reset();

        //     // Set nilai default
        //     const today = new Date().toISOString().split('T')[0];
        //     const thirtyDaysAgo = new Date();
        //     thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

        //     document.getElementById('end_date').value = today;
        //     document.getElementById('start_date').value = thirtyDaysAgo.toISOString().split('T')[0];
        //     document.getElementById('section').value = '';
        //     document.getElementById('status').value = '';
        // }

        function refreshDashboard() {
            const lastUpdateEl = document.getElementById('last-update');
            const refreshBtn = lastUpdateEl.nextElementSibling;

            // Tampilkan loading
            refreshBtn.innerHTML = '<span class="loading-spinner"></span>Loading...';
            refreshBtn.disabled = true;

            // Ambil nilai filter
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const section = document.getElementById('section').value;
            const status = document.getElementById('status').value;

            // Build URL dengan parameter
            let url = '/manager/dashboard/data';
            const params = new URLSearchParams();

            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            if (section) params.append('section', section);
            if (status) params.append('status', status);

            const queryString = params.toString();
            if (queryString) {
                url += '?' + queryString;
            }

            console.log('Fetching from:', url);

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        updateDashboardData(data.data);

                        // Update timestamp
                        const now = new Date();
                        const startDateFormatted = startDate ? new Date(startDate).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        }) : 'Semua';

                        const endDateFormatted = endDate ? new Date(endDate).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric'
                        }) : 'Semua';

                        lastUpdateEl.innerHTML = `
                                                        Data periode: ${startDateFormatted} - ${endDateFormatted}
                                                        | Terakhir diperbarui: ${now.toLocaleTimeString('id-ID')}
                                                    `;

                        // Cek loss day
                        checkTodayLossDay();
                    } else {
                        throw new Error(data.message || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('Error refreshing dashboard:', error);
                    lastUpdateEl.innerHTML += ` <span style="color: #dc2626;">(Error: ${error.message})</span>`;
                })
                .finally(() => {
                    refreshBtn.innerHTML = 'Refresh';
                    refreshBtn.disabled = false;
                });
        }

        function updateDashboardData(data) {
            console.log('Updating dashboard with data:', data);

            // Update total safety days
            const totalDaysEl = document.getElementById('total-safety-days');
            if (totalDaysEl) {
                totalDaysEl.textContent = data.total_safety_days || 0;
            }

            const totalCard = document.getElementById('total-safety-card');
            if (totalCard) {
                if (data.total_safety_days == 0) {
                    totalCard.classList.add('today-warning');
                    const cardLabel = totalCard.querySelector('.card-body .card-label');
                    if (cardLabel) {
                        cardLabel.textContent = 'Hari ini ada loss day';
                    }
                } else {
                    totalCard.classList.remove('today-warning');
                    const cardLabel = totalCard.querySelector('.card-body .card-label');
                    if (cardLabel) {
                        cardLabel.textContent = `Streak: ${data.current_streak_days || 0} hari`;
                    }
                }

                // Update reset info
                const header = totalCard.querySelector('.card-header');
                if (header) {
                    let resetBadge = header.querySelector('.reset-badge');
                    let resetInfo = header.querySelector('.reset-info');

                    if (data.last_reset_date && data.last_reset_date !== 'No reset yet') {
                        if (!resetBadge) {
                            resetBadge = document.createElement('span');
                            resetBadge.className = 'reset-badge';
                            resetBadge.textContent = 'RESET';
                            header.appendChild(resetBadge);
                        }

                        if (!resetInfo) {
                            resetInfo = document.createElement('small');
                            resetInfo.className = 'reset-info';
                            header.appendChild(resetInfo);
                        }
                        resetInfo.textContent = `Terakhir: ${data.last_reset_date}`;
                    } else {
                        if (resetBadge) resetBadge.remove();
                        if (resetInfo) resetInfo.remove();
                    }
                }
            }

            // Update incident counts - Mapping antara key JSON dan ID HTML
            if (data.incident_counts) {
                const categoryMapping = {
                    'Work Accident (Loss day)': 'card-work-accident-loss-day',
                    'Work Accident (Light)': 'card-work-accident-light',
                    'Traffic Accident': 'card-traffic-accident',
                    'Fire Accident': 'card-fire-accident',
                    'Forklift Accident': 'card-forklift-accident',
                    'Molten Spill Incident': 'card-molten-spill-incident',
                    'Property Damage Incident': 'card-property-damage-incident'
                };

                Object.keys(data.incident_counts).forEach(category => {
                    const cardId = categoryMapping[category];
                    if (!cardId) {
                        console.warn('No mapping found for category:', category);
                        return;
                    }

                    const card = document.getElementById(cardId);
                    if (card) {
                        const count = data.incident_counts[category];
                        const countEl = card.querySelector('.incident-count');

                        if (countEl) {
                            countEl.textContent = count;
                        }

                        // Update card styling
                        if (count > 0) {
                            card.classList.add('has-incident');
                            card.classList.remove('no-incident');
                        } else {
                            card.classList.add('no-incident');
                            card.classList.remove('has-incident');
                        }
                    } else {
                        console.warn('Card not found for ID:', cardId);
                    }
                });
            }
        }


        function checkTodayLossDay() {

            // Ambil elemen tanggal reset
            const resetInfo = document.getElementById('last-reset-date');

            // Format tanggal hari ini (Indonesia)
            const today = new Date();
            const todayString = today.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });

            // Hapus notifikasi lama
            const oldNotif = document.querySelector('.loss-day-notification');
            if (oldNotif) oldNotif.remove();

            // Jika ADA reset info
            if (resetInfo) {

                const resetText = resetInfo.innerText.replace('Terakhir:', '').trim();

                // 🔴 JIKA RESET = HARI INI
                if (resetText === todayString) {
                    showRedAlert();
                    return;
                }
            }

            // 🟢 JIKA BUKAN HARI INI
            showGreenAlert();
        }

        function showRedAlert() {
            const notif = document.createElement('div');
            notif.className = 'loss-day-notification';
            notif.style.cssText = `
                                position: fixed;
                                top: 70px;
                                right: 20px;
                                background: #dc2626;
                                color: white;
                                padding: 14px 18px;
                                border-radius: 8px;
                                box-shadow: 0 4px 12px rgba(220,38,38,0.3);
                                z-index: 1000;
                                animation: slideIn 0.3s ease-out;
                            `;
            notif.innerHTML = `
                                <strong>⚠️ PERINGATAN!</strong><br>
                                Terdapat Work Accident (Loss day) <b>hari ini</b>.<br>
                                Total Safety Work Day akan direset besok.
                                <button onclick="this.parentElement.remove()"
                                    style="position:absolute;top:6px;right:8px;background:none;border:none;color:white;font-size:16px;cursor:pointer;">
                                    ×
                                </button>
                            `;
            document.body.appendChild(notif);

            setTimeout(() => notif.remove(), 10000);
        }

        function showGreenAlert() {
            const notif = document.createElement('div');
            notif.className = 'loss-day-notification';
            notif.style.cssText = `
                                position: fixed;
                                top: 70px;
                                right: 20px;
                                background: #16a34a;
                                color: white;
                                padding: 12px 16px;
                                border-radius: 8px;
                                box-shadow: 0 4px 12px rgba(22,163,74,0.3);
                                z-index: 1000;
                            `;
            notif.innerHTML = `
                                <strong>✅ BAGUS!</strong><br>
                                Hari ini tidak ada Accident.
                            `;
            document.body.appendChild(notif);

            setTimeout(() => notif.remove(), 5000);
        }


        // Tambahkan style untuk animasi
        const style = document.createElement('style');
        style.textContent = `
                                        @keyframes slideIn {
                                            from { transform: translateX(100%); opacity: 0; }
                                            to { transform: translateX(0); opacity: 1; }
                                        }

                                        .filter-input[type="date"]::-webkit-calendar-picker-indicator {
                                            cursor: pointer;
                                            opacity: 0.6;
                                        }

                                        .filter-input[type="date"]::-webkit-calendar-picker-indicator:hover {
                                            opacity: 1;
                                        }
                                    `;
        document.head.appendChild(style);
    </script>

@endsection