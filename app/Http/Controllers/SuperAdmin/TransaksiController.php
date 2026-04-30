<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sistem = $request->sistem;
        $status = $request->status;
        $perPage = in_array($request->per_page, [10, 25, 50, 100]) ? (int) $request->per_page : 10;

        $ac = DB::table('pembayaran_ac')
            ->select(
                'id',
                DB::raw("'AC' as sistem"),
                'total_harga as total',
                'status',
                'tgl_bayar',
                'created_at'
            );

        $futsal = DB::table('pembayaran_futsal')
            ->select(
                'id',
                DB::raw("'Futsal' as sistem"),
                'jumlah_bayar as total',
                'status',
                'tgl_bayar',
                'created_at'
            );

        $ruko = DB::table('pembayaran_ruko')
            ->select(
                'id',
                DB::raw("'Ruko' as sistem"),
                'jumlah_tagihan as total',
                'status',
                'tgl_bayar',
                'created_at'
            );

        $servis = DB::table('pembayaran_servis')
            ->select(
                'id',
                DB::raw("'Servis Kendaraan' as sistem"),
                'total_biaya as total',
                'status_pembayaran as status',
                'tanggal_bayar as tgl_bayar',
                'created_at'
            );

        $union = $ac->unionAll($futsal)->unionAll($ruko)->unionAll($servis);
        $query = DB::query()->fromSub($union, 'transaksi');

        if ($search) $query->where('id', 'like', "%{$search}%");
        if ($sistem)  $query->where('sistem', $sistem);
        if ($status)  $query->where('status', $status);

        $transaksi = $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('dashboard.transaksi.index', compact('transaksi', 'perPage'));
    }

    public function show(Request $request, $id)
    {
        $sistem = $request->sistem;

        if ($sistem === 'AC') {
            $detail = DB::table('pembayaran_ac')
                ->join('booking_ac', 'booking_ac.id', '=', 'pembayaran_ac.booking_id')
                ->join('users', 'users.id', '=', 'booking_ac.user_id')
                ->join('layanan_ac', 'layanan_ac.id', '=', 'booking_ac.layanan_id')
                ->where('pembayaran_ac.id', $id)
                ->select(
                    'pembayaran_ac.*',
                    'users.name as nama_pelanggan',
                    'users.email',
                    'users.no_hp',
                    'layanan_ac.nama as nama_item',
                    DB::raw("'AC' as sistem")
                )
                ->first();

        } elseif ($sistem === 'Futsal') {
            $detail = DB::table('pembayaran_futsal')
                ->join('booking_futsal', 'booking_futsal.id', '=', 'pembayaran_futsal.booking_futsal_id')
                ->join('users', 'users.id', '=', 'booking_futsal.user_id')
                ->join('lapangan', 'lapangan.id', '=', 'booking_futsal.lapangan_id')
                ->where('pembayaran_futsal.id', $id)
                ->select(
                    'pembayaran_futsal.*',
                    'users.name as nama_pelanggan',
                    'users.email',
                    'users.no_hp',
                    'lapangan.nama_lapangan as nama_item',
                    'booking_futsal.tgl_booking',
                    'booking_futsal.jam_mulai',
                    'booking_futsal.jam_selesai',
                    DB::raw("'Futsal' as sistem")
                )
                ->first();

        } elseif ($sistem === 'Ruko') {
            $detail = DB::table('pembayaran_ruko')
                ->join('sewa_ruko', 'sewa_ruko.id', '=', 'pembayaran_ruko.sewa_ruko_id')
                ->join('penyewa', 'penyewa.id', '=', 'sewa_ruko.penyewa_id')
                ->join('users', 'users.id', '=', 'penyewa.user_id')
                ->join('ruko', 'ruko.id', '=', 'sewa_ruko.ruko_id')
                ->where('pembayaran_ruko.id', $id)
                ->select(
                    'pembayaran_ruko.*',
                    'users.name as nama_pelanggan',
                    'users.email',
                    'users.no_hp',
                    'ruko.nama_ruko as nama_item',
                    'sewa_ruko.tgl_mulai',
                    'sewa_ruko.tgl_selesai',
                    DB::raw("'Ruko' as sistem")
                )
                ->first();

        } elseif ($sistem === 'Servis Kendaraan') {
            $detail = DB::table('pembayaran_servis')
                ->join('booking_servis', 'booking_servis.id', '=', 'pembayaran_servis.booking_servis_id')
                ->join('users', 'users.id', '=', 'booking_servis.user_id')
                ->join('layanan_servis', 'layanan_servis.id', '=', 'booking_servis.layanan_servis_id')
                ->where('pembayaran_servis.id', $id)
                ->select(
                    'pembayaran_servis.*',
                    'users.name as nama_pelanggan',
                    'users.email',
                    'users.no_hp',
                    'layanan_servis.nama_layanan as nama_item',
                    'booking_servis.tipe_kendaraan',
                    'booking_servis.merek_kendaraan',
                    'booking_servis.nomor_plat',
                    'booking_servis.tanggal_booking',
                    DB::raw("'Servis Kendaraan' as sistem")
                )
                ->first();

        } else {
            abort(404, 'Sistem tidak dikenali.');
        }

        if (!$detail) {
            abort(404, 'Transaksi tidak ditemukan.');
        }

        return view('dashboard.transaksi.show', compact('detail'));
    }
}
