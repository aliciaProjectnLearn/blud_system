@extends('layouts.app')

@section('title', 'Proses Rincian Servis #' . $booking->id)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="mb-4">
        <a href="{{ route('kasir.booking.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row">
        <!-- Left Side: Detail Booking -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Data Pelanggan & Kendaraan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4 text-center">
                        <div class="h5 font-weight-bold text-gray-800 mb-0">{{ $booking->pelanggan->name ?? 'N/A' }}</div>
                        <div class="text-muted small">{{ $booking->pelanggan->email ?? '-' }}</div>
                        <div class="badge badge-primary mt-2">{{ $booking->pelanggan->no_hp ?? '-' }}</div>
                    </div>
                    <hr>
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Status Booking</div>
                            @php
                                $badgeClass = 'secondary';
                                if($booking->status == 'menunggu') $badgeClass = 'warning';
                                elseif($booking->status == 'diproses') $badgeClass = 'primary';
                                elseif($booking->status == 'selesai') $badgeClass = 'success';
                                elseif($booking->status == 'batal') $badgeClass = 'danger';
                            @endphp
                            <div class="h6 mb-0 font-weight-bold text-gray-800 text-uppercase">
                                <span class="badge badge-{{ $badgeClass }}">{{ $booking->status }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase d-block mb-1">Kendaraan</label>
                        <div class="h6 font-weight-bold text-gray-800 mb-0 text-uppercase">
                            {{ $booking->merek_kendaraan }}
                        </div>
                        <div class="badge badge-dark mt-1">{{ $booking->nomor_plat }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-muted text-uppercase d-block mb-1">Keluhan Pelanggan</label>
                        <div class="p-2 bg-light rounded small italic">"{{ $booking->keluhan ?? '-' }}"</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Status & Penugasan -->
        <div class="col-lg-6">
            <!-- Update Status Card -->
            <div class="card shadow mb-4 border-bottom-{{ $badgeClass }}">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tasks mr-2"></i> Update Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('kasir.booking.update', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="status" class="small font-weight-bold">Status Booking</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" {{ $booking->status == 'batal' ? 'disabled' : '' }}>
                                <option value="menunggu" {{ $booking->status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="diproses" {{ $booking->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                <option value="selesai" {{ $booking->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="batal" {{ $booking->status == 'batal' ? 'selected' : '' }}>Batal (Cancel)</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($booking->status != 'batal' && $booking->status != 'selesai')
                        <button type="button" class="btn btn-primary btn-block shadow-sm btn-confirm-status">
                            Simpan Perubahan
                        </button>
                        @elseif($booking->status == 'batal')
                        <div class="alert alert-danger mb-0 py-2 small">
                            <i class="fas fa-ban mr-1"></i> Booking telah dibatalkan.
                        </div>
                        @else
                        <div class="alert alert-success mb-0 py-2 small">
                            <i class="fas fa-check-circle mr-1"></i> Booking telah selesai.
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

                    <form action="{{ route('kasir.booking.assign', $booking->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="teknisi_id" class="small font-weight-bold">{{ $booking->teknisi ? 'Ubah Teknisi' : 'Pilih Teknisi' }}</label>
                            <select name="teknisi_id" id="teknisi_id" class="form-control @error('teknisi_id') is-invalid @enderror" {{ $booking->status == 'batal' ? 'disabled' : '' }}>
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

                        @if($booking->status != 'batal')
                        <button type="submit" class="btn btn-info btn-block shadow-sm">
                            <i class="fas fa-save mr-1"></i> {{ $booking->teknisi ? 'Perbarui Penugasan' : 'Tugaskan Teknisi' }}
                        </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Input Rincian & Suku Cadang -->
    <div class="row">
        <!-- Input Rincian -->
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Input Rincian Servis (Catatan Teknisi)</h6>
                    <button type="button" class="btn btn-primary btn-sm shadow-sm" id="btn-add-item">
                        <i class="fas fa-plus fa-sm"></i> Tambah Item
                    </button>
                </div>
                <div class="card-body">
                    <form action="{{ route('kasir.booking.simpan-rincian', $booking->id) }}" method="POST" id="form-rincian" enctype="multipart/form-data">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-items">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Servis / Suku Cadang</th>
                                        <th width="100">Jumlah</th>
                                        <th width="180">Harga Satuan</th>
                                        <th width="150">Subtotal</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody id="item-container">
                                    @forelse ($booking->rincianServis as $index => $rincian)
                                        <tr class="item-row">
                                            <td>
                                                <input type="text" name="items[{{ $index }}][nama_item]" value="{{ $rincian->nama_item }}" class="form-control form-control-sm" required placeholder="Contoh: Ganti Oli Shell Helix">
                                                <input type="hidden" name="items[{{ $index }}][produk_servis_id]" value="{{ $rincian->produk_servis_id }}">
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][jumlah]" value="{{ $rincian->jumlah }}" class="form-control form-control-sm input-jumlah" required min="1">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="number" name="items[{{ $index }}][harga_satuan]" value="{{ round($rincian->harga_satuan) }}" class="form-control form-control-sm input-harga" required min="0">
                                                </div>
                                            </td>
                                            <td class="align-middle text-right font-weight-bold">
                                                <span class="row-subtotal text-primary">{{ number_format($rincian->subtotal, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <button type="button" class="btn btn-danger btn-sm btn-remove"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <!-- Row placeholder will be added by JS if empty -->
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="3" class="text-right font-weight-bold align-middle py-3">Total Biaya Servis</td>
                                        <td class="text-right align-middle font-weight-bold text-primary h5 mb-0 py-3">
                                            Rp <span id="total-display">0</span>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-4 border-top pt-3">
                            <label class="font-weight-bold text-gray-800"><i class="fas fa-camera mr-2"></i>Upload Dokumentasi Foto (Opsional)</label>
                            <input type="file" name="foto_dokumentasi[]" class="form-control-file @error('foto_dokumentasi') is-invalid @enderror @error('foto_dokumentasi.*') is-invalid @enderror" accept="image/*" multiple>
                            @error('foto_dokumentasi')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('foto_dokumentasi.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">Anda bisa memilih lebih dari satu foto sekaligus. Format: JPG/PNG, Maks. 2MB per file.</small>
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="button" class="btn btn-primary px-4 shadow-sm btn-confirm-lanjut">
                                Simpan & Lanjut Pembayaran <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sparepart Suggestions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-secondary small text-uppercase">Daftar Harga Suku Cadang (Referensi)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-sm table-striped mb-0 small">
                            <thead>
                                <tr>
                                    <th class="px-3">Produk</th>
                                    <th>Stok</th>
                                    <th class="text-right px-3">Harga</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($produk as $p)
                                    <tr>
                                        <td class="px-3">{{ $p->nama_produk }}</td>
                                        <td>{{ $p->stok }}</td>
                                        <td class="text-right font-weight-bold">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                                        <td class="text-center px-3">
                                            <button type="button" class="btn btn-outline-primary btn-xs btn-add-ref" 
                                                data-id="{{ $p->id }}" 
                                                data-name="{{ $p->nama_produk }}" 
                                                data-price="{{ round($p->harga) }}">
                                                Pilih
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .italic { font-style: italic; }
    .btn-xs { padding: .1rem .3rem; font-size: .75rem; line-height: 1.5; border-radius: .2rem; }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        let itemIndex = {{ $booking->rincianServis->count() }};

        // Fungsi hitung subtotal per baris
        function calculateRow(row) {
            let jumlah = parseInt(row.find('.input-jumlah').val()) || 0;
            let harga = parseFloat(row.find('.input-harga').val()) || 0;
            let subtotal = jumlah * harga;
            row.find('.row-subtotal').text(subtotal.toLocaleString('id-ID'));
            return subtotal;
        }

        // Fungsi hitung total keseluruhan
        function calculateTotal() {
            let total = 0;
            $('.item-row').each(function() {
                total += calculateRow($(this));
            });
            $('#total-display').text(total.toLocaleString('id-ID'));
            
            // Toggle btn bayar logic if needed (but handled server side for better UX)
        }

        // Tambah baris kosong
        $('#btn-add-item').click(function() {
            let html = `
                <tr class="item-row">
                    <td>
                        <input type="text" name="items[${itemIndex}][nama_item]" class="form-control form-control-sm" required placeholder="Nama item/jasa">
                        <input type="hidden" name="items[${itemIndex}][produk_servis_id]" value="">
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][jumlah]" value="1" class="form-control form-control-sm input-jumlah" required min="1">
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" name="items[${itemIndex}][harga_satuan]" class="form-control form-control-sm input-harga" required min="0">
                        </div>
                    </td>
                    <td class="align-middle text-right font-weight-bold">
                        <span class="row-subtotal text-primary">0</span>
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-danger btn-sm btn-remove"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#item-container').append(html);
            itemIndex++;
            calculateTotal();
        });

        // Pilih dari referensi
        $('.btn-add-ref').click(function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let price = $(this).data('price');

            let html = `
                <tr class="item-row">
                    <td>
                        <input type="text" name="items[${itemIndex}][nama_item]" value="${name}" class="form-control form-control-sm" required>
                        <input type="hidden" name="items[${itemIndex}][produk_servis_id]" value="${id}">
                    </td>
                    <td>
                        <input type="number" name="items[${itemIndex}][jumlah]" value="1" class="form-control form-control-sm input-jumlah" required min="1">
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                            <input type="number" name="items[${itemIndex}][harga_satuan]" value="${price}" class="form-control form-control-sm input-harga" required min="0">
                        </div>
                    </td>
                    <td class="align-middle text-right font-weight-bold">
                        <span class="row-subtotal text-primary">${price.toLocaleString('id-ID')}</span>
                    </td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-danger btn-sm btn-remove"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            $('#item-container').append(html);
            itemIndex++;
            calculateTotal();
            
            // Optional: Scroll to bottom of table
            $('html, body').animate({ scrollTop: $('#table-items').offset().top + $('#table-items').height() }, 500);
        });

        // Hapus baris
        $(document).on('click', '.btn-remove', function() {
            $(this).closest('tr').remove();
            calculateTotal();
        });

        // Event listener untuk perubahan input
        $(document).on('input', '.input-jumlah, .input-harga', function() {
            calculateTotal();
        });

        // Hitung total awal
        calculateTotal();

        // Jika rincian kosong saat load, tambah satu baris kosong
        if ($('.item-row').length === 0) {
            $('#btn-add-item').trigger('click');
        }

        // Konfirmasi Update Status
        $('.btn-confirm-status').click(function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin mengubah status booking ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Konfirmasi Lanjut Pembayaran
        $('.btn-confirm-lanjut').click(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Lanjut ke Pembayaran?',
                text: "Rincian akan disimpan dan booking dipindah ke antrian pembayaran.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1cc88a',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Lanjut!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#form-rincian').submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
