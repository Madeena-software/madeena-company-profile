{{-- sections/about.blade.php --}}
@php
    $bg = match($data['background_style'] ?? 'white') {
        'light'    => 'bg-madeena-light',
        'dark'     => 'bg-madeena-blue text-white',
        'gradient' => 'bg-gradient-to-br from-madeena-blue to-teal-800 text-white',
        default    => 'bg-white',
    };
    $isDark = in_array($data['background_style'] ?? 'white', ['dark', 'gradient']);
    $page = $section['page'] ?? null;
@endphp
<section id="{{ $data['section_id'] ?? 'tentang' }}" class="py-20 {{ $bg }}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        @if($page)
            <span class="inline-block {{ $isDark ? 'bg-white/10 text-white' : 'bg-madeena-teal/10 text-madeena-teal' }} font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Tentang Kami</span>
            <h2 class="text-3xl md:text-4xl font-bold mb-6 {{ $isDark ? 'text-white' : 'text-madeena-blue' }}">{{ $page->title }}</h2>
            
            @if($page->summary)
                <p class="text-lg {{ $isDark ? 'text-white/80' : 'text-gray-600' }} leading-relaxed mb-10">
                    {{ $page->summary }}
                </p>
            @endif

            <a href="{{ route('page.show', $page->slug) }}" class="inline-block px-8 py-4 rounded-full font-semibold transition duration-300 {{ $isDark ? 'bg-white text-madeena-blue hover:bg-gray-100' : 'bg-madeena-teal text-white hover:bg-teal-600' }} shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                Baca Selengkapnya
            </a>
        @else
            <p class="{{ $isDark ? 'text-white/50' : 'text-gray-500' }}">Halaman belum diatur.</p>
        @endif
    </div>
</section>
