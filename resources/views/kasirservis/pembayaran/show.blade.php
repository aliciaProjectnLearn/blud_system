@extends('layouts.app')

@section('title', 'Proses Pembayaran - ' . $booking->kode_booking)

@section('content')
<div class="container-fluid">

    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Proses Pembayaran</h1>
            <p class="mb-0 text-muted small">Kode Booking:
                <strong>{{ $booking->kode_booking }}</strong>
            </p>
        </div>
        <a href="{{ route('kasir.pembayaran.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="row">

        {{-- Kolom Kiri: Info Pelanggan + Rincian Servis --}}
        <div class="col-lg-7">

            {{-- Card: Data Pelanggan --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user mr-1"></i> Data Pelanggan & Kendaraan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted small" width="40%">Nama</td>
                                    <td class="font-weight-bold">
                                        {{ $booking->nama_pemesan ?? $booking->pelanggan->name ?? 'N/A' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted small">Telepon</td>
                                    <td>{{ $booking->no_hp ?? $booking->pelanggan->no_hp ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small">Layanan</td>
                                    <td>{{ $booking->layananServis->nama_layanan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted small" width="40%">Kendaraan</td>
                                    <td class="font-weight-bold text-uppercase">
                                        {{ $booking->merek_kendaraan }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted small">No. Plat</td>
                                    <td>
                                        <span class="badge badge-dark px-2 py-1">
                                            {{ $booking->nomor_plat }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted small">Tanggal</td>
                                    <td>
                                        {{ $booking->tanggal_booking->translatedFormat('d M Y') }}
                                        — {{ $booking->jam_booking }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @if($booking->keluhan)
                        <hr>
                        <div class="small text-muted">
                            <strong>Keluhan:</strong> {{ $booking->keluhan }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card: Rincian Servis --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list-alt mr-1"></i> Rincian Servis
                    </h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-3 py-2" width="40">No</th>
                                <th class="px-3 py-2">Item</th>
                                <th class="px-3 py-2 text-center" width="70">Jml</th>
                                <th class="px-3 py-2 text-right" width="130">Harga Satuan</th>
                                <th class="px-3 py-2 text-right" width="130">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($booking->rincianServis as $i => $item)
                                <tr>
                                    <td class="px-3 py-2 align-middle text-center">
                                        {{ $i + 1 }}
                                    </td>
                                    <td class="px-3 py-2 align-middle">
                                        {{ $item->nama_item }}
                                        @if($item->keterangan)
                                            <div class="small text-muted">{{ $item->keterangan }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 align-middle text-center">
                                        {{ $item->jumlah }}
                                    </td>
                                    <td class="px-3 py-2 align-middle text-right">
                                        Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 align-middle text-right font-weight-bold">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="4" class="px-3 py-3 text-right font-weight-bold">
                                    TOTAL BIAYA
                                </td>
                                <td class="px-3 py-3 text-right font-weight-bold text-success"
                                    style="font-size:1.1rem;">
                                    Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Pembayaran --}}
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-cash-register mr-1"></i> Form Konfirmasi Pembayaran
                    </h6>
                    @if($booking->pembayaranServis?->kode_pembayaran)
                        <small class="opacity-75">
                            {{ $booking->pembayaranServis->kode_pembayaran }}
                        </small>
                    @endif
                </div>
                <div class="card-body">

                    {{-- Total Biaya (Read-only display) --}}
                    <div class="text-center mb-4 p-3 bg-light rounded">
                        <div class="text-muted small mb-1">Total Yang Harus Dibayar</div>
                        <div class="font-weight-bold text-success" style="font-size:1.8rem;">
                            Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                        </div>
                    </div>

                    @if($booking->pembayaranServis && $booking->pembayaranServis->status_pembayaran === 'lunas')
                        {{-- Sudah lunas, tampilkan info saja --}}
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                            <strong>Pembayaran Sudah Lunas</strong><br>
                            <small>
                                {{ $booking->pembayaranServis->tipe_pembayaran }} —
                                {{ $booking->pembayaranServis->tanggal_bayar?->format('d M Y H:i') }}
                            </small>
                        </div>
                    @else
                        {{-- Form Pembayaran --}}
                        <form action="{{ route('kasir.pembayaran.konfirmasi', $booking->id) }}"
                              method="POST"
                              id="formPembayaran">
                            @csrf

                            <div class="form-group">
                                <label class="font-weight-bold small">
                                    Total Biaya (Manual Override)
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="number" name="total_biaya"
                                           class="form-control"
                                           value="{{ old('total_biaya', $totalBiaya) }}"
                                           min="0" required>
                                </div>
                                <small class="text-muted">Biaya default dihitung otomatis dari rincian servis. Anda bisa mengubahnya jika diperlukan.</small>
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold small">
                                    Metode Pembayaran <span class="text-danger">*</span>
                                </label>
                                <select name="tipe_pembayaran"
                                        class="form-control @error('tipe_pembayaran') is-invalid @enderror"
                                        required>
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="tunai"
                                        {{ old('tipe_pembayaran') == 'tunai' ? 'selected' : '' }}>
                                        Tunai
                                    </option>
                                    <option value="transfer"
                                        {{ old('tipe_pembayaran') == 'transfer' ? 'selected' : '' }}>
                                        Transfer Bank
                                    </option>
                                    <option value="qris"
                                        {{ old('tipe_pembayaran') == 'qris' ? 'selected' : '' }}>
                                        QRIS
                                    </option>
                                </select>
                                @error('tipe_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="font-weight-bold small">Catatan (opsional)</label>
                                <textarea name="catatan"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                            </div>

                            <hr>

                            {{-- Tombol Konfirmasi dengan double-submit protection --}}
                            <button type="submit"
                                    class="btn btn-success btn-block btn-lg"
                                    id="btnKonfirmasi">
                                <i class="fas fa-check-circle mr-1"></i>
                                Konfirmasi Pembayaran
                            </button>

                            <a href="{{ route('kasir.pembayaran.index') }}"
                               class="btn btn-outline-secondary btn-block mt-2">
                                Batal
                            </a>
                        </form>
                    @endif

                </div>
            </div>


        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#formPembayaran').on('submit', function(e) {
        e.preventDefault();

        const tipe = $('select[name="tipe_pembayaran"]').val();
        if (!tipe) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih Metode Pembayaran',
                text: 'Harap pilih metode pembayaran terlebih dahulu.',
                confirmButtonColor: '#4e73df'
            });
            return;
        }

        const totalFormatted = 'Rp {{ number_format($totalBiaya, 0, ",", ".") }}';
        const tipeLabel = {
            'tunai': 'Tunai',
            'transfer': 'Transfer Bank',
            'qris': 'QRIS'
        }[tipe] || tipe;

        Swal.fire({
            title: 'Konfirmasi Pembayaran',
            html: `Metode: <strong>${tipeLabel}</strong><br>Total: <strong>${totalFormatted}</strong><br><br><small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1cc88a',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Konfirmasi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#btnKonfirmasi')
                    .prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');
                $('#formPembayaran')[0].submit();
            }
        });
    });
});
</script>
@endpush
