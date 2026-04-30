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
                        <td>: {{ $pekerjaan->nama_pelanggan ?? ($pekerjaan->user->nama_lengkap ?? ($pekerjaan->user->name ?? 'Guest')) }}</td>
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
                    <form action="{{ route('teknisi.pekerjaan.selesai', $pekerjaan->id) }}" method="POST" enctype="multipart/form-data">
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
                        
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Bagian Input Layanan Tambahan -->
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-dark border-bottom pb-2"><i class="fas fa-tools"></i> Layanan & Tindakan yang Dikerjakan</h6>
                            <div id="layanan-form-container">
                                <template x-for="(lrow, index) in layananRows" :key="lrow.id">
                                    <div class="komponen-row border p-3 mb-3 rounded bg-light" style="border-left: 4px solid #36b9cc !important;">
                                        <div class="form-group mb-3 relative">
                                            <label class="text-info font-weight-bold">Pilih Layanan / Tindakan</label>
                                            <input type="hidden" :name="'layanan_id[' + index + ']'" x-model="lrow.layanan_id">
                                            <div class="input-group">
                                                <input type="text" readonly class="form-control" :class="{'bg-white cursor-pointer': !lrow.isFixed, 'bg-light text-muted': lrow.isFixed}" x-model="lrow.layanan_nama" @click="!lrow.isFixed && openLayananModal(index)" placeholder="-- Klik untuk memilih layanan --">
                                                <div class="input-group-append" x-show="!lrow.isFixed">
                                                    <button class="btn btn-outline-info" type="button" @click="openLayananModal(index)">
                                                        <i class="fas fa-search"></i> Cari
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label>Catatan Pengerjaan Layanan</label>
                                            <textarea class="form-control" :name="'layanan_catatan[' + index + ']'" rows="2" placeholder="Catatan opsional mengenai pengerjaan layanan ini" x-model="lrow.catatan"></textarea>
                                        </div>
                                        
                                        <div class="text-right button-remove-container" x-show="!lrow.isFixed">
                                            <button type="button" class="btn btn-sm btn-danger btn-remove-row" @click="removeLayananRow(index)"><i class="fas fa-trash"></i> Hapus</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button type="button" class="btn btn-outline-info btn-sm" @click="addLayananRow()">
                                <i class="fas fa-plus"></i> Tambah Layanan Lain
                            </button>
                        </div>

                        <!-- Bagian Input Sparepart -->
                        <div class="mb-4">
                            <h6 class="font-weight-bold text-dark border-bottom pb-2"><i class="fas fa-cogs"></i> Komponen / Sparepart yang Dipakai</h6>
                            <div id="dynamic-form-container">
                                <template x-for="(row, index) in rows" :key="row.id">
                                    <div class="komponen-row border p-3 mb-3 rounded bg-light" style="border-left: 4px solid #4e73df !important;">
                                        <div class="form-group mb-3 relative">
                                            <label class="text-primary font-weight-bold">Pilih Komponen/Sparepart</label>
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
                                            <label>Catatan Penggunaan Sparepart</label>
                                            <textarea class="form-control" :name="'catatan[' + index + ']'" rows="2" placeholder="Tuliskan catatan kondisi atau alasan penggantian" x-model="row.catatan"></textarea>
                                        </div>
                                        
                                        <div class="text-right button-remove-container" x-show="rows.length > 1">
                                            <button type="button" class="btn btn-sm btn-danger btn-remove-row" @click="removeRow(index)"><i class="fas fa-trash"></i> Hapus</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" @click="addRow()">
                                <i class="fas fa-plus"></i> Tambah Komponen Lain
                            </button>
                        </div>
                        
                        <div class="form-group mb-4 border-top pt-4">
                            <label class="font-weight-bold text-dark"><i class="fas fa-camera text-primary"></i> Upload Foto Dokumentasi (WAJIB)</label>
                            <input type="file" name="foto_hasil" class="form-control-file" accept="image/jpeg, image/png, image/jpg" required>
                            <small class="text-muted d-block mt-1">Maksimal 2MB. Format: JPG, JPEG, PNG. Ini wajib disertakan sebagai bukti pekerjaan.</small>
                        </div>

                        <!-- Tailwind Modal Sparepart -->
                        <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-[1050] flex items-center justify-center bg-black bg-opacity-50 transition-opacity">
                            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 flex flex-col max-h-[90vh]" @click.away="closeModal()">
                                <div class="flex justify-between items-center p-4 border-b">
                                    <h5 class="text-lg font-bold text-gray-800 m-0">Cari Sparepart</h5>
                                    <button type="button" @click="closeModal()" class="text-gray-500 hover:text-gray-700 bg-transparent border-0 text-xl font-bold p-2">&times;</button>
                                </div>
                                <div class="p-4 flex-grow overflow-hidden flex flex-col">
                                    <div class="mb-3">
                                        <input type="text" x-model="searchQuery" class="form-control" placeholder="Ketik nama komponen..." autofocus>
                                    </div>
                                    <div class="overflow-y-auto flex-grow border rounded">
                                        <div @click="selectProduk({id: '', nama_produk: '-- Tidak pakai sparepart --'})" class="p-3 border-b cursor-pointer hover:bg-gray-100 transition-colors">
                                            <span class="font-medium text-gray-700">-- Tidak pakai sparepart --</span>
                                        </div>
                                        <template x-for="produk in filteredProduks" :key="produk.id">
                                            <div @click="selectProduk(produk)" class="p-3 border-b cursor-pointer hover:bg-gray-100 transition-colors flex justify-between items-center">
                                                <div>
                                                    <span class="font-medium text-gray-800 block" x-text="produk.nama_produk"></span>
                                                    <span class="text-xs text-gray-500" x-text="'Rp ' + Number(produk.harga).toLocaleString('id-ID')"></span>
                                                </div>
                                                <span class="badge badge-info px-2 py-1" x-text="'Stok: ' + produk.stok"></span>
                                            </div>
                                        </template>
                                        <div x-show="filteredProduks.length === 0" class="p-4 text-center text-gray-500">
                                            Komponen tidak ditemukan.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tailwind Modal Layanan -->
                        <div x-show="isLayananModalOpen" style="display: none;" class="fixed inset-0 z-[1050] flex items-center justify-center bg-black bg-opacity-50 transition-opacity">
                            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 flex flex-col max-h-[90vh]" @click.away="closeLayananModal()">
                                <div class="flex justify-between items-center p-4 border-b">
                                    <h5 class="text-lg font-bold text-gray-800 m-0">Pilih Layanan</h5>
                                    <button type="button" @click="closeLayananModal()" class="text-gray-500 hover:text-gray-700 bg-transparent border-0 text-xl font-bold p-2">&times;</button>
                                </div>
                                <div class="p-4 flex-grow overflow-hidden flex flex-col">
                                    <div class="mb-3">
                                        <input type="text" x-model="searchLayananQuery" class="form-control" placeholder="Ketik nama layanan..." autofocus>
                                    </div>
                                    <div class="overflow-y-auto flex-grow border rounded">
                                        <div @click="selectLayanan({id: '', nama: '-- Tidak ada layanan tambahan --'})" class="p-3 border-b cursor-pointer hover:bg-gray-100 transition-colors">
                                            <span class="font-medium text-gray-700">-- Tidak ada layanan tambahan --</span>
                                        </div>
                                        <template x-for="lay in filteredLayanans" :key="lay.id">
                                            <div @click="selectLayanan(lay)" class="p-3 border-b cursor-pointer hover:bg-gray-100 transition-colors flex justify-between items-center">
                                                <div>
                                                    <span class="font-medium text-gray-800 block" x-text="lay.nama"></span>
                                                    <span class="text-xs text-blue-600 font-semibold" x-text="'Rp ' + Number(lay.harga_jasa).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                        </template>
                                        <div x-show="filteredLayanans.length === 0" class="p-4 text-center text-gray-500">
                                            Layanan tidak ditemukan.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mt-3 py-2 text-lg font-weight-bold" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan pekerjaan ini? Pastikan layanan dan sparepart sudah sesuai karena ini akan masuk ke tagihan.')">
                            <i class="fas fa-check-circle"></i> Selesaikan & Simpan Tagihan
                        </button>
                    </form>
                @else
                    <div class="alert alert-success mt-2">
                        <i class="fas fa-check-circle"></i> Pekerjaan ini telah selesai pada {{ $pekerjaan->updated_at->format('d M Y H:i') }}.
                    </div>
                    
                    @if($pekerjaan->detailServis->count() > 0)
                        <h6 class="mt-4 font-weight-bold">History Tindakan & Sparepart:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pekerjaan->detailServis as $detail)
                                        <tr>
                                            <td>
                                                <strong>{{ $detail->item }}</strong>
                                                @if($detail->catatan) <div class="small text-muted">{{ $detail->catatan }}</div> @endif
                                            </td>
                                            <td>{{ $detail->quantity }} {{ $detail->satuan }}</td>
                                            <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        corePlugins: {
            preflight: false,
        }
    }
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('sparepartSelect', () => ({
            produks: @json($produks),
            layanans: @json($layanans),
            
            // State for Sparepart
            rows: [{ id: 1, produk_id: '', produk_nama: '-- Tidak pakai sparepart --', qty: 1, catatan: '' }],
            nextId: 2,
            isModalOpen: false,
            activeRowIndex: null,
            searchQuery: '',
            
            // State for Layanan
            layananRows: [{ id: 1, layanan_id: '{{ $pekerjaan->layanan_id }}', layanan_nama: '{{ addslashes($pekerjaan->layanan->nama ?? "") }}', catatan: '', isFixed: true }],
            nextLayananId: 2,
            isLayananModalOpen: false,
            activeLayananRowIndex: null,
            searchLayananQuery: '',

            get filteredProduks() {
                if (this.searchQuery === '') return this.produks;
                const lower = this.searchQuery.toLowerCase();
                return this.produks.filter(p => p.nama_produk.toLowerCase().includes(lower));
            },

            get filteredLayanans() {
                // Filter out layanans that are already selected in any row
                const selectedIds = this.layananRows.map(r => r.layanan_id).filter(id => id !== '');
                
                let result = this.layanans.filter(l => !selectedIds.includes(l.id.toString()));
                
                if (this.searchLayananQuery !== '') {
                    const lower = this.searchLayananQuery.toLowerCase();
                    result = result.filter(l => l.nama && l.nama.toLowerCase().includes(lower));
                }
                
                return result;
            },

            // Methods for Sparepart
            addRow() {
                this.rows.push({ id: this.nextId++, produk_id: '', produk_nama: '-- Tidak pakai sparepart --', qty: 1, catatan: '' });
            },
            removeRow(index) {
                if (this.rows.length > 1) this.rows.splice(index, 1);
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
            },

            // Methods for Layanan
            addLayananRow() {
                this.layananRows.push({ id: this.nextLayananId++, layanan_id: '', layanan_nama: '-- Tidak ada layanan tambahan --', catatan: '', isFixed: false });
            },
            removeLayananRow(index) {
                if (!this.layananRows[index].isFixed) {
                    this.layananRows.splice(index, 1);
                }
            },
            openLayananModal(index) {
                this.activeLayananRowIndex = index;
                this.searchLayananQuery = '';
                this.isLayananModalOpen = true;
            },
            closeLayananModal() {
                this.isLayananModalOpen = false;
                this.activeLayananRowIndex = null;
            },
            selectLayanan(layanan) {
                if (this.activeLayananRowIndex !== null) {
                    this.layananRows[this.activeLayananRowIndex].layanan_id = layanan.id;
                    this.layananRows[this.activeLayananRowIndex].layanan_nama = layanan.nama;
                }
                this.closeLayananModal();
            }
        }));
    });
</script>
@endpush
