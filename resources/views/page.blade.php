@extends('layouts.app')

@section('title', $page->title . ' - PT Madeena Karya Indonesia')

@section('content')
<div class="pt-16 md:pt-20">
    @if (is_array($page->content))
        @foreach ($page->content as $block)
            @include('partials.blocks.' . $block['type'], ['data' => $block['data']])
        @endforeach
    @else
        <section class="py-16 bg-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-4xl font-extrabold text-madeena-blue mb-8">{{ $page->title }}</h1>
                <div class="prose prose-lg max-w-none text-gray-700">
                    {!! $page->content !!}
                </div>
            </div>
        </section>
    @endif
</div>
@endsection
