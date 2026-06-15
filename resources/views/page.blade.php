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
                
                @if(is_array($page->content_json) && count($page->content_json) > 0)
                <div class="mt-8 prose prose-lg max-w-none text-gray-700">
                    @foreach($page->content_json as $index => $section)
                        @if(isset($section['type']) && $section['type'] === 'free_text')
                            <x-academic-content
                                :content="$section['data']['content'] ?? []"
                                :language="$page->content_language"
                                :enableAutoNumbering="$page->enable_auto_numbering"
                            />
                        @elseif(isset($section['type']))
                            @includeIf('sections.' . $section['type'], [
                                'data'    => $section['data'] ?? [],
                                'section' => $section,
                                'index'   => $index,
                            ])
                        @endif
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center">Konten belum tersedia.</p>
                @endif
            </div>
        </article>
    </div>
</div>
@endsection
