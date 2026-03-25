@extends('layouts.app')

@section('title', $post->title . ' - PT Madeena Karya Indonesia')

@section('content')
<div class="pt-24 pb-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 text-madeena-teal hover:text-madeena-blue transition-colors mb-8">
            <i class="fas fa-arrow-left"></i> Kembali ke Blog
        </a>

        <article class="bg-white rounded-2xl shadow-lg overflow-hidden">
            @if($post->cover_image)
            <div class="aspect-video overflow-hidden">
                <img src="{{ Storage::url($post->cover_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </div>
            @endif
            <div class="p-8">
                @if($post->category)
                <span class="inline-block text-xs font-semibold text-madeena-teal bg-madeena-teal/10 px-3 py-1 rounded-full mb-4">{{ $post->category }}</span>
                @endif
                <h1 class="text-3xl font-bold text-madeena-blue mb-4">{{ $post->title }}</h1>
                @if($post->published_at)
                <p class="text-gray-400 text-sm mb-6">{{ $post->published_at->format('d F Y') }}</p>
                @endif
                @if($post->body)
                <div class="prose max-w-none text-gray-700">{!! $post->body !!}</div>
                @endif
            </div>
        </article>
    </div>
</div>
@endsection