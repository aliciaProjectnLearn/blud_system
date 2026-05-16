<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Order - {{ $booking->kode_booking }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 14px; line-height: 1.5; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12px; }
        .wo-title { text-align: center; font-weight: bold; font-size: 18px; margin-bottom: 20px; letter-spacing: 2px; text-decoration: underline; }
        .row { display: flex; flex-wrap: wrap; margin-bottom: 20px; }
        .col-6 { width: 50%; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table th, .info-table td { padding: 5px; vertical-align: top; text-align: left; }
        .info-table th { width: 35%; font-weight: bold; }
        .info-table td { width: 65%; border-bottom: 1px dotted #ccc; }
        .keluhan-box { border: 1px solid #000; padding: 10px; margin-bottom: 20px; min-height: 60px; }
        .catatan-teknisi { border: 1px solid #000; height: 300px; padding: 10px; margin-bottom: 20px; }
        .signature-area { display: flex; justify-content: space-between; margin-top: 40px; text-align: center; }
        .signature-box { width: 30%; }
        .signature-box .line { border-top: 1px solid #000; margin-top: 60px; }
        @media print {
            body { padding: 0; }
            button { display: none; }
        }
        .btn-print { margin-bottom: 20px; padding: 10px 20px; background: #4e73df; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

    <button class="btn-print" onclick="window.print()">Cetak Work Order</button>

    <div class="header">
        <h2>BLUD BENGKEL SMK</h2>
        <p>Jl. Pendidikan No. 123, Kota Bengkel | Telp: (021) 1234567</p>
    </div>

    <div class="wo-title">WORK ORDER (WO)</div>

    <div class="row">
        <div class="col-6">
            <table class="info-table">
                <tr><th>Kode Booking</th><td>: <strong>{{ $booking->kode_booking }}</strong></td></tr>
                <tr><th>Tanggal Booking</th><td>: {{ \Carbon\Carbon::parse($booking->tanggal_booking)->translatedFormat('d F Y') }}</td></tr>
                <tr><th>Jam Kedatangan</th><td>: {{ \Carbon\Carbon::parse($booking->jam_booking)->format('H:i') }} WIB</td></tr>
                <tr><th>Layanan</th><td>: {{ $booking->layananServis->nama_layanan ?? '-' }}</td></tr>
            </table>
        </div>
        <div class="col-6">
            <table class="info-table">
                <tr><th>Nama Pelanggan</th><td>: {{ $booking->nama_pemesan }}</td></tr>
                <tr><th>No. HP (WA)</th><td>: {{ $booking->no_hp }}</td></tr>
                <tr><th>Merek & Model</th><td>: {{ $booking->merek_kendaraan }}</td></tr>
                <tr><th>Nomor Plat</th><td>: {{ $booking->nomor_plat }}</td></tr>
                <tr><th>Tahun Keluaran</th><td>: {{ $booking->tahun_kendaraan }}</td></tr>
            </table>
        </div>
    </div>

    <div>
        <strong>Keluhan / Permintaan Pelanggan:</strong>
        <div class="keluhan-box">
            {{ $booking->keluhan ?: '(Tidak ada keluhan tertulis)' }}
        </div>
    </div>

    <div>
        <strong>Rincian Pekerjaan & Catatan Teknisi (Diisi oleh Teknisi):</strong>
        <div class="catatan-teknisi">
            <!-- Ruang kosong untuk ditulis teknisi -->
        </div>
    </div>

    <div class="signature-area">
        <div class="signature-box">
            <p>Kasir / Admin</p>
            <div class="line"></div>
            <p>( ................................ )</p>
        </div>
        <div class="signature-box">
            <p>Teknisi</p>
            <div class="line"></div>
            <p>( ................................ )</p>
        </div>
        <div class="signature-box">
            <p>Pelanggan</p>
            <div class="line"></div>
            <p>( {{ $booking->nama_pemesan }} )</p>
        </div>
    </div>

</body>
</html>
