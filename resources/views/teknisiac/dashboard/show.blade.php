@extends('layouts.app')

@section('title', 'Detail Pekerjaan Servis')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Pekerjaan AC</h1>
    <a href="{{ route('teknisi.dashboard') }}" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali</a>
</div>

<div class="row">
    <!-- Informasi Detail Pekerjaan -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow border-left-info h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-info-circle"></i> Info Pelanggan & Layanan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th width="35%">Pelanggan</th>
                        <td>: {{ $pekerjaan->user->nama_lengkap ?? $pekerjaan->user->name }}</td>
                    </tr>
                    <tr>
                        <th>No HP</th>
                        <td>: {{ $pekerjaan->user->no_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>: {{ $pekerjaan->alamat }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr class="my-1"></td>
                    </tr>
                    <tr>
                        <th>Layanan</th>
                        <td>: <span class="badge badge-primary">{{ $pekerjaan->layanan->nama ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <th>Merk AC</th>
                        <td>: {{ $pekerjaan->merek_ac ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Booking</th>
                        <td>: {{ \Carbon\Carbon::parse($pekerjaan->tgl_kunjungan)->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Keluhan</th>
                        <td>:</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="p-2 border rounded bg-light">
                                {{ $pekerjaan->detail_keluhan ?: 'Tidak ada detail keluhan yang dicatat.' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Form Input Detail Servis -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow border-left-primary h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tools"></i> Input Pengerjaan & Selesaikan</h6>
            </div>
            <div class="card-body">
                @if($pekerjaan->status !== 'selesai')
                    <form action="{{ route('teknisi.pekerjaan.selesai', $pekerjaan->id) }}" method="POST">
                        @csrf
                        
                        <div id="dynamic-form-container">
                            <div class="komponen-row border p-3 mb-3 rounded bg-light">
                                <div class="form-group mb-3">
                                    <label>Pilih Komponen/Sparepart</label>
                                    <select class="form-control" name="produk_id[]">
                                        <option value="">-- Tidak pakai sparepart --</option>
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}">{{ $produk->nama_produk }} (Stok: {{ $produk->stok }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label>Quantity</label>
                                    <input type="number" min="1" step="0.1" class="form-control" name="quantity[]" value="1" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label>Catatan Tindakan Servis</label>
                                    <textarea class="form-control" name="catatan[]" rows="2" placeholder="Tuliskan catatan kondisi atau rekomendasi" required></textarea>
                                </div>
                                
                                <div class="text-right button-remove-container" style="display: none;">
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-row"><i class="fas fa-trash"></i> Hapus Baris</button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btn-add-row">
                                <i class="fas fa-plus"></i> Tambah Komponen Lain
                            </button>
                        </div>

                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan pekerjaan ini? Stok akan dipotong jika ada komponen.')">
                            <i class="fas fa-check-circle"></i> Selesaikan Pekerjaan
                        </button>
                    </form>
                @else
                    <div class="alert alert-success mt-2">
                        <i class="fas fa-check-circle"></i> Pekerjaan ini telah selesai pada {{ $pekerjaan->updated_at->format('d M Y H:i') }}.
                    </div>
                    
                    @if($pekerjaan->detailServis->count() > 0)
                        <h6 class="mt-4 font-weight-bold">History Tindakan:</h6>
                        <ul class="list-group">
                            @foreach($pekerjaan->detailServis as $detail)
                                <li class="list-group-item">
                                    <strong>{{ $detail->item }}</strong> ({{ $detail->quantity }} {{ $detail->satuan }})<br>
                                    <small class="text-muted">Catatan: {{ $detail->catatan ?: '-' }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mt-3">Tidak ada data detail pengerjaan.</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Fungsi Clone Baris Form
        $('#btn-add-row').click(function() {
            let clonedRow = $('.komponen-row').first().clone();
            
            // Reset isi value dari clone tersebut
            clonedRow.find('select').val('');
            clonedRow.find('input[type="number"]').val('1');
            clonedRow.find('textarea').val('');
            
            // Tampilkan tombol hapus
            clonedRow.find('.button-remove-container').show();
            
            $('#dynamic-form-container').append(clonedRow);
        });

        // Fungsi Hapus Baris Form Clone
        $(document).on('click', '.btn-remove-row', function() {
            $(this).closest('.komponen-row').remove();
        });
    });
</script>
@endpush
