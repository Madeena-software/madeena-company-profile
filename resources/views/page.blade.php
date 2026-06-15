@extends('layouts.app')

@section('title', $page->title . ' - PT Madeena Karya Indonesia')

@push('styles')
    @vite('resources/css/academic-article.css')
@endpush

@push('scripts')
    @vite('resources/js/katex-render.js')
@endpush

@section('content')
<div class="pt-24 pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <article class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-8 md:p-12">
                <h1 class="text-3xl md:text-4xl font-bold text-madeena-blue mb-8 text-center">{{ $page->title }}</h1>
                
                @if($page->content_json)
                <div class="mt-8 prose prose-lg max-w-none text-gray-700">
                    <x-academic-content
                        :content="$page->content_json"
                        :language="$page->content_language"
                        :enableAutoNumbering="$page->enable_auto_numbering"
                    />
                </div>
                @else
                <p class="text-gray-500 text-center">Konten belum tersedia.</p>
                @endif
            </div>
        </article>
    </div>
</div>
@endsection
