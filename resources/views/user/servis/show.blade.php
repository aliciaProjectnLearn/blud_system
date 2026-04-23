@extends('layouts.app')

@section('title', 'Detail Servis #' . $booking->kode_booking)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="mb-4">
        <a href="{{ route('user.servis.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row">
        <!-- Left Side: Status & Kendaraan -->
        <div class="col-lg-4">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Booking</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="text-xs font-weight-bold text-uppercase text-muted mb-1">Kode Booking</div>
                        <div class="h5 font-weight-bold text-gray-800">{{ $booking->kode_booking }}</div>
                        @php
                            $badgeClass = 'secondary';
                            if($booking->status == 'menunggu') $badgeClass = 'warning';
                            elseif($booking->status == 'diproses') $badgeClass = 'primary';
                            elseif($booking->status == 'selesai') $badgeClass = 'success';
                            elseif($booking->status == 'batal') $badgeClass = 'danger';
                        @endphp
                        <span class="badge badge-{{ $badgeClass }} px-3 py-2 text-uppercase mt-2">{{ $booking->status }}</span>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase d-block mb-1">Jadwal</label>
                        <div class="font-weight-bold">{{ $booking->tanggal_booking->translatedFormat('d F Y') }}</div>
                        <div class="small">{{ $booking->jam_booking }} WIB</div>
                    </div>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase d-block mb-1">Kendaraan</label>
                        <div class="h6 font-weight-bold text-gray-800 mb-0 text-uppercase">
                            {{ $booking->merek_kendaraan }} {{ $booking->tipe_kendaraan }}
                        </div>
                        <div class="badge badge-dark mt-1">{{ $booking->nomor_plat }}</div>
                    </div>
                    @if($booking->teknisi)
                        <div class="mb-0">
                            <label class="small font-weight-bold text-muted text-uppercase d-block mb-1">Teknisi</label>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-cog text-gray-400 mr-2"></i>
                                <div class="font-weight-bold text-gray-700">{{ $booking->teknisi->name }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-success">Status Pembayaran</h6>
                </div>
                <div class="card-body">
                    @if($booking->pembayaranServis)
                        <div class="mb-3 text-center">
                            <div class="h4 font-weight-bold text-success mb-0">
                                Rp {{ number_format($booking->pembayaranServis->total_biaya, 0, ',', '.') }}
                            </div>
                            <div class="small text-muted mt-1">Total yang harus dibayar</div>
                        </div>
                        <hr>
                        <div class="row small mb-2">
                            <div class="col-6 text-muted">Status</div>
                            <div class="col-6 text-right font-weight-bold">
                                @php
                                    $payStatus = $booking->pembayaranServis->status_pembayaran;
                                    $payBadge = 'secondary';
                                    if($payStatus == 'lunas') $payBadge = 'success';
                                    elseif($payStatus == 'dp') $payBadge = 'info';
                                    elseif($payStatus == 'belum_bayar') $payBadge = 'danger';
                                @endphp
                                <span class="badge badge-{{ $payBadge }}">{{ strtoupper(str_replace('_', ' ', $payStatus)) }}</span>
                            </div>
                        </div>
                        <div class="row small mb-2">
                            <div class="col-6 text-muted">Metode</div>
                            <div class="col-6 text-right font-weight-bold text-uppercase">
                                {{ $booking->pembayaranServis->tipe_pembayaran ?? '-' }}
                            </div>
                        </div>
                        @if($booking->pembayaranServis->tanggal_bayar)
                            <div class="row small">
                                <div class="col-6 text-muted">Tgl Bayar</div>
                                <div class="col-6 text-right font-weight-bold">
                                    {{ $booking->pembayaranServis->tanggal_bayar->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300 mb-2"></i>
                            <p class="small text-muted mb-0">Rincian biaya belum diinput kasir</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Side: Rincian Items -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Rincian Servis & Suku Cadang</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3 py-2">Item / Jasa</th>
                                    <th class="px-3 py-2 text-center" width="80">Qty</th>
                                    <th class="px-3 py-2 text-right" width="180">Harga</th>
                                    <th class="px-3 py-2 text-right" width="180">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($booking->rincianServis as $rincian)
                                    <tr>
                                        <td class="px-3 py-3">
                                            <div class="font-weight-bold">{{ $rincian->nama_item }}</div>
                                            @if($rincian->produkServis)
                                                <div class="small text-muted">{{ $rincian->produkServis->merk }} - {{ $rincian->produkServis->kode_part }}</div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-center align-middle">{{ $rincian->jumlah }}</td>
                                        <td class="px-3 py-3 text-right align-middle">Rp {{ number_format($rincian->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="px-3 py-3 text-right align-middle font-weight-bold text-primary">
                                            Rp {{ number_format($rincian->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fas fa-hourglass-half mb-3 fa-2x"></i>
                                            <p>Rincian servis sedang diproses oleh tim kami.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($booking->rincianServis->isNotEmpty())
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="3" class="text-right font-weight-bold py-3">Total Seluruhnya</td>
                                        <td class="text-right py-3 font-weight-bold h5 text-primary mb-0">
                                            Rp {{ number_format($booking->rincianServis->sum('subtotal'), 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
                @if($booking->catatan_admin)
                    <div class="card-footer bg-light p-3">
                        <label class="small font-weight-bold text-uppercase text-muted d-block mb-1">Catatan Tambahan</label>
                        <p class="small mb-0 text-gray-700 italic">"{{ $booking->catatan_admin }}"</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
