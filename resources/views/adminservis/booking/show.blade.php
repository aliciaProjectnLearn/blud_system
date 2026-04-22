@extends('layouts.app')

@section('title', 'Detail Booking - ' . $booking->kode_booking)

@section('content')
<div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4">
    <div class="mb-2 mb-sm-0">
        <h1 class="h3 text-gray-800">Detail Booking: #{{ $booking->kode_booking }}</h1>
        <p class="mb-0 text-muted">Dibuat pada {{ $booking->created_at->translatedFormat('d F Y, H:i') }}</p>
    </div>
    <a href="{{ route('adminservis.booking.index') }}" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left mr-1 text-white-50"></i> Kembali
    </a>
</div>

<div class="row">
    <!-- Info Column -->
    <div class="col-lg-8">
        <!-- Customer & Vehicle Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-tag mr-2"></i> Informasi Pelanggan & Kendaraan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 border-right">
                        <label class="small font-weight-bold text-gray-600 mb-1">DATA PELANGGAN</label>
                        <h5 class="font-weight-bold mb-0 text-primary">{{ $booking->user->nama_lengkap ?? $booking->user->name }}</h5>
                        <p class="small text-muted mb-3">{{ $booking->user->email ?? 'Tidak ada email' }} | {{ $booking->user->no_hp ?? '-' }}</p>
                        
                        <label class="small font-weight-bold text-gray-600 mb-1">PROFIL USER</label>
                        <div class="d-flex align-items-center mb-4">
                            <span class="badge badge-info mr-2 px-2 py-1">{{ strtoupper($booking->user->roles->first()->nama ?? 'USER') }}</span>
                            @if($booking->user->membership)
                                <span class="badge badge-warning px-2 py-1"><i class="fas fa-crown mr-1"></i> {{ strtoupper($booking->user->membership->paketMembership->nama_paket ?? 'MEMBER') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6 pl-sm-4">
                        <label class="small font-weight-bold text-gray-600 mb-1">DETAIL KENDARAAN</label>
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td width="40%" class="pl-0">Jenis</td>
                                <td class="font-weight-bold text-gray-800">: {{ strtoupper($booking->tipe_kendaraan) }}</td>
                            </tr>
                            <tr>
                                <td class="pl-0">Merek</td>
                                <td class="font-weight-bold text-gray-800">: {{ $booking->merek_kendaraan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="pl-0">No. Polisi</td>
                                <td class="font-weight-bold text-gray-800">: <span class="badge badge-dark px-2">{{ $booking->nomor_plat }}</span></td>
                            </tr>
                            <tr>
                                <td class="pl-0">Tahun</td>
                                <td class="font-weight-bold text-gray-800">: {{ $booking->tahun_kendaraan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i> Rincian Booking</h6>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded border-left-primary h-100">
                            <label class="small font-weight-bold text-primary mb-1">WAKTU KEDATANGAN</label>
                            <h4 class="font-weight-bold mb-0">{{ \Carbon\Carbon::parse($booking->jam_booking)->format('H:i') }}</h4>
                            <p class="text-muted mb-0">{{ $booking->tanggal_booking->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded border-left-info h-100">
                            <label class="small font-weight-bold text-info mb-1">STATUS SAAT INI</label>
                            @php
                                $status = $booking->status;
                                $badge = 'secondary';
                                if($status == 'menunggu') $badge = 'warning';
                                elseif($status == 'diproses') $badge = 'primary';
                                elseif($status == 'selesai') $badge = 'success';
                                elseif($status == 'batal') $badge = 'danger';
                            @endphp
                            <div class="h5 mb-0">
                                <span class="badge badge-{{ $badge }} px-3 py-2 text-uppercase">
                                    {{ $status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="small font-weight-bold text-gray-600 mb-1 d-block">KELUHAN / PERMINTAAN PELANGGAN</label>
                    <div class="p-3 bg-light rounded border italic">
                        "{{ $booking->keluhan ?? 'Tidak ada keluhan yang dicatat.' }}"
                    </div>
                </div>

                @if($booking->layananServis)
                <div class="mb-0">
                    <label class="small font-weight-bold text-gray-600 mb-1 d-block">JENIS LAYANAN YANG DIPILIH</label>
                    <h6 class="font-weight-bold text-gray-800"><i class="fas fa-concierge-bell mr-2 text-primary"></i> {{ $booking->layananServis->nama_layanan }}</h6>
                </div>
                @endif
            </div>
        </div>

        <!-- Rincian Servis List (If any) -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list mr-2"></i> Komponen & Jasa Servis</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-gray-100">
                            <tr>
                                <th>Item / Komponen</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Harga Satuan</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->rincianServis as $rincian)
                                <tr>
                                    <td>{{ $rincian->nama_item }}</td>
                                    <td class="text-center">{{ $rincian->jumlah }}</td>
                                    <td class="text-right">Rp {{ number_format($rincian->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-right font-weight-bold">Rp {{ number_format($rincian->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">Belum ada rincian komponen/jasa yang ditambahkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($booking->rincianServis->count() > 0)
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right bg-light">TOTAL ESTIMASI</th>
                                <th class="text-right bg-light text-primary h6 font-weight-bold">
                                    Rp {{ number_format($booking->rincianServis->sum('subtotal'), 0, ',', '.') }}
                                </th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Column -->
    <div class="col-lg-4">
        <!-- Update Status Card -->
        <div class="card shadow mb-4 border-bottom-{{ $badge }}">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tasks mr-2"></i> Update Status</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('adminservis.booking.update', $booking->id) }}" method="POST" x-data="{ originalStatus: '{{ $booking->status }}', currentStatus: '{{ $booking->status }}' }">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="status" class="small font-weight-bold">Status Booking</label>
                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" x-model="currentStatus" {{ $status == 'batal' ? 'disabled' : '' }}>
                            <option value="menunggu" {{ $status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="diproses" {{ $status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="selesai" {{ $status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="batal" {{ $status == 'batal' ? 'selected' : '' }}>Batal (Cancel)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($status != 'batal')
                    <button type="submit" class="btn btn-primary btn-block shadow-sm" x-show="currentStatus !== originalStatus" 
                        onclick="return confirm('Apakah Anda yakin ingin mengubah status booking ini?')">
                        Simpan Perubahan
                    </button>
                    @else
                    <div class="alert alert-danger mb-0 py-2 small">
                        <i class="fas fa-ban mr-1"></i> Booking telah dibatalkan.
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Assign Technician Card -->
        <div class="card shadow mb-4 border-left-info">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-user-cog mr-2"></i> Penugasan Teknisi</h6>
            </div>
            <div class="card-body">
                @if($booking->teknisi)
                    <div class="p-3 bg-light rounded border mb-4 text-center">
                        <div class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <i class="fas fa-user-check text-white fa-lg"></i>
                        </div>
                        <h6 class="font-weight-bold mb-1">{{ $booking->teknisi->name }}</h6>
                        <p class="text-xs text-muted mb-0">Teknisi ditugaskan</p>
                    </div>
                @endif

                <form action="{{ route('adminservis.booking.assign', $booking->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="teknisi_id" class="small font-weight-bold">{{ $booking->teknisi ? 'Ubah Teknisi' : 'Pilih Teknisi' }}</label>
                        <select name="teknisi_id" id="teknisi_id" class="form-control @error('teknisi_id') is-invalid @enderror" {{ $status == 'batal' ? 'disabled' : '' }}>
                            <option value="" disabled {{ !$booking->teknisi ? 'selected' : '' }}>-- Pilih Teknisi --</option>
                            @foreach($listTeknisi as $teknisi)
                                <option value="{{ $teknisi->id }}" {{ $booking->teknisi_id == $teknisi->id ? 'selected' : '' }}>
                                    {{ $teknisi->name }} ({{ $teknisi->roles->first()->nama ?? 'Teknisi' }})
                                </option>
                            @endforeach
                        </select>
                        @error('teknisi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($status != 'batal')
                    <button type="submit" class="btn btn-info btn-block shadow-sm">
                        <i class="fas fa-save mr-1"></i> {{ $booking->teknisi ? 'Perbarui Penugasan' : 'Tugaskan Teknisi' }}
                    </button>
                    @endif
                </form>
            </div>
        </div>

        <!-- Extra Note (Catatan Admin) -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-sticky-note mr-2"></i> Catatan Admin</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('adminservis.booking.update', $booking->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="{{ $booking->status }}"> {{-- Keep current status --}}
                    
                    <div class="form-group">
                        <textarea name="catatan_admin" class="form-control" rows="4" placeholder="Tambahkan catatan internal..." {{ $status == 'batal' ? 'disabled' : '' }}>{{ $booking->catatan_admin }}</textarea>
                    </div>
                    @if($status != 'batal')
                    <button type="submit" class="btn btn-secondary btn-block btn-sm">Simpan Catatan</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-gray-100 { background-color: #f8f9fc; }
    .border-left-primary { border-left: .25rem solid #4e73df!important; }
    .border-left-info { border-left: .25rem solid #36b9cc!important; }
</style>
@endpush
@endsection
