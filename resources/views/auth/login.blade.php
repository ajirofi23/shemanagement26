<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SHE Management</title>

    <!-- Bootstrap CDN -->
    <link id="bootstrapCSS" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <!-- Remix Icon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f0f7ff 0%, #e6f0ff 100%);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            text-align: center;
            padding: 2rem;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo img {
            width: 200px;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .logo img:hover {
            transform: scale(1.02);
        }

        .welcome-text {
            font-size: 1.2rem;
            font-weight: 500;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 1rem;
        }

        .input-wrapper input {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 16px 12px 40px; /* <-- tambah padding kiri untuk ikon */
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .input-wrapper input:focus {
            box-shadow: 0 0 0 3px rgba(74, 129, 212, 0.15);
            border-color: #4a81d4;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            pointer-events: none;
            font-size: 1.1rem;
        }
        .btn-login {
            width: 100%;
            background: #0d6efd;
            border: none;
            padding: 12px;
            font-weight: 600;
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            color: white; /* 👈 Teks jadi putih */
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(13, 110, 253, 0.3);
            color: white; /* 👈 Jaga warna teks tetap putih saat hover */
        }

        .footer {
            margin-top: 2rem;
            font-size: 0.85rem;
            color: #999;
        }

        /* Toggle Password */
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            font-size: 1.1rem;
            z-index: 10;
        }

        .password-toggle:hover {
            color: #4a81d4;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                padding: 1.5rem;
            }
            .logo img {
                width: 160px;
            }
            .welcome-text {
                font-size: 1.1rem;
            }
        }
    </style>

    <script>
        window.addEventListener('load', function() {
            if (!navigator.onLine) {
                document.getElementById('bootstrapCSS').href = "{{ asset('template/dist/assets/css/bootstrap.min.css') }}";
                console.warn("Offline mode detected — using local bootstrap.min.css");
            }
        });
    </script>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="{{ asset('template/logo/logo.png') }}" alt="SHE Management Logo">
        </div>

        <h5 class="welcome-text">Welcome Back!</h5>
        <p class="subtitle">Sign in to start your session.</p>

        @if (session('error'))
            <div class="alert alert-danger py-2 mb-3">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login.process') }}">
    @csrf

    <div class="input-wrapper mb-3">
        <span class="input-icon"><i class="ri-user-line"></i></span>
        <input type="text" name="usr" class="form-control" id="username" placeholder="Username" required>
    </div>

    <div class="input-wrapper mb-3 position-relative">
        <span class="input-icon"><i class="ri-lock-line"></i></span>
        <input type="password" name="pswd" class="form-control" id="password" placeholder="Password" required>
        <span class="password-toggle" id="togglePassword">
            <i class="ri-eye-line"></i>
        </span>
    </div>

    <button type="submit" class="btn btn-login">Log In</button>
</form>


        <div class="footer mt-4">
            © {{ date('Y') }} <strong>SHE Management</strong>. All rights reserved.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordField = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            this.innerHTML = type === 'password'
                ? '<i class="ri-eye-line"></i>'
                : '<i class="ri-eye-off-line"></i>';
        });
    </script>
</body>
</html>