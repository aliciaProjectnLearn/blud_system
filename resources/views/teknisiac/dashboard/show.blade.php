@extends('layouts.app')

@section('title', 'Detail Pekerjaan AC')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Detail Pekerjaan AC</h1>
    <a href="{{ route('teknisi.dashboard') }}" class="btn btn-sm btn-light shadow-sm border rounded-pill px-3">
        <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
    </a>
</div>

<div class="row" x-data="serviceManagement()">
    <!-- Informasi Detail Pekerjaan -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow-lg border-0 rounded-xl overflow-hidden h-100">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-info-circle text-info mr-2"></i> Info Pelanggan & Layanan</h6>
            </div>
            <div class="card-body pt-0">
                <div class="p-3 bg-light rounded-lg mb-4">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <th width="35%" class="text-muted small uppercase">Pelanggan</th>
                            <td class="font-weight-bold">: {{ $pekerjaan->nama_pelanggan ?? ($pekerjaan->user->nama_lengkap ?? '-') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted small uppercase">No HP</th>
                            <td class="font-weight-bold text-info">: {{ $pekerjaan->no_hp ?? ($pekerjaan->user->no_hp ?? '-') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted small uppercase">Alamat</th>
                            <td class="small">: {{ $pekerjaan->alamat }}</td>
                        </tr>
                    </table>
                </div>

                <div class="row text-center mb-4">
                    <div class="col-6">
                        <div class="p-2 border rounded-lg shadow-sm bg-white">
                            <span class="d-block text-muted small uppercase mb-1">Merk AC</span>
                            <span class="font-weight-bold text-dark">{{ $pekerjaan->merek_ac ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded-lg shadow-sm bg-white">
                            <span class="d-block text-muted small uppercase mb-1">Tgl Booking</span>
                            <span class="font-weight-bold text-dark">{{ \Carbon\Carbon::parse($pekerjaan->tgl_kunjungan)->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="text-muted small uppercase font-weight-bold"><i class="fas fa-comment-alt mr-1"></i> Keluhan Pelanggan</label>
                    <div class="p-3 border-left border-info bg-light rounded shadow-sm italic text-dark" style="background-color: #f0f7ff !important;">
                        "{{ $pekerjaan->detail_keluhan ?: 'Tidak ada detail keluhan yang dicatat.' }}"
                    </div>
                </div>

                <div class="mt-4">
                    <span class="text-muted small uppercase d-block mb-2 font-weight-bold">Layanan Utama:</span>
                    <div class="d-flex align-items-center p-3 border rounded-lg bg-white shadow-sm border-left-primary">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 font-weight-bold">{{ $pekerjaan->layanan->nama ?? '-' }}</h6>
                            <small class="text-muted">Layanan yang Di-booking</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Input Detail Servis -->
    <div class="col-lg-7 mb-4">
        <div class="card shadow-lg border-0 rounded-xl overflow-hidden h-100">
            <div class="card-header bg-gradient-primary py-3">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-clipboard-check mr-2"></i> Laporan Hasil Pengerjaan</h6>
            </div>
            <div class="card-body bg-light-soft">
                @if($pekerjaan->status !== 'selesai')
                    <form action="{{ route('teknisi.pekerjaan.selesai', $pekerjaan->id) }}" method="POST" enctype="multipart/form-data" id="serviceForm">
                        @csrf
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <!-- 1. Rincian Tindakan / Layanan -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="font-weight-bold text-dark d-flex align-items-center mb-0">
                                    <span class="badge badge-primary rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">1</span>
                                    <i class="fas fa-wrench text-primary mr-2"></i> Layanan yang Dilakukan
                                </label>
                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" @click="editingIndex = null" data-toggle="modal" data-target="#modalLayanan">
                                    <i class="fas fa-plus-circle mr-1"></i> Tambah
                                </button>
                            </div>
                            
                            <div id="layanan-container">
                                <template x-for="(row, index) in layananRows" :key="row.id">
                                    <div class="card border-0 shadow-sm mb-3 rounded-lg overflow-hidden transition-all hover-shadow">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="flex-grow-1 mr-2">
                                                    <div class="p-2 bg-white border rounded font-weight-bold text-dark d-flex justify-content-between align-items-center cursor-pointer hover-bg-light" 
                                                         @click="editLayanan(index)" title="Klik untuk mengubah layanan">
                                                        <div>
                                                            <span x-text="row.displayText"></span>
                                                            <i class="fas fa-edit ml-2 text-muted small"></i>
                                                        </div>
                                                        <input type="hidden" :name="'detail_layanan[' + index + ']'" :value="row.val">
                                                        <span class="badge badge-primary-light" x-show="row.harga" x-text="'Rp ' + Number(row.harga).toLocaleString('id-ID')"></span>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-outline-danger btn-sm border-0 rounded-circle" @click="removeLayanan(index)" x-show="!row.locked">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <span class="badge badge-light text-muted p-2" x-show="row.locked" title="Layanan Utama tidak bisa dihapus, tapi bisa diubah">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            </div>
                                            <textarea :name="'catatan_layanan[' + index + ']'" class="form-control form-control-sm bg-light-soft border-0 rounded" rows="2" placeholder="Alasan/Catatan pengerjaan..." x-model="row.catatan"></textarea>
                                            
                                            <template x-if="row.error">
                                                <div class="text-danger small mt-2 px-2 py-1 bg-danger-light rounded">
                                                    <i class="fas fa-exclamation-circle mr-1"></i> <span x-text="row.error"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- 2. Penggunaan Sparepart -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="font-weight-bold text-dark d-flex align-items-center mb-0">
                                    <span class="badge badge-info rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">2</span>
                                    <i class="fas fa-cog text-info mr-2"></i> Penggunaan Sparepart
                                </label>
                                <button type="button" class="btn btn-info btn-sm rounded-pill px-3 shadow-sm" data-toggle="modal" data-target="#modalSparepart">
                                    <i class="fas fa-plus-circle mr-1"></i> Tambah
                                </button>
                            </div>
                            
                            <div id="sparepart-container">
                                <template x-for="(row, index) in sparepartRows" :key="row.id">
                                    <div class="card border-0 shadow-sm mb-3 rounded-lg overflow-hidden transition-all hover-shadow">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="flex-grow-1 mr-2">
                                                    <div class="p-2 bg-white border rounded font-weight-bold text-dark d-flex justify-content-between align-items-center">
                                                        <span x-text="row.displayText"></span>
                                                        <input type="hidden" :name="'produk_id[' + index + ']'" :value="row.val">
                                                    </div>
                                                </div>
                                                <div class="input-group input-group-sm mr-2" style="width: 100px;">
                                                    <input type="number" :name="'quantity_produk[' + index + ']'" class="form-control border-right-0" min="1" x-model="row.qty" :max="row.maxStok">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text bg-white border-left-0 text-muted small">Qty</span>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-outline-danger btn-sm border-0 rounded-circle" @click="removeSparepart(index)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <textarea :name="'catatan_produk[' + index + ']'" class="form-control form-control-sm bg-light-soft border-0 rounded" rows="1" placeholder="Alasan penggantian..." x-model="row.catatan"></textarea>
                                            
                                            <template x-if="row.error">
                                                <div class="text-danger small mt-2 px-2 py-1 bg-danger-light rounded">
                                                    <i class="fas fa-exclamation-circle mr-1"></i> <span x-text="row.error"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- 3. Foto Dokumentasi -->
                        <div class="mb-5">
                            <label class="font-weight-bold text-dark d-flex align-items-center mb-3">
                                <span class="badge badge-warning text-white rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">3</span>
                                <i class="fas fa-camera text-warning mr-2"></i> Foto Dokumentasi Akhir
                            </label>
                            
                            <div class="upload-zone p-4 border-dashed rounded-xl bg-white text-center shadow-sm" x-data="{ hasFile: false, preview: '' }">
                                <input type="file" name="foto_hasil" id="foto_hasil" class="d-none" required accept="image/*" 
                                       @change="hasFile = true; preview = URL.createObjectURL($event.target.files[0])">
                                
                                <label for="foto_hasil" class="cursor-pointer mb-0 w-100" x-show="!hasFile">
                                    <div class="py-3">
                                        <div class="bg-warning-light text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                            <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                        </div>
                                        <h6 class="font-weight-bold text-dark mb-1">Ambil atau Upload Foto</h6>
                                        <p class="text-muted small mb-0">Klik untuk membuka kamera atau galeri</p>
                                    </div>
                                </label>

                                <div class="position-relative d-inline-block mt-2" x-show="hasFile">
                                    <img :src="preview" class="img-fluid rounded-lg shadow-md border" style="max-height: 250px;">
                                    <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute" style="top: -10px; right: -10px;" @click="hasFile = false; preview = ''; $refs.foto_hasil.value = ''">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-primary-light border-0 rounded-lg mb-4 text-primary small">
                            <i class="fas fa-info-circle mr-2"></i> <strong>Penting:</strong> Pastikan semua rincian di atas telah diinput dengan benar sebelum klik tombol selesaikan.
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 font-weight-bold shadow-lg rounded-pill py-3 btn-premium">
                            <i class="fas fa-paper-plane mr-2"></i> KIRIM LAPORAN SELESAI
                        </button>
                    </form>
                @else
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg pulse-animation" style="width: 100px; height: 100px;">
                                <i class="fas fa-check fa-3x"></i>
                            </div>
                        </div>
                        <h3 class="font-weight-bold text-dark mb-2">Pekerjaan Selesai</h3>
                        <p class="text-muted">Laporan pengerjaan telah dikirim dan diverifikasi.</p>
                    </div>
                    
                    @if($pekerjaan->foto_hasil)
                    <div class="mb-4">
                        <label class="font-weight-bold text-dark d-block mb-2 uppercase small"><i class="fas fa-image mr-1 text-info"></i> Dokumentasi Pekerjaan:</label>
                        <div class="card border-0 shadow-sm rounded-lg overflow-hidden text-center">
                            <img src="{{ asset('uploads/ac/hasil/' . $pekerjaan->foto_hasil) }}" class="img-fluid" style="max-height: 400px;">
                        </div>
                    </div>
                    @endif

                    <div class="card border-0 shadow-sm rounded-lg mb-4 bg-white">
                        <div class="card-body p-3">
                            <h6 class="font-weight-bold text-dark mb-3 border-bottom pb-2 small uppercase"><i class="fas fa-list-ul mr-1 text-primary"></i> Rincian Terlaporkan:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        @foreach($pekerjaan->detailServis as $detail)
                                        <tr class="border-bottom-0">
                                            <td class="font-weight-bold py-2 text-dark">{{ $detail->item }}</td>
                                            <td class="text-right py-2"><span class="badge badge-light border">{{ $detail->quantity }} {{ $detail->satuan }}</span></td>
                                        </tr>
                                        @if($detail->catatan)
                                        <tr>
                                            <td colspan="2" class="pt-0 pb-2"><small class="text-muted italic px-2 border-left ml-2">{{ $detail->catatan }}</small></td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if(!$pekerjaan->pembayaran || $pekerjaan->pembayaran->status !== 'dibayar')
                    <div class="mt-4">
                        <a href="{{ route('teknisi.pembayaran.form', $pekerjaan->id) }}" class="btn btn-warning btn-block btn-lg shadow rounded-pill py-3 font-weight-bold text-white btn-premium-warning">
                            <i class="fas fa-file-invoice-dollar mr-2"></i> BUAT TAGIHAN PEMBAYARAN
                        </a>
                    </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Layanan -->
    <div class="modal fade shadow-lg" id="modalLayanan" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 rounded-xl overflow-hidden">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-wrench mr-2"></i> Pilih Layanan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div class="input-group mb-3 shadow-sm rounded-pill overflow-hidden border">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" class="form-control border-0" placeholder="Cari layanan..." x-model="searchLayanan">
                    </div>
                    <div class="list-group list-group-flush rounded-lg shadow-sm overflow-auto" style="max-height: 400px;">
                        @foreach($layanans as $l)
                        <button type="button" class="list-group-item list-group-item-action py-3 border-bottom" 
                                x-show="!searchLayanan || '{{ strtolower($l->nama . ' ' . $l->kapasitas_ac) }}'.includes(searchLayanan.toLowerCase())"
                                @click="selectLayanan('{{ $l->id }}', '{{ $l->nama }} ({{ $l->kapasitas_ac }})', '{{ $l->harga_jasa }}')">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1 font-weight-bold text-dark">{{ $l->nama }}</h6>
                                <span class="badge badge-primary-light">Rp {{ number_format($l->harga_jasa, 0, ',', '.') }}</span>
                            </div>
                            <small class="text-muted d-block mt-1"><i class="fas fa-info-circle mr-1"></i> Kapasitas: {{ $l->kapasitas_ac }}</small>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Sparepart -->
    <div class="modal fade shadow-lg" id="modalSparepart" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 rounded-xl overflow-hidden">
                <div class="modal-header bg-info text-white border-0">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-cog mr-2"></i> Pilih Sparepart</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-light">
                    <div class="input-group mb-3 shadow-sm rounded-pill overflow-hidden border">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" class="form-control border-0" placeholder="Cari sparepart..." x-model="searchSparepart">
                    </div>
                    <div class="list-group list-group-flush rounded-lg shadow-sm overflow-auto" style="max-height: 400px;">
                        @foreach($produks as $p)
                        <button type="button" class="list-group-item list-group-item-action py-3 border-bottom" 
                                x-show="!searchSparepart || '{{ strtolower($p->nama_produk) }}'.includes(searchSparepart.toLowerCase())"
                                @click="selectSparepart('{{ $p->id }}', '{{ $p->nama_produk }}', '{{ $p->stok }}')">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1 font-weight-bold text-dark">{{ $p->nama_produk }}</h6>
                                <span class="badge {{ $p->stok > 0 ? 'badge-success-light' : 'badge-danger-light' }}">Stok: {{ $p->stok }}</span>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
=======
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: {
            preflight: false,
        }
    }
</script>
>>>>>>> 3886d7700505d81ff4e664390bc3a7c08460b8c6
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
<<<<<<< HEAD
        Alpine.data('serviceManagement', () => ({
            layananRows: [{ id: Date.now(), val: "{{ $pekerjaan->layanan_id }}", locked: true, catatan: "", error: "", displayText: "{{ $pekerjaan->layanan->nama }} ({{ $pekerjaan->layanan->kapasitas_ac }})", harga: "{{ $pekerjaan->layanan->harga_jasa }}" }],
            sparepartRows: [],
            searchLayanan: '',
            searchSparepart: '',
            editingIndex: null,

            editLayanan(index) {
                this.editingIndex = index;
                this.searchLayanan = '';
                $('#modalLayanan').modal('show');
            },

            selectLayanan(id, name, price) {
                // Check duplicate
                const isDuplicate = this.layananRows.some((row, i) => row.val == id && i !== this.editingIndex);
                if (isDuplicate) {
                    alert("Layanan ini sudah ada!");
                    return;
                }

                if (this.editingIndex !== null) {
                    // Update existing row
                    this.layananRows[this.editingIndex].val = id;
                    this.layananRows[this.editingIndex].displayText = name;
                    this.layananRows[this.editingIndex].harga = price;
                    this.editingIndex = null;
                } else {
                    // Add new row
                    this.layananRows.push({ 
                        id: Date.now(), 
                        val: id, 
                        locked: false, 
                        catatan: "", 
                        error: "", 
                        displayText: name, 
                        harga: price 
                    });
                }
                
                $('#modalLayanan').modal('hide');
            },

            selectSparepart(id, name, stok) {
                // Check duplicate
                const isDuplicate = this.sparepartRows.some(row => row.val == id);
                if (isDuplicate) {
                    alert("Sparepart ini sudah ditambahkan!");
                    return;
                }

                if (stok <= 0) {
                    alert("Stok sparepart ini kosong!");
                    return;
                }

                this.sparepartRows.push({ 
                    id: Date.now(), 
                    val: id, 
                    qty: 1, 
                    catatan: "", 
                    error: "", 
                    displayText: name, 
                    maxStok: stok 
                });
                
                $('#modalSparepart').modal('hide');
                this.searchSparepart = '';
            },

            removeLayanan(index) {
                this.layananRows.splice(index, 1);
            },
            
            removeSparepart(index) {
                this.sparepartRows.splice(index, 1);
            }
        }));
    });
</script>

@push('styles')
<style>
    :root {
        --primary: #4e73df;
        --success: #1cc88a;
        --info: #36b9cc;
        --warning: #f6c23e;
        --dark: #2c3e50;
    }

    .bg-gradient-primary { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
    .bg-light-soft { background-color: #f8f9fc; }
    .bg-primary-light { background-color: #eaecf4; color: #4e73df; font-weight: bold; }
    .bg-success-light { background-color: #e0f8f1; color: #1cc88a; font-weight: bold; }
    .bg-danger-light { background-color: #ffeef0; color: #e74a3b; font-weight: bold; }
    .bg-warning-light { background-color: #fdf5e6; }
    
    .rounded-xl { border-radius: 1rem !important; }
    .rounded-lg { border-radius: 0.75rem !important; }
    .shadow-lg { box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important; }
    
    .btn-premium {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        border: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(28, 200, 138, 0.4);
    }

    .btn-premium-warning {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        border: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .btn-premium-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(246, 194, 62, 0.4);
    }

    .cursor-pointer { cursor: pointer; }
    .border-dashed { border: 2px dashed #d1d3e2 !important; }
    
    .hover-shadow:hover { box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08) !important; }
    
    .pulse-animation {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(28, 200, 138, 0.7); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(28, 200, 138, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(28, 200, 138, 0); }
    }

    .uppercase { text-transform: uppercase; letter-spacing: 0.05em; }
    .italic { font-style: italic; }

    .list-group-item-action {
        transition: all 0.2s;
    }
    .list-group-item-action:hover {
        background-color: #fff;
        transform: translateX(5px);
        color: var(--primary);
        border-left: 4px solid var(--primary) !important;
    }

    .hover-bg-light:hover {
        background-color: #f8f9fc !important;
        border-color: var(--primary) !important;
        color: var(--primary) !important;
    }
</style>
@endpush
@endpush
