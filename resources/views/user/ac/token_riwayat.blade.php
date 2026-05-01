@extends('layouts.publik')

@section('title', 'Riwayat Servis AC Saya')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-lg border-0 rounded-lg">
            <div class="card-header bg-gradient-info text-white py-3">
                <h5 class="mb-0 font-weight-bold"><i class="fas fa-history mr-2"></i>Riwayat Servis AC Berdasarkan Nomor HP</h5>
            </div>
            <div class="card-body p-4">
                <div class="mb-4 d-flex align-items-center">
                    <div class="bg-light p-3 rounded-circle mr-3">
                        <i class="fas fa-phone-alt text-info"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted small">Menampilkan data untuk nomor:</h6>
                        <h5 class="mb-0 font-weight-bold text-dark">{{ $booking->no_hp ?? ($booking->user->no_hp ?? '-') }}</h5>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover border">
                        <thead class="bg-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Layanan</th>
                                <th>Status</th>
                                <th class="text-right">Total</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $item)
                            <tr>
                                <td class="align-middle">{{ \Carbon\Carbon::parse($item->tgl_kunjungan)->format('d/m/Y') }}</td>
                                <td class="align-middle">
                                    <div class="font-weight-bold text-dark">{{ $item->layanan->nama ?? '-' }}</div>
                                    <div class="small text-muted">{{ $item->merek_ac }}</div>
                                </td>
                                <td class="align-middle">
                                    @if($item->status == 'selesai')
                                        <span class="badge badge-success px-3 py-2">Selesai</span>
                                    @elseif($item->status == 'proses')
                                        <span class="badge badge-info px-3 py-2">Proses</span>
                                    @else
                                        <span class="badge badge-warning px-3 py-2">Menunggu</span>
                                    @endif
                                </td>
                                <td class="align-middle text-right font-weight-bold text-dark">
                                    Rp {{ number_format($item->pembayaran->total_harga ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="align-middle text-center">
                                    <a href="{{ route('user.ac.token.show', $item->access_token) }}" class="btn btn-sm btn-info rounded-pill px-3">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <img src="https://img.icons8.com/clouds/100/000000/nothing-found.png" alt="No data" class="mb-3">
                                    <p class="text-muted mb-0">Belum ada riwayat servis untuk nomor ini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('user.ac.token.show', $token) }}" class="btn btn-light btn-sm text-primary">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Detail Terakhir
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
