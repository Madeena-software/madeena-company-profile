{{-- sections/stats.blade.php --}}
@php
    $bg = match($data['background_style'] ?? 'dark') {
        'light'    => 'bg-madeena-light',
        'white'    => 'bg-white',
        'gradient' => 'bg-gradient-to-br from-madeena-blue to-teal-800 text-white',
        default    => 'bg-madeena-blue text-white',
    };
    $isDark = in_array($data['background_style'] ?? 'dark', ['dark', 'gradient']);
    $stats = $data['stats'] ?? [];
@endphp
<section id="{{ $data['section_id'] ?? 'statistik' }}" class="py-16 {{ $bg }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(!empty($stats))
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x {{ $isDark ? 'divide-white/10' : 'divide-gray-200' }}">
            @foreach($stats as $stat)
            <div class="px-4">
                @if(!empty($stat['icon']))
                <div class="text-3xl text-madeena-teal mb-4">
                    <i class="fas {{ $stat['icon'] }}"></i>
                </div>
                @endif
                <div class="text-4xl md:text-5xl font-bold {{ $isDark ? 'text-white' : 'text-madeena-blue' }} mb-2">
                    {{ $stat['number'] ?? '0' }}
                </div>
                <div class="text-sm font-medium tracking-wider uppercase {{ $isDark ? 'text-white/60' : 'text-gray-500' }}">
                    {{ $stat['label'] ?? '' }}
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
