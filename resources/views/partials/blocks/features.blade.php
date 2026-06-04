<section class="py-16 bg-madeena-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (!empty($data['title']))
            <div class="text-center mb-12">
                <h2 class="section-title">{{ $data['title'] }}</h2>
                @if (!empty($data['subtitle']))
                    <p class="section-subtitle">{{ $data['subtitle'] }}</p>
                @endif
            </div>
        @endif

        @if (!empty($data['items']))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($data['items'] as $item)
                    <div class="bg-white rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-14 h-14 bg-madeena-teal/10 rounded-full flex items-center justify-center mb-6">
                            <i class="fas {{ $item['icon'] ?? 'fa-star' }} text-madeena-teal text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-madeena-blue mb-3">{{ $item['title'] }}</h3>
                        <p class="text-gray-600 leading-relaxed text-sm">{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
