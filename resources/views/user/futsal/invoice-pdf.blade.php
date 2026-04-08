<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Booking Futsal #{{ $booking->id }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        .invoice-box {
            padding: 40px;
            position: relative;
            min-height: 297mm; /* A4 height */
        }
        .header {
            border-bottom: 3px solid #4e73df;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .school-name {
            font-size: 20px;
            font-weight: bold;
            color: #4e73df;
            margin: 0;
        }
        .school-info {
            font-size: 11px;
            color: #858796;
            margin: 5px 0 0 0;
        }
        .invoice-title {
            position: absolute;
            top: 40px;
            right: 40px;
            font-size: 40px;
            font-weight: bold;
            color: #e3e6f0;
            text-transform: uppercase;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 40px;
        }
        .info-col {
            width: 50%;
            vertical-align: top;
        }
        .label {
            font-size: 10px;
            color: #4e73df;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .value {
            font-size: 14px;
            color: #2e59d9;
            font-weight: bold;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table-items th {
            background-color: #4e73df;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        .table-items td {
            padding: 15px 12px;
            border-bottom: 1px solid #e3e6f0;
        }
        .total-section {
            margin-top: 30px;
            text-align: right;
        }
        .total-amount {
            font-size: 28px;
            font-weight: bold;
            color: #1cc88a;
        }
        .status-stamp {
            position: absolute;
            top: 200px;
            right: 100px;
            border: 5px solid #1cc88a;
            color: #1cc88a;
            padding: 10px 20px;
            font-size: 30px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 10px;
            transform: rotate(-20deg);
            opacity: 0.3;
            z-index: -1;
        }
        .signature-area {
            position: absolute;
            bottom: 100px;
            right: 40px;
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 80px;
        }
        .footer {
            position: absolute;
            bottom: 40px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #b7b9cc;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Watermark Status LUNAS -->
        @php
            $statusStr = strtolower($pembayaran->status ?? '');
            $isPaid = ($statusStr == 'lunas' || $statusStr == 'verifikasi');
        @endphp
        @if($isPaid)
        <div class="status-stamp text-center">LUNAS / PAID</div>
        @endif

        <div class="header">
            <h1 class="school-name">SMKN 1 CIREBON</h1>
            <p class="school-info">
                Jl. Perjuangan No. 50, Cirebon, Jawa Barat<br>
                Telp: (0231) 123456 | Email: info@smkn1-cirebon.sch.id
            </p>
            <div class="invoice-title">Invoice</div>
        </div>

        <table class="info-grid">
            <tr>
                <td class="info-col">
                    <div class="label">Tagihan Untuk:</div>
                    <div class="value">{{ $booking->user->name }}</div>
                    <div style="font-size: 12px; color: #858796;">{{ $booking->user->email }}</div>
                </td>
                <td class="info-col" style="text-align: right;">
                    <div class="label">Nomor Invoice:</div>
                    <div class="value">#FTS-{{ now()->format('Y') }}-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div class="label" style="margin-top: 15px;">Waktu Transaksi:</div>
                    <div class="value" style="font-size: 12px; font-weight: normal; color: #333;">{{ now()->format('d M Y, H:i') }} WIB</div>
                </td>
            </tr>
        </table>

        <table class="table-items">
            <thead>
                <tr>
                    <th>Item Layanan / Lapangan</th>
                    <th style="text-align: center;">Durasi & Jam</th>
                    <th style="text-align: right;">Harga Satuan</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="font-weight: bold; font-size: 14px;">Sewa {{ $booking->lapangan->nama }}</div>
                        <div style="font-size: 11px; color: #858796;">Ukuran Lapangan: {{ $booking->lapangan->ukuran }}</div>
                    </td>
                    <td style="text-align: center;">
                        {{ \Carbon\Carbon::parse($booking->tgl_main)->format('d F Y') }}<br>
                        <span style="font-weight: bold;">{{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }}</span><br>
                        <span style="color: #666;">({{ $booking->durasi_main }} Jam)</span>
                    </td>
                    <td style="text-align: right;">
                        Rp {{ number_format(($pembayaran->jumlah_bayar ?? 0) / ($booking->durasi_main ?: 1), 0, ',', '.') }}
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp {{ number_format($pembayaran->jumlah_bayar ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <div class="label">Total Pembayaran:</div>
            <div class="total-amount">Rp {{ number_format($pembayaran->jumlah_bayar ?? 0, 0, ',', '.') }}</div>
            <div style="margin-top: 5px; color: #858796;">
                Metode Pembayaran: <strong>{{ $pembayaran->tipePembayaran->nama ?? 'Tunai' }}</strong>
            </div>
        </div>

        <div class="signature-area">
            <div class="label" style="margin-bottom: 10px;">Petugas Pengelola,</div>
            <div class="signature-line"></div>
            <div style="font-size: 11px; margin-top: 5px;">Admin Futsal SMKN 1 Cirebon</div>
        </div>

        <div class="footer">
            Invoice ini dihasilkan secara otomatis oleh <strong>{{ $pengaturan->nama_aplikasi ?? 'BLUD SYSTEM' }}</strong>.<br>
            Harap simpan invoice ini sebagai bukti reservasi yang sah.
        </div>
    </div>
</body>
</html>
