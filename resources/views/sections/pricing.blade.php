{{-- sections/pricing.blade.php --}}
@php
    $bg = match($data['background_style'] ?? 'light') {
        'white'    => 'bg-white',
        'dark'     => 'bg-madeena-blue text-white',
        'gradient' => 'bg-gradient-to-br from-madeena-blue to-teal-800 text-white',
        default    => 'bg-madeena-light',
    };
    $isDark = in_array($data['background_style'] ?? 'light', ['dark', 'gradient']);
    $plans = $data['plans'] ?? [];
@endphp
<section id="{{ $data['section_id'] ?? 'harga' }}" class="py-20 {{ $bg }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block {{ $isDark ? 'bg-white/10 text-white' : 'bg-madeena-teal/10 text-madeena-teal' }} font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Penawaran</span>
            <h2 class="section-title {{ $isDark ? 'text-white' : '' }}">{{ $data['section_title'] ?? 'Daftar Harga & Paket' }}</h2>
            @if(!empty($data['section_subtitle']))
            <p class="{{ $isDark ? 'text-white/70' : 'section-subtitle' }}">{{ $data['section_subtitle'] }}</p>
            @endif
        </div>

        @if(!empty($plans))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min(count($plans), 4) }} gap-8 items-center max-w-5xl mx-auto">
            @foreach($plans as $plan)
            @php $isFeatured = !empty($plan['is_featured']); @endphp
            <div class="relative bg-white rounded-2xl shadow-lg {{ $isFeatured ? 'ring-4 ring-madeena-teal transform md:-translate-y-4 shadow-2xl z-10' : 'border border-gray-100' }} overflow-hidden p-8">
                @if($isFeatured)
                <div class="absolute top-0 right-0 bg-madeena-teal text-white text-xs font-bold uppercase tracking-wider py-1 px-3 rounded-bl-lg">
                    Rekomendasi
                </div>
                @endif
                <h3 class="text-xl font-bold text-madeena-blue mb-2">{{ $plan['name'] ?? '' }}</h3>
                <div class="flex items-baseline gap-1 mb-6">
                    <span class="text-3xl font-extrabold text-madeena-teal">{{ $plan['price'] ?? 'Hubungi Kami' }}</span>
                </div>
                @if(!empty($plan['features']))
                <ul class="space-y-4 mb-8">
                    @foreach($plan['features'] as $feature)
                    <li class="flex items-start gap-3">
                        <i class="fas fa-check text-madeena-teal mt-1"></i>
                        <span class="text-gray-600 text-sm">{{ $feature['item'] ?? $feature }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif
                <a href="{{ $plan['button_url'] ?? '#kontak' }}" class="block w-full text-center {{ $isFeatured ? 'btn-primary' : 'btn-secondary' }}">
                    {{ $plan['button_text'] ?? 'Pilih Paket' }}
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
