{{-- sections/current_projects.blade.php --}}
@php
    $bg = match($data['background_style'] ?? 'light') {
        'white'    => 'bg-white',
        'dark'     => 'bg-madeena-blue text-white',
        'gradient' => 'bg-gradient-to-br from-madeena-blue to-teal-800 text-white',
        default    => 'bg-madeena-light',
    };
    $isDark = in_array($data['background_style'] ?? 'light', ['dark', 'gradient']);
    $projects = $data['projects'] ?? [];
@endphp
<section id="{{ $data['section_id'] ?? 'proyek-berjalan' }}" class="py-20 {{ $bg }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="inline-block {{ $isDark ? 'bg-white/10 text-white' : 'bg-madeena-teal/10 text-madeena-teal' }} font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Proyek</span>
            <h2 class="section-title {{ $isDark ? 'text-white' : '' }}">{{ $data['section_title'] ?? 'Proyek Berjalan' }}</h2>
            @if(!empty($data['section_subtitle']))
            <p class="{{ $isDark ? 'text-white/70' : 'section-subtitle' }}">{{ $data['section_subtitle'] }}</p>
            @endif
        </div>

        @if(!empty($projects))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($projects as $project)
            <div class="{{ $isDark ? 'bg-white/10 border border-white/20' : 'bg-white shadow-lg' }} rounded-2xl overflow-hidden flex flex-col group">
                @if(!empty($project['image']))
                <div class="relative h-48 overflow-hidden">
                    <div class="absolute inset-0 bg-madeena-blue/20 group-hover:bg-transparent transition-colors z-10"></div>
                    <img src="{{ route('storage.public', ['path' => $project['image']]) }}" alt="{{ $project['title'] ?? '' }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                </div>
                @endif
                <div class="p-6 flex-grow flex flex-col">
                    <h3 class="text-xl font-bold {{ $isDark ? 'text-white' : 'text-madeena-blue' }} mb-2">{{ $project['title'] ?? '' }}</h3>
                    <p class="text-sm {{ $isDark ? 'text-white/70' : 'text-gray-600' }} mb-6 flex-grow">{{ $project['description'] ?? '' }}</p>
                    
                    @if(!empty($project['progress']))
                    <div class="mt-auto">
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-xs font-semibold text-madeena-teal uppercase">Progress</span>
                            <span class="text-sm font-bold {{ $isDark ? 'text-white' : 'text-madeena-blue' }}">{{ $project['progress'] }}%</span>
                        </div>
                        <div class="w-full {{ $isDark ? 'bg-white/20' : 'bg-gray-200' }} rounded-full h-2">
                            <div class="bg-madeena-teal h-2 rounded-full" @style(["width: {$project['progress']}%"])></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
