<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SHE Department – Safety, Health & Environment excellence at PT AICC.">
    <title>SHE Department – PT AICC</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --accent: #38bdf8;
            --slate-900: #0f172a;
            --slate-600: #475569;
            --glass: rgba(255, 255, 255, 0.8);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #ffffff;
            color: var(--slate-900);
            line-height: 1.7;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* Modern Navbar */
        .navbar {
            background: var(--glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.2rem 0;
            position: sticky;
            top: 0;
            z-index: 1030;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary-dark);
            letter-spacing: -0.5px;
            transition: transform 0.3s ease;
        }
        
        .navbar-brand:hover { transform: scale(1.05); }

        .nav-link {
            color: var(--slate-600) !important;
            font-weight: 600;
            padding: 0.6rem 1.2rem !important;
            margin: 0 0.2rem;
            border-radius: 10px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary) !important;
            background: rgba(37, 99, 235, 0.05);
        }

        /* Enhanced Hero Section */
        .hero {
            padding: 8rem 2rem;
            background: radial-gradient(circle at top right, #f0f9ff 0%, #ffffff 50%),
                        radial-gradient(circle at bottom left, #eff6ff 0%, #ffffff 50%);
            border-radius: 40px;
            margin: 1.5rem auto 5rem;
            max-width: 1320px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.03);
            transition: box-shadow 0.5s ease;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -10%; right: -10%;
            width: 400px; height: 400px;
            background: var(--accent);
            filter: blur(120px);
            opacity: 0.1;
            z-index: 0;
            animation: pulse-glow 8s infinite alternate;
        }

        @keyframes pulse-glow {
            from { opacity: 0.1; transform: scale(1); }
            to { opacity: 0.2; transform: scale(1.2); }
        }

        .hero h1 {
            font-weight: 800;
            font-size: clamp(2.5rem, 5vw, 3.8rem);
            line-height: 1.1;
            letter-spacing: -1.5px;
            background: linear-gradient(135deg, #0f172a 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
            animation: slideInLeft 0.8s ease-out;
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .btn-primary {
            padding: 1rem 2.5rem;
            border-radius: 14px;
            font-weight: 700;
            background: var(--primary);
            border: none;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.3);
            background: var(--primary-dark);
        }

        /* Modernized Feature Cards */
        .feature-card {
            background: #ffffff;
            border-radius: 30px;
            padding: 3rem 2rem;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid #f1f5f9;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .feature-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.1);
            border-color: #dbeafe;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 22px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: var(--primary);
            font-size: 2.2rem;
            transition: all 0.4s ease;
        }

        .feature-card:hover .feature-icon {
            background: var(--primary);
            color: white;
            transform: scale(1.1) rotate(10deg);
        }

        .feature-card h3 {
            font-weight: 700;
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }

        .btn-view {
            background: #f8fafc;
            color: var(--slate-600);
            border: 1px solid #e2e8f0;
            padding: 0.8rem;
            border-radius: 12px;
            font-weight: 600;
            margin-top: auto;
            transition: all 0.3s;
        }

        .feature-card:hover .btn-view {
            background: var(--slate-900);
            color: white;
            border-color: var(--slate-900);
        }

        /* Commitment Box */
        .commitment-box {
            background: #ffffff;
            border-radius: 32px;
            padding: 3.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 20px 40px rgba(0,0,0,0.03);
            position: relative;
            transition: all 0.4s ease;
        }
        
        .commitment-box:hover { transform: scale(1.01); }

        .commitment-box li {
            font-weight: 500;
            padding: 0.8rem 0;
            display: flex;
            align-items: flex-start;
            transition: transform 0.2s ease;
        }
        
        .commitment-box li:hover { transform: translateX(10px); }

        /* PDF Modal Improvements */
        .modal.fade .modal-dialog {
            transform: scale(0.9);
            transition: transform 0.3s ease-out;
        }
        .modal.show .modal-dialog { transform: scale(1); }

        .modal-content { border-radius: 35px; border: none; overflow: hidden; }
        .modal-header { border: none; padding: 1.5rem 2rem; }
        
        .modal-body { 
            background: #f1f5f9; 
            height: 80vh; 
            padding: 0; 
            overflow: hidden;
        }

        #previewFrame {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
        
        /* New Animation Keyframes */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .hero img {
            animation: float 6s ease-in-out infinite;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.1));
            transition: filter 0.3s ease;
        }
        
        .hero img:hover { filter: drop-shadow(0 25px 50px rgba(37, 99, 235, 0.2)); }

        .fade-up {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Animation Delay for Cards */
        .row > div:nth-child(1) .fade-up { transition-delay: 0.1s; }
        .row > div:nth-child(2) .fade-up { transition-delay: 0.2s; }
        .row > div:nth-child(3) .fade-up { transition-delay: 0.3s; }
        .row > div:nth-child(4) .fade-up { transition-delay: 0.4s; }
        .row > div:nth-child(5) .fade-up { transition-delay: 0.5s; }

        @media (max-width: 768px) {
            .hero { padding: 4rem 1.5rem; margin: 1rem; border-radius: 25px; }
            .hero h1 { font-size: 2.2rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <span style="color: var(--primary)">SHE</span> – PT AICC
            </a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#certifications">Sertifikasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    @auth
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle fw-bold text-primary" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->nama }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 animate__animated animate__fadeInUp">
                                <li><a class="dropdown-item p-3" href="{{ url('/dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item p-3 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3">
                            <a class="btn btn-primary btn-sm px-4 py-2" href="{{ url('/login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <section class="hero fade-up">
            <div class="row align-items-center">
                <div class="col-lg-7 text-center text-lg-start px-lg-5" style="z-index: 1;">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3 fw-bold">Safety First, Always</span>
                    <h1>Excellence in Safety & Environment</h1>
                    <p class="lead text-muted mb-5">Mendorong operasional berkelanjutan untuk melindungi manusia serta lingkungan melalui standar kepatuhan tertinggi.</p>
                    <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                        <a href="#certifications" class="btn btn-primary btn-lg">Lihat Sertifikasi</a>
                        <a href="#about" class="btn btn-outline-secondary btn-lg rounded-4 px-4" style="border-width: 2px;">Tentang Kami</a>
                    </div>
                </div>
                <div class="col-lg-5 mt-5 mt-lg-0 text-center">
                    <img src="{{ asset('template/logo/logo.png') }}" alt="PT AICC Logo" style="max-width: 280px; width: 100%;">
                </div>
            </div>
        </section>
    </div>

    <section id="certifications" class="container py-5">
        <div class="text-center mb-5 fade-up">
            <h6 class="text-primary fw-bold text-uppercase tracking-widest">Compliance</h6>
            <h2 class="fw-bold display-6">Kebijakan & Sertifikasi</h2>
            <div class="mx-auto bg-primary rounded-pill" style="width: 60px; height: 4px; margin-top: 1rem;"></div>
        </div>
        
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card fade-up text-center">
                    <div>
                        <div class="feature-icon"><i class="bi bi-journal-check"></i></div>
                        <h3>Kebijakan K3</h3>
                        <p class="text-muted">Komitmen manajemen terhadap ZERO accident dan kesehatan kerja bagi seluruh karyawan.</p>
                    </div>
                    <button class="btn-view" onclick="openPreview('Kebijakan K3 AICC', '{{ asset('kebijakan.pdf') }}')">
                        <i class="bi bi-eye me-2"></i> Quick View
                    </button>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card fade-up text-center">
                    <div>
                        <div class="feature-icon"><i class="bi bi-globe-americas"></i></div>
                        <h3>ISO 14001:2015</h3>
                        <p class="text-muted">Sertifikasi Sistem Manajemen Lingkungan untuk proses produksi Casting Parts yang ramah lingkungan.</p>
                    </div>
                    <button class="btn-view" onclick="openPreview('Sertifikat ISO 14001', '{{ asset('iso14001.pdf') }}')">
                        <i class="bi bi-eye me-2"></i> Quick View
                    </button>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card fade-up text-center">
                    <div>
                        <div class="feature-icon"><i class="bi bi-gear-wide-connected"></i></div>
                        <h3>IATF 16949</h3>
                        <p class="text-muted">Standar sistem manajemen mutu internasional khusus untuk industri komponen otomotif.</p>
                    </div>
                    <button class="btn-view" onclick="openPreview('Sertifikat IATF 16949', '{{ asset('iatf.pdf') }}')">
                        <i class="bi bi-eye me-2"></i> Quick View
                    </button>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card fade-up text-center">
                    <div>
                        <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                        <h3>SMK3</h3>
                        <p class="text-muted">Sistem Manajemen Keselamatan dan Kesehatan Kerja sesuai regulasi Pemerintah Indonesia.</p>
                    </div>
                    <button class="btn-view" onclick="openPreview('Sertifikat SMK3', '{{ asset('smk3.pdf') }}')">
                        <i class="bi bi-eye me-2"></i> Quick View
                    </button>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="feature-card fade-up text-center">
                    <div>
                        <div class="feature-icon"><i class="bi bi-award"></i></div>
                        <h3>PROPER</h3>
                        <p class="text-muted">Penghargaan kinerja pengelolaan lingkungan dari Kementerian Lingkungan Hidup & Kehutanan.</p>
                    </div>
                    <button class="btn-view" onclick="openPreview('Sertifikat PROPER', '{{ asset('proper.pdf') }}')">
                        <i class="bi bi-eye me-2"></i> Quick View
                    </button>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content shadow-2xl">
                <div class="modal-header bg-white border-0">
                    <div>
                        <h5 class="modal-title fw-bold" id="modalTitle">Preview Document</h5>
                        <span class="badge bg-light text-muted fw-normal">PT Asian Isuzu Casting Center</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe src="" id="previewFrame"></iframe>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Tutup</button>
                    <a href="" id="downloadBtn" class="btn btn-primary px-4" download>
                        <i class="bi bi-download me-2"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <section id="about" class="container py-5 mb-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 fade-up">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="feature-card p-4 text-center border-0 shadow-sm" style="background: #f8fafc;">
                            <div class="feature-icon mb-3" style="width: 50px; height: 50px; font-size: 1.5rem;"><i class="bi bi-shield-fill-plus"></i></div>
                            <h4 class="fw-bold text-primary mb-1">99%</h4>
                            <p class="small text-muted mb-0">Indeks Keamanan Kerja</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="feature-card p-4 text-center border-0 shadow-sm" style="background: #f0fdf4;">
                            <div class="feature-icon mb-3" style="width: 50px; height: 50px; font-size: 1.5rem; color: #16a34a; background: #dcfce7;"><i class="bi bi-leaf"></i></div>
                            <h4 class="fw-bold mb-1" style="color: #16a34a;">Green</h4>
                            <p class="small text-muted mb-0">Status PROPER KLHK</p>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="commitment-box p-4 border-0 shadow-sm bg-primary text-white">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-lightning-charge-fill display-5 opacity-50"></i>
                                </div>
                                <div class="ms-4">
                                    <h5 class="fw-bold mb-1 text-white">Quick Response Team</h5>
                                    <p class="small mb-0 opacity-75">Tim tanggap darurat kami bersiaga 24/7 dengan peralatan medis dan pemadam api mutakhir.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 fade-up">
                <h2 class="fw-bold mb-4 display-6">About <span class="text-primary">SHE</span> Department</h2>
                <p class="lead text-dark fw-medium">Departemen SHE adalah pilar utama komitmen PT AICC terhadap keunggulan operasional dan perlindungan aset manusia.</p>
                <p class="text-muted mb-4">Kami mengintegrasikan teknologi pemantauan real-time dan audit berkelanjutan dalam setiap proses pengecoran (casting). Fokus kami adalah memitigasi risiko tinggi, mengelola limbah industri secara bertanggung jawab, dan memastikan setiap individu bekerja dalam ekosistem yang mendukung kesehatan fisik dan mental.</p>
                
                <div class="row g-4">
                    <div class="col-sm-6">
                        <div class="p-3 border-start border-primary border-4 bg-light rounded-end">
                            <h6 class="fw-bold text-primary mb-1">Visi Kami</h6>
                            <p class="small text-muted mb-0">Menjadi tolok ukur (benchmark) industri casting nasional dalam penerapan budaya K3LL yang proaktif dan adaptif.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 border-start border-accent border-4 bg-light rounded-end">
                            <h6 class="fw-bold text-primary mb-1">Misi Kami</h6>
                            <p class="small text-muted mb-0">Mewujudkan lingkungan kerja nirlaba cidera (zero injury) dan nirlaba polusi melalui inovasi teknologi ramah lingkungan.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <p class="text-muted">Melalui filosofi **Kaikaku** dan **Kaizen**, kami terus mentransformasi sistem manajemen energi dan keselamatan kami untuk menghadapi tantangan industri manufaktur modern masa depan.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5 mb-5">
        <div class="row">
            <div class="col-lg-12 fade-up">
                <div class="commitment-box overflow-hidden">
                    <div class="position-absolute top-0 end-0 p-4 opacity-10">
                        <i class="bi bi-shield-shaded display-1 text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-4 text-primary">Komitmen Kami</h5>
                    <ul class="list-unstyled">
                        <li class="mb-4">
                            <i class="bi bi-check-circle-fill text-primary me-3 mt-1"></i> 
                            <div>
                                <strong>Zero Accident Workplace:</strong> 
                                <span class="text-muted d-block small mt-1">Menerapkan sistem pelaporan bahaya (Near-miss) secara digital dan inspeksi rutin di seluruh area furnace dan finishing untuk memastikan nihil kecelakaan kerja bagi seluruh personil.</span>
                            </div>
                        </li>
                        <li class="mb-4">
                            <i class="bi bi-check-circle-fill text-primary me-3 mt-1"></i> 
                            <div>
                                <strong>100% Regulatory Compliance:</strong> 
                                <span class="text-muted d-block small mt-1">Menjamin pemenuhan seluruh persyaratan perundangan K3, Lingkungan Hidup, dan standar internasional otomotif (IATF 16949) tanpa pengecualian.</span>
                            </div>
                        </li>
                        <li class="mb-4">
                            <i class="bi bi-check-circle-fill text-primary me-3 mt-1"></i> 
                            <div>
                                <strong>Sustainable Operations & Carbon Neutral:</strong> 
                                <span class="text-muted d-block small mt-1">Mengoptimalkan efisiensi energi melalui penggunaan sistem filtrasi udara canggih dan pengelolaan limbah B3 (pasir foundry) yang tersertifikasi untuk meminimalkan jejak karbon.</span>
                            </div>
                        </li>
                        <li class="mb-4">
                            <i class="bi bi-check-circle-fill text-primary me-3 mt-1"></i> 
                            <div>
                                <strong>Empowered Safety Heroes:</strong> 
                                <span class="text-muted d-block small mt-1">Membangun kesadaran keselamatan melalui pelatihan berkelanjutan, sertifikasi kompetensi personil K3, dan pemberian penghargaan bagi karyawan yang berkontribusi dalam perbaikan aspek keselamatan.</span>
                            </div>
                        </li>
                    </ul>
                    <div class="mt-4 p-3 bg-primary bg-opacity-10 rounded-4 text-center">
                        <span class="text-primary fw-bold italic">"Safety is not just a policy, it's our core identity. We protect each other, every day."</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 border-top bg-light">
        <div class="container text-center">
            <div class="mb-4">
                <a class="navbar-brand text-dark" href="#">SHE – <span class="text-primary">PT AICC</span></a>
            </div>
            <p class="text-muted mb-0 small">© {{ date('Y') }} SHE Department – PT Asian Isuzu Casting Center. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openPreview(title, url) {
            document.getElementById('modalTitle').innerText = title;
            const viewUrl = url + "#toolbar=1&view=FitH&scrollbar=1";
            const frame = document.getElementById('previewFrame');
            frame.src = viewUrl;
            document.getElementById('downloadBtn').href = url;
            var myModal = new bootstrap.Modal(document.getElementById('previewModal'));
            myModal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.15 });

            document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
            
            window.addEventListener('scroll', function() {
                if (window.scrollY > 50) {
                    document.querySelector('.navbar').style.padding = '0.7rem 0';
                    document.querySelector('.navbar').style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
                } else {
                    document.querySelector('.navbar').style.padding = '1.2rem 0';
                    document.querySelector('.navbar').style.boxShadow = 'none';
                }
            });
        });
    </script>
</body>
</html>