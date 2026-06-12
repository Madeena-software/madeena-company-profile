<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // ── Seed the DDR product image ─────────────────────────────────────────
        // Copies public/images/product-ddr.png into Storage::disk('public')
        // so the file lands on the S3-backed (MinIO) storage bucket.
        // Only copies if the source file exists and the destination is missing.
        $imageSourcePath = public_path('images/product-ddr.png');
        $imageDiskPath = 'products/product-ddr.png';

        $imagePath = null;
        try {
            if (File::exists($imageSourcePath) && ! Storage::disk('public')->exists($imageDiskPath)) {
                Storage::disk('public')->put($imageDiskPath, File::get($imageSourcePath));
            }
            $imagePath = Storage::disk('public')->exists($imageDiskPath) ? $imageDiskPath : null;
        } catch (\Exception $e) {
            // Silently ignore S3 connection errors during local development seeding
        }

        // ── Product 1: DDR Madeena HF100B-MDN ─────────────────────────────────
        $product = Product::updateOrCreate(
            ['slug' => 'ddr-madeena-hf100b-mdn'],
            [
                'name' => 'DDR Madeena HF100B-MDN',
                'tagline' => 'Direct Digital Radiography buatan Indonesia — TKDN 57,62%',
                'content_json' => [
                    [
                        'type' => 'academic-paragraph',
                        'data' => [],
                        'content' => [
                            ['type' => 'text', 'text' => 'DDR Madeena HF100B-MDN dikembangkan oleh Universitas Gadjah Mada dan diproduksi oleh PT Madeena Karya Indonesia menggunakan teknologi Camera Coupled X-Ray Detector (CCXD). Tersedia dalam dua modalitas: bedside radiography dan thorax radiography.']
                        ]
                    ],
                    [
                        'type' => 'academic-paragraph',
                        'data' => [],
                        'content' => [
                            ['type' => 'text', 'text' => 'Detektor 12MP 40×30 cm resolusi 16-bit, format citra DICOM, perangkat lunak DR.Grabber dan DICOM Viewer. Garansi sparepart 1 tahun, dukungan sparepart 3 tahun, garansi bodi 5 tahun. Izin Edar Kemenkes RI No. AKD 21501220581.']
                        ]
                    ]
                ],
                'specifications' => [
                    'Detektor Langsung' => 'Madeena 12MP 40×30 cm, Resolusi 16-bit 4096×3000 (12MP)',
                    'Komputer' => 'Prosesor i7, RAM 32 GB; GPU RTX, HDD SATA 1 TB, K/M, 4 USB 3.0, 4 USB 2.0, Wi-Fi, RJ45, 2 HDMI',
                    'Layar' => 'Monitor vertikal 32" 4K, 3840×2160, 10-bit, dilengkapi rangka penyangga',
                    'Radiografi Badan (Bedside)' => '120 cm (P) × 60 cm (L) × 70 cm (T); tinggi sinar X 90 cm; meja pasien: 60 cm × 200 cm; gerak maju-mundur 50 cm',
                    'Radiografi Thorax' => 'Tinggi 130 cm; alas 60 cm × 40 cm; jarak sinar X 90 cm; elevasi 20 cm; beban maksimum 140 kg',
                    'Perangkat Lunak' => 'MS Windows 10 Pro 64-bit; DR.Grabber (Body & Thorax); DICOM Converter; DICOM Viewer',
                    'Format Citra' => 'DICOM 4096×3200 (12MP) & 3200×4096 (12MP), 16-bit',
                    'Garansi Produk' => 'Panduan instalasi & pelatihan daring; garansi sparepart 1 tahun; dukungan sparepart 3 tahun; garansi bodi 5 tahun',
                ],
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        // Set image_path only if the product currently has none — preserves
        // any image later uploaded via the Filament CMS admin panel.
        if (! $product->image_path && $imagePath) {
            $product->update(['image_path' => $imagePath]);
        }

        // ── Product 2: Solusi Ruang Radiografi ────────────────────────────────
        Product::updateOrCreate(
            ['slug' => 'solusi-ruang-radiografi'],
            [
                'name' => 'Solusi Ruang Radiografi',
                'tagline' => 'Paket solusi instalasi ruang radiografi lengkap untuk fasilitas pelayanan kesehatan',
                'content_json' => [
                    [
                        'type' => 'academic-paragraph',
                        'data' => [],
                        'content' => [
                            ['type' => 'text', 'text' => 'PT Madeena menyediakan paket pengadaan dan instalasi ruang radiografi yang mencakup peralatan utama, aksesori pendukung, pemasangan, serta pelatihan operasional bagi tenaga teknis.']
                        ]
                    ],
                    [
                        'type' => 'academic-paragraph',
                        'data' => [],
                        'content' => [
                            ['type' => 'text', 'text' => 'Program kemitraan dirancang secara fleksibel untuk menyesuaikan kebutuhan dan anggaran fasilitas pelayanan kesehatan.']
                        ]
                    ]
                ],
                'specifications' => [],
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 2,
            ]
        );
    }
}
