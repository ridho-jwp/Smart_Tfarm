<section
        class="relative min-h-screen bg-gradient-to-br from-green-950 via-green-900 to-green-950 flex items-center overflow-hidden">

        <!-- background glow -->
        <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-green-500/20 blur-3xl rounded-full"></div>
        <div class="absolute bottom-0 right-0 w-[400px] h-[400px] bg-emerald-400/10 blur-3xl rounded-full"></div>

        <div class="container mx-auto px-[5vw] pt-28 pb-2 relative z-10">

            <div class="grid lg:grid-cols-2 gap-16 items-center">

                <!-- LEFT -->
                <div>

                    <span
                        class="inline-flex items-center gap-2 bg-green-400/10 border border-green-400/20 text-green-300 text-[11px] font-bold tracking-[0.2em] uppercase px-4 py-2 rounded-full mb-8">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        T-FARM V.1
                    </span>

                    <h1 class="font-serif text-5xl md:text-7xl leading-[1.05] text-white mb-8">
                        <span class="italic text-green-400">
                            T-FARM
                        </span>
                        Pakcoy Hidroponik
                    </h1>

                    <p class="text-white/70 text-lg leading-relaxed max-w-xl mb-10">
                        Pantau nutrisi, kendalikan irigasi, dan optimalkan pertumbuhan pakcoy melalui monitoring
                        real-time berbasis Internet of Things pada T-Farm.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="#abouttfarm"
                            class="border border-white/10 bg-white/5 hover:bg-green-800/15 text-white px-8 py-4 rounded-full font-medium transition-all">
                            Tentang Kami
                        </a>
                    </div>

                </div>

                <!-- RIGHT -->
                <div class="relative hidden lg:block">

                    <!-- glow -->
                    <div class="absolute inset-0 bg-green-500/20 blur-3xl rounded-full scale-90"></div>

                    <!-- image -->
                    <div class="relative rounded-[36px] overflow-hidden border border-white/10 shadow-2xl">

                        <img src="https://images.unsplash.com/photo-1509391366360-2e959784a276?q=80&w=1600&auto=format&fit=crop"
                            alt="Hydroponic IoT" class="w-full h-[500px] object-cover">

                        <!-- overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                    </div>

                    <!-- floating card -->
                    <div
                        class="absolute -bottom-6 -left-6 bg-white backdrop-blur-xl p-6 rounded-3xl shadow-2xl border border-white/20">

                        <h3 class="text-4xl font-serif text-green-700 mb-1">
                            100%
                        </h3>

                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500 font-bold">
                            Automated Monitoring
                        </p>

                    </div>

                </div>

            </div>

        </div>
    </section>


    <!-- ABOUT PARTNER -->
    <section id="abouttfarm" class="py-18 px-[5vw] bg-sand overflow-hidden">
        <div class="container mx-auto">
            <!-- HEADING -->
            <span class="text-green-600 font-bold tracking-[0.2em] text-base lg:text-2xl uppercase block mb-3">Tentang
                T-Farm</span>
            <div class="max-w-3xl mb-20">
                <h2 class="text-3xl md:text-5xl font-serif text-ink mb-6">
                    T-Farm
                </h2>

                <p class="text-gray-600 leading-relaxed text-lg">
                    T-Farm merupakan kebun hidroponik yang dikelola oleh Bapak Tatang,
                    berawal dari hobi bercocok tanam saat masa KKN hingga berkembang
                    menjadi kebun hidroponik produktif yang fokus pada budidaya pakcoy.
                    Dengan semangat inovasi dan ketekunan, T-Farm kini menerapkan
                    teknologi IoT untuk membantu monitoring nutrisi, kualitas air,
                    serta pendeteksian hama secara otomatis dan realtime.
                </p>
            </div>

            <!-- MAIN CONTENT -->
            <div class="grid lg:grid-cols-2 gap-14 items-center mb-16">

                <!-- IMAGE -->
                <div class="relative">

                    <!-- background blur -->
                    <div class="absolute -bottom-6 -left-6 w-full h-full bg-green-200 rounded-[2rem]">
                    </div>

                    <div class="relative overflow-hidden rounded-[2rem] shadow-2xl border border-green-100">

                        <img src="{{ asset('assets/img/home/gh.png') }}" alt="Kebun Hidroponik T-Farm"
                            class="w-full h-[500px] object-cover">

                        <!-- overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent">
                        </div>

                        <!-- floating info -->
                        <div
                            class="absolute bottom-6 left-6 bg-white/90 backdrop-blur-md px-6 py-4 rounded-2xl shadow-lg">

                            <h4 class="text-lg font-bold text-gray-900">
                                Green House Modern
                            </h4>

                            <p class="text-sm text-gray-600">
                                Monitoring nutrisi & irigasi berbasis IoT
                            </p>
                        </div>
                    </div>
                </div>

                <!-- CONTENT -->
                <div>

                    <div class="space-y-8">

                        <!-- ITEM -->
                        <div
                            class="bg-white rounded-[2rem] p-8 shadow-sm border border-green-100 hover:shadow-xl transition-all">

                            <div class="flex gap-5 items-start">
                                <div>
                                    <h3 class="text-2xl font-bold mb-3 text-gray-900">
                                        Teknologi Modern
                                    </h3>

                                    <p class="text-gray-600 leading-relaxed">
                                        T-Farm mengintegrasikan sensor IoT untuk
                                        memonitor suhu air, pH, dan nutrisi secara
                                        otomatis guna meningkatkan kualitas panen.
                                    </p>
                                </div>

                            </div>

                        </div>

                        <!-- ITEM -->
                        <div
                            class="bg-white rounded-[2rem] p-8 shadow-sm border border-green-100 hover:shadow-xl transition-all">

                            <div class="flex gap-5 items-start">

                                <div>
                                    <h3 class="text-2xl font-bold mb-3 text-gray-900">
                                        Kondisi Lapangan Nyata
                                    </h3>

                                    <p class="text-gray-600 leading-relaxed">
                                        Sistem dikembangkan langsung pada kebun
                                        hidroponik aktif sehingga seluruh data dan
                                        pengujian berasal dari kondisi lapangan
                                        sebenarnya.
                                    </p>
                                </div>

                            </div>

                        </div>

                        <!-- ITEM -->
                        <div
                            class="bg-white rounded-[2rem] p-8 shadow-sm border border-green-100 hover:shadow-xl transition-all">

                            <div class="flex gap-5 items-start">
                                <div>
                                    <h3 class="text-2xl font-bold mb-3 text-gray-900">
                                        Kolaborasi & Inovasi
                                    </h3>

                                    <p class="text-gray-600 leading-relaxed">
                                        Kerja sama antara pengelola kebun dan program pemerintahan (MBG)
                                        menciptakan pertanian cerdas yang efisien,
                                        modern, dan berkelanjutan.
                                    </p>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- GALLERY -->
            <div class="md:grid md:grid-cols-3 gap-6">

                <!-- MOBILE CAROUSEL -->
                <div class="md:hidden flex gap-5 overflow-x-auto snap-x snap-mandatory pb-4 scrollbar-hide">

                    <!-- IMAGE 1 -->
                    <div class="min-w-[85%] snap-center overflow-hidden rounded-[2rem] shadow-lg shrink-0">
                        <img src="{{ asset('assets/img/home/product.png') }}"
                            class="w-full h-[260px] object-cover" alt="Hidroponik">
                    </div>

                    <!-- IMAGE 2 -->
                    <div class="min-w-[85%] snap-center overflow-hidden rounded-[2rem] shadow-lg shrink-0">
                        <img src="https://images.unsplash.com/photo-1523741543316-beb7fc7023d8?q=80&w=1200&auto=format&fit=crop"
                            class="w-full h-[260px] object-cover" alt="Tanaman Pakcoy">
                    </div>

                    <!-- IMAGE 3 -->
                    <div class="min-w-[85%] snap-center overflow-hidden rounded-[2rem] shadow-lg shrink-0">
                        <img src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?q=80&w=1200&auto=format&fit=crop"
                            class="w-full h-[260px] object-cover" alt="Kebun Modern">
                    </div>

                </div>

                <!-- DESKTOP GRID -->
                <div class="hidden md:contents">

                    <div class="overflow-hidden rounded-[2rem] shadow-lg">
                        <img src="{{ asset('assets/img/home/product.png') }}"
                            class="w-full h-[260px] object-cover hover:scale-110 transition duration-700"
                            alt="Hidroponik">
                    </div>

                    <div class="overflow-hidden rounded-[2rem] shadow-lg">
                        <img src="https://images.unsplash.com/photo-1523741543316-beb7fc7023d8?q=80&w=1200&auto=format&fit=crop"
                            class="w-full h-[260px] object-cover hover:scale-110 transition duration-700"
                            alt="Tanaman Pakcoy">
                    </div>

                    <div class="overflow-hidden rounded-[2rem] shadow-lg">
                        <img src="https://images.unsplash.com/photo-1416879595882-3373a0480b5b?q=80&w=1200&auto=format&fit=crop"
                            class="w-full h-[260px] object-cover hover:scale-110 transition duration-700"
                            alt="Kebun Modern">
                    </div>

                </div>

            </div>

        </div>

    </section>


    <section id="fitur" class="py-18 px-[5vw] bg-cream">
        <div class="container mx-auto">
            <div class="max-w-2xl mb-16">
                <span
                    class="text-green-600 font-bold tracking-[0.2em] text-base lg:text-2xl uppercase block mb-3">Keunggulan
                    Sistem</span>
                <h2 class="text-3xl md:text-5xl font-serif text-ink mb-6">Solusi Pintar <span
                        class="italic text-green-600">Efisiensi & Otomatisasi</span> Kebun</h2>
                <p class="text-muted leading-relaxed text-lg">Gunakan teknologi untuk mengurangi beban kerja manual dan
                    meningkatkan kualitas hasil tani secara signifikan.</p>
            </div>

            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-1 bg-green-100 border-2 border-green-100 rounded-[2.5rem] overflow-hidden shadow-sm">
                <!-- Card 1 -->
                <div class="bg-cream p-12 hover:bg-green-50 transition-colors group">
                    <div
                        class="w-14 h-14 bg-green-100 text-green-700 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Akses Real-time</h3>
                    <p class="text-muted leading-relaxed text-sm">Data parameter lingkungan dikirim setiap detik ke
                        dashboard melalui protokol yang stabil.</p>
                </div>
                <!-- Card 2 -->
                <div class="bg-cream p-12 hover:bg-green-50 transition-colors group">
                    <div
                        class="w-14 h-14 bg-green-100 text-green-700 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Keamanan Pangan</h3>
                    <p class="text-muted leading-relaxed text-sm">Menjaga standar kualitas air dan nutrisi pakcoy agar
                        tetap higienis dan terbebas dari hama melalui deteksi dini.</p>
                </div>
                <!-- Card 3 -->
                <div class="bg-cream p-12 hover:bg-green-50 transition-colors group">
                    <div
                        class="w-14 h-14 bg-green-100 text-green-700 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Analisis Tren</h3>
                    <p class="text-muted leading-relaxed text-sm">Visualisasi data dalam bentuk grafik interaktif yang
                        membantu Anda memahami pola kualitas pada tandon dan penyerapan nutrisi pada tanaman.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SENSOR LISTING (Horizontal Scroll / Grid) -->
    <section id="sensor" class="py-28 bg-sand px-[5vw]">
        <div class="container mx-auto">
            <span class="text-green-600 font-bold tracking-[0.2em] text-base lg:text-2xl uppercase block mb-3">Sensor
                dan Akuator</span>
            <h2 class="text-3xl md:text-5xl font-serif text-ink mb-6">Sistem Monitoring<span
                    class="italic text-green-600">Terintegrasi</span></h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Sensor Card -->
                <div
                    class="bg-white p-8 rounded-[2rem] border border-green-100 shadow-sm hover:shadow-xl transition-all group">
                    <div class="flex justify-between items-start mb-6">
                        <div
                            class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center group-hover:bg-green-500 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M14 14.76V3.5a2.5 2.5 0 00-5 0v11.26a4.5 4.5 0 105 0z" stroke-width="1.5" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold text-green-700 bg-green-50 px-3 py-1 rounded-full">Suhu
                            Air</span>
                    </div>
                    <h4 class="text-xl font-bold mb-2">DS18B20 Sensor</h4>
                    <p class="text-muted text-sm leading-relaxed">Sensor kedap air dengan presisi tinggi untuk menjaga
                        larutan nutrisi tetap sejuk.</p>
                </div>
                <!-- Ulangi untuk pH, TDS, dll -->
                <div
                    class="bg-white p-8 rounded-[2rem] border border-green-100 shadow-sm hover:shadow-xl transition-all group">
                    <div class="flex justify-between items-start mb-6">
                        <div
                            class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center group-hover:bg-green-500 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    stroke-width="1.5" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold text-green-700 bg-green-50 px-3 py-1 rounded-full">Kadar
                            pH</span>
                    </div>
                    <h4 class="text-xl font-bold mb-2">pH Meter</h4>
                    <p class="text-muted text-sm leading-relaxed">Monitor keseimbangan asam-basa untuk penyerapan
                        nutrisi yang optimal oleh akar.</p>
                </div>
                <div
                    class="bg-white p-8 rounded-[2rem] border border-green-100 shadow-sm hover:shadow-xl transition-all group">
                    <div class="flex justify-between items-start mb-6">
                        <div
                            class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center group-hover:bg-green-500 group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.022.547l-2.387 2.387a2 2 0 000 2.828l.596.596a2 2 0 11-2.828 2.828l-.596-.596a2 2 0 012.828-2.828l.596.596a2 2 0 010 2.828"
                                    stroke-width="1.5" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold text-green-700 bg-green-50 px-3 py-1 rounded-full">TDS
                            Meter</span>
                    </div>
                    <h4 class="text-xl font-bold mb-2">TDS Sensor</h4>
                    <p class="text-muted text-sm leading-relaxed">Mendeteksi Total Dissolved Solids (PPM) guna
                        memastikan konsentrasi pupuk tepat.</p>
                </div>
                <div
                    class="bg-white p-8 rounded-[2rem] border border-green-100 shadow-sm hover:shadow-xl transition-all group">
                    <div class="flex justify-between items-start mb-6">
                        <div
                            class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center group-hover:bg-green-500 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16">
                                <path
                                    d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4z" />
                                <path
                                    d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5m0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7M3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold text-green-700 bg-green-50 px-3 py-1 rounded-full">TDS
                            Meter</span>
                    </div>
                    <h4 class="text-xl font-bold mb-2">ESP32 S3 CAM</h4>
                    <p class="text-muted text-sm leading-relaxed">Mengambil gambar pada tanaman guna di identifikasi kesehatannya.</p>
                </div>
            </div>
        </div>
    </section>