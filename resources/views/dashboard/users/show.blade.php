@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
    <div class="container-fluid">

        <h1 class="h3 mb-4 text-gray-800">Detail User</h1>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Pengguna</h6>
            </div>
            <div class="card-body">
                <div class="row">

                    {{-- Avatar --}}
                    <div class="col-12 col-md-3 text-center mb-4 mb-md-0">
                        <div class="border-right-md pb-3 pb-md-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4e73df&color=fff&size=128"
                                class="rounded-circle mb-3 img-fluid" style="max-width:128px;">
                            <h5 class="font-weight-bold">{{ $user->name }}</h5>
                            @foreach ($user->roles as $role)
                                <span class="badge badge-primary">{{ $role->nama }}</span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Detail --}}
                    <div class="col-12 col-md-9">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">Nama Lengkap</th>
                                    <td>{{ $user->nama_lengkap ?? $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Username</th>
                                    <td>{{ $user->username ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>No HP</th>
                                    <td>{{ $user->no_hp ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>
                                        @forelse($user->roles as $role)
                                            <span class="badge badge-primary">{{ $role->nama }}</span>
                                        @empty
                                            <span class="text-muted">-</span>
                                        @endforelse
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dibuat pada</th>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit mr-1"></i> Edit User
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
