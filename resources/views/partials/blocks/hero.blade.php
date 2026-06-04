@php
    $bgStyle = $data['bg_style'] ?? 'blue';
    $bgClass = match ($bgStyle) {
        'teal' => 'bg-gradient-to-br from-madeena-teal via-madeena-teal to-teal-700 text-white',
        'gray' => 'bg-gradient-to-br from-gray-50 to-gray-100 text-gray-900 border-b border-gray-200',
        'white' => 'bg-white text-gray-900 border-b border-gray-100',
        default => 'bg-gradient-to-br from-madeena-blue via-madeena-blue to-teal-800 text-white',
    };
    $isDark = in_array($bgStyle, ['blue', 'teal']);
@endphp

<section class="relative min-h-[50vh] flex items-center py-20 {{ $bgClass }}">
    @if ($isDark)
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute inset-0" style='background-image: url("data:image/svg+xml,%3Csvg width=%2760%27 height=%2760%27 viewBox=%270 0 60 60%27 xmlns=%27http://www.w3.org/2000/svg%27%3E%3Cg fill=%27none%27 fill-rule=%27evenodd%27%3E%3Cg fill=%27%23ffffff%27 fill-opacity=%270.4%27%3E%3Cpath d=%27M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z%27/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")'></div>
    </div>
    @endif
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full text-center">
        <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-6">
            {{ $data['title'] }}
        </h1>
        @if (!empty($data['subtitle']))
            <p class="text-lg md:text-xl font-medium mb-8 {{ $isDark ? 'text-madeena-teal' : 'text-gray-600' }}">
                {{ $data['subtitle'] }}
            </p>
        @endif
        @if (!empty($data['cta_text']) && !empty($data['cta_url']))
            <div>
                <a href="{{ $data['cta_url'] }}" class="btn-primary inline-block text-lg">
                    {{ $data['cta_text'] }}
                </a>
            </div>
        @endif
    </div>
</section>
