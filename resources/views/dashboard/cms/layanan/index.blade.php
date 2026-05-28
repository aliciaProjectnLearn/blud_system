@extends('layouts.app')

@section('title', 'Kelola Layanan')

@push('styles')
<style>
    .sortable-ghost { opacity: 0.4; }
    .drag-handle { cursor: grab; }
    .drag-handle:active { cursor: grabbing; }
</style>
@endpush

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kelola Layanan Gateway</h1>
    <a href="{{ route('admin.cms.layanan.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Layanan
    </a>
</div>





@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <h6 class="font-weight-bold"><i class="fas fa-exclamation-circle mr-2"></i>Terjadi kesalahan validasi:</h6>
    <ul class="mb-0 pl-4">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%">Urutan</th>
                        <th>Nama Layanan</th>
                        <th>Deskripsi</th>
                        <th width="10%">Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody id="layanan-list">
                    @forelse($layanans as $layanan)
                    <tr data-id="{{ $layanan->id }}">
                        <td class="text-center align-middle drag-handle">
                            <i class="fas fa-grip-lines text-muted"></i>
                        </td>
                        <td class="align-middle">{{ $layanan->nama_layanan }}</td>
                        <td class="align-middle">{{ Str::limit($layanan->deskripsi, 50) }}</td>
                        <td class="text-center align-middle">
                            <span class="badge {{ $layanan->is_active ? 'badge-success' : 'badge-secondary' }} toggle-status" style="cursor:pointer;" data-id="{{ $layanan->id }}">
                                {{ $layanan->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <a href="{{ route('admin.cms.layanan.edit', $layanan->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.cms.layanan.destroy', $layanan->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger btn-delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data layanan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // SweetAlert2 Delete Confirmation
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('form');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Layanan ini akan dihapus secara permanen!",
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

        // Toggle Status via AJAX
        document.querySelectorAll('.toggle-status').forEach(badge => {
            badge.addEventListener('click', function() {
                const id = this.dataset.id;
                const badgeEl = this;
                
                fetch(`/admin/cms/layanan/${id}/toggle`, {
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

        // Sortable JS untuk Drag & Drop Urutan
        const tbody = document.getElementById('layanan-list');
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

                    fetch(`{{ route('admin.cms.layanan.urutan') }}`, {
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
                            // Optional: show a toast or small notification that order saved
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        }
    });
</script>
@endpush
