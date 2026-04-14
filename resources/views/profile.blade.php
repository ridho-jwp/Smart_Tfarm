<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Farm Pakcoy - Profil Perusahaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">

    <x-topbar />

    <x-hero />

    <x-main />

    <x-footer />

</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const track = document.getElementById('slider-track');
        const dots = document.querySelectorAll('.slider-dot');
        const totalSlides = 3; 
        let currentSlide = 0;

        function updateSlider() {
            // Geser track ke kiri sebesar 100% dikali slide saat ini
            track.style.transform = `translateX(-${currentSlide * 100}%)`;

            // Update nyala titik indikator (dots)
            dots.forEach((dot, index) => {
                if (index === currentSlide) {
                    dot.classList.remove('opacity-40');
                    dot.classList.add('opacity-100');
                } else {
                    dot.classList.remove('opacity-100');
                    dot.classList.add('opacity-40');
                }
            });
        }
        function nextSlide() {
            // Kembali ke slide 0 jika sudah mencapai slide terakhir
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlider();
        }

        setInterval(nextSlide, 3000);

            const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                // Jika elemen masuk ke viewport (layar)
                if (entry.isIntersecting) {
                    // Hapus class transparan dan posisi turun
                    entry.target.classList.remove('opacity-0', 'translate-y-10');
                    // Tambahkan class normal (muncul penuh dan kembali ke posisi asli)
                    entry.target.classList.add('opacity-100', 'translate-y-0');
                    
                    // (Opsional) Hentikan pengamatan setelah elemen muncul, agar tidak berulang kali animasi
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1 // Memicu animasi ketika 10% bagian dari elemen sudah terlihat di layar
        });

        // Ambil semua elemen yang memiliki class 'reveal-item'
        const revealElements = document.querySelectorAll('.reveal-item');
        revealElements.forEach((el) => observer.observe(el));
    }
    
);
    
    
</script>
</html>