{{-- sections/partners.blade.php --}}
@php
    $bg = match($data['background_style'] ?? 'white') {
        'light'    => 'bg-madeena-light',
        'dark'     => 'bg-madeena-blue text-white',
        'gradient' => 'bg-gradient-to-br from-madeena-blue to-teal-800 text-white',
        default    => 'bg-white',
    };
    $isDark = in_array($data['background_style'] ?? 'white', ['dark', 'gradient']);
    $partners = $data['partners'] ?? [];
@endphp
<section id="{{ $data['section_id'] ?? 'mitra' }}" class="py-16 {{ $bg }} border-y {{ $isDark ? 'border-white/10' : 'border-gray-100' }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        @if(!empty($data['section_title']))
        <h3 class="text-sm font-semibold {{ $isDark ? 'text-white/60' : 'text-gray-400' }} tracking-widest uppercase mb-8">
            {{ $data['section_title'] }}
        </h3>
        @endif

        @if(!empty($partners))
        <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16 opacity-70 hover:opacity-100 transition-opacity duration-500">
            @foreach($partners as $partner)
                @if(!empty($partner['logo']))
                    @if(!empty($partner['url']))
                    <a href="{{ $partner['url'] }}" target="_blank" class="block grayscale hover:grayscale-0 transition-all duration-300 transform hover:scale-110">
                        <img src="{{ route('storage.public', ['path' => $partner['logo']]) }}" alt="{{ $partner['name'] ?? 'Mitra' }}" class="h-12 md:h-16 object-contain">
                    </a>
                    @else
                    <div class="block grayscale hover:grayscale-0 transition-all duration-300 transform hover:scale-110">
                        <img src="{{ route('storage.public', ['path' => $partner['logo']]) }}" alt="{{ $partner['name'] ?? 'Mitra' }}" class="h-12 md:h-16 object-contain">
                    </div>
                    @endif
                @endif
            @endforeach
        </div>
        @endif
    </div>
</section>
