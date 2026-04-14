<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Smart Pakcoy — Sistem Monitoring Hidroponik Cerdas Berbasis IoT. Pantau pH, suhu, TDS, dan nutrisi tanaman pakcoy Anda secara real-time.">

    <!-- title -->
    <title>Smart Pakcoy — Sistem Monitoring Hidroponik</title>

    <!-- favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('template1/assets/img/favicon.png') }}">
    <!-- google font -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <!-- fontawesome -->
    <link rel="stylesheet" href="{{ asset('template1/assets/css/all.min.css') }}">
    <!-- bootstrap -->
    <link rel="stylesheet" href="{{ asset('template1/assets/bootstrap/css/bootstrap.min.css') }}">
    <!-- owl carousel -->
    <link rel="stylesheet" href="{{ asset('template1/assets/css/owl.carousel.css') }}">
    <!-- animate css -->
    <link rel="stylesheet" href="{{ asset('template1/assets/css/animate.css') }}">
    <!-- mean menu css -->
    <link rel="stylesheet" href="{{ asset('template1/assets/css/meanmenu.min.css') }}">
    <!-- main style -->
    <link rel="stylesheet" href="{{ asset('template1/assets/css/main.css') }}">
    <!-- responsive -->
    <link rel="stylesheet" href="{{ asset('template1/assets/css/responsive.css') }}">

    <style>
        /* =====================================================
           OVERRIDE WARNA TEMA: #F28123 (orange) → #2e7d32 (hijau)
           agar serasi dengan Smart Pakcoy
        ====================================================== */
        :root {
            --sp-green:      #2e7d32;
            --sp-green-mid:  #388e3c;
            --sp-green-light:#4caf50;
            --sp-green-pale: #e8f5e9;
            --sp-dark:       #1a2e1b;
            --sp-orange:     #F28123; /* tetap dipakai di beberapa aksen */
        }

        /* Hero background — pakai hero-bg.jpg template */
        .hero-bg {
            background-image: url("{{ asset('template1/assets/img/hero-bg.jpg') }}");
        }

        /* ---- Accent color overrides ---- */
        a:hover,
        nav.main-menu ul li.current-list-item > a,
        nav.main-menu li:hover > a,
        .orange-text { color: var(--sp-green) !important; }

        a.boxed-btn {
            background-color: var(--sp-green) !important;
        }
        a.boxed-btn:hover {
            background-color: var(--sp-green-mid) !important;
            color: #fff !important;
        }

        a.bordered-btn {
            border-color: var(--sp-green) !important;
        }
        a.bordered-btn:hover {
            background-color: var(--sp-green) !important;
            color: #fff !important;
        }

        a.cart-btn,
        .section-title h3:after { background-color: var(--sp-green) !important; }

        a.cart-btn {
            background: var(--sp-green) !important;
            color: #fff !important;
            font-family: 'Poppins', sans-serif;
            padding: 10px 25px;
            display: inline-block;
        }
        a.cart-btn:hover {
            background: var(--sp-green-mid) !important;
        }

        .mean-bar a.meanmenu-reveal { background-color: var(--sp-green) !important; }
        .sticky-wrapper.is-sticky .top-header-area { background-color: var(--sp-dark) !important; }

        /* Search bar */
        .search-bar-tablecell input { border-bottom-color: var(--sp-green) !important; }
        .search-bar-tablecell button[type=submit] { background-color: var(--sp-green) !important; }

        /* Loader spinner */
        .circle { border-right-color: var(--sp-dark) !important; }
        .circle:before { border-left-color: var(--sp-green-light) !important; }

        /* Shop banner */
        .shop-banner { background-color: var(--sp-green) !important; }
        .shop-banner h3 span.orange-text { color: #fff !important; }

        /* Cart banner accent */
        .price-box { border-color: var(--sp-green) !important; }
        .inner-price { background-color: var(--sp-green) !important; }

        /* Features list icons */
        .list-icon i { color: var(--sp-green) !important; }

        /* Footer */
        .footer-area { background-color: var(--sp-dark) !important; }
        .copyright { background-color: #111f12 !important; }
        .footer-box subscribe button { background-color: var(--sp-green) !important; }

        /* Testimonial quote icon */
        .last-icon i { color: var(--sp-green) !important; }

        /* Breadcrumb dot */
        .breadcrumb-text p { color: var(--sp-green) !important; }

        /* ---- Tombol Login di navbar ---- */
        .btn-login-nav {
            font-family: 'Poppins', sans-serif;
            background-color: var(--sp-green);
            color: #fff !important;
            padding: 8px 22px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 14px;
            display: inline-block;
            transition: background 0.3s;
        }
        .btn-login-nav:hover {
            background-color: var(--sp-green-mid) !important;
            color: #fff !important;
        }

        /* ---- Stats bar ---- */
        .stats-bar {
            background: linear-gradient(135deg, var(--sp-green) 0%, var(--sp-green-mid) 100%);
            padding: 40px 0;
        }
        .stat-item { text-align: center; color: #fff; }
        .stat-item h3 { font-size: 2.5rem; font-weight: 700; margin: 0; color: #fff; }
        .stat-item p  { margin: 0; font-size: 0.9rem; opacity: 0.9; }

        /* ---- Sensor card ---- */
        .sensor-card {
            background: #fff;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(46,125,50,.10);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .sensor-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(46,125,50,.18);
        }
        .sensor-card .sensor-icon {
            width: 64px; height: 64px;
            background: var(--sp-green-pale);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
        .sensor-card .sensor-icon i { font-size: 26px; color: var(--sp-green); }
        .sensor-card h5 { font-size: 1rem; font-weight: 700; color: var(--sp-dark); margin-bottom: 6px; }
        .sensor-card p  { font-size: 0.85rem; color: #666; margin: 0; }

        /* ---- CTA section ---- */
        .cta-section {
            background: linear-gradient(135deg, var(--sp-dark) 0%, var(--sp-green) 100%);
            padding: 80px 0;
            text-align: center;
            color: #fff;
        }
        .cta-section h2 { color: #fff; margin-bottom: 16px; }
        .cta-section p  { opacity: 0.85; margin-bottom: 32px; font-size: 1.05rem; }

        /* ---- NAVBAR STICKY FIXED ---- */
        .top-header-area {
            position: fixed !important;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 9999;
            padding: 18px 0;
            background-color: transparent;
            transition: background-color 0.35s ease, padding 0.35s ease, box-shadow 0.35s ease;
        }
        .top-header-area.scrolled {
            background-color: var(--sp-dark) !important;
            padding: 12px 0;
            box-shadow: 0 4px 20px rgba(0,0,0,.35);
        }

        /* Hero harus punya padding-top supaya tidak tertutup navbar */
        .hero-area.hero-bg {
            padding-top: 80px;
        }

        /* Subscribe button in footer */
        .footer-box.subscribe form button { background-color: var(--sp-green) !important; }
        .footer-box.subscribe form button:hover { background-color: var(--sp-green-mid) !important; }

        /* Social icons */
        .social-icons ul li a:hover { color: var(--sp-green-light) !important; }

        /* Read more */
        a.read-more-btn:hover { color: var(--sp-green) !important; }
    </style>
</head>
<body>

    <!--PreLoader-->
    <div class="loader">
        <div class="loader-inner">
            <div class="circle"></div>
        </div>
    </div>
    <!--PreLoader Ends-->

    <!-- header -->
    <div class="top-header-area" id="sticker">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-sm-12 text-center">
                    <div class="main-menu-wrap">
                        <!-- logo -->
                        <div class="site-logo">
                            <a href="{{ route('landing') }}">
                                <span style="font-family:'Poppins',sans-serif;font-weight:700;font-size:1.2rem;color:#fff;">
                                    Smart Pakcoy
                                </span>
                            </a>
                        </div>
                        <!-- logo -->

                        <!-- menu start -->
                        <nav class="main-menu">
                            <ul>
                                <li class="current-list-item"><a href="{{ route('landing') }}">Beranda</a></li>
                                <li><a href="#fitur">Fitur</a></li>
                                <li><a href="#sensor">Sensor</a></li>
                                <li><a href="#tentang">Tentang</a></li>
                                <li>
                                    <a href="{{ route('login') }}" class="btn-login-nav">Masuk</a>
                                </li>
                            </ul>
                        </nav>
                        <div class="mobile-menu"></div>
                        <!-- menu end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end header -->

    <!-- hero area -->
    <div class="hero-area hero-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 offset-lg-2 text-center">
                    <div class="hero-text">
                        <div class="hero-text-tablecell">
                            <p class="subtitle">IoT &amp; Hidroponik</p>
                            <h1>Sistem Cerdas Pemantau<br>Tanaman Pakcoy</h1>
                            <div class="hero-btns">
                                <a href="{{ route('login') }}" class="boxed-btn">Masuk ke Sistem</a>
                                <a href="#fitur" class="bordered-btn">Pelajari Fitur</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end hero area -->

    <!-- features list section -->
    <div class="list-section pt-80 pb-80" id="fitur">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                    <div class="list-box d-flex align-items-center">
                        <div class="list-icon">
                            <i class="fas fa-wifi"></i>
                        </div>
                        <div class="content">
                            <h3>Monitoring Real-time</h3>
                            <p>Data sensor langsung dari ESP32</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                    <div class="list-box d-flex align-items-center">
                        <div class="list-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="content">
                            <h3>Otomasi Pompa</h3>
                            <p>Kontrol sirkulasi dan nutrisi otomatis</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="list-box d-flex justify-content-start align-items-center">
                        <div class="list-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="content">
                            <h3>Analisis Data</h3>
                            <p>Grafik dan riwayat sensor lengkap</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end features list section -->

    <!-- stats bar -->
    <div class="stats-bar">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-item">
                        <h3>5+</h3>
                        <p>Jenis Sensor Terpasang</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-item">
                        <h3>24/7</h3>
                        <p>Pemantauan Non-Stop</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-item">
                        <h3>IoT</h3>
                        <p>Berbasis ESP32 & Laravel</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <h3>Real</h3>
                        <p>Data Akurat & Terpercaya</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end stats bar -->

    <!-- sensor section -->
    <div class="product-section mt-150 mb-150" id="sensor">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="section-title">
                        <h3><span class="orange-text">Sensor</span> yang Dipantau</h3>
                        <p>Semua parameter penting tanaman pakcoy dipantau secara real-time untuk memastikan pertumbuhan optimal di sistem hidroponik Anda.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="sensor-card">
                        <div class="sensor-icon"><i class="fas fa-thermometer-half"></i></div>
                        <h5>Suhu Air</h5>
                        <p>Pemantauan suhu larutan nutrisi menggunakan sensor DS18B20 waterproof yang presisi.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="sensor-card">
                        <div class="sensor-icon"><i class="fas fa-flask"></i></div>
                        <h5>Tingkat pH</h5>
                        <p>Sensor pH analog memastikan larutan nutrisi berada pada kisaran ideal 5.5 – 6.5 untuk pakcoy.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="sensor-card">
                        <div class="sensor-icon"><i class="fas fa-tint"></i></div>
                        <h5>Kadar TDS</h5>
                        <p>Mengukur Total Dissolved Solids untuk memantau konsentrasi nutrisi dalam air.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="sensor-card">
                        <div class="sensor-icon"><i class="fas fa-ruler-vertical"></i></div>
                        <h5>Level Air</h5>
                        <p>Sensor ultrasonik mendeteksi ketinggian air di reservoir secara akurat tanpa kontak.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="sensor-card">
                        <div class="sensor-icon"><i class="fas fa-sun"></i></div>
                        <h5>Suhu Udara</h5>
                        <p>DHT11/22 memantau suhu dan kelembaban lingkungan sekitar instalasi hidroponik.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="sensor-card">
                        <div class="sensor-icon"><i class="fas fa-cogs"></i></div>
                        <h5>Kontrol Pompa</h5>
                        <p>Relay 4-channel mengontrol pompa sirkulasi dan pompa peristaltik nutrisi secara otomatis.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end sensor section -->

    <!-- about section (banner) -->
    <section class="cart-banner pt-100 pb-100" id="tentang">
        <div class="container">
            <div class="row clearfix align-items-center">
                <!-- Image Column -->
                <div class="image-column col-lg-6 mb-4 mb-lg-0">
                    <div class="image">
                        <div class="price-box">
                            <div class="inner-price">
                                <span class="price">
                                    <strong>IoT</strong><br>Based
                                </span>
                            </div>
                        </div>
                        <img src="{{ asset('template1/assets/img/abt.jpg') }}" alt="Hidroponik Smart Pakcoy" style="border-radius:8px;">
                    </div>
                </div>
                <!-- Content Column -->
                <div class="content-column col-lg-6">
                    <h3><span class="orange-text">Tentang</span> Smart Pakcoy</h3>
                    <h4>Platform IoT untuk Pertanian Masa Depan</h4>
                    <div class="text">
                        Smart Pakcoy adalah sistem monitoring hidroponik berbasis ESP32 dan Laravel yang memungkinkan pemantauan dan pengendalian parameter pertumbuhan tanaman pakcoy secara otomatis dan real-time. Dibangun untuk memudahkan petani modern mengelola instalasi hidroponik mereka dari mana saja.
                    </div>
                    <a href="{{ route('login') }}" class="cart-btn mt-3">
                        <i class="fas fa-sign-in-alt"></i> Akses Dashboard
                    </a>
                </div>
            </div>
        </div>
    </section>
    <!-- end about section -->

    <!-- CTA section -->
    <div class="cta-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2">
                    <h2>Mulai Pantau Tanaman Anda Sekarang</h2>
                    <p>Masuk ke dashboard admin untuk melihat data sensor real-time, riwayat pengukuran, dan mengontrol sistem irigasi tanaman pakcoy Anda.</p>
                    <a href="{{ route('login') }}" class="boxed-btn" style="font-size:1rem;padding:14px 36px;">
                        <i class="fas fa-sign-in-alt me-2"></i> Masuk ke Sistem
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- end CTA section -->

    <!-- testimonial section -->
    <div class="testimonail-section mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1 text-center">
                    <div class="testimonial-sliders">
                        <div class="single-testimonial-slider">
                            <div class="client-avater">
                                <img src="{{ asset('template1/assets/img/avaters/avatar1.png') }}" alt="">
                            </div>
                            <div class="client-meta">
                                <h3>Ahmad Fauzi <span>Petani Hidroponik, Bandung</span></h3>
                                <p class="testimonial-body">
                                    "Smart Pakcoy sangat membantu dalam memantau kondisi tanaman saya. pH dan nutrisi selalu terjaga, hasil panen meningkat drastis sejak menggunakan sistem ini."
                                </p>
                                <div class="last-icon">
                                    <i class="fas fa-quote-right"></i>
                                </div>
                            </div>
                        </div>
                        <div class="single-testimonial-slider">
                            <div class="client-avater">
                                <img src="{{ asset('template1/assets/img/avaters/avatar2.png') }}" alt="">
                            </div>
                            <div class="client-meta">
                                <h3>Siti Rahayu <span>Pengelola Kebun Urban, Jakarta</span></h3>
                                <p class="testimonial-body">
                                    "Dashboard yang intuitif dan data real-time membuat manajemen kebun hidroponik jauh lebih efisien. Saya bisa monitor dari smartphone kapan saja."
                                </p>
                                <div class="last-icon">
                                    <i class="fas fa-quote-right"></i>
                                </div>
                            </div>
                        </div>
                        <div class="single-testimonial-slider">
                            <div class="client-avater">
                                <img src="{{ asset('template1/assets/img/avaters/avatar3.png') }}" alt="">
                            </div>
                            <div class="client-meta">
                                <h3>Budi Santoso <span>Mahasiswa Agritek, Yogyakarta</span></h3>
                                <p class="testimonial-body">
                                    "Sistem notifikasi anomali sangat berguna untuk mendeteksi masalah sejak dini. Pakcoy saya tumbuh sehat dan konsisten berkat monitoring otomatis ini."
                                </p>
                                <div class="last-icon">
                                    <i class="fas fa-quote-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end testimonial section -->

    <!-- footer -->
    <div class="footer-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-box about-widget">
                        <h2 class="widget-title">Smart Pakcoy</h2>
                        <p>Sistem monitoring dan kontrol hidroponik berbasis IoT menggunakan ESP32 dan Laravel. Solusi cerdas untuk pertanian masa depan.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="footer-box get-in-touch">
                        <h2 class="widget-title">Informasi Teknis</h2>
                        <ul>
                            <li>Hardware: ESP32 + Relay 4CH</li>
                            <li>Sensor: pH, TDS, DS18B20, Ultrasonik</li>
                            <li>Backend: Laravel 10 + MySQL</li>
                            <li>Dashboard: Real-time Monitoring</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="footer-box pages">
                        <h2 class="widget-title">Navigasi</h2>
                        <ul>
                            <li><a href="{{ route('landing') }}">Beranda</a></li>
                            <li><a href="#fitur">Fitur Sistem</a></li>
                            <li><a href="#sensor">Data Sensor</a></li>
                            <li><a href="#tentang">Tentang</a></li>
                            <li><a href="{{ route('login') }}">Masuk ke Sistem</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end footer -->

    <!-- copyright -->
    <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <p>&copy; {{ date('Y') }} Smart Pakcoy Hidroponik. Semua hak dilindungi.</p>
                </div>
                <div class="col-lg-6 text-right col-md-12">
                    <p style="color:#aaa;font-size:0.85rem;">Powered by ESP32 &amp; Laravel — IoT Hidroponik System</p>
                </div>
            </div>
        </div>
    </div>
    <!-- end copyright -->

    <!-- jquery -->
    <script src="{{ asset('template1/assets/js/jquery-1.11.3.min.js') }}"></script>
    <!-- bootstrap -->
    <script src="{{ asset('template1/assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <!-- isotope -->
    <script src="{{ asset('template1/assets/js/jquery.isotope-3.0.6.min.js') }}"></script>
    <!-- waypoints -->
    <script src="{{ asset('template1/assets/js/waypoints.js') }}"></script>
    <!-- owl carousel -->
    <script src="{{ asset('template1/assets/js/owl.carousel.min.js') }}"></script>
    <!-- mean menu -->
    <script src="{{ asset('template1/assets/js/jquery.meanmenu.min.js') }}"></script>
    <!-- main js -->
    <script src="{{ asset('template1/assets/js/main.js') }}"></script>

    <script>
        // Navbar: transparan di atas, solid gelap saat scroll
        $(window).on('scroll', function () {
            if ($(this).scrollTop() > 60) {
                $('.top-header-area').addClass('scrolled');
            } else {
                $('.top-header-area').removeClass('scrolled');
            }
        });
    </script>

</body>
</html>
