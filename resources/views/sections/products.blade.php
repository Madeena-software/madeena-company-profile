{{-- sections/products.blade.php --}}
@php $products = $section['products'] ?? collect(); @endphp
<section id="{{ $data['section_id'] ?? 'produk' }}" class="py-20 bg-madeena-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block bg-madeena-teal/10 text-madeena-teal font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Produk Kami</span>
            <h2 class="section-title">{{ $data['section_title'] ?? 'Produk Inovasi Teknologi Kesehatan' }}</h2>
            @if(!empty($data['section_subtitle']))
            <p class="section-subtitle">{{ $data['section_subtitle'] }}</p>
            @endif
        </div>

        @if($products->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($products as $product)
            <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 group">
                @if($product->image_path)
                <div class="aspect-video bg-gray-50 overflow-hidden">
                    <img src="{{ route('storage.public', ['path' => $product->image_path]) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-300">
                </div>
                @else
                <div class="aspect-video bg-gradient-to-br from-madeena-blue to-madeena-teal flex items-center justify-center">
                    <i class="fas fa-x-ray text-white text-5xl opacity-50"></i>
                </div>
                @endif
                <div class="p-6">
                    <h3 class="text-xl font-bold text-madeena-blue mb-2">{{ $product->name }}</h3>
                    @if($product->tagline)
                    <p class="text-madeena-teal font-medium text-sm mb-3">{{ $product->tagline }}</p>
                    @endif
                    <a href="{{ route('product.show', ['product' => $product->slug ?: $product->id]) }}"
                       class="mt-4 inline-block text-madeena-teal font-semibold hover:text-madeena-blue transition-colors text-sm">
                        Selengkapnya <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-center text-gray-500">Belum ada produk yang ditampilkan.</p>
        @endif

        <div class="mt-10 text-center">
            <a href="#kontak" class="btn-primary">Konsultasi Produk</a>
        </div>
    </div>
</section>
