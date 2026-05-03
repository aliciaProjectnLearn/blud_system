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
                            </td>
                        </tr>

                        {{-- Modal Detail --}}
                        <div class="modal fade" id="detailModal{{ $b->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-info text-white">
                                        <h5 class="modal-title"><i class="fas fa-info-circle"></i> Detail Booking</h5>
                                        <button class="close text-white" type="button" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-left">
                                        <p><strong>Pelanggan:</strong> {{ $b->nama_pelanggan ?? ($b->user->name ?? '-') }}</p>
                                        <p><strong>No. HP:</strong> {{ $b->no_hp ?? ($b->user->no_hp ?? '-') }}</p>
                                        <p><strong>Layanan:</strong> {{ $b->layanan->nama ?? '-' }}</p>
                                        <p><strong>Tgl Kunjungan:</strong> {{ \Carbon\Carbon::parse($b->tgl_kunjungan)->format('d F Y') }}</p>
                                        <p><strong>Alamat:</strong> {{ $b->alamat }}</p>
                                        <p><strong>Merek AC:</strong> {{ $b->merek_ac ?? '-' }}</p>
                                        <p><strong>Keluhan:</strong> {{ $b->detail_keluhan ?? '-' }}</p>
                                        <p><strong>Teknisi:</strong> {{ $b->teknisi->name ?? '-' }}</p>
                                        <p><strong>Status:</strong>
                                            <span class="badge badge-{{ $badgeColor }}">{{ ucfirst($b->status) }}</span>
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
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

</div>
@endsection
