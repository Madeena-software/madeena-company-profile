<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ \App\Filament\Resources\PostResource::getUrl('create') }}" class="block p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm hover:ring-2 hover:ring-primary-500 transition duration-200">
            <div class="flex flex-col items-center justify-center text-center">
                <x-heroicon-o-pencil-square class="w-10 h-10 text-primary-500 mb-3" />
                <h3 class="text-lg font-semibold text-gray-950 dark:text-white">Tulis Artikel Baru</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Buat artikel penelitian atau berita terbaru.</p>
            </div>
        </a>

        <a href="{{ \App\Filament\Pages\HomepageEditor::getUrl() }}" class="block p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm hover:ring-2 hover:ring-primary-500 transition duration-200">
            <div class="flex flex-col items-center justify-center text-center">
                <x-heroicon-o-home class="w-10 h-10 text-success-500 mb-3" />
                <h3 class="text-lg font-semibold text-gray-950 dark:text-white">Edit Halaman Utama</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ubah konten, slider, dan info halaman depan.</p>
            </div>
        </a>

        <a href="{{ \App\Filament\Resources\ProductResource::getUrl('create') }}" class="block p-6 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl shadow-sm hover:ring-2 hover:ring-primary-500 transition duration-200">
            <div class="flex flex-col items-center justify-center text-center">
                <x-heroicon-o-beaker class="w-10 h-10 text-info-500 mb-3" />
                <h3 class="text-lg font-semibold text-gray-950 dark:text-white">Tambah Produk Baru</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Input data produk inovasi dan alat kesehatan.</p>
            </div>
        </a>
    </div>
</x-filament-widgets::widget>
