@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.kantin.audit.index') }}" 
           class="btn btn-outline-secondary btn-sm mr-3 shadow-sm px-3">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
        <h5 class="font-weight-bold mb-0 text-gray-800">
            Detail Aktivitas Audit #{{ $log->id }}
        </h5>
    </div>

    <div class="row">
        {{-- Info Utama --}}
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Aktivitas</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted small" width="40%">Waktu</td>
                            <td class="small font-weight-bold">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Aksi</td>
                            <td>
                                <span class="badge badge-{{ $log->badge_color }}">
                                    {{ $log->label_aksi }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Tabel Entitas</td>
                            <td class="small text-uppercase">{{ $log->tabel_entitas }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">ID Data</td>
                            <td class="small font-weight-bold">#{{ $log->entitas_id ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Pelaku</td>
                            <td class="small font-weight-bold">
                                {{ $log->nama_pelaku ?? 'Sistem' }}
                                <span class="badge badge-light border small ml-1">{{ $log->tipe_pelaku }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted small">IP Address</td>
                            <td class="small font-weight-bold text-info">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">Keterangan</td>
                            <td class="small">{{ $log->keterangan ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- Before vs After --}}
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Perubahan Data (JSON)</h6>
                </div>
                <div class="card-body">
                    @if($log->data_lama || $log->data_baru)
                    <div class="row">
                        {{-- Sebelum --}}
                        @if($log->data_lama)
                        <div class="col-md-6">
                            <h6 class="text-danger font-weight-bold mb-3 small text-uppercase">
                                <i class="fas fa-minus-circle mr-1"></i> Sebelum Perubahan
                            </h6>
                            <div class="bg-light rounded p-3 border">
                                @foreach($log->data_lama as $key => $val)
                                <div class="mb-2 border-bottom pb-1 last-child-no-border">
                                    <small class="text-muted d-block" style="font-size: 10px">{{ $key }}</small>
                                    <span class="small font-weight-bold text-danger">
                                        {{ is_array($val) ? json_encode($val) : ($val ?? '—') }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Sesudah --}}
                        @if($log->data_baru)
                        <div class="col-md-6">
                            <h6 class="text-success font-weight-bold mb-3 small text-uppercase">
                                <i class="fas fa-plus-circle mr-1"></i> Sesudah Perubahan
                            </h6>
                            <div class="bg-light rounded p-3 border">
                                @foreach($log->data_baru as $key => $val)
                                <div class="mb-2 border-bottom pb-1 last-child-no-border">
                                    <small class="text-muted d-block" style="font-size: 10px">{{ $key }}</small>
                                    @if(is_array($val) && isset($val['dari']))
                                        {{-- Format diff --}}
                                        <div class="d-flex align-items-center flex-wrap">
                                            <span class="small text-danger text-decoration-line-through bg-white px-1 border rounded mr-1 mb-1">
                                                {{ $val['dari'] ?? '—' }}
                                            </span>
                                            <i class="fas fa-arrow-right mx-1 small text-muted mb-1"></i>
                                            <span class="small font-weight-bold text-success bg-white px-1 border rounded mb-1">
                                                {{ $val['ke'] ?? '—' }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="small font-weight-bold text-success">
                                            {{ is_array($val) ? json_encode($val) : ($val ?? '—') }}
                                        </span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <p>Tidak ada detail perubahan data yang terekam untuk aksi ini.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.last-child-no-border:last-child {
    border-bottom: none !important;
}
.text-decoration-line-through {
    text-decoration: line-through;
}
</style>
@endsection
