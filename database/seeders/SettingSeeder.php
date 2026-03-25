<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'company_name', 'value' => 'PT Madeena Karya Indonesia', 'group' => 'general'],
            ['key' => 'tagline', 'value' => 'Know Sciences, Learn Engineering, Create Technology, Develop Business.', 'group' => 'general'],
            ['key' => 'email', 'value' => 'madeenajog@gmail.com', 'group' => 'contact'],
            ['key' => 'phone', 'value' => '+62 821 3811 4011', 'group' => 'contact'],
            ['key' => 'whatsapp', 'value' => '+62 857 2830 4141', 'group' => 'contact'],
            ['key' => 'address', 'value' => 'Jl. Lowanu No. 68-72, Sorosutan, Umbulharjo, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55162', 'group' => 'contact'],
            ['key' => 'instagram', 'value' => '', 'group' => 'social'],
            ['key' => 'linkedin', 'value' => '', 'group' => 'social'],
            ['key' => 'youtube', 'value' => '', 'group' => 'social'],
            ['key' => 'meta_title', 'value' => 'PT Madeena Karya Indonesia - Digital Radiography Indonesia', 'group' => 'seo'],
            ['key' => 'meta_description', 'value' => 'PT Madeena Karya Indonesia — produsen alat Digital Direct Radiography (DDR) berbasis teknologi Camera Coupled X-Ray Detector (CCXD) buatan Indonesia. TKDN 57,62%, Izin Edar Kemenkes RI AKD 21501220581.', 'group' => 'seo'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
