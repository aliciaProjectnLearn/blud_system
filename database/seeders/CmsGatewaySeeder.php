<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CmsGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // HERO
            ['key' => 'hero_eyebrow',   'label' => 'Teks Kecil di Atas Judul Hero',  
             'grup' => 'hero',     'tipe' => 'text',
             'value' => 'SATU TEMPAT UNTUK SEMUA KEBUTUHAN', 'urutan' => 1],
            ['key' => 'hero_title',     'label' => 'Judul Utama Hero',                
             'grup' => 'hero',     'tipe' => 'text',
             'value' => 'Booking Layanan Kapan Saja, Dari Mana Saja.', 'urutan' => 2],
            ['key' => 'hero_subtitle',  'label' => 'Subjudul Hero',                   
             'grup' => 'hero',     'tipe' => 'textarea',
             'value' => 'Nikmati Kemudahan Dalam Melakukan Booking Online di Berbagai Layanan.', 
             'urutan' => 3],
            ['key' => 'hero_cta_text',  'label' => 'Teks Tombol CTA Hero',            
             'grup' => 'hero',     'tipe' => 'text',
             'value' => 'Jelajahi Layanan', 'urutan' => 4],

            // SECTION LAYANAN
            ['key' => 'layanan_heading',    'label' => 'Judul Section Layanan',       
             'grup' => 'layanan',  'tipe' => 'text',
             'value' => 'Layanan Kami', 'urutan' => 1],
            ['key' => 'layanan_subheading', 'label' => 'Subjudul Section Layanan',    
             'grup' => 'layanan',  'tipe' => 'textarea',
             'value' => 'Temukan layanan yang sesuai dengan kebutuhan Anda', 
             'urutan' => 2],

            // SECTION KEUNGGULAN (ditambahkan berdasarkan prompt bagian 6)
            ['key' => 'keunggulan_heading',    'label' => 'Judul Section Keunggulan',       
             'grup' => 'keunggulan',  'tipe' => 'text',
             'value' => 'Mengapa Memilih Kami?', 'urutan' => 1],
            ['key' => 'keunggulan_subheading', 'label' => 'Subjudul Section Keunggulan',    
             'grup' => 'keunggulan',  'tipe' => 'textarea',
             'value' => 'Kami berkomitmen memberikan layanan cepat, terintegrasi, dan transparan.', 
             'urutan' => 2],

            // FOOTER
            ['key' => 'footer_brand',     'label' => 'Nama Brand di Footer',          
             'grup' => 'footer',   'tipe' => 'text',
             'value' => 'BLUD PORTAL', 'urutan' => 1],
            ['key' => 'footer_tagline',   'label' => 'Tagline Footer',                
             'grup' => 'footer',   'tipe' => 'textarea',
             'value' => 'Platform terintegrasi untuk layanan publik SMKN 1 Cirebon yang lebih baik dan efisien.', 
             'urutan' => 2],
            ['key' => 'footer_copyright', 'label' => 'Teks Copyright',                
             'grup' => 'footer',   'tipe' => 'text',
             'value' => 'BLUD SMKN 1 Cirebon. Semua hak dilindungi undang-undang.', 
             'urutan' => 3],
            ['key' => 'footer_link_1',    'label' => 'Tautan Cepat 1',                
             'grup' => 'footer',   'tipe' => 'text',
             'value' => 'Kebijakan Privasi', 'urutan' => 4],
            ['key' => 'footer_link_2',    'label' => 'Tautan Cepat 2',                
             'grup' => 'footer',   'tipe' => 'text',
             'value' => 'Syarat & Ketentuan', 'urutan' => 5],
            ['key' => 'footer_link_3',    'label' => 'Tautan Cepat 3',                
             'grup' => 'footer',   'tipe' => 'text',
             'value' => 'Bantuan', 'urutan' => 6],
            ['key' => 'footer_link_4',    'label' => 'Tautan Cepat 4',                
             'grup' => 'footer',   'tipe' => 'text',
             'value' => 'FAQ', 'urutan' => 7],
        ];

        foreach ($items as $item) {
            \App\Models\CmsGateway::updateOrCreate(['key' => $item['key']], $item);
        }
    }
}
