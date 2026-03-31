<!DOCTYPE html>
<html>
<head>
    <title>MOU Penyewaan - {{ $sewa->no_mou ?? 'AUTO' }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12pt; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .title { font-size: 16pt; font-weight: bold; margin-bottom: 5px; }
        .mou-number { font-size: 12pt; }
        .section { margin-top: 20px; }
        .section-title { font-weight: bold; text-decoration: underline; margin-bottom: 10px; }
        .content { margin-left: 20px; }
        .footer { margin-top: 50px; }
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 40px; }
        .signature-table td { width: 50%; text-align: center; }
        .signature-space { height: 80px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">SURAT PERJANJIAN SEWA MENYEWA (MOU)</div>
        <div class="mou-number">Nomor: {{ $no_mou }}</div>
    </div>

    <p>Pada hari ini, <b>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</b>, telah disepakati perjanjian sewa menyewa antara pihak pengelola kantin dan penyewa dengan rincian sebagai berikut:</p>

    <div class="section">
        <div class="section-title">I. DATA PIHAK KEDUA (PENYEWA)</div>
        <div class="content">
            <table style="width: 100%;">
                <tr><td style="width: 30%;">Nama Lengkap</td><td>: {{ $sewa->penyewa->user->nama_lengkap ?? '-' }}</td></tr>
                <tr><td style="width: 30%;">NIK</td><td>: {{ $sewa->penyewa->nik ?? '-' }}</td></tr>
                <tr><td style="width: 30%;">Nama Usaha</td><td>: {{ $sewa->penyewa->nama_usaha }}</td></tr>
                <tr><td style="width: 30%;">Alamat</td><td>: {{ $sewa->penyewa->alamat }}</td></tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">II. DATA UNIT & PERIODE SEWA</div>
        <div class="content">
            <table style="width: 100%;">
                <tr><td style="width: 30%;">Kode Unit Ruko</td><td>: <b>{{ $sewa->ruko->kode_unit }}</b></td></tr>
                <tr><td style="width: 30%;">Jenis Unit</td><td>: {{ $sewa->ruko->kategori->nama ?? '-' }}</td></tr>
                <tr><td style="width: 30%;">Periode Sewa</td><td>: {{ \Carbon\Carbon::parse($sewa->tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sewa->tgl_selesai)->format('d/m/Y') }}</td></tr>
                <tr><td style="width: 30%;">Nominal Pembayaran</td><td>: <b>Rp {{ number_format($sewa->harga_sewa_tahunan, 0, ',', '.') }}</b> (per tahun)</td></tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">III. KETENTUAN UMUM</div>
        <div class="content">
            <ol>
                <li>Penyewa wajib menjaga kebersihan dan ketertiban di area unit yang disewa.</li>
                <li>Penyewa tidak diperkenankan memindahtangankan unit sewa tanpa izin pengelola.</li>
                <li>Segala kerusakan unit akibat penggunaan penyewa menjadi tanggung jawab penyewa.</li>
                <li>Pembayaran sewa dilakukan sesuai dengan termin yang telah disepakati.</li>
            </ol>
        </div>
    </div>

    <p style="margin-top: 30px;">Demikian surat perjanjian ini dibuat dengan sebenar-benarnya untuk dipergunakan sebagaimana mestinya.</p>

    <table class="signature-table">
        <tr>
            <td>
                Pihak Pertama (Pengelola)<br>
                <div class="signature-space"></div>
                ( ................................. )
            </td>
            <td>
                Pihak Kedua (Penyewa)<br>
                <div class="signature-space"></div>
                ( <b>{{ $sewa->penyewa->user->nama_lengkap ?? $sewa->penyewa->nama_usaha }}</b> )
            </td>
        </tr>
    </table>
</body>
</html>
