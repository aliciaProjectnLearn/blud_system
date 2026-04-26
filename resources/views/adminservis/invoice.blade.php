<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Servis #{{ $booking->kode_booking }}</title>
    
    <!-- Custom SB Admin 2 / Bootstrap style -->
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fc;
            padding-top: 30px;
            padding-bottom: 50px;
            font-family: 'Inter', sans-serif;
        }
        .invoice-card {
            max-width: 850px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.1);
            overflow: hidden;
            border: none;
        }
        .invoice-header {
            padding: 3rem;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
        }
        .invoice-body {
            padding: 3rem;
        }
        .invoice-footer {
            padding: 1.5rem;
            background: #f8f9fc;
            border-top: 1px solid #e3e6f0;
            text-align: center;
            font-size: 0.85rem;
            color: #858796;
        }
        .table thead th {
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            color: #4e73df;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.1em;
        }
        .badge-status {
            padding: 0.6em 1.2em;
            font-size: 0.85rem;
            border-radius: 50px;
            font-weight: 700;
        }
        
        /* Floating Toolbar */
        .toolbar {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            background: rgba(255,255,255,0.95);
            padding: 12px 25px;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
            display: flex;
            gap: 15px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        @media print {
            body { 
                background: white; 
                padding: 0;
                margin: 0;
            }
            .toolbar, .no-print {
                display: none !important;
            }
            .invoice-card {
                box-shadow: none !important;
                max-width: 100% !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }
            .container {
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
            .invoice-header {
                background: #4e73df !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="card invoice-card">
            <!-- Header -->
            <div class="invoice-header d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="font-weight-bold mb-1 text-white">BLUD SYSTEM</h2>
                    <p class="mb-0 opacity-75">Bengkel Servis Kendaraan Terpadu</p>
                    <div class="mt-4 small">
                        <p class="mb-0 text-white-50">Alamat Bengkel:</p>
                        <p class="mb-0">Jl. Contoh No. 123, Kota Bengkel</p>
                        <p class="mb-0">Telp: (021) 1234-5678</p>
                    </div>
                </div>
                <div class="text-right">
                    <h1 class="display-4 font-weight-bold mb-0 text-white-50">INVOICE</h1>
                    <p class="h4 font-weight-bold mb-0">#{{ $booking->kode_booking }}</p>
                    <div class="mt-4 small">
                        <p class="mb-0 text-white-50">Tanggal Cetak:</p>
                        <p class="mb-0 font-weight-bold">{{ now()->translatedFormat('d F Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="invoice-body">
                <div class="row mb-5">
                    <div class="col-sm-6">
                        <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-3">Informasi Pelanggan:</h6>
                        <h5 class="font-weight-bold text-dark mb-1">{{ $booking->user->nama_lengkap ?? $booking->user->name ?? '-' }}</h5>
                        <p class="text-muted mb-0"><i class="fas fa-phone fa-xs mr-2"></i> {{ $booking->user->no_hp ?? '-' }}</p>
                        <p class="text-muted mb-0"><i class="fas fa-envelope fa-xs mr-2"></i> {{ $booking->user->email ?? '-' }}</p>
                    </div>
                    <div class="col-sm-6 text-sm-right mt-4 mt-sm-0">
                        <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-3">Detail Kendaraan & Servis:</h6>
                        <p class="text-dark mb-1"><strong>Unit:</strong> {{ ucfirst($booking->tipe_kendaraan) }} - {{ $booking->merek_kendaraan ?? '-' }}</p>
                        <p class="text-dark mb-1"><strong>No. Polisi:</strong> <span class="badge badge-dark">{{ $booking->nomor_plat }}</span></p>
                        <p class="text-dark mb-1"><strong>Layanan:</strong> {{ $booking->layananServis->nama_layanan ?? '-' }}</p>
                        <p class="text-dark mb-0"><strong>Teknisi:</strong> {{ $booking->teknisi->name ?? '-' }}</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="50%">Deskripsi Pekerjaan / Komponen</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->rincianServis as $rincian)
                            <tr>
                                <td class="py-3">
                                    <span class="font-weight-bold text-dark">{{ $rincian->nama_item }}</span>
                                </td>
                                <td class="text-center py-3">{{ $rincian->jumlah }}</td>
                                <td class="text-right py-3">Rp {{ number_format($rincian->harga_satuan, 0, ',', '.') }}</td>
                                <td class="text-right py-3 font-weight-bold text-dark">Rp {{ number_format($rincian->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted font-italic">Tidak ada rincian komponen tambahan</td>
                            </tr>
                            @endforelse
                            
                            <!-- Harga Layanan Utama jika ada -->
                            @if($booking->layananServis)
                            <tr class="bg-light-50">
                                <td class="py-3">
                                    <span class="font-weight-bold text-primary">Biaya Jasa: {{ $booking->layananServis->nama_layanan }}</span>
                                </td>
                                <td class="text-center py-3">1</td>
                                <td class="text-right py-3">Rp {{ number_format($booking->layananServis->harga ?? 0, 0, ',', '.') }}</td>
                                <td class="text-right py-3 font-weight-bold text-dark">Rp {{ number_format($booking->layananServis->harga ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right border-0 pt-4">Total Biaya</th>
                                <th class="text-right border-0 pt-4 text-primary h4 font-weight-bold">
                                    Rp {{ number_format(optional($booking->pembayaranServis)->total_biaya ?? 0, 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row mt-5">
                    <div class="col-sm-7 pt-2">
                        <div class="alert alert-{{ optional($booking->pembayaranServis)->status_pembayaran == 'lunas' ? 'success' : 'warning' }} d-inline-block border-0 px-4">
                            <i class="fas fa-{{ optional($booking->pembayaranServis)->status_pembayaran == 'lunas' ? 'check-circle' : 'exclamation-circle' }} mr-2"></i>
                            STATUS PEMBAYARAN: <strong>{{ strtoupper(optional($booking->pembayaranServis)->status_pembayaran ?? 'BELUM BAYAR') }}</strong>
                        </div>
                        <p class="mt-3 text-muted small">* Harap simpan invoice ini sebagai bukti servis yang sah.</p>
                    </div>
                    <div class="col-sm-5 mt-4 mt-sm-0">
                        <div class="text-center" style="margin-top: 20px;">
                            <p class="mb-5 text-dark font-weight-bold">Kasir / Admin Servis,</p>
                            <div style="border-bottom: 2px solid #e3e6f0; width: 180px; margin: 0 auto 5px;"></div>
                            <p class="text-muted small">Tanda Tangan & Cap Bengkel</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="invoice-footer">
                <p class="mb-1">Terima kasih telah mempercayakan servis kendaraan Anda kepada kami.</p>
                <p class="mb-0 font-weight-bold text-primary">BLUD SYSTEM - Solusi Layanan Kendaraan Anda</p>
            </div>
        </div>
    </div>

    <!-- Floating Toolbar -->
    <div class="toolbar no-print">
        <a href="{{ route('admin.servis.transaksi.download-pdf', $booking->kode_booking) }}" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm font-weight-bold">
            <i class="fas fa-print mr-2 text-white-50"></i> Cetak Sekarang
        </a>
        <button onclick="window.close()" class="btn btn-outline-danger btn-sm rounded-pill px-4 shadow-sm border-0 font-weight-bold ml-2">
            <i class="fas fa-times mr-2"></i> Tutup
        </button>
    </div>

    <script>
        // Optional: Auto print preview
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
