<!DOCTYPE html>
<html>
<head>
    <title>MOU Penyewaan - {{ $sewa->ruko->kode_unit }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; line-height: 1.5; color: #333; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 10px; }
        .title { font-size: 16pt; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        .mou-number { font-size: 12pt; }
        .section { margin-top: 20px; }
        .section-title { font-weight: bold; text-decoration: underline; margin-bottom: 10px; text-transform: uppercase; }
        .content { margin-left: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 3px 0; }
        .label { width: 35%; }
        .separator { width: 3%; }
        .value { width: 62%; font-weight: bold; }
        .footer { margin-top: 50px; }
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 40px; }
        .signature-table td { width: 50%; text-align: center; }
        .signature-space { height: 80px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="kop-surat" style="margin-bottom: 20px;">
        <table style="width: 100%; border-bottom: 3px solid #000; margin-bottom: 2px;">
            <tr>
                <td style="width: 15%; text-align: center; vertical-align: middle; padding-bottom: 10px;">
                    <img src="{{ asset('img/logo_smk.png') }}" style="width: 90px;" alt="Logo">
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
        <div class="mou-number">KANTIN BLUD SYSTEM</div>
    </div>

    <p>Pada hari ini, <b>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</b>, telah disepakati perjanjian sewa menyewa antara pihak pengelola kantin dan penyewa dengan rincian sebagai berikut:</p>

    <div class="section">
        <div class="section-title">I. DATA PIHAK KEDUA (PENYEWA)</div>
        <div class="content">
            <table>
                <tr>
                    <td class="label">Nama Lengkap (Sesuai KTP)</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $sewa->nama_penyewa }}</td>
                </tr>
                <tr>
                    <td class="label">NIK</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $sewa->nik_penyewa ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Usaha</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $sewa->user->nama_usaha ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis Usaha</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $sewa->user->jenis_usaha ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">No. Handphone</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $sewa->no_hp_snapshot ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $sewa->user->alamat ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">II. DATA UNIT & PERIODE SEWA</div>
        <div class="content">
            <table>
                <tr>
                    <td class="label">Kode Unit Ruko/Kantin</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $sewa->ruko->kode_unit }}</td>
                </tr>
                <tr>
                    <td class="label">Kategori Unit</td>
                    <td class="separator">:</td>
                    <td class="value">{{ $sewa->ruko->kategori->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Harga Sewa (Per Tahun)</td>
                    <td class="separator">:</td>
                    <td class="value">Rp {{ number_format($sewa->ruko->harga ?? $sewa->harga_sewa_tahunan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Mulai Sewa</td>
                    <td class="separator">:</td>
                    <td class="value">{{ \Carbon\Carbon::parse($sewa->tanggal_mulai_sewa)->isoFormat('D MMMM Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Selesai Sewa</td>
                    <td class="separator">:</td>
                    <td class="value">{{ \Carbon\Carbon::parse($sewa->tanggal_selesai_sewa)->isoFormat('D MMMM Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Tipe Pembayaran</td>
                    <td class="separator">:</td>
                    <td class="value">{{ str_replace('_', ' ', ucwords($sewa->tipe_pembayaran, '_')) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section">
        <div class="section-title">III. KETENTUAN UMUM</div>
        <div class="content">
            <ol>
                <li>Penyewa wajib menjaga kebersihan dan ketertiban di area unit yang disewa.</li>
                <li>Penyewa tidak diperkenankan memindahtangankan unit sewa tanpa izin pengelola secara tertulis.</li>
                <li>Segala kerusakan unit akibat penggunaan penyewa menjadi tanggung jawab penuh penyewa.</li>
                <li>Pembayaran sewa dilakukan tepat waktu sesuai dengan termin yang telah disepakati.</li>
                <li>Keterlambatan pembayaran dapat dikenakan sanksi sesuai peraturan yang berlaku.</li>
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
