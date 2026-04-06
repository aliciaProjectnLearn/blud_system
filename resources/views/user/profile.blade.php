@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Profil Saya</h1>
</div>

<div class="row">
    <!-- Profil Card (Kiri) -->
    <div class="col-xl-4 col-md-5 mb-4">
        <div class="card shadow mb-4 h-100 py-4">
            <div class="card-body text-center">
                <div class="mb-4">
                    <img class="img-profile rounded-circle shadow-sm p-1"
                        src="{{ asset('img/undraw_profile.svg') }}"
                        alt="Profile Picture"
                        style="width: 150px; height: 150px; border: 3px solid #4e73df;">
                </div>
                <h4 class="font-weight-bold text-gray-800">{{ $user->name }}</h4>
                <div class="badge badge-primary px-3 py-2 mb-3">Pelanggan</div>
                <hr class="my-4">
                <div class="text-left px-3">
                    <p class="mb-1 text-xs font-weight-bold text-uppercase text-gray-500">Username</p>
                    <p class="text-gray-700 font-weight-medium">{{ $user->username }}</p>
                    <p class="mb-1 text-xs font-weight-bold text-uppercase text-gray-500">Member Sejak</p>
                    <p class="text-gray-700 font-weight-medium">{{ $user->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Details Form (Kanan) -->
    <div class="col-xl-8 col-md-7 mb-4">
        <div class="card shadow mb-4 border-left-primary h-100">
            <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Detail Akun</h6>
                <span class="small text-muted font-italic">* Wajib diisi</span>
            </div>
            <div class="card-body">
                <form action="{{ route('user.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h6 class="heading-small text-muted mb-4 font-weight-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05rem;">Informasi Dasar</h6>
                    <div class="pl-lg-2">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold" for="input-name">Nama Panggilan *</label>
                                    <input type="text" name="name" id="input-name" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Panggilan" value="{{ old('name', $user->name) }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold" for="input-nama-lengkap">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" id="input-nama-lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" placeholder="Nama Lengkap Sesuai KTP" value="{{ old('nama_lengkap', $user->nama_lengkap) }}">
                                    @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold" for="input-email">Email *</label>
                                    <input type="email" name="email" id="input-email" class="form-control @error('email') is-invalid @enderror" placeholder="example@email.com" value="{{ old('email', $user->email) }}" required>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold" for="input-no_hp">Nomor Telepon</label>
                                    <input type="text" name="no_hp" id="input-no_hp" class="form-control @error('no_hp') is-invalid @enderror" placeholder="08xxxxxxxx" value="{{ old('no_hp', $user->no_hp) }}">
                                    @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="heading-small text-muted mb-4 font-weight-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05rem;">Informasi Tambahan</h6>
                    <div class="pl-lg-2">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold" for="input-alamat">Alamat</label>
                                    <textarea name="alamat" id="input-alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" placeholder="Alamat lengkap Anda">{{ old('alamat', $user->alamat) }}</textarea>
                                    @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="heading-small text-muted mb-4 font-weight-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05rem;">Ubah Password</h6>
                    <div class="pl-lg-2">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold" for="input-username">Username *</label>
                                    <input type="text" name="username" id="input-username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold" for="input-password">Password Baru</label>
                                    <input type="password" name="password" id="input-password" class="form-control @error('password') is-invalid @enderror" placeholder="Kosongkan jika tidak ingin mengubah">
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-control-label font-weight-bold" for="input-password-confirmation">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" id="input-password-confirmation" class="form-control" placeholder="Ulangi password baru">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-right">
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
