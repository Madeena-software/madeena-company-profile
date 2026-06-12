{{-- sections/legalities.blade.php --}}
@php
    $bg = match($data['background_style'] ?? 'dark') {
        'light'    => 'bg-madeena-light',
        'white'    => 'bg-white',
        'gradient' => 'bg-gradient-to-br from-madeena-blue to-teal-800 text-white',
        default    => 'bg-madeena-blue text-white',
    };
    $isDark = in_array($data['background_style'] ?? 'dark', ['dark', 'gradient']);
@endphp
<section id="{{ $data['section_id'] ?? 'legalitas' }}" class="py-20 {{ $bg }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block {{ $isDark ? 'bg-white/10 text-white' : 'bg-madeena-teal/10 text-madeena-teal' }} font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Legalitas</span>
            <h2 class="text-3xl md:text-4xl font-bold {{ $isDark ? 'text-white' : 'text-madeena-blue' }} mb-4">{{ $data['section_title'] ?? 'Legalitas Formal' }}</h2>
            @if(!empty($data['section_subtitle']))
            <p class="{{ $isDark ? 'text-white/70' : 'text-gray-600' }} text-lg">{{ $data['section_subtitle'] }}</p>
            @endif
        </div>
        @if(!empty($data['certificates']))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($data['certificates'] as $cert)
            <div class="{{ $isDark ? 'bg-white/10 border-white/20 hover:bg-white/20' : 'bg-gray-50 border-gray-200 hover:bg-white' }} backdrop-blur rounded-xl p-6 border transition-colors">
                <div class="w-12 h-12 bg-madeena-teal/30 rounded-lg flex items-center justify-center mb-4">
                    <i class="fas {{ $cert['icon'] ?? 'fa-certificate' }} text-madeena-teal text-xl"></i>
                </div>
                <h3 class="font-bold {{ $isDark ? 'text-white' : 'text-madeena-blue' }} mb-2">{{ $cert['title'] ?? '' }}</h3>
                <p class="{{ $isDark ? 'text-white/70' : 'text-gray-600' }} text-sm">{{ $cert['detail'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
