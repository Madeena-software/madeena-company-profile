{{-- sections/about.blade.php --}}
@php
    $bg = match($data['background_style'] ?? 'white') {
        'light'    => 'bg-madeena-light',
        'dark'     => 'bg-madeena-blue text-white',
        'gradient' => 'bg-gradient-to-br from-madeena-blue to-teal-800 text-white',
        default    => 'bg-white',
    };
    $isDark = in_array($data['background_style'] ?? 'white', ['dark', 'gradient']);
@endphp
<section id="{{ $data['section_id'] ?? 'tentang' }}" class="py-20 {{ $bg }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="inline-block {{ $isDark ? 'bg-white/10 text-white' : 'bg-madeena-teal/10 text-madeena-teal' }} font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Tentang Kami</span>
            <h2 class="section-title {{ $isDark ? 'text-white' : '' }}">Profil, Visi &amp; Misi</h2>
            <p class="{{ $isDark ? 'text-white/70' : 'section-subtitle' }}">PT Madeena Karya Indonesia &mdash; Inovasi Teknologi Alat Kesehatan Indonesia</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            <div>
                @if(!empty($data['company_profile']))
                <div class="{{ $isDark ? 'bg-white/10 border border-white/20' : 'bg-madeena-light' }} rounded-2xl p-8 mb-8">
                    <h3 class="text-xl font-bold {{ $isDark ? 'text-white' : 'text-madeena-blue' }} mb-4">Tentang Perusahaan</h3>
                    <p class="{{ $isDark ? 'text-white/80' : 'text-gray-600' }} leading-relaxed">{{ $data['company_profile'] }}</p>
                </div>
                @endif
                @if(!empty($data['motto']))
                <div class="{{ $isDark ? 'bg-white/5 border border-white/20' : 'bg-madeena-blue' }} rounded-2xl p-8 text-white">
                    <blockquote class="text-xl font-medium italic text-white/90 mb-2">&ldquo;{{ $data['motto'] }}&rdquo;</blockquote>
                    <p class="text-white/60 text-sm">Kredo PT Madeena Karya Indonesia</p>
                </div>
                @endif
            </div>
            <div class="space-y-6">
                @if(!empty($data['vision']))
                <div class="border-l-4 border-madeena-teal pl-6">
                    <h3 class="text-xl font-bold {{ $isDark ? 'text-white' : 'text-madeena-blue' }} mb-3">Visi</h3>
                    <p class="{{ $isDark ? 'text-white/80' : 'text-gray-600' }} italic">&ldquo;{{ $data['vision'] }}&rdquo;</p>
                </div>
                @endif
                @if(!empty($data['mission']))
                <div class="border-l-4 border-madeena-blue pl-6">
                    <h3 class="text-xl font-bold {{ $isDark ? 'text-white' : 'text-madeena-blue' }} mb-3">Misi</h3>
                    <ol class="list-decimal list-inside space-y-2 {{ $isDark ? 'text-white/80' : 'text-gray-600' }}">
                        @foreach($data['mission'] as $m)
                        <li>{{ $m['item'] ?? $m }}</li>
                        @endforeach
                    </ol>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
