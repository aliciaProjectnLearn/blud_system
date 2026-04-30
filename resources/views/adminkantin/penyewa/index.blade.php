@extends('layouts.app')

@section('title', 'Manajemen Penyewa Kantin')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users mr-2 text-primary"></i>Manajemen Penyewa
        </h1>
    </div>

    {{-- Alert Success / Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Card Tabel Utama --}}
    <div class="card shadow mb-4">
        {{-- Card Header + Filter --}}
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list mr-1"></i> Daftar Penyewa Kantin / Ruko
                    </h6>
                </div>
                {{-- Filter Form --}}
                <div class="col-md-6 text-right">
                    {{-- Pencarian dihapus sesuai revisi --}}
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width:5%;">No</th>
                            <th>Nama Penyewa</th>
                            <th class="d-none d-md-table-cell">Kontak</th>
                            <th>Jenis Usaha</th>
                            <th>Unit Terakhir</th>
                            <th class="d-none d-lg-table-cell">Periode Sewa</th>
                            <th class="text-center" style="width:18%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penyewas as $i => $item)
                            @php
                                // Ambil sewa terbaru berdasarkan tgl_mulai
                                $latestSewa = $item->sewaRuko->sortByDesc('tgl_mulai')->first();
                            @endphp
                            <tr>
                                <td class="text-center">{{ $penyewas->firstItem() + $i }}</td>
                                
                                <td>
                                    <div class="font-weight-bold">{{ $item->user->nama_lengkap ?? $item->user->name ?? '-' }}</div>
                                    <small class="text-muted">NIK: {{ $item->user->nik ?? '-' }}</small>
                                </td>

                                <td class="d-none d-md-table-cell">
                                    <i class="fas fa-phone-alt fa-xs text-muted mr-1"></i>
                                    {{ $item->user->no_hp ?? '-' }}
                                </td>

                                <td>{{ $item->nama_usaha }}</td>

                                <td>
                                    @if($latestSewa)
                                        <span class="font-weight-bold text-primary">{{ $latestSewa->ruko->kode_unit ?? '-' }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <td class="d-none d-lg-table-cell">
                                    @if($latestSewa && $latestSewa->tgl_mulai && $latestSewa->tgl_selesai)
                                        <small>{{ \Carbon\Carbon::parse($latestSewa->tgl_mulai)->format('d M Y') }} - <br>
                                        {{ \Carbon\Carbon::parse($latestSewa->tgl_selesai)->format('d M Y') }}</small>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>



                                <td class="text-center">
                                    <a href="{{ route('admin.kantin.penyewa.show', $item->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fas fa-eye"></i> <span class="d-none d-md-inline"></span>
                                    </a>

                                    <a href="{{ route('admin.kantin.penyewa.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-pencil-alt"></i> <span class="d-none d-md-inline"></span>
                                    </a>

                                    @php
                                        $isRestricted = $item->sewaRuko->whereIn('status', ['disetujui', 'menunggu'])->isNotEmpty();
                                    @endphp

                                    @if($isRestricted)
                                        <button class="btn btn-danger btn-sm" disabled title="Penyewa Sedang Aktif / Menunggu">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-danger btn-sm btn-hapus" 
                                                data-id="{{ $item->id }}" 
                                                data-nama="{{ $item->user->nama_lengkap ?? $item->nama_usaha }}"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                        <form id="form-hapus-{{ $item->id }}" action="{{ route('admin.kantin.penyewa.destroy', $item->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2 d-block text-gray-400"></i>
                                    Belum ada data penyewa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($penyewas->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small">
                        Menampilkan {{ $penyewas->firstItem() }}–{{ $penyewas->lastItem() }}
                        dari {{ $penyewas->total() }} data
                    </div>
                    {{ $penyewas->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Apakah Anda yakin ingin menghapus penyewa ini?</p>
                <div class="h5 font-weight-bold text-gray-800" id="namaPenyewaTarget">-</div>
                <div class="alert alert-warning mt-3 mb-0 small">
                    <i class="fas fa-info-circle mr-1"></i> Data sewa ruko tidak bisa dihapus jika status sewanya bernilai aktif/menunggu persetujuan.
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="btnKonfirmasiHapus">
                    <i class="fas fa-trash mr-1"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let targetFormId = null;

    document.querySelectorAll('.btn-hapus').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const nama = this.dataset.nama;

            targetFormId = 'form-hapus-' + id;
            document.getElementById('namaPenyewaTarget').textContent = nama;
            $('#modalHapus').modal('show');
        });
    });

    document.getElementById('btnKonfirmasiHapus').addEventListener('click', function () {
        if (targetFormId) {
            document.getElementById(targetFormId).submit();
        }
    });
</script>
@endpush
