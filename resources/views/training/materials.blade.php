<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Training Materials – PT AICC</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #ffffff;
            color: #1e293b;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .navbar {
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1030;
            transition: box-shadow 0.3s ease;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.4rem;
            color: #1e40af;
            letter-spacing: -0.5px;
        }

        .page-header {
            padding: 3.25rem 1rem;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-radius: 20px;
            margin-top: 1.75rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(29,78,216,0.03) 0%, transparent 70%);
            z-index: 0;
        }

        .page-header > * {
            position: relative;
            z-index: 1;
        }

        .page-header h1 {
            font-weight: 800;
            font-size: 2.1rem;
            color: #0f172a;
            margin-bottom: 0.8rem;
            letter-spacing: -0.7px;
        }

        .page-header p {
            color: #475569;
            max-width: 750px;
            margin: 0 auto;
            font-size: 1.02rem;
        }

        /* Material Card Animation */
        .material-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 1.75rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 6px 18px rgba(0,0,0,0.05);
            transition: all 0.45s cubic-bezier(0.16, 1, 0.3, 1);
            height: 100%;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s forwards;
            position: relative;
            overflow: hidden;
        }

        .material-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 40px rgba(0,0,0,0.11);
            border-color: #d1d5db;
        }

        /* Staggered animation */
        .material-card:nth-child(1) { animation-delay: 0.1s; }
        .material-card:nth-child(2) { animation-delay: 0.18s; }
        .material-card:nth-child(3) { animation-delay: 0.26s; }
        .material-card:nth-child(4) { animation-delay: 0.34s; }
        .material-card:nth-child(5) { animation-delay: 0.42s; }
        .material-card:nth-child(6) { animation-delay: 0.5s; }

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

        .material-icon {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1.1rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            transition: all 0.35s ease;
        }

        .material-card:hover .material-icon {
            transform: scale(1.08);
            background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
            color: #334155;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .material-card h5 {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.85rem;
            font-size: 1.15rem;
        }

        .material-card p {
            color: #475569;
            margin-bottom: 1rem;
            font-size: 0.98rem;
        }

        a.material-link {
            text-decoration: none;
            color: #334155;
            font-weight: 600;
            position: relative;
            padding: 0.25rem 0;
            display: inline-block;
            transition: color 0.25s ease;
        }

        a.material-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 1.5px;
            background: linear-gradient(to right, #64748b, #94a3b8);
            transition: width 0.35s ease;
        }

        a.material-link:hover {
            color: #1e293b;
        }

        a.material-link:hover::after {
            width: 100%;
        }

        .footer {
            margin-top: 3.5rem;
            padding: 2.25rem 0 1.5rem;
            border-top: 1px solid #f1f5f9;
            color: #64748b;
            font-size: 0.95rem;
            background-color: #fafbfd;
        }

        /* Optional floating pulse for first card */
        .material-card:first-child {
            animation: fadeInUp 0.8s forwards, float 6s ease-in-out infinite 0.5s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">SHE Department – PT AICC</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="nav" class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#features">Capabilities</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}#contact">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="container">
        <section class="page-header">
            <h1>Training Materials</h1>
            <p>Browse essential SHE training resources for onboarding, refreshers, and certification support. Contact the SHE team for the latest updates or custom sessions.</p>
        </section>
    </div>

    <!-- Materials Grid -->
    <section class="container py-4">
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="material-card h-100">
                    <div class="material-icon"><i class="bi bi-journal-check"></i></div>
                    <h5 class="fw-bold mb-2" style="color:#0f172a;">Safety Induction</h5>
                    <p class="text-muted mb-3">Introductory materials for new employees and contractors.</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a class="material-link" href="#">Onboarding Slide Deck (PDF)</a></li>
                        <li class="mb-2"><a class="material-link" href="#">General Site Rules (PDF)</a></li>
                        <li><a class="material-link" href="#">Emergency Procedures (PDF)</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="material-card h-100">
                    <div class="material-icon"><i class="bi bi-shield"></i></div>
                    <h5 class="fw-bold mb-2" style="color:#0f172a;">PPE & Safety Practices</h5>
                    <p class="text-muted mb-3">Guidelines for proper PPE selection, use, and maintenance.</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a class="material-link" href="#">PPE Quick Reference (PDF)</a></li>
                        <li class="mb-2"><a class="material-link" href="#">Safe Lifting Techniques (PDF)</a></li>
                        <li><a class="material-link" href="#">Housekeeping & 5S (PDF)</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="material-card h-100">
                    <div class="material-icon"><i class="bi bi-fire"></i></div>
                    <h5 class="fw-bold mb-2" style="color:#0f172a;">Fire Safety</h5>
                    <p class="text-muted mb-3">Fire prevention, alarm response, and extinguisher use.</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a class="material-link" href="#">Fire Prevention Basics (PDF)</a></li>
                        <li class="mb-2"><a class="material-link" href="#">Evacuation Drill Checklist (PDF)</a></li>
                        <li><a class="material-link" href="#">Extinguisher Types & Use (PDF)</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="material-card h-100">
                    <!-- 🔸 Ikon diperbarui dari bi-beaker ke bi-flask -->
                    <div class="material-icon"><i class="bi bi-flask"></i></div>
                    <h5 class="fw-bold mb-2" style="color:#0f172a;">Chemical Handling</h5>
                    <p class="text-muted mb-3">Standard procedures for storage, labeling, and spill response.</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a class="material-link" href="#">GHS & SDS Overview (PDF)</a></li>
                        <li class="mb-2"><a class="material-link" href="#">Chemical Storage Guide (PDF)</a></li>
                        <li><a class="material-link" href="#">Spill Response Flow (PDF)</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="material-card h-100">
                    <div class="material-icon"><i class="bi bi-clipboard-data"></i></div>
                    <h5 class="fw-bold mb-2" style="color:#0f172a;">Incident Reporting</h5>
                    <p class="text-muted mb-3">Near-miss reporting, investigation, and corrective actions.</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a class="material-link" href="#">Near-Miss Form (PDF)</a></li>
                        <li class="mb-2"><a class="material-link" href="#">Root Cause Analysis (PDF)</a></li>
                        <li><a class="material-link" href="#">CAPA Template (Excel)</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="material-card h-100">
                    <div class="material-icon"><i class="bi bi-people"></i></div>
                    <h5 class="fw-bold mb-2" style="color:#0f172a;">Emergency & Drills</h5>
                    <p class="text-muted mb-3">Roles, communication, and drill planning resources.</p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><a class="material-link" href="#">Emergency Roles (PDF)</a></li>
                        <li class="mb-2"><a class="material-link" href="#">Drill Planner (PDF)</a></li>
                        <li><a class="material-link" href="#">Post-Drill Review (PDF)</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center text-muted">
            © {{ date('Y') }} SHE Department – PT AICC. All rights reserved.
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>