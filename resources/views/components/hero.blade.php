<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out forwards;
    }
    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }
    .delay-300 { animation-delay: 300ms; }
</style>

<section class="relative pt-28 pb-20 lg:pt-36 lg:pb-32 bg-green-50 overflow-hidden">
    
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <svg class="absolute -top-20 -left-20 w-96 h-96 text-green-200 opacity-50 blur-3xl" fill="currentColor" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
            <path d="M45.7,-76.3C58.9,-69.3,69.1,-55.3,77.2,-40.4C85.3,-25.5,91.3,-9.7,89.5,5.4C87.7,20.5,78.2,34.8,67.3,47.2C56.4,59.6,44.1,70,29.8,75.9C15.5,81.8,-0.8,83.1,-16.1,79.8C-31.4,76.5,-45.7,68.6,-57.3,57.4C-68.9,46.2,-77.8,31.7,-81.9,15.8C-86,-0.1,-85.4,-17.3,-78.2,-31.6C-71,-45.9,-57.2,-57.3,-43.1,-63.9C-29,-70.5,-14.5,-72.3,1.2,-74.2C16.9,-76.1,32.5,-83.3,45.7,-76.3Z" transform="translate(100 100)" />
        </svg>
        <svg class="absolute -bottom-32 -right-20 w-[30rem] h-[30rem] text-green-300 opacity-40 blur-3xl" fill="currentColor" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
            <path d="M47.7,-73.3C61.4,-65.4,71.8,-51.7,79.6,-36.4C87.4,-21.1,92.6,-4.2,90.2,11.8C87.8,27.8,77.8,42.9,64.9,53.8C52,64.7,36.2,71.4,19.9,76.4C3.6,81.4,-13.2,84.7,-28.4,80.6C-43.6,76.5,-57.2,65,-68.2,51.3C-79.2,37.6,-87.6,21.7,-88.7,5.3C-89.8,-11.1,-83.6,-28,-72.5,-41.2C-61.4,-54.4,-45.4,-63.9,-30.7,-71.4C-16,-78.9,-2.6,-84.4,6.7,-81.4C16,-78.4,34,-74.9,47.7,-73.3Z" transform="translate(100 100)" />
        </svg>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
            
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm font-semibold mb-6 animate-fade-in-up opacity-0 shadow-sm border border-green-200">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-green-600"></span>
                </span>
                Berdiri Sejak 2024
            </div>

            <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 tracking-tight mb-6 leading-tight animate-fade-in-up delay-100 opacity-0">
                LOREM IPSUM <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-500 to-emerald-700">
                    LOREM IPSUM JUGA
                </span>
            </h1>
            
            <p class="text-lg md:text-xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed animate-fade-in-up delay-200 opacity-0">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 animate-fade-in-up delay-300 opacity-0">
                <a href="#tentang" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-3.5 px-8 rounded-full shadow-lg shadow-green-600/30 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-green-600/50 focus:ring-4 focus:ring-green-300 focus:outline-none">
                    More About Us
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    </div>
</section>