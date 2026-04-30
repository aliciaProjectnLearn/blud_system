<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi {{ $pembayaran->no_kwitansi }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 11px; }
        .no-kwitansi { text-align: right; margin-bottom: 15px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table td { padding: 5px 8px; vertical-align: top; }
        table.detail td { border: 1px solid #ddd; }
        table.detail th { border: 1px solid #ddd; background: #f5f5f5; padding: 6px 8px; }
        .label-col { width: 35%; font-weight: bold; }
        .total { font-size: 14px; font-weight: bold; }
        .footer { margin-top: 40px; }
        .ttd { float: right; text-align: center; width: 200px; }
        .ttd .garis { border-bottom: 1px solid #333; margin-top: 60px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Kwitansi Pembayaran Sewa</h2>
        <p>Sistem Manajemen BLUD</p>
    </div>

    <div class="no-kwitansi">
        No. Kwitansi: <strong>{{ $pembayaran->no_kwitansi }}</strong>
    </div>

    <table>
        <tr>
            <td width="150" class="label">Diterima Dari</td>
            <td>: {{ $pembayaran->sewaRuko->nama_penyewa ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Pemilik</td>
            <td>: {{ $pembayaran->sewaRuko->nama_penyewa ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Kode Unit</td>
            <td>: {{ $pembayaran->sewaRuko->ruko->kode_unit ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-col">Kategori</td>
            <td>: {{ $pembayaran->sewaRuko->ruko->kategori->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Periode Sewa</td>
            <td>: 
                {{ \Carbon\Carbon::parse($pembayaran->sewaRuko->tanggal_mulai_sewa)->format('d M Y') }} 
                s/d 
                {{ \Carbon\Carbon::parse($pembayaran->sewaRuko->tanggal_selesai_sewa)->format('d M Y') }}
            </td>
        </tr>
        <tr>
            <td class="label-col">No. MOU</td>
            <td>: {{ $pembayaran->sewaRuko->dokumen->first()?->no_mou ?? '-' }}</td>
        </tr>
    </table>

    <table class="detail">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th>Termin</th>
                <th>Tgl Bayar</th>
                <th>Tipe Pembayaran</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Pembayaran Sewa Unit</td>
                <td>Termin {{ $pembayaran->termin_ke }}</td>
                <td>{{ $pembayaran->tanggal_bayar ? \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d M Y') : '-' }}</td>
                <td>{{ $pembayaran->tipe->nama ?? '-' }}</td>
                <td class="total">Rp {{ number_format($pembayaran->jumlah_tagihan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="ttd">
            <p>{{ now()->format('d M Y') }}</p>
            <p>Admin Kantin/Ruko</p>
            <div class="garis"></div>
            <p>( ........................... )</p>
        </div>
    </div>

</body>
</html>
