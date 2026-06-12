{{-- sections/team.blade.php --}}
@php
    $bg = match($data['background_style'] ?? 'light') {
        'white'    => 'bg-white',
        'dark'     => 'bg-madeena-blue text-white',
        'gradient' => 'bg-gradient-to-br from-madeena-blue to-teal-800 text-white',
        default    => 'bg-madeena-light',
    };
    $isDark = in_array($data['background_style'] ?? 'light', ['dark', 'gradient']);
    $members = $data['members'] ?? [];
@endphp
<section id="{{ $data['section_id'] ?? 'tim' }}" class="py-20 {{ $bg }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block {{ $isDark ? 'bg-white/10 text-white' : 'bg-madeena-teal/10 text-madeena-teal' }} font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Tim Kami</span>
            <h2 class="section-title {{ $isDark ? 'text-white' : '' }}">{{ $data['section_title'] ?? 'Struktur Organisasi' }}</h2>
            @if(!empty($data['section_subtitle']))
            <p class="{{ $isDark ? 'text-white/70' : 'section-subtitle' }}">{{ $data['section_subtitle'] }}</p>
            @endif
        </div>

        @if(!empty($members))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($members as $member)
            <div class="{{ $isDark ? 'bg-white/10 border border-white/20' : 'bg-white shadow-md hover:shadow-lg' }} rounded-2xl overflow-hidden transition-all duration-300 group text-center p-6">
                <div class="relative w-32 h-32 mx-auto mb-6">
                    <div class="absolute inset-0 bg-madeena-teal rounded-full opacity-0 group-hover:opacity-20 scale-110 transition-all duration-300"></div>
                    @if(!empty($member['photo']))
                    <img src="{{ route('storage.public', ['path' => $member['photo']]) }}" alt="{{ $member['name'] ?? '' }}" class="w-full h-full object-cover rounded-full ring-4 {{ $isDark ? 'ring-white/20' : 'ring-madeena-light' }} relative z-10">
                    @else
                    <div class="w-full h-full bg-madeena-teal/20 rounded-full flex items-center justify-center ring-4 {{ $isDark ? 'ring-white/20' : 'ring-madeena-light' }} relative z-10">
                        <i class="fas fa-user text-4xl text-madeena-teal"></i>
                    </div>
                    @endif
                </div>
                <h3 class="text-xl font-bold {{ $isDark ? 'text-white' : 'text-madeena-blue' }} mb-1">{{ $member['name'] ?? '' }}</h3>
                <p class="text-madeena-teal font-medium text-sm mb-4">{{ $member['role'] ?? '' }}</p>
                @if(!empty($member['bio']))
                <p class="{{ $isDark ? 'text-white/70' : 'text-gray-600' }} text-sm leading-relaxed">{{ $member['bio'] }}</p>
                @endif
                @if(!empty($member['linkedin']))
                <a href="{{ $member['linkedin'] }}" target="_blank" class="inline-block mt-4 text-gray-400 hover:text-madeena-teal transition-colors">
                    <i class="fab fa-linkedin text-xl"></i>
                </a>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
