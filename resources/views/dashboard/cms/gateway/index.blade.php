@extends('layouts.app')

@section('title', 'Editor Gateway')

@push('styles')
<style>
    .sortable-ghost { opacity: 0.4; }
    .drag-handle { cursor: grab; }
    .drag-handle:active { cursor: grabbing; }
</style>
@endpush

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Editor Gateway</h1>
</div>

<ul class="nav nav-tabs mb-4" id="gatewayTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="konten-tab" data-toggle="tab" href="#konten" role="tab" aria-controls="konten" aria-selected="true">Konten Teks Gateway</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="keunggulan-tab" data-toggle="tab" href="#keunggulan" role="tab" aria-controls="keunggulan" aria-selected="false">Keunggulan</a>
    </li>
</ul>

<div class="tab-content" id="gatewayTabContent">
    <!-- TAB 1: KONTEN -->
    <div class="tab-pane fade show active" id="konten" role="tabpanel" aria-labelledby="konten-tab">
        <form action="{{ route('admin.cms.gateway.konten') }}" method="POST">
            @csrf
            
            <div class="text-right mb-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Semua</button>
            </div>

            @foreach(['hero' => 'Hero Section', 'layanan' => 'Section Layanan', 'keunggulan' => 'Section Keunggulan', 'footer' => 'Footer'] as $grupKey => $grupLabel)
                @if(isset($cms[$grupKey]))
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ $grupLabel }}</h6>
                    </div>
                    <div class="card-body">
                        @foreach($cms[$grupKey] as $item)
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label font-weight-bold">{{ $item->label }}</label>
                            <div class="col-sm-9">
                                @if($item->tipe == 'textarea')
                                    <textarea class="form-control" name="konten[{{ $item->key }}]" rows="3">{{ $item->value }}</textarea>
                                @else
                                    <input type="text" class="form-control" name="konten[{{ $item->key }}]" value="{{ $item->value }}">
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endforeach
            
            <div class="text-right mb-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Semua</button>
            </div>
        </form>
    </div>

    <!-- TAB 2: KEUNGGULAN -->
    <div class="tab-pane fade" id="keunggulan" role="tabpanel" aria-labelledby="keunggulan-tab">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Keunggulan</h6>
                <button type="button" class="btn btn-sm btn-primary shadow-sm" x-data @click="$dispatch('open-modal', 'keunggulan-modal'); document.getElementById('keunggulan-form').reset(); document.getElementById('keunggulan-form').action = '{{ route('admin.cms.gateway.keunggulan.store') }}'; document.getElementById('keunggulan-method').value = 'POST'; document.getElementById('keunggulan-modal-title').innerText = 'Tambah Keunggulan';">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Keunggulan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%">Urutan</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th width="10%">Status</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="keunggulan-list">
                            @forelse($keunggulan as $item)
                            <tr data-id="{{ $item->id }}">
                                <td class="text-center align-middle drag-handle">
                                    <i class="fas fa-grip-lines text-muted"></i>
                                </td>
                                <td class="align-middle">{{ $item->judul }}</td>
                                <td class="align-middle">{{ Str::limit($item->deskripsi, 50) }}</td>
                                <td class="text-center align-middle">
                                    <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-secondary' }} toggle-keunggulan-status" style="cursor:pointer;" data-id="{{ $item->id }}">
                                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-warning btn-edit-keunggulan" 
                                        data-id="{{ $item->id }}"
                                        data-judul="{{ $item->judul }}"
                                        data-deskripsi="{{ $item->deskripsi }}"
                                        data-iconsvg="{{ htmlspecialchars($item->icon_svg, ENT_QUOTES) }}"
                                        data-active="{{ $item->is_active }}"
                                        data-urutan="{{ $item->urutan }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.cms.gateway.keunggulan.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger btn-delete-keunggulan"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data keunggulan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<x-modal name="keunggulan-modal" maxWidth="lg">
    <div class="p-4" style="background-color: white;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="m-0 font-weight-bold text-primary" id="keunggulan-modal-title">Tambah Keunggulan</h5>
            <button type="button" class="close" x-data @click="$dispatch('close-modal', 'keunggulan-modal')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        
        <form id="keunggulan-form" action="{{ route('admin.cms.gateway.keunggulan.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="keunggulan-method" value="POST">
            
            <div class="form-group">
                <label>Judul <span class="text-danger">*</span></label>
                <input type="text" name="judul" id="judul" class="form-control" required maxlength="255">
            </div>

            <div class="form-group">
                <label>Deskripsi <span class="text-danger">*</span></label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label>Icon SVG Path <span class="text-danger">*</span></label>
                <textarea name="icon_svg" id="icon_svg" class="form-control" rows="2" placeholder="M13 2 3 14h9l-1 8 10-12h-9l1-8z" required></textarea>
                <small class="form-text text-muted">Ambil path dari heroicons.com</small>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Urutan</label>
                    <input type="number" name="urutan" id="urutan" class="form-control" min="0" value="0">
                </div>
                <div class="form-group col-md-6">
                    <label class="d-block">Status Aktif</label>
                    <div class="custom-control custom-switch mt-2">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" checked>
                        <label class="custom-control-label" for="is_active">Aktif</label>
                    </div>
                </div>
            </div>

            <div class="text-right mt-3">
                <button type="button" class="btn btn-secondary" x-data @click="$dispatch('close-modal', 'keunggulan-modal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</x-modal>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // SweetAlert2 Delete Confirmation untuk Keunggulan
        document.querySelectorAll('.btn-delete-keunggulan').forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('form');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Keunggulan ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // Edit Keunggulan
        document.querySelectorAll('.btn-edit-keunggulan').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const form = document.getElementById('keunggulan-form');
                
                document.getElementById('keunggulan-modal-title').innerText = 'Edit Keunggulan';
                form.action = `/admin/cms/gateway/keunggulan/${id}`;
                document.getElementById('keunggulan-method').value = 'PUT';
                
                document.getElementById('judul').value = this.dataset.judul;
                document.getElementById('deskripsi').value = this.dataset.deskripsi;
                document.getElementById('icon_svg').value = this.dataset.iconsvg;
                document.getElementById('urutan').value = this.dataset.urutan;
                document.getElementById('is_active').checked = this.dataset.active == "1";
                
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'keunggulan-modal' }));
            });
        });

        // Toggle Status Keunggulan
        document.querySelectorAll('.toggle-keunggulan-status').forEach(badge => {
            badge.addEventListener('click', function() {
                const id = this.dataset.id;
                const badgeEl = this;
                
                fetch(`/admin/cms/gateway/keunggulan/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        if(data.is_active) {
                            badgeEl.classList.remove('badge-secondary');
                            badgeEl.classList.add('badge-success');
                            badgeEl.innerText = 'Aktif';
                        } else {
                            badgeEl.classList.remove('badge-success');
                            badgeEl.classList.add('badge-secondary');
                            badgeEl.innerText = 'Nonaktif';
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });

        // Sortable JS untuk Keunggulan
        const tbody = document.getElementById('keunggulan-list');
        if(tbody) {
            new Sortable(tbody, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function () {
                    const rows = tbody.querySelectorAll('tr');
                    const urutanData = [];
                    
                    rows.forEach((row, index) => {
                        const id = row.dataset.id;
                        if(id) {
                            urutanData.push({
                                id: id,
                                urutan: index + 1
                            });
                        }
                    });

                    fetch(`{{ route('admin.cms.gateway.keunggulan.urutan') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ urutan: urutanData })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            // Optional: show a toast
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        }
    });
</script>
@endpush
