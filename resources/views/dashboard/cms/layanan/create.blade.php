@extends('layouts.app')

@section('title', 'Tambah Layanan Baru')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Layanan Baru</h1>
    <a href="{{ route('admin.cms.layanan.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.cms.layanan.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nama Layanan <span class="text-danger">*</span></label>
                <input type="text" name="nama_layanan" id="nama_layanan" class="form-control" required maxlength="100" value="{{ old('nama_layanan') }}">
                <small class="form-text text-muted">Preview slug: <span id="slug-preview" class="font-weight-bold"></span></small>
            </div>

            <div class="form-group">
                <label>Deskripsi <span class="text-danger">*</span></label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required>{{ old('deskripsi') }}</textarea>
            </div>

            <div class="form-group">
                <label>Pilih Icon (FontAwesome 5) <span class="text-danger">*</span></label>
                <input type="hidden" name="icon_class" id="icon_class" value="{{ old('icon_class') }}" required>
                
                <div class="mb-2">
                    <input type="text" id="search_icon" class="form-control" placeholder="Cari icon...">
                </div>

                <div class="icon-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; max-height: 250px; overflow-y: auto; padding: 10px; border: 1px solid #e3e6f0; border-radius: 5px;">
                    <!-- Icons injected via JS -->
                </div>

                <div class="mt-3 p-3 border rounded text-center">
                    <p class="mb-2 text-muted">Preview Icon Terpilih:</p>
                    <i id="icon-preview" class="fas fa-question-circle" style="font-size: 3rem; color: #4e73df;"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="font-weight-bold">Route yang akan di-generate</label>
                <div class="alert alert-light border p-2 mb-0">
                    <code class="text-primary" id="route-preview">
                        user.[slug].index
                    </code>
                    <small class="d-block text-muted mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Route di-generate otomatis dari nama layanan
                    </small>
                </div>
            </div>

            <div class="form-group">
                <label>URL</label>
                <input type="url" name="url" id="url" class="form-control" placeholder="https://..." value="{{ old('url') }}">
                <small class="form-text text-warning font-weight-bold">Catatan: Isi jika layanan punya URL eksternal</small>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Urutan</label>
                    <input type="number" name="urutan" id="urutan" class="form-control" min="0" value="{{ old('urutan', $nextUrutan ?? 0) }}">
                </div>
                <div class="form-group col-md-6">
                    <label class="d-block">Status Aktif</label>
                    <div class="custom-control custom-switch mt-2">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">Aktif</label>
                    </div>
                </div>
            </div>

            <div class="text-right mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan & Generate
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<style>
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
<script>
    const icons = [
        'fas fa-futbol', 'fas fa-tools', 'fas fa-snowflake', 'fas fa-store', 
        'fas fa-car', 'fas fa-motorcycle', 'fas fa-graduation-cap', 'fas fa-hospital',
        'fas fa-dumbbell', 'fas fa-swimming-pool', 'fas fa-basketball-ball',
        'fas fa-book', 'fas fa-music', 'fas fa-camera', 'fas fa-coffee', 'fas fa-utensils',
        'fas fa-shopping-cart', 'fas fa-laptop', 'fas fa-desktop', 'fas fa-mobile-alt',
        'fas fa-wifi', 'fas fa-gamepad', 'fas fa-paint-brush', 'fas fa-cut', 'fas fa-tshirt',
        'fas fa-shoe-prints', 'fas fa-bicycle', 'fas fa-bus', 'fas fa-plane', 'fas fa-ship',
        'fas fa-bolt', 'fas fa-fire', 'fas fa-leaf', 'fas fa-recycle', 'fas fa-flask',
        'fas fa-stethoscope', 'fas fa-heartbeat', 'fas fa-baby', 'fas fa-dog', 'fas fa-cat'
    ];

    document.addEventListener('DOMContentLoaded', function() {
        const grid = document.querySelector('.icon-grid');
        const inputIconClass = document.getElementById('icon_class');
        const iconPreview = document.getElementById('icon-preview');
        const searchInput = document.getElementById('search_icon');

        function renderIcons(filter = '') {
            grid.innerHTML = '';
            icons.filter(icon => icon.includes(filter.toLowerCase())).forEach(icon => {
                const div = document.createElement('div');
                div.className = 'icon-box' + (inputIconClass.value === icon ? ' selected' : '');
                div.innerHTML = `
                    <i class="${icon}"></i>
                    <div class="icon-name">${icon}</div>
                `;
                div.addEventListener('click', () => {
                    document.querySelectorAll('.icon-box').forEach(b => b.classList.remove('selected'));
                    div.classList.add('selected');
                    inputIconClass.value = icon;
                    iconPreview.className = icon;
                });
                grid.appendChild(div);
            });
        }

        renderIcons();

        if(inputIconClass.value) {
            iconPreview.className = inputIconClass.value;
        }

        searchInput.addEventListener('input', function() {
            renderIcons(this.value);
        });

        // Generate Slug
        const namaLayanan = document.getElementById('nama_layanan');
        const slugPreview = document.getElementById('slug-preview');
        const routePreview = document.getElementById('route-preview');
        
        namaLayanan.addEventListener('input', function() {
            const slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
            slugPreview.textContent = slug;
            routePreview.textContent = slug ? 'user.' + slug + '.index' : 'user.[slug].index';
        });
    });
</script>
@endpush
