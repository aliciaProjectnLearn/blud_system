@extends('layouts.app')

@section('title', 'Detail Penyewa')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user text-primary mr-2"></i>Detail Penyewa
        </h1>
        <a href="{{ route('admin.kantin.penyewa.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <!-- Card Informasi Penyewa -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Penyewa</h6>
                    <a href="{{ route('admin.kantin.penyewa.edit', $penyewa->id) }}" class="btn btn-warning btn-sm" title="Edit Data">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4 mt-2">
                        <div class="rounded-circle bg-gray-200 d-inline-flex align-items-center justify-content-center mb-2" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-tie fa-3x text-gray-400"></i>
                        </div>
                        <h5 class="font-weight-bold mb-0">{{ $penyewa->user->nama_lengkap ?? $penyewa->user->name ?? '-' }}</h5>
                        <div class="text-xs text-muted mb-2">Penyewa Kantin / Ruko</div>
                    </div>

                    <table class="table table-sm table-borderless text-gray-800">
                        <tbody>
                            <tr>
                                <td style="width: 40%;" class="text-muted">Nama Usaha</td>
                                <td class="font-weight-bold">{{ $penyewa->nama_usaha }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kontak (HP)</td>
                                <td class="font-weight-bold">
                                    <i class="fas fa-phone-alt fa-xs mr-1 text-primary"></i>
                                    {{ $penyewa->user->no_hp ?? '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIK</td>
                                <td class="font-weight-bold">{{ $penyewa->user->nik ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Alamat</td>
                                <td class="font-weight-bold">{{ $penyewa->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Terdaftar Sejak</td>
                                <td class="font-weight-bold">{{ $penyewa->created_at->format('d M Y') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card Riwayat & Status Sewa -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Data Unit Sewa & Riwayat</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" style="width:5%;">No</th>
                                    <th>Kode Unit</th>
                                    <th>Periode Sewa</th>
                                    <th class="text-right">Biaya Tahunan</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($penyewa->sewaRuko as $i => $sewa)
                                    <tr>
                                        <td class="text-center">{{ $i + 1 }}</td>
                                        <td>
                                            <span class="font-weight-bold text-primary">{{ $sewa->ruko->kode_unit ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if($sewa->tgl_mulai && $sewa->tgl_selesai)
                                                {{ \Carbon\Carbon::parse($sewa->tgl_mulai)->format('d M Y') }} - 
                                                {{ \Carbon\Carbon::parse($sewa->tgl_selesai)->format('d M Y') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            Rp {{ number_format($sewa->total_biaya_tahunan, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusText = 'Selesai';
                                                $badgeClass = 'secondary';
                                                
                                                if($sewa->status === 'disetujui') {
                                                    $statusText = 'Aktif';
                                                    $badgeClass = 'success';
                                                } elseif(in_array($sewa->status, ['menunggu', 'pending'])) {
                                                    $statusText = 'Menunggu';
                                                    $badgeClass = 'warning';
                                                }
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }} px-3 py-1">{{ $statusText }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-door-open fa-2x mb-2 d-block text-gray-400"></i>
                                            Belum ada data penyewaan ruko untuk pengguna ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
