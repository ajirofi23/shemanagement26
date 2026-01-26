@extends('layout.itsidebar')

@section('content')

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ===================================================== */
        /* MODERN UI VARIABLES & RESET */
        /* ===================================================== */
        :root {
            --bg-main: #f8fafc;
            --glass-white: rgba(255, 255, 255, 0.85);
            --accent-primary: #6366f1;
            --accent-success: #10b981;
            --accent-danger: #f43f5e;
            --accent-warning: #f59e0b;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --shadow-sm: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.05);
        }

        .content {
            margin-left: 0;
            padding: 2.5rem;
            background-color: var(--bg-main);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        /* ===================================================== */
        /* ANIMATIONS */
        /* ===================================================== */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse-soft {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
        }

        .fade-up {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===================================================== */
        /* STAT CARDS - PREMIUM DESIGN */
        /* ===================================================== */
        .stat-card {
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 1.5rem;
            padding: 2.2rem 1.8rem;
            background: var(--glass-white);
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow-sm);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--shadow-lg);
            background: #ffffff;
            border-color: var(--accent-primary);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent 80%, rgba(99, 102, 241, 0.05));
            pointer-events: none;
        }

        .stat-label {
            font-weight: 700;
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1;
        }

        /* Progress Customization */
        .progress-container {
            margin-top: 1.2rem;
        }

        .progress {
            height: 10px;
            border-radius: 100px;
            background: #f1f5f9;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .progress-bar {
            border-radius: 100px;
            transition: width 1.5s cubic-bezier(0.65, 0, 0.35, 1);
        }

        /* ===================================================== */
        /* CHART CARDS */
        /* ===================================================== */
        .chart-card {
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 1.5rem;
            background: #ffffff;
            padding: 2rem;
            box-shadow: var(--shadow-sm);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .chart-card h5 {
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chart-card h5::before {
            content: '';
            width: 4px;
            height: 20px;
            background: var(--accent-primary);
            border-radius: 10px;
        }

        .chart-box {
            flex-grow: 1;
            width: 100%;
            min-height: 300px;
        }

        /* ===================================================== */
        /* DATA TABLE */
        /* ===================================================== */
        .table-container {
            margin-top: 3rem;
            background: #ffffff;
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid #f1f5f9;
        }

        .table thead th {
            background: #f8fafc;
            border: none;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 1.2rem;
        }

        .table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s ease;
        }

        .table tbody tr:hover {
            background: #fcfdff;
        }

        .badge-pill-custom {
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            border-radius: 12px;
        }
    </style>

    <div class="content">
        <div class="page-header mb-4">
            <div class="page-title fade-up">ANALYTICS DASHBOARD</div>
            <p class="page-subtitle fade-up">Insights and real-time user metrics</p>
        </div>

        <style>
            .page-title {
                font-size: 24px;
                font-weight: 800;
                margin-bottom: 2px;
                color: #0f172a;
                text-transform: uppercase;
            }

            .page-subtitle {
                color: #64748b;
                margin-bottom: 15px;
                font-size: 0.95rem;
            }
        </style>

        <div class="row g-4 mb-5 animate-in" style="animation-delay: 0.1s">
            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value text-primary">{{ $totalUsers }}</div>
                    <div class="small mt-2 text-muted fw-500"><i class="bi bi-people-fill"></i> Registered accounts</div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-label">Active Users</div>
                    <div class="stat-value text-success">{{ $activeUsers }}</div>
                    <div class="progress-container">
                        <div class="progress">
                            <div class="progress-bar bg-success"
                                style="width: {{ $totalUsers ? ($activeUsers / $totalUsers * 100) : 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-label">Inactive Users</div>
                    <div class="stat-value text-danger">{{ $inactiveUsers }}</div>
                    <div class="progress-container">
                        <div class="progress">
                            <div class="progress-bar bg-danger"
                                style="width: {{ $totalUsers ? ($inactiveUsers / $totalUsers * 100) : 0 }}%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #ffffff 0%, #fef3c7 100%);">
                    <div class="stat-label">Top Section</div>
                    <div class="stat-value text-warning" style="font-size: 1.8rem;">
                        {{ $topSectionName ?? '-' }}
                    </div>
                    <div class="fw-700 text-dark mt-1">
                        {{ $topSectionCount ?? 0 }} <span class="text-muted fw-normal small">Members</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 animate-in" style="animation-delay: 0.2s">
            <div class="col-lg-7">
                <div class="chart-card">
                    <h5>Distribution by Section</h5>
                    <div class="chart-box">
                        <canvas id="sectionChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="chart-card">
                    <h5>Account Integrity</h5>
                    <div class="chart-box">
                        <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4">
                <div class="chart-card" style="min-height: 400px;">
                    <h5>Monthly Growth Analytics</h5>
                    <div class="chart-box">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container animate-in" style="animation-delay: 0.3s">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-800 m-0">Performance Overview</h5>
                <button class="btn btn-light btn-sm rounded-pill px-3 border shadow-sm">View Full Report</button>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-3">Organization Section</th>
                            <th>User Count</th>
                            <th>Activity Rate</th>
                            <th class="text-end pe-3">Performance Tag</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topSections as $section)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-700 text-dark">{{ $section['name'] }}</div>
                                </td>
                                <td class="fw-600 text-muted">{{ $section['users'] }} Members</td>
                                <td style="width: 30%;">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="fw-800 text-primary small">{{ round($section['activePercent'], 1) }}%</span>
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-primary shadow-sm"
                                                style="width: {{ $section['activePercent'] }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    @if($section['activePercent'] > 80)
                                        <span class="badge bg-success bg-opacity-10 text-success badge-pill-custom">EXCELLENT</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning badge-pill-custom">STABLE</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        /* ===================================================== */
        /* CHART JS - REFINED CONFIGURATION */
        /* ===================================================== */
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#94a3b8';

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: { boxWidth: 8, usePointStyle: true, padding: 25, font: { weight: 600, size: 12 } }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    borderRadius: 10,
                    usePointStyle: true
                }
            }
        };

        /* 1. SECTION BAR CHART */
        new Chart(document.getElementById('sectionChart'), {
            type: 'bar',
            data: {
                labels: @json($sectionLabels),
                datasets: [{
                    label: 'Users per Section',
                    data: @json($sectionData),
                    backgroundColor: '#6366f1',
                    hoverBackgroundColor: '#4f46e5',
                    borderRadius: 8,
                    barThickness: 25
                }]
            },
            options: {
                ...commonOptions,
                plugins: { ...commonOptions.plugins, legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9', drawBorder: false } },
                    x: { grid: { display: false } }
                }
            }
        });

        /* 2. DOUGHNUT CHART */
        new Chart(document.getElementById('statusPieChart'), {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Inactive'],
                datasets: [{
                    data: [{{ $activeUsers }}, {{ $inactiveUsers }}],
                    backgroundColor: ['#10b981', '#f43f5e'],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                ...commonOptions,
                cutout: '75%',
                plugins: {
                    ...commonOptions.plugins,
                    legend: { position: 'bottom', labels: { padding: 20 } }
                }
            }
        });

        /* 3. TREND LINE CHART */
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        const gradient = ctxTrend.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: @json($trendLabels),
                datasets: [{
                    label: 'User Growth Trend',
                    data: @json($trendData),
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    borderWidth: 4,
                    fill: true,
                    tension: 0.45,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: { grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        /* STATS COUNTER & OBSERVER */
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

            // Replace animate-in with fade-up for consistency
            document.querySelectorAll('.animate-in').forEach(el => {
                el.classList.add('fade-up');
                observer.observe(el);
            });

            document.querySelectorAll('.stat-value').forEach(el => {
                const target = parseInt(el.innerText) || 0;
                if (target > 0) {
                    let current = 0;
                    const duration = 1500;
                    const step = target / (duration / 16);
                    const update = () => {
                        current += step;
                        if (current < target) {
                            el.innerText = Math.floor(current);
                            requestAnimationFrame(update);
                        } else {
                            el.innerText = target;
                        }
                    };
                    update();
                }
            });
        });
    </script>

@endsection