@extends('layouts.publik')

@section('title', 'Detail Servis Kendaraan')

@push('styles')
    <style>
        .detail-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .detail-header {
            background: linear-gradient(90deg, #4e73df, #224abe);
            color: white;
            padding: 25px;
        }

        .status-badge {
            font-size: 0.9rem;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: bold;
        }

        .status-menunggu { background-color: #f6c23e; color: #fff; }
        .status-dikonfirmasi { background-color: #36b9cc; color: #fff; }
        .status-diproses { background-color: #4e73df; color: #fff; }
        .status-selesai { background-color: #1cc88a; color: #fff; }
        .status-dibatalkan, .status-batal { background-color: #e74a3b; color: #fff; }

        .section-title {
            font-size: 1.1rem;
            font-weight: bold;
            color: #4e73df;
            border-bottom: 2px solid #eaecf4;
            padding-bottom: 10px;
            margin-bottom: 20px;
            margin-top: 30px;
        }

        .info-row {
            display: flex;
            margin-bottom: 15px;
            border-bottom: 1px dashed #eaecf4;
            padding-bottom: 10px;
        }

        .info-label {
            width: 40%;
            font-weight: 600;
            color: #858796;
            font-size: 0.9rem;
        }

        .info-value {
            width: 60%;
            font-weight: bold;
            color: #5a5c69;
        }

        .photo-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }

        .photo-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #eaecf4;
        }

        @media (max-width: 576px) {
            .info-row {
                flex-direction: column;
            }
            .info-label {
                width: 100%;
                margin-bottom: 4px;
            }
            .info-value {
                width: 100%;
            }
            .detail-header {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 12px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- NOTIFIKASI STATUS SELESAI --}}
                @if(session('warning') || isset($warning))
                    <div class="alert alert-warning border-left-warning shadow-sm py-3 px-4 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-lock fa-lg mr-3 text-warning"></i>
                            <div>
                                <strong>Servis Selesai</strong><br>
                                <span class="small">{{ session('warning') ?? $warning }}</span>
                            </div>
                        </div>
                    </div>
                @endif


                @if(session('success'))
                    <div class="alert alert-success border-left-success shadow-sm py-3 px-4 mb-4">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger border-left-danger shadow-sm py-3 px-4 mb-4">
                        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                    </div>
                @endif

                {{-- KARTU DETAIL UTAMA --}}
                <div class="card detail-card">
                    <div class="detail-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 font-weight-bold">Informasi Booking</h5>
                            <p class="mb-0 text-white-50 small">Kode: {{ $booking->kode_booking }}</p>
                        </div>
                        <div>
                            <span class="status-badge status-{{ strtolower($booking->status) }}">
                                {{ strtoupper($booking->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-gray-800 mb-3"><i class="fas fa-car mr-2 text-primary"></i>Kendaraan</h6>
                                <div class="info-row">
                                    <div class="info-label">Merek & Model</div>
                                    <div class="info-value">{{ $booking->merek_kendaraan }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Nomor Plat</div>
                                    <div class="info-value">{{ $booking->nomor_plat }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Tahun</div>
                                    <div class="info-value">{{ $booking->tahun_kendaraan }}</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-gray-800 mb-3"><i class="fas fa-calendar-alt mr-2 text-primary"></i>Jadwal & Layanan</h6>
                                <div class="info-row">
                                    <div class="info-label">Tanggal</div>
                                    <div class="info-value">{{ \Carbon\Carbon::parse($booking->tanggal_booking)->translatedFormat('d F Y') }}</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Jam</div>
                                    <div class="info-value">{{ \Carbon\Carbon::parse($booking->jam_booking)->format('H:i') }} WIB</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Layanan</div>
                                    <div class="info-value">{{ $booking->layananServis->nama_layanan ?? '-' }}</div>
                                </div>
                            </div>
                        </div>

                        @if(strtolower($booking->status) === 'menunggu')
                            <div class="mt-4 pt-3 border-top text-center">
                                <a href="#"
                                   onclick="konfirmasiBatal(event)"
                                   class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-times-circle mr-1"></i> Batalkan Booking
                                </a>
                            </div>
                        @elseif(in_array(strtolower($booking->status), ['diproses','siap_bayar','selesai']))
                            <div class="mt-4 pt-3 border-top text-center">
                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                    <i class="fas fa-lock mr-1"></i>
                                    Tidak dapat dibatalkan — Kendaraan sedang dalam proses pengerjaan
                                </button>
                            </div>
                        @endif

                        {{-- JIKA STATUS SELESAI, TAMPILKAN RINCIAN --}}
                        @if(strtolower($booking->status) === 'selesai' || strtolower($booking->status) === 'siap_bayar')
                            <div class="section-title"><i class="fas fa-clipboard-list mr-2"></i>Rincian Pekerjaan & Biaya</div>
                            
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-sm text-sm">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Item/Layanan/Sparepart</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-right">Harga</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalRincian = 0; @endphp
                                        @forelse($booking->rincianServis as $rincian)
                                            @php $totalRincian += $rincian->subtotal; @endphp
                                            <tr>
                                                <td>{{ $rincian->nama_item }}</td>
                                                <td class="text-center">{{ $rincian->jumlah }}</td>
                                                <td class="text-right">Rp {{ number_format($rincian->harga_satuan, 0, ',', '.') }}</td>
                                                <td class="text-right font-weight-bold">Rp {{ number_format($rincian->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Belum ada rincian yang diinput kasir/teknisi.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if($booking->rincianServis->count() > 0)
                                        <tfoot class="bg-light font-weight-bold">
                                            <tr>
                                                <td colspan="3" class="text-right">TOTAL BIAYA:</td>
                                                <td class="text-right text-primary">Rp {{ number_format($totalRincian, 0, ',', '.') }}</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>

                            {{-- STATUS PEMBAYARAN --}}
                            @if($booking->pembayaranServis)
                                <div class="alert {{ $booking->pembayaranServis->status_pembayaran === 'lunas' ? 'alert-success' : 'alert-warning' }} d-flex justify-content-between align-items-center" style="border-radius: 10px;">
                                    <div>
                                        <h6 class="mb-1 font-weight-bold"><i class="fas fa-wallet mr-2"></i>Status Pembayaran</h6>
                                        <small>Tipe: {{ strtoupper($booking->pembayaranServis->tipe_pembayaran) }}</small>
                                    </div>
                                    <div class="text-right">
                                        <h5 class="mb-0 font-weight-bold text-uppercase">{{ $booking->pembayaranServis->status_pembayaran }}</h5>
                                    </div>
                                </div>
                            @endif

                            {{-- FOTO DOKUMENTASI --}}
                            @if($booking->fotoServis && $booking->fotoServis->count() > 0)
                                <div class="section-title"><i class="fas fa-camera mr-2"></i>Dokumentasi Pekerjaan</div>
                                <div class="photo-gallery">
                                    @foreach($booking->fotoServis as $foto)
                                        <div class="photo-item">
                                            <a href="{{ asset('storage/' . $foto->path_foto) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $foto->path_foto) }}" alt="Foto Servis">
                                            </a>
                                            @if($foto->keterangan)
                                                <div class="small text-muted text-center mt-1">{{ $foto->keterangan }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function konfirmasiBatal(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Batalkan Booking?',
        text: 'Apakah Anda yakin ingin membatalkan booking ini? Tindakan ini tidak dapat diurungkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            {{-- REVISI 3: Gunakan route servis yang benar --}}
            window.location.href = "{{ route('user.servis.token.batalkan', $booking->access_token) }}";
        }
    });
}
</script>
@endpush