{{-- sections/free_text.blade.php --}}
@php
    $bg = match($data['background_style'] ?? 'white') {
        'light'    => 'bg-madeena-light',
        'dark'     => 'bg-madeena-blue text-white',
        'gradient' => 'bg-gradient-to-br from-madeena-blue to-teal-800 text-white',
        default    => 'bg-white',
    };
    $isDark = in_array($data['background_style'] ?? 'white', ['dark', 'gradient']);
    $content = $data['content'] ?? null;
@endphp
<section id="{{ $data['section_id'] ?? 'info' }}" class="py-20 {{ $bg }}">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(!empty($data['section_title']))
        <div class="text-center mb-10">
            <h2 class="{{ $isDark ? 'text-white text-3xl font-bold' : 'section-title' }}">{{ $data['section_title'] }}</h2>
        </div>
        @endif
        @if($content)
        @push('styles')
            @vite('resources/css/academic-article.css')
        @endpush
        @push('scripts')
            @vite('resources/js/katex-render.js')
        @endpush
        <x-academic-content
            :content="$content"
            :language="'id'"
        />
        @endif
    </div>
</section>
