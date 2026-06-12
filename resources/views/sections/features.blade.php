{{-- sections/features.blade.php --}}
<section id="{{ $data['section_id'] ?? 'keunggulan' }}" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block bg-madeena-teal/10 text-madeena-teal font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Keunggulan</span>
            <h2 class="section-title">{{ $data['section_title'] ?? 'Keunggulan Teknologi' }}</h2>
        </div>
        @if(!empty($data['items']))
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($data['items'] as $item)
            <div class="bg-white rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow border border-gray-100">
                <div class="w-14 h-14 bg-madeena-teal/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas {{ $item['icon'] ?? 'fa-star' }} text-madeena-teal text-2xl"></i>
                </div>
                <h4 class="font-bold text-madeena-blue mb-2 text-sm">{{ $item['title'] ?? '' }}</h4>
                @if(!empty($item['description']))
                <p class="text-gray-500 text-xs leading-relaxed">{{ $item['description'] }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
