<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;
use App\Models\User;

class TestimonialSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        $userId = $user ? $user->id : 1;

        $testimonials = [
            // 5 Approved
            [
                'user_id' => $userId,
                'content' => 'Layanannya sangat memuaskan dan cepat! Fasilitas BLUD sangat membantu saya dalam melakukan penyewaan kantin.',
                'rating' => 5,
                'status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => now(),
            ],
            [
                'user_id' => $userId,
                'content' => 'Sistem booking futsal yang sangat praktis. Tidak perlu repot-repot datang ke lokasi lagi untuk booking.',
                'rating' => 4,
                'status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => now(),
            ],
            [
                'user_id' => $userId,
                'content' => 'Pelayanan servis AC sangat profesional. Teknisi datang tepat waktu dan kerjanya rapi.',
                'rating' => 5,
                'status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => now(),
            ],
            [
                'user_id' => $userId,
                'content' => 'Katalog ruko sangat jelas dan transparan. Sangat mudah untuk melihat mana yang tersedia.',
                'rating' => 5,
                'status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => now(),
            ],
            [
                'user_id' => $userId,
                'content' => 'Aplikasi sangat responsif dan UI-nya juga bagus. Lanjutkan terus inovasinya!',
                'rating' => 4,
                'status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => now(),
            ],
            // 2 Pending
            [
                'user_id' => $userId,
                'content' => 'Saran saya agar metode pembayarannya diperbanyak lagi, misalnya tambah e-wallet lain.',
                'rating' => 4,
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'user_id' => $userId,
                'content' => 'Secara keseluruhan bagus, tapi kadang loading websitenya agak lambat saat jam sibuk.',
                'rating' => 3,
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}
