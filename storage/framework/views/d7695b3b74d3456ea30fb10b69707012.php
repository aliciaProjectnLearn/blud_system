<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi <?php echo e($pembayaran->no_kwitansi); ?></title>
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
        No. Kwitansi: <strong><?php echo e($pembayaran->no_kwitansi); ?></strong>
    </div>

    <table>
        <tr>
            <td class="label-col">Nama Usaha</td>
            <td>: <?php echo e($pembayaran->sewaRuko->penyewa->nama_usaha ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="label-col">Nama Pemilik</td>
            <td>: <?php echo e($pembayaran->sewaRuko->penyewa->user->name ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="label-col">Kode Unit</td>
            <td>: <?php echo e($pembayaran->sewaRuko->ruko->kode_unit ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="label-col">Kategori</td>
            <td>: <?php echo e($pembayaran->sewaRuko->ruko->kategori->nama ?? '-'); ?></td>
        </tr>
        <tr>
            <td class="label-col">Periode Sewa</td>
            <td>:
                <?php echo e(\Carbon\Carbon::parse($pembayaran->sewaRuko->tgl_mulai)->format('d M Y')); ?>

                s/d
                <?php echo e(\Carbon\Carbon::parse($pembayaran->sewaRuko->tgl_selesai)->format('d M Y')); ?>

            </td>
        </tr>
        <tr>
            <td class="label-col">No. MOU</td>
            <td>: <?php echo e($pembayaran->sewaRuko->dokumen->first()?->no_mou ?? '-'); ?></td>
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
                <td>Termin <?php echo e($pembayaran->termin); ?></td>
                <td><?php echo e(\Carbon\Carbon::parse($pembayaran->tgl_bayar)->format('d M Y')); ?></td>
                <td><?php echo e($pembayaran->tipe->nama ?? '-'); ?></td>
                <td class="total">Rp <?php echo e(number_format($pembayaran->jumlah_tagihan, 0, ',', '.')); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="ttd">
            <p><?php echo e(now()->format('d M Y')); ?></p>
            <p>Admin Kantin/Ruko</p>
            <div class="garis"></div>
            <p>( ........................... )</p>
        </div>
    </div>

</body>
</html>
<?php /**PATH C:\laragon\www\blud_system\resources\views/adminkantin/pembayaran/kwitansi.blade.php ENDPATH**/ ?>