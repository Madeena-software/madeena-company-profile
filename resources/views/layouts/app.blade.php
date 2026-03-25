<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $settings['meta_title'] ?? 'PT Madeena Karya Indonesia - Digital Radiography')</title>
    <meta name="description" content="@yield('description', $settings['meta_description'] ?? 'PT Madeena Karya Indonesia — produsen alat Digital Direct Radiography buatan Indonesia.')">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('head')
</head>

<body class="font-sans bg-white text-gray-900 antialiased">

    <header class="fixed top-0 left-0 right-0 z-50 bg-madeena-blue/95 backdrop-blur-sm shadow-lg" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-no-bg.png') }}" alt="Logo Madeena" class="h-10 w-auto">
                    <span class="text-white font-bold text-xl hidden sm:block">Madeena</span>
                </a>

                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-white/90 hover:text-white font-medium transition-colors">Home</a>
                    <a href="{{ route('home') }}#produk" class="text-white/90 hover:text-white font-medium transition-colors">Produk</a>
                    <a href="{{ route('home') }}#tentang" class="text-white/90 hover:text-white font-medium transition-colors">Tentang Kami</a>
                    <a href="{{ route('home') }}#blog" class="text-white/90 hover:text-white font-medium transition-colors">Blog</a>
                    <a href="{{ route('home') }}#kontak"
                        class="bg-madeena-teal text-white font-semibold px-5 py-2 rounded-lg hover:bg-opacity-90 transition-all duration-200">
                        Hubungi Kami
                    </a>
                </nav>

                <button @click="open = !open" class="md:hidden text-white p-2">
                    <i class="fas fa-bars text-xl" x-show="!open"></i>
                    <i class="fas fa-times text-xl" x-show="open"></i>
                </button>
            </div>

            <div x-show="open" x-transition class="md:hidden pb-4 border-t border-white/20 mt-2 pt-4 space-y-2">
                <a href="{{ route('home') }}" class="block text-white/90 hover:text-white font-medium py-2 transition-colors">Home</a>
                <a href="{{ route('home') }}#produk" class="block text-white/90 hover:text-white font-medium py-2 transition-colors">Produk</a>
                <a href="{{ route('home') }}#tentang" class="block text-white/90 hover:text-white font-medium py-2 transition-colors">Tentang Kami</a>
                <a href="{{ route('home') }}#blog" class="block text-white/90 hover:text-white font-medium py-2 transition-colors">Blog</a>
                <a href="{{ route('home') }}#kontak"
                    class="block bg-madeena-teal text-white font-semibold px-5 py-2 rounded-lg text-center mt-2">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="bg-madeena-blue text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('images/logo-no-bg.png') }}" alt="Logo Madeena" class="h-10 w-auto">
                        <span class="font-bold text-xl">Madeena</span>
                    </div>
                    <p class="text-white/70 text-sm leading-relaxed">
                        PT Madeena Karya Indonesia — Produsen alat Digital Radiography buatan Indonesia dengan TKDN 57,62%.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold text-lg mb-4">Navigasi</h4>
                    <ul class="space-y-2 text-white/70">
                        <li><a href="{{ route('home') }}#produk" class="hover:text-white transition-colors">Produk</a></li>
                        <li><a href="{{ route('home') }}#tentang" class="hover:text-white transition-colors">Tentang Kami</a></li>
                        <li><a href="{{ route('home') }}#blog" class="hover:text-white transition-colors">Blog</a></li>
                        <li><a href="{{ route('home') }}#legalitas" class="hover:text-white transition-colors">Legalitas</a></li>
                        <li><a href="{{ route('home') }}#kontak" class="hover:text-white transition-colors">Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-lg mb-4">Kontak</h4>
                    <ul class="space-y-2 text-white/70 text-sm">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-envelope mt-1"></i>
                            <a href="mailto:{{ $settings['email'] ?? 'madeenajog@gmail.com' }}" class="hover:text-white transition-colors">
                                {{ $settings['email'] ?? 'madeenajog@gmail.com' }}
                            </a>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-phone mt-1"></i>
                            <a href="tel:{{ $settings['phone'] ?? '+6282138114011' }}" class="hover:text-white transition-colors">
                                {{ $settings['phone'] ?? '+62 821 3811 4011' }}
                            </a>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fab fa-whatsapp mt-1"></i>
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings['whatsapp'] ?? '6285728304141') }}" target="_blank" class="hover:text-white transition-colors">
                                {{ $settings['whatsapp'] ?? '+62 857 2830 4141' }}
                            </a>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-map-marker-alt mt-1 flex-shrink-0"></i>
                            <span>{{ $settings['address'] ?? 'Jl. Lowanu No. 68-72, Sorosutan, Umbulharjo, Yogyakarta' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-white/20 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex gap-4">
                    @if(!empty($settings['instagram']))
                    <a href="{{ $settings['instagram'] }}" target="_blank" class="text-white/70 hover:text-white transition-colors">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    @endif
                    @if(!empty($settings['linkedin']))
                    <a href="{{ $settings['linkedin'] }}" target="_blank" class="text-white/70 hover:text-white transition-colors">
                        <i class="fab fa-linkedin text-xl"></i>
                    </a>
                    @endif
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings['whatsapp'] ?? '6285728304141') }}" target="_blank" class="text-white/70 hover:text-white transition-colors">
                        <i class="fab fa-whatsapp text-xl"></i>
                    </a>
                    <a href="mailto:{{ $settings['email'] ?? 'madeenajog@gmail.com' }}" class="text-white/70 hover:text-white transition-colors">
                        <i class="fas fa-envelope text-xl"></i>
                    </a>
                </div>
                <p class="text-white/50 text-sm">&copy; {{ date('Y') }} PT Madeena Karya Indonesia. Seluruh hak dilindungi.</p>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/{{ preg_replace('/\D/', '', $settings['whatsapp'] ?? '6285728304141') }}"
        target="_blank" rel="noopener" aria-label="Chat WhatsApp"
        class="fixed bottom-6 right-6 z-50 bg-green-500 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform duration-200">
        <i class="fab fa-whatsapp text-2xl"></i>
    </a>

    @stack('scripts')
</body>

</html>