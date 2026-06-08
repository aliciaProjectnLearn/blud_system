@extends('layouts.app')

@section('title', 'Manajemen Booking AC')

@section('content')
<div class="container-fluid">

    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Manajemen Booking AC</h1>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- Tabel Booking --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Booking AC</h6>

            {{-- Filter Form --}}
            <form method="GET" action="{{ route('admin.ac.booking.index') }}" class="form-inline">
                <select name="status" class="form-control form-control-sm mr-2">
                    <option value="">-- Semua Status --</option>
                    @foreach($statusList as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-secondary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if(request()->filled('status'))
                    <a href="{{ route('admin.ac.booking.index') }}" class="btn btn-sm btn-light ml-1">Reset</a>
                @endif
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered text-center" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Booking</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Tgl Kunjungan</th>
                            <th>Teknisi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $i => $b)
                        <tr>
                            <td>{{ $bookings->firstItem() + $i }}</td>
                            <td>{{ \Carbon\Carbon::parse($b->created_at)->format('d M Y H:i') }}</td>
                            <td>
                                {{ $b->nama_pelanggan ?? ($b->user->name ?? '-') }}<br>
                                <small class="text-muted">{{ $b->no_hp ?? ($b->user->no_hp ?? '') }}</small>
                            </td>
                            <td>{{ $b->layanan->nama ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($b->tgl_kunjungan)->format('d M Y') }}</td>
                            <td>
                                @if($b->teknisi)
                                    {{ $b->teknisi->name }}
                                @else
                                    <span class="text-muted">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeColor = match($b->status) {
                                        'menunggu' => 'warning',
                                        'proses'   => 'info',
                                        'selesai'  => 'success',
                                        'dibatalkan' => 'danger',
                                        default    => 'secondary',
                                    };
                                @endphp
                                <span class="badge badge-{{ $badgeColor }}">{{ ucfirst($b->status) }}</span>
                            </td>
                            <td>
                                {{-- Tombol Detail --}}
                                <button type="button" class="btn btn-info btn-circle btn-sm" title="Detail"
                                    data-toggle="modal" data-target="#detailModal{{ $b->id }}">
                                    <i class="fas fa-info-circle"></i>
                                </button>

                                {{-- Tombol Approve (hanya jika menunggu/pending) --}}
                                @if(in_array($b->status, ['menunggu', 'pending']))
                                    <button type="button" class="btn btn-success btn-circle btn-sm" title="Approve"
                                        data-toggle="modal" data-target="#approveModal{{ $b->id }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif

                                {{-- Tombol Selesai (hanya jika proses) --}}
                                @if($b->status === 'proses')
                                    <button type="button" class="btn btn-primary btn-circle btn-sm" title="Selesai"
                                        data-toggle="modal" data-target="#selesaiModal{{ $b->id }}">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                @endif

                                {{-- Tombol Batalkan / Tolak (hanya jika menunggu atau proses) --}}
                                @if(in_array($b->status, ['menunggu', 'proses']))
                                    <button type="button" class="btn btn-danger btn-circle btn-sm" 
                                        title="{{ $b->status === 'menunggu' ? 'Tolak' : 'Batalkan' }}"
                                        data-toggle="modal" data-target="#cancelModal{{ $b->id }}">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data booking.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $bookings->links() }}
        </div>
    </div>


    {{-- Modals --}}
    @foreach($bookings as $b)
                        {{-- Modal Detail --}}
                        <div class="modal fade" id="detailModal{{ $b->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content border-0 shadow-lg">
                                    <div class="modal-header border-0 bg-info text-white p-4">
                                        <h5 class="modal-title font-weight-bold"><i class="fas fa-info-circle mr-2"></i>Detail Booking #{{ $b->id }}</h5>
                                        <button class="close text-white" type="button" data-dismiss="modal">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body p-4 bg-light">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="card border-0 shadow-sm rounded-lg mb-4">
                                                    <div class="card-body">
                                                        <h6 class="text-primary font-weight-bold mb-3 border-bottom pb-2">
                                                            <i class="fas fa-user mr-1"></i> Informasi Pelanggan
                                                        </h6>
                                                        <div class="row mb-2">
                                                            <div class="col-sm-4 text-muted">Pelanggan</div>
                                                            <div class="col-sm-8 font-weight-bold">{{ $b->nama_pelanggan ?? ($b->user->name ?? '-') }}</div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-sm-4 text-muted">No. HP</div>
                                                            <div class="col-sm-8 font-weight-bold text-info">{{ $b->no_hp ?? ($b->user->no_hp ?? '-') }}</div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-sm-4 text-muted">Alamat</div>
                                                            <div class="col-sm-8 small font-weight-bold">{{ $b->alamat }}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card border-0 shadow-sm rounded-lg">
                                                    <div class="card-body">
                                                        <h6 class="text-primary font-weight-bold mb-3 border-bottom pb-2">
                                                            <i class="fas fa-tools mr-1"></i> Detail Layanan
                                                        </h6>
                                                        <div class="row mb-2">
                                                            <div class="col-sm-4 text-muted">Layanan</div>
                                                            <div class="col-sm-8 font-weight-bold"><span class="badge badge-primary">{{ $b->layanan->nama ?? '-' }}</span></div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-sm-4 text-muted">Kunjungan</div>
                                                            <div class="col-sm-8 font-weight-bold">{{ \Carbon\Carbon::parse($b->tgl_kunjungan)->format('d F Y') }}</div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-sm-4 text-muted">Merek AC</div>
                                                            <div class="col-sm-8 font-weight-bold">{{ $b->merek_ac ?? '-' }}</div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-sm-4 text-muted">Teknisi</div>
                                                            <div class="col-sm-8 font-weight-bold text-dark">{{ $b->teknisi->name ?? 'Belum ditugaskan' }}</div>
                                                        </div>
                                                        <div class="row mb-2">
                                                            <div class="col-sm-4 text-muted">Status</div>
                                                            <div class="col-sm-8">
                                                                <span class="badge badge-{{ $badgeColor }} shadow-sm">{{ ucfirst($b->status) }}</span>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <p class="mb-1 text-muted small font-weight-bold">Keluhan:</p>
                                                        <div class="p-2 bg-white border rounded small">
                                                            {{ $b->detail_keluhan ?? 'Tidak ada keluhan detail' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-5">
                                                @if($b->status === 'selesai')
                                                    {{-- Card Dokumentasi Hasil --}}
                                                    <div class="card border-0 shadow-sm rounded-lg mb-3">
                                                        <div class="card-body text-center p-3">
                                                            <h6 class="text-success font-weight-bold mb-2 border-bottom pb-2 text-left">
                                                                <i class="fas fa-camera mr-1"></i> Dokumentasi Hasil
                                                            </h6>
                                                            @if($b->foto_hasil)
                                                                <div class="d-flex align-items-center justify-content-center bg-dark rounded overflow-hidden shadow-sm" style="min-height: 150px;">
                                                                    <a href="{{ asset('uploads/ac/hasil/' . $b->foto_hasil) }}" target="_blank">
                                                                        <img src="{{ asset('uploads/ac/hasil/' . $b->foto_hasil) }}" 
                                                                             class="img-fluid rounded hover-zoom" 
                                                                             alt="Hasil Pengerjaan" 
                                                                             style="max-height: 180px; transition: transform .3s ease;">
                                                                    </a>
                                                                </div>
                                                                <p class="mt-1 text-muted small italic mb-0">Klik gambar untuk memperbesar</p>
                                                            @else
                                                                <p class="text-muted small my-3">Tidak ada dokumentasi foto</p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Card Detail Rincian Biaya --}}
                                                    <div class="card border-0 shadow-sm rounded-lg">
                                                        <div class="card-body p-3">
                                                            <h6 class="text-primary font-weight-bold mb-2 border-bottom pb-2">
                                                                <i class="fas fa-receipt mr-1"></i> Rincian Pembayaran
                                                            </h6>
                                                            @if($b->detailServis->count() > 0)
                                                                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                                                                    <table class="table table-sm table-hover border-bottom mb-2" style="font-size: 0.8rem;">
                                                                        <thead>
                                                                            <tr class="bg-light">
                                                                                <th>Item</th>
                                                                                <th class="text-center">Qty</th>
                                                                                <th class="text-right">Total</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($b->detailServis as $detail)
                                                                            <tr>
                                                                                <td class="align-middle">
                                                                                    <strong>{{ $detail->item }}</strong>
                                                                                    @if($detail->catatan)
                                                                                        <br><small class="text-muted">{{ $detail->catatan }}</small>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-center align-middle">{{ $detail->quantity }}</td>
                                                                                <td class="text-right align-middle font-weight-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                                                            </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="d-flex justify-content-between font-weight-bold p-2 bg-light rounded" style="font-size: 0.9rem;">
                                                                    <span>Total Tagihan:</span>
                                                                    <span class="text-primary">Rp {{ number_format($b->pembayaran->total_harga ?? $b->detailServis->sum('subtotal'), 0, ',', '.') }}</span>
                                                                </div>
                                                                <div class="mt-2 d-flex justify-content-between align-items-center" style="font-size: 0.85rem;">
                                                                    <span>Status Bayar:</span>
                                                                    @if(($b->pembayaran->status ?? 'belum_dibayar') === 'dibayar')
                                                                        <span class="badge badge-success px-3 py-1 rounded-pill">Lunas</span>
                                                                    @else
                                                                        <span class="badge badge-warning px-3 py-1 rounded-pill">Belum Bayar</span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <p class="text-muted small text-center my-3">Belum ada rincian biaya dari teknisi.</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="card border-0 shadow-sm rounded-lg h-100 bg-white d-flex align-items-center justify-content-center p-5">
                                                        <div class="text-center opacity-50">
                                                            <i class="fas fa-images fa-3x text-light mb-3"></i>
                                                            <p class="text-muted small">Belum ada dokumentasi & rincian pembayaran</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-4">
                                        <button class="btn btn-secondary shadow-sm px-4 rounded-pill" type="button" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Approve --}}
                        @if(in_array($b->status, ['menunggu', 'pending']))
                        <div class="modal fade" id="approveModal{{ $b->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title"><i class="fas fa-check"></i> Approve & Tugaskan Teknisi</h5>
                                        <button class="close text-white" type="button" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.ac.booking.approve', $b->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body text-left">
                                            <p><strong>Pelanggan:</strong> {{ $b->nama_pelanggan ?? ($b->user->name ?? '-') }}</p>
                                            <p><strong>Layanan:</strong> {{ $b->layanan->nama ?? '-' }}</p>
                                            <p><strong>Tgl Kunjungan:</strong> {{ \Carbon\Carbon::parse($b->tgl_kunjungan)->format('d F Y') }}</p>
                                            <hr>
                                            @if($teknisiTersedia->isEmpty())
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    Tidak ada teknisi tersedia. Booking tetap dalam antrian.
                                                </div>
                                            @else
                                                <div class="form-group">
                                                    <label><strong>Pilih Teknisi Tersedia</strong></label>
                                                    <select name="teknisi_id" class="form-control" required>
                                                        <option value="">-- Pilih Teknisi --</option>
                                                        @foreach($teknisiTersedia as $t)
                                                            <option value="{{ $t->id }}">{{ $t->name }} — {{ $t->no_hp }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                            @if(!$teknisiTersedia->isEmpty())
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check"></i> Approve & Tugaskan
                                                </button>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Modal Selesai --}}
                        @if($b->status === 'proses')
                        <div class="modal fade" id="selesaiModal{{ $b->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title"><i class="fas fa-check-double"></i> Tandai Selesai</h5>
                                        <button class="close text-white" type="button" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-left">
                                        <p>Tandai booking ini sebagai selesai?</p>
                                        <p><strong>Pelanggan:</strong> {{ $b->nama_pelanggan ?? ($b->user->name ?? '-') }}</p>
                                        <p><strong>Teknisi:</strong> {{ $b->teknisi->name ?? '-' }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin.ac.booking.selesai', $b->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-primary">Ya, Selesai</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Modal Batalkan / Tolak --}}
                        @if(in_array($b->status, ['menunggu', 'proses']))
                        <div class="modal fade" id="cancelModal{{ $b->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title"><i class="fas fa-ban"></i> {{ $b->status === 'menunggu' ? 'Tolak Booking' : 'Batalkan Booking' }}</h5>
                                        <button class="close text-white" type="button" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-left">
                                        <p>Apakah Anda yakin ingin {{ $b->status === 'menunggu' ? 'menolak' : 'membatalkan' }} booking ini?</p>
                                        <p><strong>Pelanggan:</strong> {{ $b->nama_pelanggan ?? ($b->user->name ?? '-') }}</p>
                                        <p><strong>Layanan:</strong> {{ $b->layanan->nama ?? '-' }}</p>
                                        @if($b->teknisi)
                                            <p><strong>Teknisi:</strong> {{ $b->teknisi->name }}</p>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                        <form action="{{ route('admin.ac.booking.cancel', $b->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-danger">{{ $b->status === 'menunggu' ? 'Ya, Tolak' : 'Ya, Batalkan' }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

    @endforeach

</div>
@endsection
