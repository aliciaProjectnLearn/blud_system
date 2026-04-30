@extends('layouts.app')

@section('title', 'Daftar Pelanggan')

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-2 text-gray-800">Daftar Pelanggan BLUD</h1>
        <p class="mb-4">Dengan menu ini, Super Admin dapat memantau dan mengetahui jumlah pelanggan dalam sistem.</p>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Pelanggan</h6>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-4">
                            <input type="text" name="search" class="form-control form-control-sm"
                                value="{{ $search ?? '' }}" placeholder="Cari nama pelanggan...">
                        </div>
                        <div class="col-12 col-md-3 mt-2 mt-md-0">
                            <button class="btn btn-primary btn-sm btn-block">Cari</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama</th>
                                <th class="d-none d-md-table-cell">No HP</th>
                                <th class="d-none d-lg-table-cell">Email</th>
                                <th width="12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $user->name }}
                                        <div class="d-md-none small text-muted">{{ $user->email }}</div>
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $user->no_hp ?? '-' }}</td>
                                    <td class="d-none d-lg-table-cell">{{ $user->email }}</td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info btn-sm mb-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form id="delete-form-{{ $user->id }}"
                                                action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm mb-1"
                                                    onclick="confirmDelete({{ $user->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada data pelanggan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Yakin?',
                text: "Data pelanggan akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
