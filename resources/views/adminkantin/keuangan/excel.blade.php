<table>
    <!-- Kop Surat -->
    <tr>
        <th colspan="5" style="text-align: center; font-size: 14pt;">PEMERINTAH DAERAH PROVINSI JAWA BARAT</th>
    </tr>
    <tr>
        <th colspan="5" style="text-align: center; font-size: 14pt;">DINAS PENDIDIKAN</th>
    </tr>
    <tr>
        <th colspan="5" style="text-align: center; font-size: 14pt;">CABANG DINAS PENDIDIKAN WILAYAH X</th>
    </tr>
    <tr>
        <th colspan="5" style="text-align: center; font-size: 18pt; font-weight: bold;">SMK NEGERI 1 CIREBON</th>
    </tr>
    <tr>
        <th colspan="5" style="text-align: center; font-size: 10pt;">Jl. Perjuangan By Pass Sunyaragi Telp. (0231) 480202 Kota Cirebon 45132</th>
    </tr>
    <tr>
        <th colspan="5" style="text-align: center; font-size: 10pt;">Website : http://www.smkn1-cirebon.sch.id E-mail : info@smkn1-cirebon.sch.id</th>
    </tr>
    <tr><td colspan="5"></td></tr> <!-- Spacing -->
    
    <tr>
        <th colspan="5" style="text-align: center; font-size: 16pt; font-weight: bold;">LAPORAN KEUANGAN KANTIN</th>
    </tr>
    <tr>
        <th colspan="5" style="text-align: center;">Periode: 
            @if($request->tanggal_mulai && $request->tanggal_selesai)
                {{ \Carbon\Carbon::parse($request->tanggal_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($request->tanggal_selesai)->format('d/m/Y') }}
            @else
                Keseluruhan
            @endif
        </th>
    </tr>
    <tr><td colspan="5"></td></tr>

    <tr><td colspan="4" style="font-weight: bold; font-size: 13pt;">Data Pemasukan</td></tr>
    <thead>
        <tr>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000;">No</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000;">Tanggal</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000;">Deskripsi</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000;">Nominal</th>
        </tr>
    </thead>
    <tbody>
        @php $noPemasukan = 1; @endphp
        @forelse($transaksi->where('tipe', 'pemasukan') as $item)
            <tr>
                <td style="border: 1px solid #000; text-align: center;">{{ $noPemasukan++ }}</td>
                <td style="border: 1px solid #000;">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}</td>
                <td style="border: 1px solid #000;">{{ $item['deskripsi'] }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ $item['nominal'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="border: 1px solid #000; text-align: center;">Tidak ada data pemasukan pada periode ini.</td>
            </tr>
        @endforelse
    </tbody>
    
    <tr><td colspan="4"></td></tr>
    <tr><td colspan="4"></td></tr>
    <tr><td colspan="4" style="font-weight: bold; font-size: 13pt;">Data Pengeluaran</td></tr>
    <thead>
        <tr>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000;">No</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000;">Tanggal</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000;">Deskripsi</th>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000;">Nominal</th>
        </tr>
    </thead>
    <tbody>
        @php $noPengeluaran = 1; @endphp
        @forelse($transaksi->where('tipe', 'pengeluaran') as $item)
            <tr>
                <td style="border: 1px solid #000; text-align: center;">{{ $noPengeluaran++ }}</td>
                <td style="border: 1px solid #000;">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}</td>
                <td style="border: 1px solid #000;">{{ $item['deskripsi'] }}</td>
                <td style="border: 1px solid #000; text-align: right;">{{ $item['nominal'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="border: 1px solid #000; text-align: center;">Tidak ada data pengeluaran pada periode ini.</td>
            </tr>
        @endforelse
    </tbody>
    
    <tr><td colspan="5"></td></tr>
    
    <tr>
        <td colspan="3"></td>
        <td style="font-weight: bold; border: 1px solid #000;">Total Pemasukan</td>
        <td style="font-weight: bold; text-align: right; border: 1px solid #000;">{{ $totalPemasukan }}</td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td style="font-weight: bold; border: 1px solid #000;">Total Pengeluaran</td>
        <td style="font-weight: bold; text-align: right; border: 1px solid #000;">{{ $totalPengeluaran }}</td>
    </tr>
    <tr>
        <td colspan="3"></td>
        <td style="font-weight: bold; border: 1px solid #000;">Saldo Akhir</td>
        <td style="font-weight: bold; text-align: right; border: 1px solid #000;">{{ $saldoAkhir }}</td>
    </tr>
</table>
