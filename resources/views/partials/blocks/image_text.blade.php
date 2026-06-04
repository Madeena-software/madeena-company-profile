@php
    $imagePosition = $data['image_position'] ?? 'left';
    $imageOrder = $imagePosition === 'right' ? 'lg:order-2' : '';
    $textOrder = $imagePosition === 'right' ? 'lg:order-1' : '';
@endphp

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="{{ $imageOrder }}">
                @if (!empty($data['image']))
                    <div class="aspect-video bg-gray-50 rounded-2xl overflow-hidden shadow-md">
                        <img src="{{ route('storage.public', ['path' => $data['image']]) }}" 
                             alt="{{ $data['title'] ?? 'Gambar Halaman' }}" 
                             class="w-full h-full object-cover">
                    </div>
                @endif
            </div>
            <div class="{{ $textOrder }}">
                @if (!empty($data['title']))
                    <h2 class="text-3xl font-bold text-madeena-blue mb-6">{{ $data['title'] }}</h2>
                @endif
                <div class="prose max-w-none text-gray-600 leading-relaxed">
                    {!! $data['content'] !!}
                </div>
            </div>
        </div>
    </div>
</section>
