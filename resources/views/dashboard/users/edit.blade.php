@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Edit User</h1>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('users.update', $user->id) }}" method="POST" autocomplete="off">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama_lengkap"
                           class="form-control @error('nama_lengkap') is-invalid @enderror"
                           value="{{ old('nama_lengkap', $user->nama_lengkap) }}">
                    @error('nama_lengkap')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Username <span class="text-danger">*</span></label>
                    <input type="text" name="username"
                           class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username', $user->username) }}">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>No. HP <span class="text-danger">*</span></label>
                    <input type="text" name="no_hp"
                           class="form-control @error('no_hp') is-invalid @enderror"
                           value="{{ old('no_hp', $user->no_hp) }}">
                    @error('no_hp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Password <span class="text-muted">(kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                           class="form-control" autocomplete="new-password">
                </div>

                <div class="form-group">
                    <label>Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-control @error('role') is-invalid @enderror">
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}"
                                {{ old('role', $user->roles->first()?->id) == $role->id ? 'selected' : '' }}>
                                {{ $role->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary mt-3">Update</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Kembali</a>
            </form>
        </div>
    </div>
</div>
@endsection
