{{-- sections/faq.blade.php --}}
@php
    $bg = match($data['background_style'] ?? 'white') {
        'light'    => 'bg-madeena-light',
        'dark'     => 'bg-madeena-blue text-white',
        'gradient' => 'bg-gradient-to-br from-madeena-blue to-teal-800 text-white',
        default    => 'bg-white',
    };
    $isDark = in_array($data['background_style'] ?? 'white', ['dark', 'gradient']);
    $faqs = $data['faqs'] ?? [];
@endphp
<section id="{{ $data['section_id'] ?? 'faq' }}" class="py-20 {{ $bg }}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block {{ $isDark ? 'bg-white/10 text-white' : 'bg-madeena-teal/10 text-madeena-teal' }} font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">FAQ</span>
            <h2 class="section-title {{ $isDark ? 'text-white' : '' }}">{{ $data['section_title'] ?? 'Pertanyaan Umum' }}</h2>
            @if(!empty($data['section_subtitle']))
            <p class="{{ $isDark ? 'text-white/70' : 'section-subtitle' }}">{{ $data['section_subtitle'] }}</p>
            @endif
        </div>

        @if(!empty($faqs))
        <div class="space-y-4" x-data="{ activeIndex: null }">
            @foreach($faqs as $index => $faq)
            <div class="{{ $isDark ? 'bg-white/10 border-white/20' : 'bg-white border-gray-200' }} border rounded-xl overflow-hidden">
                <button 
                    @click="activeIndex === {{ $index }} ? activeIndex = null : activeIndex = {{ $index }}"
                    class="w-full flex items-center justify-between p-6 text-left focus:outline-none transition-colors {{ $isDark ? 'hover:bg-white/5' : 'hover:bg-gray-50' }}"
                >
                    <span class="font-bold text-lg {{ $isDark ? 'text-white' : 'text-madeena-blue' }} pr-8">{{ $faq['question'] ?? '' }}</span>
                    <span class="flex-shrink-0 text-madeena-teal transition-transform duration-300" :class="{ 'rotate-180': activeIndex === {{ $index }} }">
                        <i class="fas fa-chevron-down"></i>
                    </span>
                </button>
                <div 
                    x-show="activeIndex === {{ $index }}" 
                    x-collapse
                    class="border-t {{ $isDark ? 'border-white/20' : 'border-gray-100' }}"
                    style="display: none;"
                >
                    <div class="p-6 {{ $isDark ? 'text-white/80' : 'text-gray-600' }} leading-relaxed">
                        {!! nl2br(e($faq['answer'] ?? '')) !!}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
