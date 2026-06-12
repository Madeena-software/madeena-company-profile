{{-- sections/gallery.blade.php --}}
@php $images = $data['images'] ?? []; @endphp
@if(!empty($images))
<section id="{{ $data['section_id'] ?? 'galeri' }}" class="py-20 bg-madeena-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block bg-madeena-teal/10 text-madeena-teal font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Galeri</span>
            <h2 class="section-title">{{ $data['section_title'] ?? 'Galeri Foto' }}</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($images as $img)
            @if(!empty($img['image']))
            <div class="group relative overflow-hidden rounded-xl aspect-square bg-gray-100">
                <img src="{{ route('storage.public', ['path' => $img['image']]) }}"
                     alt="{{ $img['caption'] ?? '' }}"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                @if(!empty($img['caption']))
                <div class="absolute inset-0 bg-madeena-blue/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                    <p class="text-white text-sm font-medium">{{ $img['caption'] }}</p>
                </div>
                @endif
            </div>
            @endif
            @endforeach
        </div>
    </div>
</section>
@endif
