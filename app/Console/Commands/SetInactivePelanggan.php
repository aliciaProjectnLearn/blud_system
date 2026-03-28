<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\BookingFutsal;
use Illuminate\Console\Command;

class SetInactivePelanggan extends Command
{
    protected $signature   = 'pelanggan:set-inactive';
    protected $description = 'Set pelanggan inactive jika tidak booking lebih dari 30 hari';

    public function handle(): void
    {
        $userIds = BookingFutsal::where('jenis_pembayaran', 'reguler')
            ->pluck('user_id')
            ->unique();

        foreach ($userIds as $userId) {
            $lastBooking = BookingFutsal::where('user_id', $userId)
                ->where('jenis_pembayaran', 'reguler')
                ->latest('tgl_main')
                ->first();

            if ($lastBooking && now()->diffInDays($lastBooking->tgl_main, true) > 30) {
                User::where('id', $userId)->update(['status_futsal' => 'inactive']);
            } else {
                User::where('id', $userId)->update(['status_futsal' => 'active']);
            }
        }

        $this->info('Status pelanggan berhasil diperbarui.');
    }
}
