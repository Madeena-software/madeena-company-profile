@extends('layouts.app')

@section('title', 'Blog - PT Madeena Karya Indonesia')

@section('content')
<div class="bg-white">
    <!-- Blog Header -->
    <section class="pt-32 pb-16 bg-gradient-to-b from-madeena-light to-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-madeena-blue mb-6">Blog</h1>
            <p class="text-xl text-gray-600">Artikel terbaru tentang inovasi teknologi kesehatan dan perkembangan industri medis</p>
        </div>
    </section>

    <!-- Blog Posts -->
    <section class="py-20">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($posts->isNotEmpty())
            <div class="space-y-16">
                @foreach($posts as $post)
                <article class="pb-16 border-b border-gray-200 last:border-b-0">
                    @if($post->cover_image)
                    <div class="mb-8">
                        <a href="{{ route('post.show', ['post' => $post->slug ?: $post->id]) }}" class="block overflow-hidden rounded-lg hover:opacity-90 transition-opacity">
                            <img src="{{ route('storage.public', ['path' => $post->cover_image]) }}"
                                alt="{{ $post->title }}"
                                class="w-full h-96 object-cover">
                        </a>
                    </div>
                    @endif

                    <!-- Category -->
                    @if($post->category)
                    <div class="mb-4">
                        <span class="inline-block text-sm font-semibold text-madeena-teal bg-madeena-teal/10 px-3 py-1 rounded-full">
                            {{ $post->category }}
                        </span>
                    </div>
                    @endif

                    <!-- Title -->
                    <h2 class="text-3xl md:text-4xl font-bold text-madeena-blue mb-4 hover:text-madeena-teal transition-colors">
                        <a href="{{ route('post.show', ['post' => $post->slug ?: $post->id]) }}">{{ $post->title }}</a>
                    </h2>

                    <!-- Metadata -->
                    <div class="flex flex-wrap items-center gap-4 mb-6 text-gray-500 text-sm">
                        @if($post->author)
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-madeena-teal/20 flex items-center justify-center text-madeena-teal font-semibold">
                                {{ substr($post->author->name, 0, 1) }}
                            </div>
                            <span class="font-medium">{{ $post->author->name }}</span>
                        </div>
                        @endif

                        @if($post->published_at)
                        <span>{{ $post->published_at->format('M d, Y') }}</span>
                        @endif

                        <!-- Reading Time Estimate -->
                        <span>{{ ceil(str_word_count(strip_tags($post->body)) / 200) }} min read</span>
                    </div>

                    <!-- Excerpt -->
                    @if($post->excerpt)
                    <p class="text-lg text-gray-700 leading-relaxed mb-6">{{ $post->excerpt }}</p>
                    @endif

                    <!-- Read More Link -->
                    <a href="{{ route('post.show', ['post' => $post->slug ?: $post->id]) }}"
                        class="inline-flex items-center text-madeena-teal font-semibold hover:text-madeena-blue transition-colors group">
                        Baca Selengkapnya
                        <span class="ml-2 transform group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($posts->hasPages())
            <div class="mt-16 pt-16 border-t border-gray-200">
                <div class="flex items-center justify-center gap-2">
                    @if($posts->onFirstPage())
                    <span class="px-4 py-2 rounded-lg text-gray-400 cursor-not-allowed">← Sebelumnya</span>
                    @else
                    <a href="{{ $posts->previousPageUrl() }}" class="px-4 py-2 rounded-lg bg-madeena-light text-madeena-teal font-semibold hover:bg-madeena-teal hover:text-white transition-colors">← Sebelumnya</a>
                    @endif

                    <div class="flex gap-1">
                        @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
                        @if($page == $posts->currentPage())
                        <span class="px-3 py-2 rounded-lg bg-madeena-teal text-white font-semibold">{{ $page }}</span>
                        @else
                        <a href="{{ $url }}" class="px-3 py-2 rounded-lg bg-madeena-light text-madeena-teal font-semibold hover:bg-madeena-teal hover:text-white transition-colors">{{ $page }}</a>
                        @endif
                        @endforeach
                    </div>

                    @if($posts->hasMorePages())
                    <a href="{{ $posts->nextPageUrl() }}" class="px-4 py-2 rounded-lg bg-madeena-light text-madeena-teal font-semibold hover:bg-madeena-teal hover:text-white transition-colors">Berikutnya →</a>
                    @else
                    <span class="px-4 py-2 rounded-lg text-gray-400 cursor-not-allowed">Berikutnya →</span>
                    @endif
                </div>
            </div>
            @endif
            @else
            <div class="text-center py-20">
                <div class="mb-6">
                    <i class="fas fa-newspaper text-6xl text-gray-300"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-600 mb-2">Belum Ada Blog</h3>
                <p class="text-gray-500">Artikel akan segera hadir di sini.</p>
            </div>
            @endif
        </div>
    </section>
</div>
@endsection