<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Smart Pakcoy Hidroponik</title>
    <link rel="icon" type="image/png" href="{{ asset('wowdash/images/favicon.png') }}" sizes="16x16">
    <link rel="stylesheet" href="{{ asset('wowdash/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('wowdash/css/lib/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('wowdash/css/style.css') }}">
</head>

<body>
    <section class="auth bg-base d-flex flex-wrap">
        <div class="auth-left d-lg-block d-none">
            <div class="d-flex align-items-center flex-column h-100 justify-content-center">
                <div class="text-center text-white px-32">
                    <h1 class="fw-bold mb-16 display-5">🌱 Smart Pakcoy</h1>
                    <p class="text-lg opacity-75">Sistem Monitoring Hidroponik Real-time. Pantau pH, suhu, dan nutrisi
                        tanaman pakcoy Anda kapan saja.</p>
                </div>
            </div>
        </div>
        <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
            <div class="max-w-464-px mx-auto w-100">
                <div>
                    <a href="{{ route('login') }}" class="mb-40 d-inline-block">
                        <h4 class="fw-bold text-primary-600 mb-0">🌱 Smart Pakcoy</h4>
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
                        <iconify-icon icon="mdi:login" class="me-2 text-lg"></iconify-icon>
                        Masuk
                    </button>
                </form>
            </div>
        </div>
    </section>

    <script src="{{ asset('wowdash/js/lib/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('wowdash/js/lib/bootstrap.bundle.min.js') }}"></script>
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