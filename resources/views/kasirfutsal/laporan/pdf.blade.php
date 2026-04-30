<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Kasir Futsal</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #000; padding: 5px; }
        .table th { background-color: #f2f2f2; }
        .summary { margin-top: 20px; width: 100%; }
        .summary td { padding: 5px; }
    </style>
</head>
<body>

    <div class="text-center">
        <h2>LAPORAN TRANSAKSI KASIR FUTSAL</h2>
        <p>Tanggal: {{ $tanggalLabel }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Transaksi</th>
                <th>Tanggal Bayar</th>
                <th>Pemesan</th>
                <th>Metode Pembayaran</th>
                <th>Status</th>
                <th>Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $key => $item)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>{{ $item->kode_pembayaran }}</td>
                    <td>{{ $item->tgl_bayar ? \Carbon\Carbon::parse($item->tgl_bayar)->format('d-m-Y H:i') : '-' }}</td>
                    <td>{{ $item->bookingFutsal->nama_pemesan ?? ($item->bookingFutsal->user->name ?? '-') }}</td>
                    <td>{{ $item->tipePembayaran->nama ?? '-' }}</td>
                    <td class="text-center">{{ ucfirst($item->status) }}</td>
                    <td class="text-right">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada transaksi.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right">Total Pemasukan (Lunas)</th>
                <th class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <table class="summary">
        <tr>
            <td width="70%"></td>
            <td class="text-center">
                <br>
                Kasir Futsal,<br><br><br><br>
                ( {{ auth()->user()->name ?? '......................' }} )
            </td>
        </tr>
    </table>

</body>
</html>
