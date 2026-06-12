{{-- sections/map.blade.php --}}
@php
    $bg = match($data['background_style'] ?? 'white') {
        'light'    => 'bg-madeena-light',
        'dark'     => 'bg-madeena-blue text-white',
        'gradient' => 'bg-gradient-to-br from-madeena-blue to-teal-800 text-white',
        default    => 'bg-white',
    };
    $isDark = in_array($data['background_style'] ?? 'white', ['dark', 'gradient']);
@endphp
<section id="{{ $data['section_id'] ?? 'lokasi' }}" class="py-20 {{ $bg }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="inline-block {{ $isDark ? 'bg-white/10 text-white' : 'bg-madeena-teal/10 text-madeena-teal' }} font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Lokasi</span>
            <h2 class="section-title {{ $isDark ? 'text-white' : '' }}">{{ $data['section_title'] ?? 'Temukan Kami' }}</h2>
            @if(!empty($data['address']))
            <p class="{{ $isDark ? 'text-white/70' : 'section-subtitle' }}"><i class="fas fa-map-marker-alt text-madeena-teal mr-2"></i> {{ $data['address'] }}</p>
            @endif
        </div>

        @if(!empty($data['embed_url']))
        <div class="rounded-2xl overflow-hidden shadow-xl aspect-video md:aspect-[21/9] w-full relative">
            <iframe 
                src="{{ $data['embed_url'] }}" 
                class="absolute inset-0 w-full h-full border-0" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
        @endif
    </div>
</section>
