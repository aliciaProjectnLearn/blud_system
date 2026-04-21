<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $booking->kode_booking }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            font-size: 11px;
        }
        .container {
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #4e73df;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            color: #666;
        }
        .info-section {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            vertical-align: top;
            padding: 5px 0;
        }
        .label {
            font-weight: bold;
            color: #4e73df;
            text-transform: uppercase;
            font-size: 9px;
            display: block;
            margin-bottom: 2px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .details-table th {
            background-color: #4e73df;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
        }
        .details-table td {
            border-bottom: 1px solid #e3e6f0;
            padding: 8px;
        }
        .total-row td {
            font-weight: bold;
            font-size: 12px;
            background-color: #f8f9fc;
            color: #4e73df;
            border-top: 2px solid #4e73df;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
            margin-top: 10px;
        }
        .status-lunas {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .status-belum {
            background-color: #fff3cd;
            color: #664d03;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #858796;
            border-top: 1px solid #e3e6f0;
            padding-top: 15px;
        }
        .signature-section {
            margin-top: 30px;
            width: 100%;
        }
        .signature-box {
            float: right;
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            width: 150px;
            margin-left: auto;
            margin-right: auto;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>INVOICE SERVIS KENDARAAN</h1>
            <p><strong>BLUD Servis Kendaraan Daerah</strong></p>
            <p>{{ config('app.name') }}</p>
        </div>

        <table class="info-table">
            <tr>
                <td width="33%">
                    <span class="label">No. Invoice</span>
                    <strong>{{ $booking->kode_booking }}</strong>
                </td>
                <td width="33%">
                    <span class="label">Pelanggan</span>
                    <strong>{{ $booking->user->nama_lengkap ?? $booking->user->name ?? '-' }}</strong><br>
                    {{ $booking->user->no_hp ?? '-' }}<br>
                    {{ $booking->user->email ?? '-' }}
                </td>
                <td width="33%" style="text-align: right;">
                    <span class="label">Tanggal Servis</span>
                    <strong>{{ $booking->tanggal_booking->translatedFormat('d F Y') }}</strong>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Kendaraan</span>
                    <strong>{{ ucfirst($booking->tipe_kendaraan) }} - {{ $booking->merek_kendaraan ?? '-' }}</strong><br>
                    No. Polisi: {{ $booking->nomor_plat }}
                </td>
                <td>
                    <span class="label">Teknisi</span>
                    <strong>{{ $booking->teknisi->name ?? '-' }}</strong>
                </td>
                <td style="text-align: right;">
                    <span class="label">Layanan</span>
                    <strong>{{ $booking->layananServis->nama_layanan ?? '-' }}</strong>
                </td>
            </tr>
        </table>

        <table class="details-table">
            <thead>
                <tr>
                    <th width="50%">Nama Item / Layanan</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Harga Satuan</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->rincianServis as $r)
                <tr>
                    <td>{{ $r->nama_item }}</td>
                    <td style="text-align: center;">{{ $r->jumlah }}</td>
                    <td style="text-align: right;">Rp {{ number_format($r->harga_satuan, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($r->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                
                @if($booking->layananServis)
                <tr>
                    <td>Biaya Jasa: {{ $booking->layananServis->nama_layanan }}</td>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: right;">Rp {{ number_format($booking->layananServis->harga ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($booking->layananServis->harga ?? 0, 0, ',', '.') }}</td>
                </tr>
                @endif
                
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">TOTAL BIAYA</td>
                    <td style="text-align: right;">Rp {{ number_format(optional($booking->pembayaranServis)->total_biaya ?? 0, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <div class="status-badge {{ optional($booking->pembayaranServis)->status_pembayaran == 'lunas' ? 'status-lunas' : 'status-belum' }}">
                ✓ STATUS PEMBAYARAN: {{ strtoupper(optional($booking->pembayaranServis)->status_pembayaran ?? 'BELUM BAYAR') }}
            </div>
            <p style="margin-top: 10px; color: #858796; font-style: italic;">
                * Harap simpan invoice ini sebagai bukti servis yang sah.
            </p>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <p>Kasir / Admin Servis,</p>
                <div class="signature-line"></div>
                <p style="font-size: 8px; color: #858796;">Tanda Tangan & Cap Bengkel</p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="footer">
            <p>Terima kasih telah mempercayakan servis kendaraan Anda kepada kami.</p>
            <p><strong>BLUD SYSTEM - Solusi Layanan Kendaraan Anda</strong></p>
        </div>
    </div>
</body>
</html>
