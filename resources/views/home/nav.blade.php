<!-- NAVBAR -->
<nav id="navbar"
    class="fixed top-0 inset-x-0 z-[100] px-[5vw] h-[72px] flex items-center justify-between transition-all duration-500">

    <!-- LOGO -->
    <a href="#" class="flex items-center gap-2 group">
        <div
            class="w-9 h-9 bg-green-500 rounded-xl flex items-center justify-center group-hover:rotate-6 transition-transform">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5"
                viewBox="0 0 24 24">
                <path d="M12 3v19M5 8h14M7 15h10" stroke-linecap="round" />
            </svg>
        </div>
        <span class="font-serif text-xl text-white tracking-tight">Smart Pakcoy</span>
    </a>

    <!-- MENU DESKTOP -->
    <div class="hidden md:flex items-center gap-10">
        <div class="flex gap-8 text-white/80 text-[13px] font-bold uppercase tracking-widest">
            <a href="#abouttfarm" class="hover:text-green-400 transition-colors">Tentang</a>
            <a href="#fitur" class="hover:text-green-400 transition-colors">Fitur</a>
            <a href="#sensor" class="hover:text-green-400 transition-colors">Teknologi</a>
        </div>

        <a href="{{ route('login') }}"
            class="bg-green-500 hover:bg-green-400 text-white px-6 py-2.5 rounded-full font-bold text-sm shadow-lg shadow-green-500/20 transition-all hover:-translate-y-1">
            Dashboard Admin
        </a>
    </div>

    <!-- BUTTON HAMBURGER -->
    <button id="menu-btn"
        class="md:hidden flex flex-col justify-center gap-1.5 w-10 h-10 items-center">
        <span class="w-6 h-0.5 bg-white transition-all duration-300"></span>
        <span class="w-6 h-0.5 bg-white transition-all duration-300"></span>
        <span class="w-6 h-0.5 bg-white transition-all duration-300"></span>
    </button>
</nav>

<!-- MOBILE MENU -->
<div id="mobile-menu"
    class="fixed top-[72px] left-0 w-full bg-green-600/30 backdrop-blur-xl z-[99]
           transform -translate-y-full opacity-0 transition-all duration-500 md:hidden">

    <div class="flex flex-col items-start gap-6 py-10 text-white px-8">
        <a href="#abouttfarm" class="hover:text-green-400 transition-colors">Tentang</a>
        <a href="#fitur" class="hover:text-green-400 transition-colors">Fitur</a>
        <a href="#sensor" class="hover:text-green-400 transition-colors">Teknologi</a>

        <a href="{{ route('login') }}"
            class="bg-green-500 hover:bg-green-400 text-white px-6 py-3 rounded-full font-bold text-sm shadow-lg shadow-green-500/20 transition-all">
            Dashboard Admin
        </a>
    </div>
</div>

<!-- SCRIPT -->
<script>
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    let isOpen = false;

    menuBtn.addEventListener('click', () => {
        isOpen = !isOpen;

        if (isOpen) {
            mobileMenu.classList.remove('-translate-y-full', 'opacity-0');
            mobileMenu.classList.add('translate-y-0', 'opacity-100');
        } else {
            mobileMenu.classList.add('-translate-y-full', 'opacity-0');
            mobileMenu.classList.remove('translate-y-0', 'opacity-100');
        }
    });
</script>