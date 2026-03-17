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


        $ac = DB::table('pembayaran_ac')
            ->select(
            'id',
            DB::raw("'AC' as sistem"),
            'total_biaya as total',
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

        $union = $ac
        ->unionAll($futsal)
        ->unionAll($ruko);

        $query = DB::query()->fromSub($union,'transaksi');

        if($search){
        $query->where('id',$search);
        }

        if($sistem){
        $query->where('sistem',$sistem);
        }

        if($status){
        $query->where('status',$status);
        }


        $transaksi = $query
            ->orderBy('created_at','desc')
            ->paginate(10)
            ->withQueryString();


    return view('dashboard.transaksi.index', compact('transaksi'));
    }
}
