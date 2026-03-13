@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Detail User</h1>

    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pengguna</h6>
        </div>

        <div class="card-body">

            <div class="row">

                <!-- Avatar -->
                <div class="col-md-3 text-center border-right">

                    <img src="https://ui-avatars.com/api/?name={{ $user->name }}&background=4e73df&color=fff&size=128"
                        class="rounded-circle mb-3">

                    <h5 class="font-weight-bold">{{ $user->name }}</h5>

                    @if($user->role == 'admin')
                        <span class="badge badge-primary">Admin</span>
                    @else
                        <span class="badge badge-secondary">User</span>
                    @endif

                </div>

                <!-- Detail Data -->
                <div class="col-md-9">

                    <table class="table table-borderless">

                        <tr>
                            <th width="30%">Nama</th>
                            <td>{{ $user->name }}</td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>

                        <tr>
                            <th>Role</th>
                            <td>

                                @if($user->role == 'admin')
                                    <span class="badge badge-primary">Admin</span>
                                @else
                                    <span class="badge badge-secondary">User</span>
                                @endif

                            </td>
                        </tr>

                        <tr>
                            <th>Dibuat pada</th>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                        </tr>

                    </table>

                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>

                    <a href="{{ route('users.edit',$user->id) }}" class="btn btn-warning">
                        Edit User
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection