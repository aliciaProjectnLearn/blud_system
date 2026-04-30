<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Servis Kendaraan</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 15px;
        }
        .header {
            margin-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            text-align: center;
        }
        .header h2 {
            margin: 0 0 4px 0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 10px;
            color: #555;
        }
        .meta-info {
            margin-bottom: 12px;
            font-size: 10px;
        }
        .meta-info span {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }
        table thead tr th {
            background-color: #2c3e50;
            color: #fff;
            border: 1px solid #2c3e50;
            padding: 6px 5px;
            text-align: left;
            white-space: nowrap;
        }
        table tbody tr td {
            border: 1px solid #ddd;
            padding: 5px;
            vertical-align: middle;
        }
        table tbody tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .tfoot-row td {
            background-color: #ecf0f1;
            font-weight: bold;
            border: 1px solid #bdc3c7;
        }
        .total-box {
            padding: 10px 15px;
            background-color: #eafaf1;
            border: 1px solid #a9dfbf;
            width: 320px;
            float: right;
            margin-top: 5px;
            border-radius: 4px;
        }
        .total-box .label {
            font-size: 10px;
            color: #555;
            margin-bottom: 3px;
        }
        .total-box .amount {
            font-size: 15px;
            color: #1e8449;
            font-weight: bold;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .badge-lunas    { color: #155724; background-color: #d4edda; padding: 2px 6px; border-radius: 3px; }
        .badge-dp       { color: #856404; background-color: #fff3cd; padding: 2px 6px; border-radius: 3px; }
        .badge-belum    { color: #721c24; background-color: #f8d7da; padding: 2px 6px; border-radius: 3px; }
        .badge-other    { color: #383d41; background-color: #e2e3e5; padding: 2px 6px; border-radius: 3px; }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            font-size: 9px;
            color: #777;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Laporan Transaksi Servis Kendaraan</h2>
        <p>Sistem Informasi BLUD &mdash; Modul Servis Motor &amp; Mobil</p>
        <p>
            Periode:
            @if(!empty($tanggal_dari) && !empty($tanggal_sampai))
                {{ $tanggal_dari }} s/d {{ $tanggal_sampai }}
            @elseif(!empty($tanggal_dari))
                Sejak {{ $tanggal_dari }}
            @elseif(!empty($tanggal_sampai))
                Hingga {{ $tanggal_sampai }}
            @else
                Semua Waktu
            @endif
            @if(!empty($status))
                &mdash; Status: {{ ucwords(str_replace('_', ' ', $status)) }}
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width:30px;">No</th>
                <th>Kode Booking</th>
                <th>Pelanggan</th>
                <th>Layanan</th>
                <th>Teknisi</th>
                <th>Tipe Kendaraan</th>
                <th>Merek | No Plat</th>
                <th>Tgl Booking</th>
                <th>Tgl Bayar</th>
                <th class="text-right">Total Biaya</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $item->bookingServis->kode_booking ?? '-' }}</td>
                    <td>{{ $item->bookingServis->pelanggan->name ?? $item->bookingServis->pelanggan->nama_lengkap ?? '-' }}</td>
                    <td>{{ $item->bookingServis->layananServis->nama_layanan ?? '-' }}</td>
                    <td>{{ $item->bookingServis->teknisi->name ?? '-' }}</td>
                    <td>{{ $item->bookingServis->tipe_kendaraan ?? '-' }}</td>
                    <td>{{ ($item->bookingServis->merek_kendaraan ?? '-') . ' | ' . ($item->bookingServis->nomor_plat ?? '-') }}</td>
                    <td>
                        @if($item->bookingServis->tanggal_booking)
                            {{ \Carbon\Carbon::parse($item->bookingServis->tanggal_booking)->format('d-m-Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($item->tanggal_bayar)
                            {{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d-m-Y H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right font-bold">Rp {{ number_format($item->total_biaya, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @php $st = $item->status_pembayaran @endphp
                        @if($st == 'lunas')
                            <span class="badge-lunas">Lunas</span>
                        @elseif($st == 'dp')
                            <span class="badge-dp">DP / Cicilan</span>
                        @elseif($st == 'belum_bayar')
                            <span class="badge-belum">Belum Bayar</span>
                        @else
                            <span class="badge-other">{{ $st ?? '-' }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Data tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="tfoot-row">
                <td colspan="9" class="text-right">Total Pendapatan (Status Lunas):</td>
                <td class="text-right" style="color:#1e8449;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }} &mdash; Sistem BLUD
    </div>

</body>
</html>
