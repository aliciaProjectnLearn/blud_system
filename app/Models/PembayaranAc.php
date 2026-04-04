<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranAc extends Model
{
    protected $table = 'pembayaran_ac';

    protected $fillable = [
        'booking_id',
        'total_harga',
        'tgl_bayar',
        'tipe_pembayaran_id',
        'status',
        'bukti',
        'invoice_no',
    ];

    public function bookingAc()
    {
        return $this->belongsTo(BookingAc::class, 'booking_id');
    }

    public function tipePembayaran()
    {
        return $this->belongsTo(TipePembayaran::class, 'tipe_pembayaran_id');
    }

    public function detailServis()
    {
        return $this->hasMany(DetailServis::class, 'booking_id', 'booking_id');
    }

    /**
     * Generate Nomor Invoice Unik: INV-YYYYMMDD-XXXX
     */
    public static function generateInvoiceNo()
    {
        $date = now()->format('Ymd');
        $lastInvoice = self::where('invoice_no', 'like', "INV-$date-%")->orderBy('invoice_no', 'desc')->first();

        if ($lastInvoice) {
            $lastNo = intval(substr($lastInvoice->invoice_no, -4));
            $newNo = str_pad($lastNo + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNo = '0001';
        }

        return "INV-$date-$newNo";
    }
}
