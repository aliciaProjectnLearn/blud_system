<!DOCTYPE html>
<html>
<head>
    <title>MOU Penyewaan - {{ $sewa->no_mou ?? 'AUTO' }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12pt; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 10px; }
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
    <div class="kop-surat" style="margin-bottom: 20px;">
        <table style="width: 100%; border-bottom: 3px solid #000; margin-bottom: 2px;">
            <tr>
                <td style="width: 15%; text-align: center; vertical-align: middle; padding-bottom: 10px;">
                    @php
                        $path = public_path('img/logo_smk.png');
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    @endphp
                    <img src="{{ $base64 }}" style="width: 90px;" alt="Logo">
                </td>
                <td style="width: 85%; text-align: center; line-height: 1.3; padding-bottom: 10px;">
                    <div style="font-size: 14pt;">PEMERINTAH DAERAH PROVINSI JAWA BARAT</div>
                    <div style="font-size: 14pt;">DINAS PENDIDIKAN</div>
                    <div style="font-size: 14pt;">CABANG DINAS PENDIDIKAN WILAYAH X</div>
                    <div style="font-size: 18pt; font-weight: bold; margin: 3px 0;">SMK NEGERI 1 CIREBON</div>
                    <div style="font-size: 10pt;">Jl. Perjuangan By Pass Sunyaragi Telp. (0231) 480202 Kota Cirebon 45132</div>
                    <div style="font-size: 10pt;">Website : http://www.smkn1-cirebon.sch.id E-mail : info@smkn1-cirebon.sch.id</div>
                </td>
            </tr>
        </table>
        <div style="border-bottom: 1px solid #000; margin-bottom: 20px;"></div>
    </div>

    <div class="header">
        <div class="title">SURAT PERJANJIAN SEWA MENYEWA (MOU)</div>
        <div class="mou-number">Nomor: {{ $no_mou }}</div>
    </div>

    <p>Pada hari ini, <b>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</b>, telah disepakati perjanjian sewa menyewa antara pihak pengelola kantin dan penyewa dengan rincian sebagai berikut:</p>

    <div class="section">
        <div class="section-title">I. DATA PIHAK KEDUA (PENYEWA)</div>
        <div class="content">
            <table style="width: 100%;">
                <tr><td style="width: 35%;">Nama Lengkap (Sesuai KTP)</td><td>: {{ $sewa->nama_penyewa }}</td></tr>
                <tr><td style="width: 35%;">NIK</td><td>: {{ $sewa->nik_penyewa ?? '-' }}</td></tr>
                <tr><td style="width: 35%;">Nama Usaha</td><td>: {{ $sewa->user->nama_usaha ?? '-' }}</td></tr>
                <tr><td style="width: 35%;">Jenis Usaha</td><td>: {{ $sewa->user->jenis_usaha ?? '-' }}</td></tr>
                <tr><td style="width: 35%;">No. Handphone</td><td>: {{ $sewa->no_hp_snapshot ?? '-' }}</td></tr>
                <tr><td style="width: 35%;">Alamat</td><td>: {{ $sewa->user->alamat ?? '-' }}</td></tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">II. DATA UNIT & PERIODE SEWA</div>
        <div class="content">
            <table style="width: 100%;">
                <tr><td style="width: 30%;">Kode Unit Ruko</td><td>: <b>{{ $sewa->ruko->kode_unit }}</b></td></tr>
                <tr><td style="width: 30%;">Jenis Unit</td><td>: {{ $sewa->ruko->kategori->nama ?? '-' }}</td></tr>
                <tr><td style="width: 30%;">Periode Sewa</td><td>: {{ \Carbon\Carbon::parse($sewa->tanggal_mulai_sewa)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($sewa->tanggal_selesai_sewa)->format('d/m/Y') }}</td></tr>
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
                ( <b>{{ $sewa->nama_penyewa }}</b> )
            </td>
        </tr>
    </table>
</body>
</html>
