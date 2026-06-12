{{-- sections/timeline.blade.php --}}
@php
    $bg = match($data['background_style'] ?? 'light') {
        'white'    => 'bg-white',
        'dark'     => 'bg-madeena-blue text-white',
        'gradient' => 'bg-gradient-to-br from-madeena-blue to-teal-800 text-white',
        default    => 'bg-madeena-light',
    };
    $isDark = in_array($data['background_style'] ?? 'light', ['dark', 'gradient']);
    $milestones = $data['milestones'] ?? [];
@endphp
<section id="{{ $data['section_id'] ?? 'sejarah' }}" class="py-20 {{ $bg }}">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block {{ $isDark ? 'bg-white/10 text-white' : 'bg-madeena-teal/10 text-madeena-teal' }} font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Sejarah</span>
            <h2 class="section-title {{ $isDark ? 'text-white' : '' }}">{{ $data['section_title'] ?? 'Perjalanan Kami' }}</h2>
        </div>

        @if(!empty($milestones))
        <div class="relative wrap overflow-hidden p-4 md:p-10 h-full">
            <div class="absolute border-opacity-20 border-madeena-teal h-full border-l-2" style="left: 50%;"></div>
            
            @foreach($milestones as $index => $milestone)
            <div class="mb-8 flex justify-between items-center w-full {{ $index % 2 == 0 ? 'flex-row-reverse left-timeline' : 'right-timeline' }}">
                <div class="order-1 w-5/12"></div>
                <div class="z-20 flex items-center order-1 bg-madeena-teal shadow-xl w-10 h-10 rounded-full justify-center">
                    <h1 class="mx-auto font-semibold text-sm text-white">{{ substr($milestone['year'] ?? '', -2) }}</h1>
                </div>
                <div class="order-1 {{ $isDark ? 'bg-white/10 border border-white/20' : 'bg-white shadow-md' }} rounded-xl w-5/12 px-6 py-5">
                    <h3 class="mb-2 font-bold {{ $isDark ? 'text-white' : 'text-madeena-blue' }} text-lg flex items-center gap-2">
                        <span class="text-madeena-teal">{{ $milestone['year'] ?? '' }}</span>
                        <span class="{{ $isDark ? 'text-white/30' : 'text-gray-300' }}">|</span>
                        {{ $milestone['title'] ?? '' }}
                    </h3>
                    <p class="text-sm leading-snug tracking-wide {{ $isDark ? 'text-white/70' : 'text-gray-600' }}">{{ $milestone['description'] ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
