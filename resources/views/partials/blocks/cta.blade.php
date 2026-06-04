<section class="py-16 bg-gradient-to-br from-madeena-blue to-teal-800 text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $data['title'] }}</h2>
        @if (!empty($data['description']))
            <p class="text-white/80 text-lg mb-8 leading-relaxed">{{ $data['description'] }}</p>
        @endif
        @if (!empty($data['button_text']) && !empty($data['button_url']))
            <a href="{{ $data['button_url'] }}" class="btn-primary text-lg inline-block">
                {{ $data['button_text'] }}
            </a>
        @endif
    </div>
</section>
