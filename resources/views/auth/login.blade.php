<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Smart Pakcoy</title>

    <link rel="icon" type="image/png" href="{{ asset('template1/assets/img/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;700&family=DM+Serif+Display&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
        }

        .font-serif {
            font-family: 'DM Serif Display', serif;
        }

        .glass {
            backdrop-filter: blur(14px);
            background: rgba(255, 255, 255, .7);
        }

        .bg-grid {
            background-image:
                radial-gradient(rgba(255, 255, 255, .08) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>

<body class="bg-[#f6f8f5] overflow-hidden">

    <section class="min-h-screen grid lg:grid-cols-2">

        <!-- LEFT -->
        <div
            class="hidden lg:flex relative overflow-hidden bg-gradient-to-br from-green-950 via-green-900 to-green-800 items-center justify-center px-20">

            <!-- pattern -->
            <div class="absolute inset-0 bg-grid opacity-30"></div>

            <!-- glow -->
            <div class="absolute top-0 left-0 w-[400px] h-[400px] bg-green-500/20 blur-3xl rounded-full">
            </div>

            <div class="absolute bottom-0 right-0 w-[350px] h-[350px] bg-emerald-400/10 blur-3xl rounded-full">
            </div>

            <!-- content -->
            <div class="relative z-10 max-w-xl">

                <div
                    class="inline-flex items-center gap-3 bg-white/10 border border-white/10 rounded-full px-5 py-2 mb-8">

                    <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>

                    <span class="text-xs tracking-[0.25em] uppercase text-white/70 font-bold">
                        T-Farm Hidroponik Pakcoy
                    </span>

                </div>

                <h1 class="font-serif text-6xl leading-tight text-green-400 mb-8">
                    T-Farm
                    <span class="italic text-white">
                        Pakcoy Hidroponik
                    </span>

                </h1>

                <p class="text-lg leading-relaxed text-white/70 mb-10">

                    Sistem monitoring hidroponik modern untuk memantau nutrisi,
                    suhu, pH, dan pertumbuhan tanaman secara real-time.

                </p>

                <!-- feature -->
                <div class="space-y-5">

                    <div class="flex items-start gap-4">

                        <div
                            class="w-11 h-11 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-green-400 shrink-0">

                            ✓

                        </div>

                        <div>
                            <h4 class="text-white font-semibold mb-1">
                                Monitoring Real-time
                            </h4>

                            <p class="text-sm text-white/60">
                                Pantau pH, TDS, suhu air, dan kondisi kebun kapan saja.
                            </p>
                        </div>

                    </div>

                    <div class="flex items-start gap-4">

                        <div
                            class="w-11 h-11 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-green-400 shrink-0">

                            ✓

                        </div>

                        <div>
                            <h4 class="text-white font-semibold mb-1">
                                Smart Automation
                            </h4>

                            <p class="text-sm text-white/60">
                                Sistem kontrol otomatis untuk pompa dan nutrisi.
                            </p>
                        </div>

                    </div>

                    <div class="flex items-start gap-4">

                        <div
                            class="w-11 h-11 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-green-400 shrink-0">

                            ✓

                        </div>

                        <div>
                            <h4 class="text-white font-semibold mb-1">
                                Analitik Data
                            </h4>

                            <p class="text-sm text-white/60">
                                Riwayat monitoring lengkap untuk evaluasi tanaman.
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- RIGHT -->
        <div class="relative flex items-center justify-center px-6 py-10 lg:px-16">

            <!-- mobile bg -->
            <div class="absolute top-0 left-0 w-full h-[240px] bg-gradient-to-br from-green-950 to-green-800 lg:hidden">
            </div>

            <!-- card -->
            <div
                class="relative z-10 w-full max-w-md rounded-[2rem] bg-white shadow-2xl border border-gray-100 p-8 lg:p-10">

                <!-- back -->
                <a href="{{ route('landing') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-green-700 transition mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-house" viewBox="0 0 16 16">
                        <path
                            d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z" />
                    </svg>
                    Kembali
                </a>

                <!-- logo -->
                <div class="mb-8">

                    <h2 class="font-serif text-4xl text-gray-900 mb-3">

                        Smart
                        <span class="italic text-green-600">
                            Pakcoy
                        </span>

                    </h2>

                    <p class="text-gray-500 leading-relaxed">
                        Masuk ke dashboard monitoring hidroponik Anda.
                    </p>

                </div>

                <!-- error -->
                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">

                        @foreach ($errors->all() as $error)
                            <p class="text-sm text-red-600">
                                {{ $error }}
                            </p>
                        @endforeach

                    </div>
                @endif

                <!-- form -->
                <form action="{{ route('login') }}" method="POST" class="space-y-5">

                    @csrf

                    <!-- email -->
                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Email
                        </label>

                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email"
                            required autofocus
                            class="w-full h-14 rounded-2xl border border-gray-200 bg-gray-50 px-5 outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 transition">

                    </div>

                    <!-- password -->
                    <div>

                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Password
                        </label>

                        <div class="relative">

                            <input type="password" name="password" id="password" placeholder="Masukkan password"
                                required
                                class="w-full h-14 rounded-2xl border border-gray-200 bg-gray-50 px-5 pr-14 outline-none focus:ring-4 focus:ring-green-100 focus:border-green-500 transition">

                            <button type="button" id="togglePassword"
                                class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-green-600">

                                👁

                            </button>

                        </div>

                    </div>

                    <!-- remember -->
                    <div class="flex items-center justify-between">

                        <label class="flex items-center gap-3 text-sm text-gray-600">

                            <input type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">

                            Ingat saya

                        </label>

                    </div>

                    <!-- submit -->
                    <button type="submit"
                        class="w-full h-14 rounded-2xl bg-green-600 hover:bg-green-700 text-white font-semibold transition-all hover:-translate-y-1 shadow-lg shadow-green-600/20">

                        Masuk

                    </button>

                </form>

                <!-- footer -->
                <div class="mt-8 text-center">

                    <p class="text-xs text-gray-400">
                        Smart Pakcoy © {{ date('Y') }}
                    </p>

                </div>

            </div>

        </div>

    </section>

    <script>
        const togglePassword =
            document.getElementById('togglePassword');

        const password =
            document.getElementById('password');

        togglePassword.addEventListener('click', () => {

            const type =
                password.getAttribute('type') === 'password' ?
                'text' :
                'password';

            password.setAttribute('type', type);

        });
    </script>

</body>

</html>
