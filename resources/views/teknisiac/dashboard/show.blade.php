@extends('layouts.app')

@section('title', 'Detail Pekerjaan Servis')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Pekerjaan AC</h1>
    <a href="{{ route('teknisi.dashboard') }}" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali</a>
</div>

<div class="row" x-data="sparepartSelect()">
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
                            <template x-for="(row, index) in rows" :key="row.id">
                                <div class="komponen-row border p-3 mb-3 rounded bg-light">
                                    <div class="form-group mb-3 relative">
                                        <label>Pilih Komponen/Sparepart</label>
                                        <input type="hidden" :name="'produk_id[' + index + ']'" x-model="row.produk_id">
                                        <div class="input-group">
                                            <input type="text" readonly class="form-control bg-white cursor-pointer" x-model="row.produk_nama" @click="openModal(index)" placeholder="-- Klik untuk mencari komponen --">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-primary" type="button" @click="openModal(index)">
                                                    <i class="fas fa-search"></i> Cari
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Quantity</label>
                                        <input type="number" min="1" step="0.1" class="form-control" :name="'quantity[' + index + ']'" x-model="row.qty" required>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Catatan Tindakan Servis</label>
                                        <textarea class="form-control" :name="'catatan[' + index + ']'" rows="2" placeholder="Tuliskan catatan kondisi atau rekomendasi" x-model="row.catatan" required></textarea>
                                    </div>
                                    
                                    <div class="text-right button-remove-container" x-show="rows.length > 1">
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-row" @click="removeRow(index)"><i class="fas fa-trash"></i> Hapus Baris</button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="mb-4">
                            <button type="button" class="btn btn-outline-primary btn-sm" @click="addRow()">
                                <i class="fas fa-plus"></i> Tambah Komponen Lain
                            </button>
                        </div>

                        <!-- Tailwind Modal Alpine -->
                        <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-[1050] flex items-center justify-center bg-black bg-opacity-50 transition-opacity">
                            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 flex flex-col max-h-[90vh]" @click.away="closeModal()">
                                <!-- Modal Header -->
                                <div class="flex justify-between items-center p-4 border-b">
                                    <h5 class="text-lg font-bold text-gray-800 m-0">Cari Sparepart</h5>
                                    <button type="button" @click="closeModal()" class="text-gray-500 hover:text-gray-700 bg-transparent border-0 text-xl font-bold p-2">&times;</button>
                                </div>
                                
                                <!-- Modal Body -->
                                <div class="p-4 flex-grow overflow-hidden flex flex-col">
                                    <div class="mb-3">
                                        <input type="text" x-model="searchQuery" class="form-control" placeholder="Ketik nama komponen..." autofocus>
                                    </div>
                                    
                                    <div class="overflow-y-auto flex-grow border rounded">
                                        <!-- Opsi Tanpa Sparepart -->
                                        <div @click="selectProduk({id: '', nama_produk: '-- Tidak pakai sparepart --'})" class="p-3 border-b cursor-pointer hover:bg-gray-100 transition-colors">
                                            <span class="font-medium text-gray-700">-- Tidak pakai sparepart --</span>
                                        </div>
                                        
                                        <!-- Hasil Pencarian -->
                                        <template x-for="produk in filteredProduks" :key="produk.id">
                                            <div @click="selectProduk(produk)" class="p-3 border-b cursor-pointer hover:bg-gray-100 transition-colors flex justify-between items-center">
                                                <span class="font-medium text-gray-800" x-text="produk.nama_produk"></span>
                                                <span class="badge badge-info px-2 py-1" x-text="'Stok: ' + produk.stok"></span>
                                            </div>
                                        </template>
                                        
                                        <!-- State Kosong -->
                                        <div x-show="filteredProduks.length === 0" class="p-4 text-center text-gray-500">
                                            Komponen tidak ditemukan.
                                        </div>
                                    </div>
                                </div>
                            </div>
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
<!-- Scripts stack untul Tailwind & Alpine di dalam dashboard admin -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: {
            preflight: false, // Penting agar tidak merusak Bootstrap 4 SB Admin 2
        }
    }
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('sparepartSelect', () => ({
            produks: @json($produks),
            rows: [{ id: 1, produk_id: '', produk_nama: '-- Tidak pakai sparepart --', qty: 1, catatan: '' }],
            nextId: 2,
            isModalOpen: false,
            activeRowIndex: null,
            searchQuery: '',

            get filteredProduks() {
                if (this.searchQuery === '') {
                    return this.produks;
                }
                const lowerCaseQuery = this.searchQuery.toLowerCase();
                return this.produks.filter(p => p.nama_produk.toLowerCase().includes(lowerCaseQuery));
            },

            addRow() {
                this.rows.push({ id: this.nextId++, produk_id: '', produk_nama: '-- Tidak pakai sparepart --', qty: 1, catatan: '' });
            },

            removeRow(index) {
                if (this.rows.length > 1) {
                    this.rows.splice(index, 1);
                }
            },

            openModal(index) {
                this.activeRowIndex = index;
                this.searchQuery = '';
                this.isModalOpen = true;
            },

            closeModal() {
                this.isModalOpen = false;
                this.activeRowIndex = null;
            },

            selectProduk(produk) {
                if (this.activeRowIndex !== null) {
                    this.rows[this.activeRowIndex].produk_id = produk.id;
                    this.rows[this.activeRowIndex].produk_nama = produk.nama_produk;
                }
                this.closeModal();
            }
        }));
    });
</script>
@endpush
