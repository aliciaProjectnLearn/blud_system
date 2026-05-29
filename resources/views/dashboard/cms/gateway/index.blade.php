@extends('layouts.app')

@section('title', 'Editor Gateway')

@push('styles')
<style>
    .sortable-ghost { opacity: 0.4; }
    .drag-handle { cursor: grab; }
    .drag-handle:active { cursor: grabbing; }
    .icon-box {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .icon-box:hover {
        background-color: #f8f9fc;
    }
    .icon-box.selected {
        border-color: #4e73df;
        background-color: #eaecf4;
        box-shadow: 0 0 0 0.2rem rgba(78,115,223,.25);
    }
    .icon-box i {
        font-size: 1.5rem;
        margin-bottom: 5px;
        color: #5a5c69;
    }
    .icon-box.selected i {
        color: #4e73df;
    }
    .icon-box .icon-name {
        font-size: 0.7rem;
        word-break: break-all;
    }
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
                <button type="button" class="btn btn-sm btn-primary shadow-sm" x-data @click="window.initAddKeunggulan()">
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
                <label>Pilih Icon (FontAwesome 5) <span class="text-danger">*</span></label>
                <input type="hidden" name="icon_svg" id="icon_svg" value="" required>
                
                <div class="mb-2">
                    <input type="text" id="search_icon" class="form-control" placeholder="Cari icon...">
                </div>

                <div class="icon-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; max-height: 250px; overflow-y: auto; padding: 10px; border: 1px solid #e3e6f0; border-radius: 5px;">
                    <!-- Icons injected via JS -->
                </div>

                <div class="mt-3 p-3 border rounded text-center">
                    <p class="mb-2 text-muted">Preview Icon Terpilih:</p>
                    <div id="icon-preview-container" class="d-inline-block p-3 rounded bg-light">
                        <i id="icon-preview" class="fas fa-question-circle" style="font-size: 3rem; color: #4e73df;"></i>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Urutan</label>
                    <input type="number" name="urutan" id="urutan" class="form-control" min="0" value="{{ $nextUrutan }}">
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
                
                const iconVal = this.dataset.iconsvg;
                document.getElementById('icon_svg').value = iconVal;
                document.getElementById('urutan').value = this.dataset.urutan;
                document.getElementById('is_active').checked = this.dataset.active == "1";
                
                // Highlight correct icon in grid if it exists
                document.querySelectorAll('.icon-box').forEach(box => {
                    const boxIcon = box.dataset.icon;
                    if (boxIcon === iconVal) {
                        box.classList.add('selected');
                    } else {
                        box.classList.remove('selected');
                    }
                });
                window.updateIconPreview(iconVal);
                
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'keunggulan-modal' }));
                
                setTimeout(() => {
                    const formEl = document.getElementById('keunggulan-form');
                    if (formEl) {
                        formEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 100);
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

        // Icons configuration and helpers
        const icons = [
            'fas fa-futbol', 'fas fa-tools', 'fas fa-snowflake', 'fas fa-store', 
            'fas fa-car', 'fas fa-motorcycle', 'fas fa-graduation-cap', 'fas fa-hospital',
            'fas fa-dumbbell', 'fas fa-swimming-pool', 'fas fa-basketball-ball',
            'fas fa-book', 'fas fa-music', 'fas fa-camera', 'fas fa-coffee', 'fas fa-utensils',
            'fas fa-shopping-cart', 'fas fa-laptop', 'fas fa-desktop', 'fas fa-mobile-alt',
            'fas fa-wifi', 'fas fa-gamepad', 'fas fa-paint-brush', 'fas fa-cut', 'fas fa-tshirt',
            'fas fa-shoe-prints', 'fas fa-bicycle', 'fas fa-bus', 'fas fa-plane', 'fas fa-ship',
            'fas fa-bolt', 'fas fa-fire', 'fas fa-leaf', 'fas fa-recycle', 'fas fa-flask',
            'fas fa-stethoscope', 'fas fa-heartbeat', 'fas fa-baby', 'fas fa-dog', 'fas fa-cat',
            'fas fa-user-shield', 'fas fa-lock', 'fas fa-key', 'fas fa-shield-alt',
            'fas fa-check-circle', 'fas fa-info-circle', 'fas fa-exclamation-triangle',
            'fas fa-thumbs-up', 'fas fa-heart', 'fas fa-star', 'fas fa-eye', 'fas fa-clock',
            'fas fa-map-marker-alt', 'fas fa-phone', 'fas fa-envelope', 'fas fa-globe'
        ];

        window.updateIconPreview = function(value) {
            const previewContainer = document.getElementById('icon-preview-container');
            if (!previewContainer) return;
            
            if (!value) {
                previewContainer.innerHTML = '<i id="icon-preview" class="fas fa-question-circle" style="font-size: 3rem; color: #4e73df;"></i>';
                return;
            }

            if (value.trim().startsWith('<')) {
                previewContainer.innerHTML = `<div id="icon-preview" style="width: 3rem; height: 3rem; color: #4e73df; display: flex; align-items: center; justify-content: center;">${value}</div>`;
            } else if (value.trim().match(/^[M|m|L|l|H|h|V|v|C|c|S|s|Q|q|T|t|A|a|Z|z|0-9\s,\.\-]+$/)) {
                previewContainer.innerHTML = `
                    <svg id="icon-preview" class="text-primary" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="${value}" />
                    </svg>
                `;
            } else {
                previewContainer.innerHTML = `<i id="icon-preview" class="${value}" style="font-size: 3rem; color: #4e73df;"></i>`;
            }
        };

        window.initAddKeunggulan = function() {
            const form = document.getElementById('keunggulan-form');
            if(form) {
                form.reset();
                form.action = '{{ route('admin.cms.gateway.keunggulan.store') }}';
            }
            const methodEl = document.getElementById('keunggulan-method');
            if(methodEl) methodEl.value = 'POST';
            
            const titleEl = document.getElementById('keunggulan-modal-title');
            if(titleEl) titleEl.innerText = 'Tambah Keunggulan';
            
            const iconInput = document.getElementById('icon_svg');
            if(iconInput) iconInput.value = '';
            
            const urutanInput = document.getElementById('urutan');
            if(urutanInput) urutanInput.value = '{{ $nextUrutan }}';
            
            document.querySelectorAll('.icon-box').forEach(box => box.classList.remove('selected'));
            window.updateIconPreview('');
            
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'keunggulan-modal' }));

            setTimeout(() => {
                const formEl = document.getElementById('keunggulan-form');
                if (formEl) {
                    formEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 100);
        };

        const grid = document.querySelector('.icon-grid');
        const inputIcon = document.getElementById('icon_svg');
        const searchInput = document.getElementById('search_icon');

        function renderIcons(filter = '') {
            if(!grid) return;
            grid.innerHTML = '';
            icons.filter(icon => icon.includes(filter.toLowerCase())).forEach(icon => {
                const div = document.createElement('div');
                div.className = 'icon-box' + (inputIcon && inputIcon.value === icon ? ' selected' : '');
                div.dataset.icon = icon;
                div.innerHTML = `
                    <i class="${icon}"></i>
                    <div class="icon-name">${icon.replace('fas ', '').replace('fab ', '').replace('far ', '')}</div>
                `;
                div.addEventListener('click', () => {
                    document.querySelectorAll('.icon-box').forEach(b => b.classList.remove('selected'));
                    div.classList.add('selected');
                    if(inputIcon) {
                        inputIcon.value = icon;
                    }
                    window.updateIconPreview(icon);
                });
                grid.appendChild(div);
            });
        }

        renderIcons();

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                renderIcons(this.value);
            });
        }

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
