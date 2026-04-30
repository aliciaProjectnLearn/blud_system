<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi Kantin/Ruko</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #2c3e50; }
        .header p { margin: 5px 0 0 0; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.table-bordered th, table.table-bordered td { border: 1px solid #ccc; padding: 8px; }
        table.table-bordered th { background-color: #f8f9fa; color: #333; }
        
        .page-break { page-break-before: always; }
        
        .total-box { padding: 10px; background-color: #fdfefe; border: 1px solid #ddd; width: 300px; float: right; margin-top: 10px;}
        .clearfix::after { content: ""; clear: both; display: table; }
        
        .text-success { color: #27ae60; }
        .text-danger { color: #c0392b; }
        .bg-light { background-color: #f9f9f9; }
    </style>
</head>
<body>

    {{-- HALAMAN 1: LAPORAN TRANSAKSI PENYEWAAN --}}
    <div class="header text-center">
        <h2>LAPORAN TRANSAKSI PENYEWAAN</h2>
        <p>
            Periode: 
            @if(request('tanggal_dari') && request('tanggal_sampai'))
                {{ \Carbon\Carbon::parse(request('tanggal_dari'))->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d/m/Y') }}
            @elseif(request('tanggal_dari'))
                Sejak {{ \Carbon\Carbon::parse(request('tanggal_dari'))->format('d/m/Y') }}
            @elseif(request('tanggal_sampai'))
                Hingga {{ \Carbon\Carbon::parse(request('tanggal_sampai'))->format('d/m/Y') }}
            @else
                Semua Waktu
            @endif
        </p>
    </div>

    <table class="table-bordered">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Nama Penyewa</th>
                <th>Unit</th>
                <th>Periode Sewa</th>
                <th>Tgl Bayar</th>
                <th class="text-right">Jumlah Bayar</th>
                <th width="80">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->sewaRuko?->nama_penyewa ?? '-' }}</td>
                    <td class="text-center">{{ $item->sewaRuko?->ruko?->kode_unit ?? '-' }}</td>
                    <td>
                        @if($item->sewaRuko)
                            {{ \Carbon\Carbon::parse($item->sewaRuko->tanggal_mulai_sewa)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($item->sewaRuko->tanggal_selesai_sewa)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">{{ $item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</td>
                    <td class="text-center">{{ ucfirst($item->status_pembayaran) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Data transaksi tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- HALAMAN 2: LAPORAN KEUANGAN KANTIN/RUKO --}}
    <div class="page-break"></div>

    <div class="header text-center">
        <h2>LAPORAN KEUANGAN KANTIN/RUKO</h2>
        <p>Ringkasan Pemasukan & Pengeluaran Operasional</p>
    </div>

    <!-- Tabel Pemasukan -->
    <h3 style="margin-bottom: 10px;">A. Data Pemasukan (Sewa Unit)</h3>
    <table class="table-bordered">
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Nama Penyewa</th>
                <th>Kode Ruko</th>
                <th>Tgl Bayar</th>
                <th>Termin</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pemasukan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->sewaRuko?->nama_penyewa ?? '-' }}</td>
                    <td class="text-center">{{ $item->sewaRuko?->ruko?->kode_unit ?? '-' }}</td>
                    <td class="text-center">{{ $item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $item->termin_ke }}</td>
                    <td class="text-right">Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data pemasukan.</td>
                </tr>
            @endforelse
            @if($pemasukan->isNotEmpty())
            <tr class="bg-light font-bold">
                <td colspan="5" class="text-right">Total Pemasukan</td>
                <td class="text-right text-success">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Tabel Pengeluaran -->
    <h3 style="margin-bottom: 10px; margin-top: 30px;">B. Data Pengeluaran Operasional</h3>
    <table class="table-bordered">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="100">Tanggal</th>
                <th>Deskripsi Pengeluaran</th>
                <th class="text-right" width="120">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengeluaran as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $item->deskripsi }}</td>
                    <td class="text-right">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data pengeluaran.</td>
                </tr>
            @endforelse
            @if($pengeluaran->isNotEmpty())
            <tr class="bg-light font-bold">
                <td colspan="3" class="text-right">Total Pengeluaran</td>
                <td class="text-right text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Ringkasan Keuangan -->
    <div class="clearfix" style="margin-top: 30px;">
        <div class="total-box">
            <h4 style="margin: 0 0 10px 0; border-bottom: 1px solid #eee; padding-bottom: 5px;">Rekapitulasi Akhir</h4>
            <table style="border:none; margin:0; font-size: 12px;">
                <tr>
                    <td style="border:none; padding:4px;" class="font-bold text-success">Total Pemasukan</td>
                    <td style="border:none; padding:4px;" class="text-right text-success font-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td style="border:none; padding:4px;" class="font-bold text-danger">Total Pengeluaran</td>
                    <td style="border:none; padding:4px;" class="text-right text-danger font-bold">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
                </tr>
                <tr style="border-top: 2px solid #333;">
                    <td style="border:none; padding:8px 4px; font-size:14px;" class="font-bold">Saldo Akhir</td>
                    <td style="border:none; padding:8px 4px; font-size:14px;" class="text-right font-bold">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
