{{-- sections/video.blade.php --}}
@php
    $videoUrl = $data['video_url'] ?? '';
    // Convert YouTube watch URL to embed URL
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]+)/', $videoUrl, $m)) {
        $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
    } elseif (preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $m)) {
        $embedUrl = 'https://player.vimeo.com/video/' . $m[1];
    } else {
        $embedUrl = $videoUrl;
    }
@endphp
@if(!empty($videoUrl))
<section id="{{ $data['section_id'] ?? 'video' }}" class="py-20 bg-madeena-light">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(!empty($data['section_title']))
        <div class="text-center mb-10">
            <h2 class="section-title">{{ $data['section_title'] }}</h2>
        </div>
        @endif
        <div class="relative aspect-video rounded-2xl overflow-hidden shadow-2xl">
            <iframe src="{{ $embedUrl }}"
                    title="{{ $data['section_title'] ?? 'Video' }}"
                    class="w-full h-full"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
        </div>
        @if(!empty($data['video_caption']))
        <p class="text-center text-gray-500 mt-4 text-sm italic">{{ $data['video_caption'] }}</p>
        @endif
    </div>
</section>
@endif
