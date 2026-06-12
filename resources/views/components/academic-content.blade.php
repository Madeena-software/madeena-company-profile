@props([
    'content' => [],
    'language' => 'id',
    'enableAutoNumbering' => true,
])

@php
    $renderer = new \App\Services\AcademicContentRenderer($language, $enableAutoNumbering);
    $html = $renderer->render($content);
@endphp

<div class="academic-article">
    {!! $html !!}
</div>
