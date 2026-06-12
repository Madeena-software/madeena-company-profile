{{-- sections/project_investment.blade.php --}}
@php
    $bg = match($data['background_style'] ?? 'dark') {
        'light'    => 'bg-madeena-light',
        'white'    => 'bg-white',
        'gradient' => 'bg-gradient-to-br from-madeena-blue to-teal-800 text-white',
        default    => 'bg-madeena-blue text-white',
    };
    $isDark = in_array($data['background_style'] ?? 'dark', ['dark', 'gradient']);
@endphp
<section id="{{ $data['section_id'] ?? 'investasi' }}" class="py-20 {{ $bg }}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-12 items-center">
            <div class="lg:w-1/2">
                <span class="inline-block {{ $isDark ? 'bg-white/10 text-white' : 'bg-madeena-teal/10 text-madeena-teal' }} font-semibold text-sm uppercase tracking-wider px-3 py-1 rounded-full mb-4">Peluang Investasi</span>
                <h2 class="text-3xl md:text-4xl font-bold {{ $isDark ? 'text-white' : 'text-madeena-blue' }} mb-6 leading-tight">
                    {{ $data['section_title'] ?? 'Investasi Proyek Alkes' }}
                </h2>
                @if(!empty($data['description']))
                <div class="{{ $isDark ? 'text-white/80' : 'text-gray-600' }} text-lg mb-8 leading-relaxed space-y-4">
                    {!! nl2br(e($data['description'])) !!}
                </div>
                @endif
                
                @if(!empty($data['button_text']) && !empty($data['button_url']))
                <a href="{{ $data['button_url'] }}" class="inline-flex items-center justify-center bg-madeena-teal hover:bg-teal-500 text-white font-bold px-8 py-3 rounded-full shadow-lg transition-colors gap-2">
                    <i class="fas fa-file-download"></i> {{ $data['button_text'] }}
                </a>
                @endif
            </div>
            
            <div class="lg:w-1/2 w-full">
                <div class="{{ $isDark ? 'bg-white/10 border border-white/20' : 'bg-white shadow-xl border border-gray-100' }} p-8 rounded-2xl">
                    <h3 class="text-xl font-bold {{ $isDark ? 'text-white' : 'text-madeena-blue' }} mb-6 border-b {{ $isDark ? 'border-white/10' : 'border-gray-100' }} pb-4">Highlight Investasi</h3>
                    
                    <div class="space-y-6">
                        @if(!empty($data['target_funding']))
                        <div>
                            <p class="text-sm {{ $isDark ? 'text-white/60' : 'text-gray-500' }} mb-1">Target Pendanaan</p>
                            <p class="text-2xl font-bold text-madeena-teal">{{ $data['target_funding'] }}</p>
                        </div>
                        @endif
                        
                        @if(!empty($data['roi']))
                        <div>
                            <p class="text-sm {{ $isDark ? 'text-white/60' : 'text-gray-500' }} mb-1">Estimasi ROI / Benefit</p>
                            <p class="text-xl font-semibold {{ $isDark ? 'text-white' : 'text-madeena-blue' }}">{{ $data['roi'] }}</p>
                        </div>
                        @endif

                        @if(!empty($data['highlights']))
                        <div class="pt-4">
                            <ul class="space-y-3">
                                @foreach($data['highlights'] as $highlight)
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check-circle text-madeena-teal mt-1"></i>
                                    <span class="{{ $isDark ? 'text-white/80' : 'text-gray-700' }} text-sm">{{ $highlight['item'] ?? $highlight }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
