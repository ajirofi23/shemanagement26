<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SHE Policies – PT AICC</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #ffffff;
            color: #1e293b;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .page-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 3.75rem 1rem;
            margin-bottom: 3rem;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }

        .page-header h1 {
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            font-size: 2.6rem;
            letter-spacing: -0.6px;
            background: linear-gradient(to right, #1e293b, #475569);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .page-header p {
            max-width: 720px;
            margin: 0.85rem auto 0;
            color: #475569;
            font-weight: 500;
            font-size: 1.05rem;
        }

        .page-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 70px;
            height: 3px;
            background: linear-gradient(90deg, #64748b, #94a3b8);
            border-radius: 2px;
            transform: translateX(-50%);
        }

        .policy-card {
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 1.85rem;
            transition: all 0.45s cubic-bezier(0.16, 1, 0.3, 1);
            background: #ffffff;
            opacity: 0;
            transform: translateY(16px);
            animation: fadeInUp 0.75s forwards;
            box-shadow: 0 4px 12px rgba(0,0,0,0.025);
            position: relative;
            overflow: hidden;
        }

        .policy-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, #64748b, #94a3b8);
            opacity: 0;
            transition: opacity 0.35s ease;
        }

        .policy-card:hover {
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.08);
            transform: translateY(-8px);
            border-color: #cbd5e1;
        }

        .policy-card:hover::before {
            opacity: 1;
        }

        /* Staggered entrance — now supports up to 9 cards */
        .policy-card:nth-child(1) { animation-delay: 0.1s; }
        .policy-card:nth-child(2) { animation-delay: 0.18s; }
        .policy-card:nth-child(3) { animation-delay: 0.26s; }
        .policy-card:nth-child(4) { animation-delay: 0.34s; }
        .policy-card:nth-child(5) { animation-delay: 0.42s; }
        .policy-card:nth-child(6) { animation-delay: 0.50s; }
        .policy-card:nth-child(7) { animation-delay: 0.58s; }
        .policy-card:nth-child(8) { animation-delay: 0.66s; }
        .policy-card:nth-child(9) { animation-delay: 0.74s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .policy-card h5 {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.8rem;
            position: relative;
            display: inline-block;
            padding-bottom: 4px;
            font-size: 1.15rem;
        }

        .policy-card h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2.5px;
            background: linear-gradient(to right, #64748b, #94a3b8);
            border-radius: 1px;
            transition: width 0.45s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .policy-card:hover h5::after {
            width: 100%;
        }

        .policy-card p {
            color: #475569;
            margin-bottom: 0;
            font-size: 0.98rem;
            line-height: 1.65;
        }

        .btn-outline-primary {
            border: 2px solid #64748b;
            color: #1e293b;
            font-weight: 600;
            padding: 0.65rem 1.5rem;
            border-radius: 16px;
            transition: all 0.35s ease;
            background: transparent;
            font-size: 1.05rem;
            letter-spacing: 0.3px;
        }

        .btn-outline-primary:hover {
            background-color: #64748b;
            color: white;
            box-shadow: 0 8px 20px rgba(100, 116, 139, 0.35);
            transform: translateY(-3px);
        }

        footer {
            background-color: #fafbfd;
            border-top: 1px solid #f1f5f9;
            margin-top: 3.5rem;
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }

        /* Additional subtle enhancements */
        .container {
            max-width: 960px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">SHE Department – PT AICC</a>
        </div>
    </nav>

    <header class="page-header">
        <div class="container">
            <h1>SHE Policies</h1>
            <p class="text-muted mb-0">Comprehensive guidelines to ensure a safe, healthy, and sustainable workplace.</p>
        </div>
    </header>

    <main class="container mb-5">
        <div class="row g-4">
            <div class="col-12">
                <div class="policy-card">
                    <h5 class="mb-2">General SHE Policy</h5>
                    <p>Commitment to zero incidents through proactive risk management, legal compliance, and continuous SHE improvement aligned with international standards.</p>
                </div>
            </div>
            <div class="col-12">
                <div class="policy-card">
                    <h5 class="mb-2">Occupational Health Policy</h5>
                    <p>Ensuring employee well-being through health surveillance, ergonomic assessments, mental health support, and exposure control programs.</p>
                </div>
            </div>
            <div class="col-12">
                <div class="policy-card">
                    <h5 class="mb-2">Environmental Policy</h5>
                    <p>Minimizing environmental impact via waste reduction, energy efficiency, water conservation, and responsible emissions management.</p>
                </div>
            </div>
            <div class="col-12">
                <div class="policy-card">
                    <h5 class="mb-2">Workplace Safety Policy</h5>
                    <p>Enforcing safe work practices, hazard identification, emergency preparedness, and mandatory PPE usage across all operational areas.</p>
                </div>
            </div>
            <div class="col-12">
                <div class="policy-card">
                    <h5 class="mb-2">Contractor & Visitor SHE Management</h5>
                    <p>All external parties must comply with PT AICC SHE standards, complete orientation, and adhere to site-specific safety protocols.</p>
                </div>
            </div>
            <div class="col-12">
                <div class="policy-card">
                    <h5 class="mb-2">SHE Training & Competency</h5>
                    <p>Regular training programs to build awareness, develop SHE competencies, and empower employees as safety ambassadors.</p>
                </div>
            </div>
            <div class="col-12">
                <div class="policy-card">
                    <h5 class="mb-2">Emergency Response & Crisis Management</h5>
                    <p>Established protocols for fire, chemical spills, medical emergencies, and natural disasters, with regular drills and clear chain of command.</p>
                </div>
            </div>
            <div class="col-12">
                <div class="policy-card">
                    <h5 class="mb-2">Hazard Reporting & Incident Investigation</h5>
                    <p>Encouraging proactive reporting of near-misses and incidents, followed by root cause analysis and corrective action implementation.</p>
                </div>
            </div>
            <div class="col-12">
                <div class="policy-card">
                    <h5 class="mb-2">Sustainability & Community Engagement</h5>
                    <p>Promoting eco-friendly operations and active participation in community health and environmental initiatives beyond regulatory requirements.</p>
                </div>
            </div>
            <div class="mt-5 text-center">
                <a href="{{ url('/') }}" class="btn btn-outline-primary">Back to Home</a>
            </div>
        </div>
    </main>

    <footer class="text-center text-muted py-4 border-top">
        © {{ date('Y') }} SHE Department – PT AICC. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>