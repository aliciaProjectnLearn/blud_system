@extends('layouts.app')

@section('content')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Manajemen Admin</h1>
                    <p class="mb-4">
                        Manajemen Admin meliputi penambahan, pengeditan, penghapusan, dan melihat detail informasi akun dengan role Admin
                    </p>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Admin Sistem</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="d-flex justify-content-between mb-3">
                                    <form method="GET">
                                        <input type="text" name="search" class="form-control" value="{{ $search ?? '' }}"
                                            placeholder="Cari admin...">
                                    </form>


                                    <div>
                                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                                        Tambah Admin
                                    </a>
                            </div>
                        </div>
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>No HP</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->no_hp }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>@foreach($user->roles as $role)
                                                {{ $role->nama }}
                                            @endforeach</td>
                                            <td>
                                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="{{ route('users.show',$user->id) }}"
                                                    class="btn btn-info btn-sm">
                                                    Detail
                                                </a>
                                                <form id="delete-form-{{ $user->id }}"
                                                    action="{{ route('users.destroy', $user->id) }}"
                                                    method="POST"
                                                    style="display:inline-block">

                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="confirmDelete({{ $user->id }})">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
<!-- /.container-fluid -->

@push('scripts')

<script>

function confirmDelete(id){

    Swal.fire({
    title: 'Yakin?',
    text: "Data user akan dihapus!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
    }).then((result) => {

        if(result.isConfirmed){
        document.getElementById('delete-form-'+id).submit();
        }
    })
}

</script>

<script>
$(document).ready(function() {

let table = $('#dataTable').DataTable({
pageLength: 3
})

$('#searchUser').on('keyup', function(){
table.search(this.value).draw()
})

})
</script>

@endpush
@endsection

