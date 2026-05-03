@extends('layouts.app')

@section('title', 'Detail Pekerjaan Servis')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Pekerjaan AC</h1>
    <a href="{{ route('teknisi.dashboard') }}" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali</a>
</div>

<div class="row" x-data="serviceManagement()">
    <!-- Informasi Detail Pekerjaan -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow border-left-info h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-info-circle"></i> Info Pelanggan & Layanan</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th width="35%">Pelanggan</th>
                        <td>: {{ $pekerjaan->nama_pelanggan ?? ($pekerjaan->user->nama_lengkap ?? '-') }}</td>
                    </tr>
                    <tr>
                        <th>No HP</th>
                        <td>: {{ $pekerjaan->no_hp ?? ($pekerjaan->user->no_hp ?? '-') }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>: {{ $pekerjaan->alamat }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><hr class="my-1"></td>
                    </tr>
                    <tr>
                        <th>Layanan Utama</th>
                        <td>: <span class="badge badge-primary">{{ $pekerjaan->layanan->nama ?? '-' }}</span></td>
                    </tr>
                    <tr>
                        <th>Merk AC</th>
                        <td>: {{ $pekerjaan->merek_ac ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Booking</th>
                        <td>: {{ \Carbon\Carbon::parse($pekerjaan->tgl_kunjungan)->format('d M Y') }}</td>
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
    <div class="col-lg-7 mb-4">
        <div class="card shadow border-0 h-100 overflow-hidden">
            <div class="card-header bg-primary py-3">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-tools mr-1"></i> Rincian Hasil Pengerjaan</h6>
            </div>
            <div class="card-body bg-light">
                @if($pekerjaan->status !== 'selesai')
                    <form action="{{ route('teknisi.pekerjaan.selesai', $pekerjaan->id) }}" method="POST" enctype="multipart/form-data" class="needs-validation">
                        @csrf
                        
                        <!-- 1. Rincian Tindakan / Layanan -->
                        <div class="card shadow-sm mb-4 border-left-primary">
                            <div class="card-body">
                                <label class="font-weight-bold text-primary d-flex align-items-center mb-3">
                                    <span class="badge badge-primary mr-2">1</span>
                                    <i class="fas fa-wrench mr-2"></i> Tindakan / Layanan yang Dilakukan
                                </label>
                                <div id="layanan-container">
                                    <template x-for="(row, index) in layananRows" :key="row.id">
                                        <div class="card border mb-3 shadow-sm">
                                            <div class="card-body p-3">
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-10">
                                                        <template x-if="row.locked">
                                                            <div>
                                                                <select class="form-control bg-light" disabled x-model="row.val">
                                                                    <option value="">-- Pilih Layanan --</option>
                                                                    @foreach($layanans as $l)
                                                                        <option value="{{ $l->id }}">{{ $l->nama }} ({{ $l->kapasitas_ac }}) - Rp {{ number_format($l->harga_jasa, 0, ',', '.') }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <input type="hidden" :name="'detail_layanan[' + index + ']'" :value="row.val">
                                                            </div>
                                                        </template>
                                                        <template x-if="!row.locked">
                                                            <select :name="'detail_layanan[' + index + ']'" class="form-control" required x-model="row.val" @change="checkDuplicateLayanan(index)">
                                                                <option value="">-- Pilih Layanan Tambahan --</option>
                                                                @foreach($layanans as $l)
                                                                    <option value="{{ $l->id }}">{{ $l->nama }} ({{ $l->kapasitas_ac }}) - Rp {{ number_format($l->harga_jasa, 0, ',', '.') }}</option>
                                                                @endforeach
                                                            </select>
                                                        </template>
                                                    </div>
                                                    <div class="col-2 text-right">
                                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" @click="removeLayanan(index)" x-show="!row.locked">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                        <span class="text-primary" x-show="row.locked"><i class="fas fa-lock"></i></span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <textarea :name="'catatan_layanan[' + index + ']'" class="form-control form-control-sm" rows="2" placeholder="Catatan/Alasan (Contoh: Evaporator sangat kotor, perlu pembersihan ekstra)" x-model="row.catatan"></textarea>
                                                    </div>
                                                </div>
                                                <template x-if="row.error">
                                                    <div class="text-danger small mt-1 font-weight-bold">
                                                        <i class="fas fa-exclamation-circle mr-1"></i> <span x-text="row.error"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm mt-1" @click="addLayanan()">
                                    <i class="fas fa-plus mr-1"></i> Tambah Layanan Tambahan
                                </button>
                            </div>
                        </div>

                        <!-- 2. Penggunaan Sparepart -->
                        <div class="card shadow-sm mb-4 border-left-info">
                            <div class="card-body">
                                <label class="font-weight-bold text-info d-flex align-items-center mb-3">
                                    <span class="badge badge-info mr-2">2</span>
                                    <i class="fas fa-cog mr-2"></i> Penggunaan Sparepart / Komponen
                                </label>
                                <div id="sparepart-container">
                                    <template x-for="(row, index) in sparepartRows" :key="row.id">
                                        <div class="card border mb-3 shadow-sm">
                                            <div class="card-body p-3">
                                                <button type="button" class="btn btn-link text-danger p-0 position-absolute" style="top: 10px; right: 10px;" @click="removeSparepart(index)">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                                <div class="row mb-2">
                                                    <div class="col-md-8">
                                                        <label class="small font-weight-bold text-muted mb-1">Nama Sparepart</label>
                                                        <select :name="'produk_id[' + index + ']'" class="form-control" x-model="row.val" @change="checkDuplicateSparepart(index)">
                                                            <option value="">-- Pilih sparepart --</option>
                                                            @foreach($produks as $p)
                                                                <option value="{{ $p->id }}">{{ $p->nama_produk }} (Stok: {{ $p->stok }})</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="small font-weight-bold text-muted mb-1">Jumlah</label>
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" :name="'quantity_produk[' + index + ']'" class="form-control" min="1" x-model="row.qty">
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">Qty</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <textarea :name="'catatan_produk[' + index + ']'" class="form-control form-control-sm" rows="2" placeholder="Alasan penggantian sparepart..." x-model="row.catatan"></textarea>
                                                    </div>
                                                </div>
                                                <template x-if="row.error">
                                                    <div class="text-danger small mt-1 font-weight-bold">
                                                        <i class="fas fa-exclamation-circle mr-1"></i> <span x-text="row.error"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <button type="button" class="btn btn-info btn-sm mt-1" @click="addSparepart()">
                                    <i class="fas fa-plus mr-1"></i> Tambah Sparepart
                                </button>
                            </div>
                        </div>

                        <!-- 3. Foto Dokumentasi -->
                        <div class="card shadow-sm mb-4 border-left-warning">
                            <div class="card-body">
                                <label class="font-weight-bold text-warning d-flex align-items-center mb-3">
                                    <span class="badge badge-warning mr-2">3</span>
                                    <i class="fas fa-camera mr-2"></i> Foto Dokumentasi Hasil
                                </label>
                                <div class="form-group mb-0">
                                    <input type="file" name="foto_hasil" class="form-control-file" required accept="image/*">
                                    <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Ambil foto bukti pekerjaan yang sudah selesai.</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-primary bg-primary text-white border-0 small shadow-sm mb-4">
                            <i class="fas fa-shield-alt mr-1"></i> <strong>Konfirmasi:</strong> Pastikan rincian di atas sudah sesuai dengan pekerjaan riil di lapangan.
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 font-weight-bold shadow rounded-pill py-3">
                            <i class="fas fa-check-circle mr-2"></i> SELESAIKAN PEKERJAAN
                        </button>
                    </form>
                @else
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px;">
                                <i class="fas fa-check text-white fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="font-weight-bold text-dark mb-2">Laporan Berhasil Terkirim</h4>
                        <p class="text-muted">Pekerjaan ini telah ditandai sebagai selesai.</p>
                    </div>
                    
                    @if($pekerjaan->foto_hasil)
                    <div class="mb-4 text-center">
                        <label class="font-weight-bold text-dark d-block text-left mb-2"><i class="fas fa-image mr-1"></i> Dokumentasi:</label>
                        <div class="position-relative d-inline-block">
                            <img src="{{ asset('uploads/ac/hasil/' . $pekerjaan->foto_hasil) }}" class="img-fluid rounded shadow border" style="max-height: 300px;">
                        </div>
                    </div>
                    @endif

                    <div class="card bg-light border-0 mb-4">
                        <div class="card-body">
                            <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-list-ul mr-1"></i> Rincian yang Dilaporkan:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0 bg-white rounded overflow-hidden">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th>Item / Layanan</th>
                                            <th class="text-center">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pekerjaan->detailServis as $detail)
                                        <tr>
                                            <td class="font-weight-bold">{{ $detail->item }}</td>
                                            <td class="text-center">{{ $detail->quantity }} {{ $detail->satuan }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if(!$pekerjaan->pembayaran || $pekerjaan->pembayaran->status !== 'dibayar')
                    <div class="mt-4">
                        <a href="{{ route('teknisi.pembayaran.form', $pekerjaan->id) }}" class="btn btn-warning btn-block btn-lg shadow rounded-pill py-3 font-weight-bold">
                            <i class="fas fa-money-bill-wave mr-2"></i> LANJUT KE TAGIHAN PEMBAYARAN
                        </a>
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('serviceManagement', () => ({
            layananRows: [{ id: Date.now(), val: "{{ $pekerjaan->layanan_id }}", locked: true, catatan: "", error: "" }],
            sparepartRows: [{ id: Date.now(), val: "", qty: 1, catatan: "", error: "" }],

            addLayanan() {
                this.layananRows.push({ id: Date.now(), val: "", locked: false, catatan: "", error: "" });
            },
            removeLayanan(index) {
                this.layananRows.splice(index, 1);
            },
            checkDuplicateLayanan(index) {
                const currentVal = this.layananRows[index].val;
                if (!currentVal) return;
                
                const isDuplicate = this.layananRows.some((row, i) => i !== index && row.val === currentVal);
                if (isDuplicate) {
                    this.layananRows[index].error = "Layanan ini sudah ditambahkan!";
                    this.layananRows[index].val = "";
                } else {
                    this.layananRows[index].error = "";
                }
            },

            addSparepart() {
                this.sparepartRows.push({ id: Date.now(), val: "", qty: 1, catatan: "", error: "" });
            },
            removeSparepart(index) {
                this.sparepartRows.splice(index, 1);
            },
            checkDuplicateSparepart(index) {
                const currentVal = this.sparepartRows[index].val;
                if (!currentVal) return;
                
                const isDuplicate = this.sparepartRows.some((row, i) => i !== index && row.val === currentVal);
                if (isDuplicate) {
                    this.sparepartRows[index].error = "Sparepart ini sudah ditambahkan!";
                    this.sparepartRows[index].val = "";
                } else {
                    this.sparepartRows[index].error = "";
                }
            }
        }));
    });
</script>
<style>
    .card-header.bg-primary { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
    .btn-success { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); border: none; }
    .btn-warning { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); border: none; color: #fff; }
</style>
@endpush

