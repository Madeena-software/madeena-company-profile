{{-- sections/testimonial.blade.php --}}
@php $testimonials = $data['testimonials'] ?? []; @endphp
@if(!empty($testimonials))
<section id="{{ $data['section_id'] ?? 'testimoni' }}" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block bg-madeena-teal/10 text-madeena-teal font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Testimoni</span>
            <h2 class="section-title">{{ $data['section_title'] ?? 'Testimoni' }}</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($testimonials as $testimonial)
            <div class="bg-madeena-light rounded-2xl p-8 flex flex-col gap-4">
                <div class="flex items-center gap-4">
                    @if(!empty($testimonial['photo']))
                    <img src="{{ route('storage.public', ['path' => $testimonial['photo']]) }}"
                         alt="{{ $testimonial['name'] ?? '' }}"
                         class="w-14 h-14 rounded-full object-cover ring-2 ring-madeena-teal">
                    @else
                    <div class="w-14 h-14 rounded-full bg-madeena-teal/20 flex items-center justify-center ring-2 ring-madeena-teal">
                        <span class="text-madeena-teal font-bold text-xl">{{ strtoupper(substr($testimonial['name'] ?? 'T', 0, 1)) }}</span>
                    </div>
                    @endif
                    <div>
                        <p class="font-bold text-madeena-blue">{{ $testimonial['name'] ?? '' }}</p>
                        @if(!empty($testimonial['role']))
                        <p class="text-gray-500 text-sm">{{ $testimonial['role'] }}</p>
                        @endif
                    </div>
                </div>
                @if(!empty($testimonial['quote']))
                <blockquote class="text-gray-700 italic leading-relaxed">&ldquo;{{ $testimonial['quote'] }}&rdquo;</blockquote>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
