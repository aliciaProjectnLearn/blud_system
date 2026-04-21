@extends('layouts.app')

@section('title', 'Profil Admin Servis')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Profil Admin</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 mb-0">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item">Admin Servis</li>
            <li class="breadcrumb-item active" aria-current="page">Profil</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Kolom Kiri: Profil Singkat -->
    <div class="col-xl-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <img src="{{ asset('assets/img/undraw_profile.svg') }}" class="img-profile rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #f8f9fc;">
                <h4 class="font-weight-bold text-gray-800 mb-1">{{ $admin['nama'] }}</h4>
                <p class="text-primary mb-1">{{ $admin['jabatan'] }}</p>
                <p class="text-muted small mb-3">{{ $admin['unit'] }}</p>
                <hr>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    <button class="btn btn-primary btn-sm px-4 mr-2"><i class="fas fa-edit mr-1"></i> Edit Profil</button>
                    <button class="btn btn-outline-secondary btn-sm px-4"><i class="fas fa-key mr-1"></i> Ganti Password</button>
                </div>
                <div class="row text-center mt-4">
                    <div class="col-6 border-right">
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $admin['totalLogin'] }}</div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted">Total Login</div>
                    </div>
                    <div class="col-6">
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $admin['terakhirLogin'] }}</div>
                        <div class="text-xs font-weight-bold text-uppercase text-muted">Terakhir Login</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Detail & Aktivitas -->
    <div class="col-xl-8">
        <!-- Informasi Akun -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-circle mr-1"></i> Informasi Akun</h6>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="text-xs font-weight-bold text-uppercase">Nama Lengkap</label>
                            <input type="text" class="form-control-plaintext font-weight-bold text-gray-800 border-bottom" value="{{ $admin['nama'] }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="text-xs font-weight-bold text-uppercase">NIP</label>
                            <input type="text" class="form-control-plaintext font-weight-bold text-gray-800 border-bottom" value="{{ $admin['nip'] }}" readonly>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="text-xs font-weight-bold text-uppercase">Email</label>
                            <input type="email" class="form-control-plaintext font-weight-bold text-gray-800 border-bottom" value="{{ $admin['email'] }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="text-xs font-weight-bold text-uppercase">No. Telepon</label>
                            <input type="text" class="form-control-plaintext font-weight-bold text-gray-800 border-bottom" value="{{ $admin['telepon'] }}" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-weight-bold text-uppercase">Jabatan</label>
                        <input type="text" class="form-control-plaintext font-weight-bold text-gray-800 border-bottom" value="{{ $admin['jabatan'] }}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="text-xs font-weight-bold text-uppercase">Unit Kerja</label>
                        <input type="text" class="form-control-plaintext font-weight-bold text-gray-800 border-bottom" value="{{ $admin['unit'] }}" readonly>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs font-weight-bold text-uppercase">Tanggal Bergabung</label>
                        <input type="text" class="form-control-plaintext font-weight-bold text-gray-800" value="{{ $admin['tglBergabung'] }}" readonly>
                    </div>
                </form>
            </div>
        </div>

        <!-- Aktivitas Terakhir -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list mr-1"></i> Aktivitas Terakhir</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="pl-4">No</th>
                                <th>Waktu</th>
                                <th>Aktivitas</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aktivitas as $index => $item)
                            <tr>
                                <td class="pl-4">{{ $index + 1 }}</td>
                                <td>{{ $item->created_at->translatedFormat('d M Y, H:i') }}</td>
                                <td>{{ $item->aktivitas }}</td>
                                <td><code class="text-primary">-</code></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
