@extends('layouts.app')

@section('title', $product->name . ' - PT Madeena Karya Indonesia')

@section('content')
<div class="pt-24 pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}#produk" class="inline-flex items-center gap-2 text-madeena-teal hover:text-madeena-blue transition-colors mb-8">
            <i class="fas fa-arrow-left"></i> Kembali ke Produk
        </a>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            @if($product->image_path)
            <div class="aspect-video bg-gray-50">
                <img src="{{ route('storage.public', ['path' => $product->image_path]) }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-8">
            </div>
            @endif
            <div class="p-8">
                <h1 class="text-3xl font-bold text-madeena-blue mb-3">{{ $product->name }}</h1>
                @if($product->tagline)
                <p class="text-madeena-teal font-medium text-lg mb-6">{{ $product->tagline }}</p>
                @endif
                @if($product->description)
                <div class="prose max-w-none text-gray-600 mb-8">{!! $product->description !!}</div>
                @endif

                @if($product->specifications)
                <div class="mt-8">
                    <h2 class="text-xl font-bold text-madeena-blue mb-4">Spesifikasi Teknis</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-madeena-blue text-white">
                                    <th class="py-3 px-4 text-left">Komponen</th>
                                    <th class="py-3 px-4 text-left">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->specifications as $key => $value)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium text-madeena-blue">{{ $key }}</td>
                                    <td class="py-3 px-4 text-gray-600">{{ $value }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <div class="mt-8">
                    <a href="{{ route('home') }}#kontak" class="btn-primary">Konsultasi Produk Ini</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
