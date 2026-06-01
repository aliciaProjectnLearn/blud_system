<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan Servis Kendaraan</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; line-height: 1.5; color: #333; }
        .kop-surat { margin-bottom: 20px; }
        .kop-surat table { width: 100%; border-bottom: 3px solid #000; margin-bottom: 2px; }
        .kop-surat td { vertical-align: middle; padding-bottom: 10px; }
        .kop-surat img { width: 90px; }
        .kop-text { text-align: center; line-height: 1.3; }
        .kop-text .pemerintah { font-size: 14pt; }
        .kop-text .dinas { font-size: 14pt; }
        .kop-text .cabang { font-size: 14pt; }
        .kop-text .sekolah { font-size: 18pt; font-weight: bold; margin: 3px 0; }
        .kop-text .alamat { font-size: 10pt; }
        .garis-bawah { border-bottom: 1px solid #000; margin-bottom: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .title { font-size: 16pt; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
        table.data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table.data-table th, table.data-table td { border: 1px solid #000; padding: 8px; text-align: left; }
        table.data-table th { background-color: #f2f2f2; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .summary-box { margin-top: 20px; border: 1px solid #000; padding: 15px; width: 300px; float: right; }
        .summary-box table { width: 100%; }
        .summary-box td { padding: 3px; }
    </style>
</head>
<body>
    <div class="kop-surat">
        <table>
            <tr>
                <td style="width: 15%; text-align: center;">
                    @php
                        $path = public_path('img/logo_smk.png');
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    @endphp
                    <img src="{{ $base64 }}" alt="Logo">
                </td>
                <td style="width: 85%;" class="kop-text">
                    <div class="pemerintah">PEMERINTAH DAERAH PROVINSI JAWA BARAT</div>
                    <div class="dinas">DINAS PENDIDIKAN</div>
                    <div class="cabang">CABANG DINAS PENDIDIKAN WILAYAH X</div>
                    <div class="sekolah">SMK NEGERI 1 CIREBON</div>
                    <div class="alamat">Jl. Perjuangan By Pass Sunyaragi Telp. (0231) 480202 Kota Cirebon 45132</div>
                    <div class="alamat">Website : http://www.smkn1-cirebon.sch.id E-mail : info@smkn1-cirebon.sch.id</div>
                </td>
            </tr>
        </table>
        <div class="garis-bawah"></div>
    </div>

    <div class="header">
        <div class="title">LAPORAN KEUANGAN SERVIS KENDARAAN</div>
        <div>Periode: 
            @if($request->start_date && $request->end_date)
                {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
            @else
                Keseluruhan
            @endif
        </div>
    </div>

    <h3 style="margin-bottom: 5px; font-size: 13pt;">Data Pemasukan</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 60%;">Deskripsi</th>
                <th class="text-right" style="width: 20%;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @php $noPemasukan = 1; @endphp
            @forelse($transaksi->where('tipe', 'pemasukan') as $item)
                <tr>
                    <td class="text-center">{{ $noPemasukan++ }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $item->deskripsi }}</td>
                    <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data pemasukan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="margin-top: 30px; margin-bottom: 5px; font-size: 13pt;">Data Pengeluaran</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 60%;">Deskripsi</th>
                <th class="text-right" style="width: 20%;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @php $noPengeluaran = 1; @endphp
            @forelse($transaksi->where('tipe', 'pengeluaran') as $item)
                <tr>
                    <td class="text-center">{{ $noPengeluaran++ }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $item->deskripsi }}</td>
                    <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data pengeluaran pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table>
            <tr>
                <td>Total Pemasukan</td>
                <td>:</td>
                <td class="text-right">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td>:</td>
                <td class="text-right">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3"><hr></td>
            </tr>
            <tr>
                <td><strong>Saldo Akhir</strong></td>
                <td><strong>:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>
</body>
</html>
