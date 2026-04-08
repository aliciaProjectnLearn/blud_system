<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Booking Futsal #{{ $booking->id }}</title>
    
    <!-- Custom SB Admin 2 / Bootstrap style -->
    <link href="{{ asset('assets/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fc;
            padding-top: 30px;
            padding-bottom: 50px;
        }
        .invoice-card {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 0;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.1);
        }
        .invoice-header {
            padding: 2.5rem;
            border-bottom: 2px solid #e3e6f0;
        }
        .invoice-body {
            padding: 2.5rem;
        }
        .invoice-footer {
            padding: 1.5rem;
            background: #f8f9fc;
            border-top: 1px solid #e3e6f0;
            text-align: center;
            font-size: 0.85rem;
            color: #858796;
        }
        .badge-status {
            padding: 0.5em 1em;
            font-size: 0.9rem;
            text-transform: uppercase;
            font-weight: 700;
        }
        
        /* Floating Toolbar */
        .toolbar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            background: rgba(255,255,255,0.9);
            padding: 10px 20px;
            border-radius: 50px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            backdrop-filter: blur(5px);
            display: flex;
            gap: 10px;
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
                box-shadow: none;
                max-width: 100%;
                margin: 0;
                border: none;
            }
            .container {
                width: 100%;
                max-width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="card invoice-card">
            <!-- Header -->
            <div class="invoice-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="text-primary font-weight-bold mb-0">{{ $pengaturan->nama_aplikasi ?? 'BLUD SYSTEM' }}</h3>
                    <p class="text-muted mb-0 small text-uppercase tracking-wider">Layanan Futsal Unggulan</p>
                </div>
                <div class="text-right">
                    <h2 class="text-gray-300 font-weight-bold mb-0">INVOICE</h2>
                    <p class="text-dark font-weight-bold mb-0">#FTS-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>

            <!-- Body -->
            <div class="invoice-body">
                <div class="row mb-5">
                    <div class="col-sm-6">
                        <p class="text-muted mb-2 text-uppercase font-weight-bold small">Diterbitkan Untuk:</p>
                        <h5 class="font-weight-bold text-dark mb-1">{{ $booking->user->name }}</h5>
                        <p class="text-muted mb-0">{{ $booking->user->email }}</p>
                    </div>
                    <div class="col-sm-6 text-sm-right mt-3 mt-sm-0">
                        <p class="text-muted mb-2 text-uppercase font-weight-bold small">Rincian Transaksi:</p>
                        <p class="text-dark mb-0"><strong>Tgl Terbit:</strong> {{ now()->format('d M Y, H:i') }}</p>
                        <p class="text-dark mb-0"><strong>Metode:</strong> {{ $pembayaran->tipePembayaran->nama ?? 'Tunai' }}</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead class="bg-light shadow-sm">
                            <tr class="text-primary text-uppercase font-weight-bold small">
                                <th>Deskripsi Layanan</th>
                                <th class="text-center">Jadwal & Durasi</th>
                                <th class="text-right">Harga Unit</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-4">
                                    <h6 class="font-weight-bold text-dark mb-1">Sewa {{ $booking->lapangan->nama }}</h6>
                                    <p class="text-muted small mb-0">Ukuran: {{ $booking->lapangan->ukuran }}</p>
                                </td>
                                <td class="text-center py-4">
                                    <p class="mb-1">{{ \Carbon\Carbon::parse($booking->tgl_main)->format('d F Y') }}</p>
                                    <p class="mb-0 text-muted small">{{ substr($booking->jam_mulai, 0, 5) }} - {{ substr($booking->jam_selesai, 0, 5) }} ({{ $booking->durasi_main }} Jam)</p>
                                </td>
                                <td class="text-right py-4">Rp {{ number_format(($pembayaran->jumlah_bayar ?? 0) / ($booking->durasi_main ?: 1), 0, ',', '.') }}</td>
                                <td class="text-right py-4 font-weight-bold text-dark">Rp {{ number_format($pembayaran->jumlah_bayar ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-5">
                    <div class="col-sm-6 mb-3">
                        <p class="text-muted mb-2 text-uppercase font-weight-bold small">Status Pembayaran:</p>
                        @php
                            $status = strtolower($pembayaran->status ?? 'menunggu');
                            $badgeClass = ($status == 'lunas' || $status == 'verifikasi') ? 'badge-success' : 'badge-warning';
                            $statusLabel = ($status == 'lunas' || $status == 'verifikasi') ? 'LUNAS' : 'MENUNGGU';
                        @endphp
                        <span class="badge {{ $badgeClass }} badge-status px-3">{{ $statusLabel }}</span>
                    </div>
                    <div class="col-sm-6">
                        <div class="bg-light p-4 rounded text-right shadow-sm border-left-success">
                            <p class="text-muted text-uppercase font-weight-bold small mb-2">Total Tagihan:</p>
                            <h2 class="text-success font-weight-bold mb-0">Rp {{ number_format($pembayaran->jumlah_bayar ?? 0, 0, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="invoice-footer">
                <p class="mb-1">Terima kasih telah menggunakan fasilitas futsal kami.</p>
                <p class="mb-0"><strong>BLUD Sekolah</strong> - Sistem Informasi Manajemen Digital.</p>
            </div>
        </div>
    </div>

    <!-- Floating Toolbar -->
    <div class="toolbar no-print">
        <button onclick="window.print()" class="btn btn-outline-primary btn-sm rounded-pill px-4 shadow-sm border-0 font-weight-bold bg-white">
            <i class="fas fa-print mr-2"></i> Cetak Sekarang
        </button>
        <a href="{{ route('user.futsal.download-invoice', $booking->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm font-weight-bold">
            <i class="fas fa-download mr-2"></i> Download PDF
        </a>
        <button onclick="window.close()" class="btn btn-light btn-sm rounded-pill px-3 text-danger border-0 font-weight-bold ml-2">
            Tutup
        </button>
    </div>

    <script>
        // Otomatis picu cetak saat halaman dimuat
        window.onload = function() {
            setTimeout(() => {
                // window.print(); // Opsional: Berikan jeda agar user bisa melihat halaman dulu
            }, 500);
        };
    </script>
</body>
</html>
