@extends('layouts.app')

@section('title', 'Detail Unit ' . $unit->kode_unit)

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-eye mr-2 text-primary"></i>Detail Unit
            <span class="text-primary">{{ $unit->kode_unit }}</span>
        </h1>
        <div>
            <a href="{{ route('admin.kantin.unit.edit', $unit->id) }}" class="btn btn-warning btn-sm shadow-sm mr-1">
                <i class="fas fa-pencil-alt mr-1"></i> Edit
            </a>
            <a href="{{ route('admin.kantin.unit.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">

        {{-- ── Kolom Kiri: Info Unit ── --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-info-circle mr-1"></i> Informasi Unit
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                            <tr>
                                <td class="font-weight-bold text-gray-600" style="width:40%;">Kode Unit</td>
                                <td>
                                    <span class="badge badge-primary px-2 py-1" style="font-size:.9rem;">
                                        {{ $unit->kode_unit ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Kategori</td>
                                <td>{{ $unit->kategori->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Harga Sewa</td>
                                <td>
                                    @if ($unit && $unit->harga)
                                        <strong>Rp {{ number_format($unit->harga, 0, ',', '.') }}</strong>
                                        <small class="text-muted">/tahun</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Ukuran Ruko</td>
                                <td>{{ $unit->ukuran_ruko ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Deskripsi</td>
                                <td>
                                    <div class="text-muted small">
                                        {!! nl2br(e($unit->deskripsi ?? 'Tidak ada deskripsi.')) !!}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Status</td>
                                <td>
                                    @if ($unit->status_unit === 'terisi')
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-circle fa-xs mr-1"></i>Terisi
                                        </span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">
                                            <i class="fas fa-circle fa-xs mr-1"></i>Kosong
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Dibuat</td>
                                <td>{{ $unit->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Diperbarui</td>
                                <td>{{ $unit->updated_at->format('d M Y, H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── Kolom Kanan: Riwayat Sewa & Dokumentasi ── --}}
        <div class="col-lg-7 mb-4">

            {{-- Riwayat Sewa --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history mr-1"></i> Riwayat Sewa
                    </h6>
                    <span class="badge badge-primary">{{ $unit->sewaRuko->count() }} transaksi</span>
                </div>
                <div class="card-body p-0">
                    @if ($unit->sewaRuko->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Penyewa</th>
                                        <th>Mulai</th>
                                        <th>Selesai</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($unit->sewaRuko->take(5) as $sewa)
                                        <tr>
                                            <td>{{ $sewa->nama_penyewa ?? '-' }}</td>
                                            <td>{{ $sewa->tanggal_mulai_sewa ? \Carbon\Carbon::parse($sewa->tanggal_mulai_sewa)->format('d M Y') : '-' }}</td>
                                            <td>{{ $sewa->tanggal_selesai_sewa ? \Carbon\Carbon::parse($sewa->tanggal_selesai_sewa)->format('d M Y') : '-' }}</td>
                                            <td class="text-center">
                                                @php
                                                    $s = $sewa->status_sewa ?? '';
                                                    $badgeMap = [
                                                        'disetujui'  => 'success',
                                                        'menunggu'   => 'warning',
                                                        'dibatalkan' => 'danger',
                                                        'selesai'    => 'secondary',
                                                    ];
                                                    $color = $badgeMap[$s] ?? 'light';
                                                @endphp
                                                <span class="badge badge-{{ $color }}">
                                                    {{ ucfirst($s ?: '-') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($unit->sewaRuko->count() > 5)
                            <div class="text-center py-2 small text-muted">
                                +{{ $unit->sewaRuko->count() - 5 }} transaksi lainnya
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4 text-muted small">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Belum ada riwayat sewa untuk unit ini.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Dokumentasi Unit --}}
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-alt mr-1"></i> Dokumentasi Unit
                    </h6>
                    <span class="badge badge-info">{{ $unit->dokumentasiUnit->count() }} file</span>
                </div>
                <div class="card-body p-0">
                    @if ($unit->dokumentasiUnit->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0" id="tabel-dokumentasi">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Preview</th>
                                        <th>Nama Dokumen</th>
                                        <th>Tgl Upload</th>
                                        <th>Tipe</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($unit->dokumentasiUnit as $i => $dok)
                                        @php
                                            $ext     = strtolower(pathinfo($dok->file, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, ['jpg','jpeg','png']);
                                            $judul   = $dok->judul_dokumen ?: $dok->tipe;
                                        @endphp
                                        <tr id="row-dok-{{ $dok->id }}">
                                            <td class="text-muted align-middle">{{ $i + 1 }}</td>
                                            <td class="align-middle">
                                                <a href="{{ asset('storage/' . $dok->file) }}" target="_blank">
                                                    @if ($isImage)
                                                        <img src="{{ asset('storage/' . $dok->file) }}"
                                                             alt="preview"
                                                             class="img-thumbnail"
                                                             style="width:48px; height:48px; object-fit:cover;">
                                                    @else
                                                        <i class="fas fa-file-pdf text-danger fa-2x"></i>
                                                    @endif
                                                </a>
                                            </td>
                                            <td class="align-middle">
                                                <span id="judul-dok-{{ $dok->id }}" class="font-weight-bold">{{ $dok->judul_dokumen }}</span>
                                            </td>
                                            <td class="align-middle small">
                                                {{ $dok->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="align-middle">
                                                <span id="tipe-dok-{{ $dok->id }}" class="badge badge-light">{{ $dok->tipe }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span id="btn-lihat-wrapper-{{ $dok->id }}">
                                                    @if (!empty($dok->deskripsi))
                                                        <button type="button" 
                                                                class="btn btn-info btn-sm btn-lihat-deskripsi" 
                                                                data-deskripsi="{{ $dok->deskripsi }}" 
                                                                title="Lihat Deskripsi">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    @endif
                                                </span>
                                                <button type="button"
                                                        class="btn btn-warning btn-sm btn-edit-dok"
                                                        title="Edit detail dokumen"
                                                        data-id="{{ $dok->id }}"
                                                        data-judul="{{ $dok->judul_dokumen }}"
                                                        data-deskripsi="{{ $dok->deskripsi }}"
                                                        data-tipe="{{ $dok->tipe }}"
                                                        data-url="{{ route('admin.kantin.unit.dokumen.updateDetail', $dok->id) }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <a href="{{ asset('storage/' . $dok->file) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-info"
                                                   title="Lihat file">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted small">
                            <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                            Belum ada dokumentasi.
                            <a href="{{ route('admin.kantin.unit.edit', $unit->id) }}">Upload sekarang</a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- Modal Edit Detail Dokumen                                      --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditDokDetail" tabindex="-1" role="dialog" aria-labelledby="modalEditDokDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white font-weight-bold" id="modalEditDokDetailLabel">
                    <i class="fas fa-pencil-alt mr-2"></i>Edit Detail Dokumen
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-dok-id">
                <input type="hidden" id="edit-dok-url">
                
                <div class="form-group">
                    <label for="edit-judul-dokumen" class="font-weight-bold">Nama Dokumen <span class="text-danger">*</span></label>
                    <input type="text"
                           id="edit-judul-dokumen"
                           class="form-control"
                           maxlength="150"
                           placeholder="cth: Denah Unit, Kwitansi">
                </div>

                <div class="form-group">
                    <label for="edit-deskripsi-dokumen" class="font-weight-bold">Deskripsi</label>
                    <textarea id="edit-deskripsi-dokumen" 
                              class="form-control" 
                              rows="3" 
                              placeholder="Tambahkan keterangan dokumen jika perlu..."></textarea>
                </div>

                <div class="form-group mb-0">
                    <label class="font-weight-bold d-block">Tipe File</label>
                    <span id="preview-tipe-view" class="badge badge-secondary p-2"></span>
                    <small class="text-muted d-block mt-1">Tipe diatur otomatis oleh sistem berdasarkan file asli.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Batal
                </button>
                <button type="button" class="btn btn-warning" id="btn-simpan-dok-detail">
                    <i class="fas fa-save mr-1"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // ── 1. Tampilkan modal saat tombol edit diklik ───────────────────
    $(document).on('click', '.btn-edit-dok', function () {
        var id    = $(this).data('id');
        var judul = $(this).data('judul');
        var desk  = $(this).data('deskripsi');
        var tipe  = $(this).data('tipe');
        var url   = $(this).data('url');

        $('#edit-dok-id').val(id);
        $('#edit-dok-url').val(url);
        $('#edit-judul-dokumen').val(judul);
        $('#edit-deskripsi-dokumen').val(desk);
        $('#preview-tipe-view').text(tipe);

        $('#modalEditDokDetail').modal('show');
    });

    // ── 2. Submit form modal via AJAX ───────────────────────────────
    $('#btn-simpan-dok-detail').on('click', function () {
        var url   = $('#edit-dok-url').val();
        var id    = $('#edit-dok-id').val();
        var judul = $.trim($('#edit-judul-dokumen').val());
        var desk  = $.trim($('#edit-deskripsi-dokumen').val());

        if (judul === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Nama dokumen wajib diisi.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            return;
        }

        // Disable tombol saat request berlangsung
        $('#btn-simpan-dok-detail').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');

        $.ajax({
            url     : url,
            method  : 'POST',
            data    : {
                _method         : 'PATCH',
                _token          : '{{ csrf_token() }}',
                judul_dokumen   : judul,
                deskripsi       : desk
            },
            success : function (resp) {
                if (resp.success) {
                    // Update teks di tabel tanpa reload halaman
                    $('#judul-dok-' + id).text(resp.judul_dokumen);
                    
                    // Update tampilan tombol lihat deskripsi (tampil hanya jika ada deskripsi)
                    var wrapper = $('#btn-lihat-wrapper-' + id);
                    if (resp.deskripsi && resp.deskripsi !== '-') {
                        wrapper.html('<button type="button" class="btn btn-info btn-sm btn-lihat-deskripsi" data-deskripsi="' + resp.deskripsi + '" title="Lihat Deskripsi"><i class="fas fa-eye"></i></button>');
                    } else {
                        wrapper.empty();
                    }

                    // Update data attribute tombol edit agar sinkron
                    var $btn = $('.btn-edit-dok[data-id="' + id + '"]');
                    $btn.data('judul', resp.judul_dokumen);
                    $btn.data('deskripsi', resp.deskripsi !== '-' ? resp.deskripsi : null);

                    $('#modalEditDokDetail').modal('hide');

                    Swal.fire({
                        icon              : 'success',
                        title             : 'Berhasil!',
                        text              : resp.message,
                        toast             : true,
                        position          : 'top-end',
                        showConfirmButton : false,
                        timer             : 3000,
                        timerProgressBar  : true
                    });
                }
            },
            error   : function (xhr) {
                var msg = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    var firstKey = Object.keys(errors)[0];
                    msg = errors[firstKey][0];
                }

                Swal.fire({
                    icon              : 'error',
                    title             : 'Gagal!',
                    text              : msg,
                    toast             : true,
                    position          : 'top-end',
                    showConfirmButton : false,
                    timer             : 4000
                });
            },
            complete: function () {
                $('#btn-simpan-dok-detail').prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Simpan Perubahan');
            }
        });
    });

    // ── 3. Reset nilai input saat modal ditutup ──────────────────────
    $('#modalEditDokDetail').on('hidden.bs.modal', function () {
        $('#edit-judul-dokumen').val('');
        $('#edit-deskripsi-dokumen').val('');
        $('#edit-dok-id').val('');
        $('#edit-dok-url').val('');
    });

    // ── 4. Tampilkan Detail Deskripsi via Swal diklik ───────────────────
    $(document).on('click', '.btn-lihat-deskripsi', function () {
        var desk = $(this).data('deskripsi');
        
        Swal.fire({
            icon     : 'info',
            title    : 'Deskripsi Dokumen',
            html     : '<div class="text-left">' + desk.replace(/\n/g, "<br>") + '</div>',
            confirmButtonText: 'Tutup',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        });
    });

});
</script>
@endpush
