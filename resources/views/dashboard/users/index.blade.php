@extends('layouts.app')

@section('content')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Kelola User</h1>
                    <p class="mb-4">
                        Manajemen pengguna sistem meliputi penambahan, pengeditan, penghapusan, dan melihat detail informasi akun.
                    </p>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar User</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="d-flex justify-content-between mb-3">
                                    <input 
                                    type="text" 
                                    id="searchUser" 
                                    class="form-control w-25" 
                                    placeholder="Search user...">

                                    
                                    <div>
                                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                                        Tambah User
                                    </a>
                            </div>
                        </div>
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                        <tr>
                                            <td>{{ $users->firstItem() + $loop->index }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->role ?? '-' }}</td>
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
                                    <tr id="noDataRow" style="display:none;">
                                        <td colspan="5" class="text-center text-muted">
                                            User yang kamu cari tidak ditemukan
                                        </td>
                                    </tr>
                                </table>
                                <div class="d-flex justify-content-end mt-3">
                                    {{ $users->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<!-- /.container-fluid -->

@push('scripts')
    <script>

$(document).ready(function() {
$('#dataTable').DataTable({
"pageLength": 3
});
});

</script>

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

document.getElementById("searchUser").addEventListener("keyup", function() {

let value = this.value.toLowerCase()
let rows = document.querySelectorAll("#dataTable tbody tr:not(#noDataRow)")
let found = false

rows.forEach(row => {

let text = row.innerText.toLowerCase()

if(text.includes(value)){
row.style.display = ""
found = true
}else{
row.style.display = "none"
}

})

document.getElementById("noDataRow").style.display = found ? "none" : ""

})

</script>

@endpush
@endsection

