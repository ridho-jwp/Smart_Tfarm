<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Smart Pakcoy Hidroponik</title>
    <link rel="icon" type="image/png" href="{{ asset('template1/assets/img/favicon.png') }}" sizes="16x16">
    <!-- Google Fonts — sama dengan landing page -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600,700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('template1/assets/css/all.min.css') }}">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('template1/assets/bootstrap/css/bootstrap.min.css') }}">
    <!-- Wowdash untuk iconify & style form -->
    <link rel="stylesheet" href="{{ asset('wowdash/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('wowdash/css/style.css') }}">

    <style>
        :root {
            --sp-green:      #2e7d32;
            --sp-green-mid:  #388e3c;
            --sp-green-light:#4caf50;
            --sp-green-pale: #e8f5e9;
            --sp-dark:       #1a2e1b;
        }

        /* Override wowdash primary ke hijau */
        .btn-primary,
        .btn-primary:focus {
            background-color: var(--sp-green) !important;
            border-color: var(--sp-green) !important;
        }
        .btn-primary:hover {
            background-color: var(--sp-green-mid) !important;
            border-color: var(--sp-green-mid) !important;
        }

        .text-primary-600 { color: var(--sp-green) !important; }
        .border-primary   { border-color: var(--sp-green) !important; }
        .bg-primary-600   { background-color: var(--sp-green) !important; }
        .bg-primary-50    { background-color: var(--sp-green-pale) !important; }

        /* Panel kiri — hero */
        .auth-left {
            background: linear-gradient(160deg, var(--sp-dark) 0%, var(--sp-green) 100%);
            position: relative;
            overflow: hidden;
        }
        .auth-left::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: rgba(255,255,255,.04);
            border-radius: 50%;
            top: -100px; left: -100px;
        }
        .auth-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: rgba(255,255,255,.06);
            border-radius: 50%;
            bottom: -80px; right: -80px;
        }

        /* Judul panel kiri */
        .auth-left-brand {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 16px;
        }
        .auth-left-brand span {
            display: block;
            width: 50px; height: 4px;
            background: rgba(255,255,255,.5);
            border-radius: 2px;
            margin: 10px auto;
        }
        .auth-left p {
            color: rgba(255,255,255,.8);
            font-family: 'Open Sans', sans-serif;
            font-size: 1rem;
            line-height: 1.7;
            max-width: 320px;
        }

        /* Fitur list di panel kiri */
        .feature-list {
            list-style: none;
            padding: 0; margin: 24px 0 0;
            text-align: left;
        }
        .feature-list li {
            color: rgba(255,255,255,.85);
            font-size: 0.9rem;
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .feature-list li i { color: #a5d6a7; font-size: 1rem; }

        /* Panel kanan */
        .auth-right { background: #f9fafb; }

        /* Logo di atas form */
        .login-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--sp-green);
            text-decoration: none;
        }
        .login-brand:hover { color: var(--sp-green-mid); }

        /* Link kembali ke landing */
        .back-link {
            font-size: 0.85rem;
            color: #888;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color .2s;
        }
        .back-link:hover { color: var(--sp-green); }

        /* Form inputs */
        .form-control:focus {
            border-color: var(--sp-green) !important;
            box-shadow: 0 0 0 3px rgba(46,125,50,.15) !important;
        }

        /* Remember me checkbox */
        .form-check-input:checked {
            background-color: var(--sp-green) !important;
            border-color: var(--sp-green) !important;
        }

        /* Footer note */
        .login-footer-note {
            font-size: 0.78rem;
            color: #aaa;
            text-align: center;
            margin-top: 24px;
        }
    </style>
</head>

<body>
    <section class="auth bg-base d-flex flex-wrap">
        <!-- Panel Kiri -->
        <div class="auth-left d-lg-block d-none">
            <div class="d-flex align-items-center flex-column h-100 justify-content-center text-center px-32" style="position:relative;z-index:1;">
                <div class="auth-left-brand">
                    Smart Pakcoy
                    <span></span>
                </div>
                <p>Sistem Monitoring &amp; Kontrol Hidroponik Berbasis IoT. Pantau kondisi tanaman pakcoy Anda secara real-time dari mana saja.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check-circle"></i> Monitoring pH, TDS &amp; Suhu Real-time</li>
                    <li><i class="fas fa-check-circle"></i> Kontrol Pompa Otomatis</li>
                    <li><i class="fas fa-check-circle"></i> Deteksi Anomali Tanaman</li>
                    <li><i class="fas fa-check-circle"></i> Riwayat Data Lengkap</li>
                </ul>
            </div>
        </div>

        <!-- Panel Kanan -->
        <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
            <div class="max-w-464-px mx-auto w-100">

                <!-- Kembali ke landing -->
                <div class="mb-24">
                    <a href="{{ route('landing') }}" class="back-link">
                        <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                    </a>
                </div>

                <div>
                    <a href="{{ route('landing') }}" class="login-brand mb-40 d-inline-block">
                        Smart Pakcoy
                    </a>
                    <h4 class="mb-12">Masuk ke Akun Anda</h4>
                    <p class="mb-32 text-secondary-light text-lg">Selamat datang! Silakan masukkan detail akun Anda.</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger d-flex align-items-center gap-2 mb-24" role="alert">
                        <i class="ri-error-warning-line text-lg"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <p class="mb-0">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="icon-field mb-16">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="mage:email"></iconify-icon>
                        </span>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control h-56-px bg-neutral-50 radius-12 @error('email') is-invalid @enderror"
                            placeholder="Email" required autofocus>
                    </div>
                    <div class="position-relative mb-20">
                        <div class="icon-field">
                            <span class="icon top-50 translate-middle-y">
                                <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                            </span>
                            <input type="password" name="password" class="form-control h-56-px bg-neutral-50 radius-12"
                                id="your-password" placeholder="Password" required>
                        </div>
                        <span
                            class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                            data-toggle="#your-password"></span>
                    </div>
                    <div class="mb-24">
                        <div class="form-check style-check d-flex align-items-center">
                            <input class="form-check-input border border-neutral-300" type="checkbox" name="remember"
                                id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Masuk
                    </button>
                </form>

                <div class="login-footer-note">
                    Smart Pakcoy &copy; {{ date('Y') }} — Sistem Monitoring Hidroponik
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('template1/assets/js/jquery-1.11.3.min.js') }}"></script>
    <script src="{{ asset('template1/assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('wowdash/js/lib/iconify-icon.min.js') }}"></script>
    <script src="{{ asset('wowdash/js/app.js') }}"></script>
    <script>
        function initializePasswordToggle(toggleSelector) {
            $(toggleSelector).on("click", function () {
                $(this).toggleClass("ri-eye-off-line");
                var input = $($(this).attr("data-toggle"));
                if (input.attr("type") === "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        }
        initializePasswordToggle(".toggle-password");
    </script>
</body>

</html>