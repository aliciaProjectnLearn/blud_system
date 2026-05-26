@extends('layouts.publik')
@php $hideNavbarBack = true; @endphp

@section('title', 'Detail Sewa #' . substr($sewa->access_token, 0, 8))

@section('content')
<div class="row">
    <div class="col-lg-8">
        {{-- Status Header & Stepper Gabungan --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                {{-- Baris atas: status badge + kode + tombol batal --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="m-0 font-weight-bold text-dark">Tahapan Sewa</h5>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        @if(!session('testimonial_submitted_' . $sewa->access_token))
                        <button type="button" class="btn btn-primary btn-sm rounded-pill shadow-sm px-3 d-flex align-items-center" data-toggle="modal" data-target="#modalTestimoni" id="btnBeriTestimoni" style="gap: 5px;">
                            <i class="fas fa-star text-warning"></i> Beri Testimoni
                        </button>
                        @else
                        <button type="button" class="btn btn-secondary btn-sm rounded-pill shadow-sm px-3 d-flex align-items-center" disabled style="gap: 5px;">
                            <i class="fas fa-check-circle text-white"></i> Testimoni Terkirim
                        </button>
                        @endif
                        {{-- Tombol batal hanya jika status pending --}}
                        @if($sewa->status_sewa === 'pending')
                        <button class="btn btn-outline-danger btn-sm rounded-pill px-3" data-toggle="modal" data-target="#modalBatal">
                            Batalkan
                        </button>
                        @endif
                    </div>
                </div>

                {{-- Stepper --}}
                <div class="stepper-wrapper">
                    <!-- Step 1 -->
                    <div class="stepper-item {{ in_array($sewa->status_sewa, ['ditolak', 'dibatalkan']) ? 'rejected' : ($sewa->status_sewa !== 'pending' ? 'completed' : 'active') }}">
                        <div class="step-counter">
                            @if($sewa->status_sewa === 'ditolak')
                                <i class="fas fa-times"></i>
                            @elseif($sewa->status_sewa !== 'pending')
                                <i class="fas fa-check"></i>
                            @else
                                1
                            @endif
                        </div>
                            @if($sewa->status_sewa === 'ditolak')
                                Pengajuan<br>Ditolak
                            @elseif($sewa->status_sewa === 'dibatalkan')
                                Pengajuan<br>Dibatalkan
                            @else
                                Menunggu<br>Persetujuan
                            @endif
                    </div>

                    <!-- Step 2 -->
                    <div class="stepper-item {{ in_array($sewa->status_sewa, ['ditolak', 'dibatalkan']) ? 'disabled' : (in_array($sewa->status_sewa, ['disetujui','aktif','selesai']) ? 'completed' : ($sewa->status_sewa === 'disetujui' ? 'active' : '')) }}">
                        <div class="step-counter">
                            @if(in_array($sewa->status_sewa, ['disetujui','aktif','selesai']))
                                <i class="fas fa-check"></i>
                            @else
                                2
                            @endif
                        </div>
                        <div class="step-name">Admin<br>Menyetujui</div>
                    </div>

                    <!-- Step 3 -->
                    @php
                        $termin1 = $sewa->pembayaran->where('termin_ke', 1)->first();
                    @endphp
                    <div class="stepper-item {{ in_array($sewa->status_sewa, ['ditolak', 'dibatalkan']) ? 'disabled' : ($termin1 && $termin1->status_pembayaran === 'dibayar' ? 'completed' : (in_array($sewa->status_sewa, ['aktif', 'disetujui']) ? 'active' : '')) }}">
                        <div class="step-counter">
                            @if($termin1 && $termin1->status_pembayaran === 'dibayar')
                                <i class="fas fa-check"></i>
                            @else
                                3
                            @endif
                        </div>
                        <div class="step-name">Pembayaran<br>Termin 1</div>
                    </div>

                    <!-- Step Ruko Siap Dihuni -->
                    <div class="stepper-item {{ in_array($sewa->status_sewa, ['ditolak', 'dibatalkan']) ? 'disabled' : ($termin1 && $termin1->status_pembayaran === 'dibayar' ? 'completed' : '') }}">
                        <div class="step-counter">
                            @if($termin1 && $termin1->status_pembayaran === 'dibayar')
                                <i class="fas fa-key"></i>
                            @else
                                <i class="fas fa-home text-muted"></i>
                            @endif
                        </div>
                        <div class="step-name">Ruko Siap<br>Dihuni</div>
                    </div>

                    <!-- Step 4 — hanya tampil jika 2 termin -->
                    @if($sewa->tipe_pembayaran === '2_termin')
                    @php $termin2 = $sewa->pembayaran->where('termin_ke', 2)->first(); @endphp
                    <div class="stepper-item {{ in_array($sewa->status_sewa, ['ditolak', 'dibatalkan']) ? 'disabled' : ($termin2 && $termin2->status_pembayaran === 'dibayar' ? 'completed' : ($termin1 && $termin1->status_pembayaran === 'dibayar' ? 'active' : '')) }}">
                        <div class="step-counter">
                            @if($termin2 && $termin2->status_pembayaran === 'dibayar')
                                <i class="fas fa-check"></i>
                            @else
                                4
                            @endif
                        </div>
                        <div class="step-name">Pelunasan<br>Termin 2</div>
                    </div>
                    @endif

                    <!-- Step Selesai -->
                    <div class="stepper-item {{ in_array($sewa->status_sewa, ['ditolak', 'dibatalkan']) ? 'disabled' : ($sewa->status_sewa === 'selesai' ? 'completed' : '') }}">
                        <div class="step-counter">
                            @if($sewa->status_sewa === 'selesai')
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas fa-flag"></i>
                            @endif
                        </div>
                        <div class="step-name">Sewa<br>Selesai</div>
                    </div>
                </div>

                {{-- Info teks bawah stepper --}}
                @if($sewa->status_sewa === 'ditolak')
                    <div class="alert alert-danger mt-3 mb-0 border-left-danger shadow-sm">
                        <div class="d-flex">
                            <i class="fas fa-times-circle fa-lg mt-1 mr-3"></i>
                            <div>
                                <h6 class="font-weight-bold mb-1">Pengajuan Sewa Ditolak</h6>
                                <p class="mb-2 small">Mohon maaf, pengajuan sewa Anda tidak dapat kami setujui saat ini.</p>
                                @if($sewa->catatan)
                                    <div class="bg-white p-2 rounded small text-dark border">
                                        <strong>Alasan:</strong> {{ $sewa->catatan }}
                                    </div>
                                @endif
                                <div class="mt-2">
                                    <a href="{{ route('user.kantin.katalog') }}" class="btn btn-danger btn-sm rounded-pill px-3">
                                        Cari Unit Lain
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($sewa->status_sewa === 'dibatalkan')
                    <div class="alert alert-dark mt-3 mb-0 border-left-dark shadow-sm">
                        <div class="d-flex">
                            <i class="fas fa-ban fa-lg mt-1 mr-3"></i>
                            <div>
                                <h6 class="font-weight-bold mb-1">Pengajuan Sewa Dibatalkan</h6>
                                <p class="mb-2 small">Anda telah membatalkan pengajuan sewa ini.</p>
                                <div class="mt-2">
                                    <a href="{{ route('user.kantin.katalog') }}" class="btn btn-dark btn-sm rounded-pill px-3">
                                        Cari Unit Lain
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info mt-3 mb-0 border-left-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        @if($sewa->status_sewa === 'pending')
                            Status sewamu masih menunggu persetujuan admin.
                            @if($termin1)
                                <div class="mt-2 pt-2 border-top border-info text-dark" style="font-size: 0.85rem;">
                                    <i class="fas fa-clock mr-1 text-primary"></i> 
                                    <strong>Batas Waktu Pembayaran:</strong> <br class="d-md-none">
                                    Unggah bukti bayar termin 1 paling lambat 
                                    <span class="text-danger font-weight-bold">{{ \Carbon\Carbon::parse($termin1->tgl_jatuh_tempo)->translatedFormat('d F Y, H:i') }}</span>
                                    <br>
                                    <span class="x-small text-muted font-italic">*Booking akan dibatalkan otomatis jika melewati batas waktu tersebut.</span>
                                </div>
                            @endif
                        @elseif($sewa->status_sewa === 'disetujui')
                            Pengajuan telah disetujui. Silakan lakukan pembayaran.
                            @if($termin1 && $termin1->status_pembayaran === 'pending')
                                <div class="mt-2 pt-2 border-top border-info text-dark" style="font-size: 0.85rem;">
                                    <i class="fas fa-clock mr-1 text-primary"></i> 
                                    <strong>Batas Waktu Pembayaran:</strong> <br class="d-md-none">
                                    Unggah bukti bayar termin 1 paling lambat 
                                    <span class="text-danger font-weight-bold">{{ \Carbon\Carbon::parse($termin1->tgl_jatuh_tempo)->translatedFormat('d F Y, H:i') }}</span>
                                    <br>
                                    <span class="x-small text-muted font-italic">*Booking akan dibatalkan otomatis jika melewati batas waktu tersebut.</span>
                                </div>
                            @endif
                        @elseif($sewa->status_sewa === 'aktif')
                            Sewa sedang berjalan.
                        @elseif($sewa->status_sewa === 'selesai')
                            Masa sewa telah selesai.
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Info Unit & Sewa --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Penyewaan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4 mb-3 mb-sm-0">
                        @if($sewa->ruko->foto_ruko)
                            <img src="{{ asset('storage/' . $sewa->ruko->foto_ruko) }}" class="img-fluid rounded shadow-sm" alt="{{ $sewa->ruko->nama_ruko }}">
                        @else
                            <div class="bg-light rounded p-4 text-center h-100 d-flex align-items-center justify-content-center border">
                                <i class="fas fa-store fa-3x text-gray-300"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-sm-8">
                        <h5 class="font-weight-bold text-gray-800">{{ $sewa->ruko->nama_ruko }}</h5>
                        <p class="text-muted small mb-3">{{ $sewa->ruko->kode_unit }} | {{ $sewa->ruko->ukuran_ruko }}</p>
                        
                        <div class="row small">
                            <div class="col-6 mb-2">
                                <span class="text-muted d-block">Nama Penyewa</span>
                                <span class="font-weight-bold">{{ $sewa->nama_penyewa }}</span>
                            </div>
                            <div class="col-6 mb-2">
                                <span class="text-muted d-block">Nomor HP</span>
                                <span class="font-weight-bold">{{ substr($sewa->no_hp_snapshot, 0, 4) }}****{{ substr($sewa->no_hp_snapshot, -4) }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">Mulai Sewa</span>
                                <span class="font-weight-bold text-gray-800">{{ \Carbon\Carbon::parse($sewa->tanggal_mulai_sewa)->format('d M Y') }}</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">Selesai Sewa</span>
                                <span class="font-weight-bold text-gray-800">{{ \Carbon\Carbon::parse($sewa->tanggal_selesai_sewa)->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Termin Pembayaran --}}
        <h6 class="font-weight-bold text-gray-800 mb-3 mt-4">Jadwal Pembayaran</h6>
        @foreach($sewa->pembayaran as $bayar)
        <div class="card border mb-3">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-center">
                    {{-- Info termin --}}
                    <div class="mb-3 mb-md-0">
                        <h6 class="mb-1">
                            Termin {{ $bayar->termin_ke }}
                            @if($sewa->tipe_pembayaran === '1_termin')
                                — Pembayaran Lunas
                            @else
                                ({{ $bayar->termin_ke == 1 ? '50% di awal' : '50% bulan ke-6' }})
                            @endif
                        </h6>
                        <p class="mb-1 font-weight-bold text-primary">
                            Rp {{ number_format($bayar->jumlah_tagihan, 0, ',', '.') }}
                        </p>
                        <small class="text-muted">
                            Jatuh tempo: {{ \Carbon\Carbon::parse($bayar->tgl_jatuh_tempo)->format('d M Y') }}
                        </small>
                    </div>
                    
                    {{-- Badge status & Tombol Lakukan Pembayaran --}}
                    @php
                        $bConfig = [
                            'pending'               => ['label' => 'Belum Dibayar',        'class' => 'secondary'],
                            'menunggu_verifikasi'   => ['label' => 'Menunggu Verifikasi',  'class' => 'warning'],
                            'dibayar'               => ['label' => 'Lunas',                'class' => 'success'],
                            'ditolak'               => ['label' => 'Ditolak',              'class' => 'danger'],
                        ];
                        $bc = $bConfig[$bayar->status_pembayaran] ?? ['label' => $bayar->status_pembayaran, 'class' => 'secondary'];
                    @endphp
                    
                    <div class="text-left text-md-right">
                        <span class="badge badge-{{ $bc['class'] }} mb-2 d-inline-block d-md-block">{{ $bc['label'] }}</span>

                        @if(in_array($bayar->status_pembayaran, ['pending', 'ditolak']))
                            @if(in_array($sewa->status_sewa, ['disetujui', 'aktif']))
                                {{-- AKTIF: bisa klik --}}
                                <a href="{{ route('user.kantin.sewa.pembayaran', ['token' => $sewa->access_token, 'pembayaran_id' => $bayar->id]) }}"
                                   class="btn btn-primary btn-sm btn-responsive">
                                    <i class="fas fa-credit-card mr-1"></i> {{ $bayar->status_pembayaran === 'ditolak' ? 'Upload Ulang Bukti' : 'Lakukan Pembayaran' }}
                                </a>
                            @else
                                {{-- DISABLED: belum disetujui --}}
                                <button class="btn btn-secondary btn-sm btn-responsive" disabled
                                        title="Menunggu persetujuan admin">
                                    <i class="fas fa-lock mr-1"></i> Lakukan Pembayaran
                                </button>
                                <small class="text-muted d-block mt-1 text-center text-md-right" style="font-size:10px;">
                                    Tersedia setelah disetujui
                                </small>
                            @endif
                        @elseif($bayar->status_pembayaran === 'menunggu_verifikasi')
                            <button class="btn btn-warning btn-sm btn-responsive" disabled>
                                <i class="fas fa-clock mr-1"></i> Sedang Diverifikasi
                            </button>
                        @elseif($bayar->status_pembayaran === 'dibayar')
                            <button class="btn btn-success btn-sm btn-responsive" disabled>
                                <i class="fas fa-check mr-1"></i> Lunas
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Side Action --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4 bg-primary text-white">
            <div class="card-body">
                <h6 class="font-weight-bold mb-3">Menu Akses Cepat</h6>
                <div class="list-group list-group-flush list-group-transparent">
                    {{-- Riwayat Sewa Saya removed per user request --}}
                    <a href="{{ route('user.kantin.sewa.dokumen', $sewa->access_token) }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-file-contract mr-3"></i> Dokumen & MOU
                    </a>
                    <a href="{{ route('user.kantin.katalog') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-store mr-3"></i> Sewa Unit Lainnya
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-4">
                <i class="fas fa-headset fa-3x text-gray-300 mb-3"></i>
                <h6 class="font-weight-bold text-gray-800">Butuh Bantuan?</h6>
                <p class="small text-muted mb-3">Hubungi Bendahara BLUD untuk pertanyaan seputar pembayaran.</p>
                <a href="https://wa.me/{{ config('blud.bendahara.no_hp') }}" class="btn btn-success btn-sm btn-block rounded-pill">
                    <i class="fab fa-whatsapp mr-1"></i> Chat Bendahara
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Modal Batal --}}
<div class="modal fade" id="modalBatal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">Konfirmasi Pembatalan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                <p class="mb-0">Apakah Anda yakin ingin membatalkan pengajuan sewa ini?</p>
                <small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light px-4" data-dismiss="modal">Batal</button>
                <form action="{{ route('user.kantin.sewa.batalkan', $sewa->access_token) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger px-4 shadow-sm">Ya, Batalkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Testimoni --}}
<div class="modal fade" id="modalTestimoni" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem; overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-dark">Beri Testimoni</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pt-2 pb-4 px-4">
                <p class="text-muted small mb-4">Bagaimana pengalaman Anda menggunakan layanan sewa kantin kami?</p>
                
                <form id="formTestimoni">
                    @csrf
                    {{-- Star Rating --}}
                    <div class="form-group text-center mb-4">
                        <div class="star-rating d-inline-flex flex-row-reverse justify-content-center">
                            <input type="radio" id="star5" name="rating" value="5" required />
                            <label for="star5" title="Sangat Memuaskan"><i class="fas fa-star"></i></label>
                            
                            <input type="radio" id="star4" name="rating" value="4" />
                            <label for="star4" title="Bagus"><i class="fas fa-star"></i></label>
                            
                            <input type="radio" id="star3" name="rating" value="3" />
                            <label for="star3" title="Cukup"><i class="fas fa-star"></i></label>
                            
                            <input type="radio" id="star2" name="rating" value="2" />
                            <label for="star2" title="Kurang"><i class="fas fa-star"></i></label>
                            
                            <input type="radio" id="star1" name="rating" value="1" />
                            <label for="star1" title="Buruk"><i class="fas fa-star"></i></label>
                        </div>
                        <div id="rating-label" class="text-primary font-weight-bold mt-2" style="height: 20px; font-size: 14px;"></div>
                        <div class="invalid-feedback d-block mt-1" id="error-rating" style="display: none !important;"></div>
                    </div>
                    
                    {{-- Textarea --}}
                    <div class="form-group position-relative">
                        <textarea class="form-control bg-light border-0" id="contentTestimoni" name="content" rows="4" placeholder="Bagikan pengalaman Anda menggunakan layanan kantin..." maxlength="300" style="border-radius: 1rem; padding: 1.25rem; resize: none; font-size: 14px;"></textarea>
                        <div class="text-right mt-2">
                            <small class="text-muted"><span id="charCount">0</span>/300 karakter</small>
                        </div>
                        <div class="invalid-feedback" id="error-content" style="padding-left: 0.5rem;"></div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block rounded-pill py-2 font-weight-bold mt-4 shadow-sm transition-all" id="btnSubmitTestimoni">
                        Kirim Testimoni
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .list-group-transparent .list-group-item {
        background: transparent;
        color: white;
        border-color: rgba(255,255,255,0.1);
        font-weight: 500;
        transition: all 0.2s;
    }
    .list-group-transparent .list-group-item:hover {
        background: rgba(255,255,255,0.1);
        padding-left: 1.5rem;
    }
    .border-left-primary { border-left: 0.25rem solid #4e73df !important; }
    .border-left-success { border-left: 0.25rem solid #1cc88a !important; }
    .border-left-warning { border-left: 0.25rem solid #f6c23e !important; }
    .border-left-info { border-left: 0.25rem solid #36b9cc !important; }
    .x-small { font-size: 0.75rem; }

    .stepper-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin: 30px 0;
        position: relative;
    }

    .stepper-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
    }

    /* Garis penghubung */
    .stepper-item:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 20px;
        left: 50%;
        width: 100%;
        height: 3px;
        background-color: #dee2e6;
        z-index: 0;
    }

    .stepper-item.completed:not(:last-child)::after {
        background-color: #1cc88a;
    }

    /* Lingkaran step */
    .step-counter {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #dee2e6;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        position: relative;
        z-index: 1;
        border: 3px solid #dee2e6;
    }

    .stepper-item.active .step-counter {
        background-color: #fff;
        border-color: #3d5af1;
        color: #3d5af1;
    }

    .stepper-item.completed .step-counter {
        background-color: #1cc88a;
        border-color: #1cc88a;
        color: #fff;
    }

    /* Label */
    .step-name {
        margin-top: 8px;
        font-size: 11px;
        text-align: center;
        color: #6c757d;
        line-height: 1.3;
    }

    .stepper-item.active .step-name {
        color: #3d5af1;
        font-weight: 600;
    }

    .stepper-item.completed .step-name {
        color: #1cc88a;
        font-weight: 600;
    }

    /* Rejected State */
    .stepper-item.rejected .step-counter {
        background-color: #e74a3b;
        border-color: #e74a3b;
        color: #fff;
    }

    .stepper-item.rejected .step-name {
        color: #e74a3b;
        font-weight: 600;
    }

    /* Disabled State (setelah ditolak) */
    .stepper-item.disabled {
        opacity: 0.5;
    }

    .stepper-item.disabled .step-counter {
        background-color: #f8f9fc;
        border-color: #eaecf4;
        color: #d1d3e2;
    }

    .stepper-item.disabled .step-name {
        color: #d1d3e2;
    }

    /* Responsive — di mobile scroll horizontal */
    @media (max-width: 576px) {
        .stepper-wrapper {
            overflow-x: auto;
            padding-bottom: 10px;
        }
        .stepper-item {
            min-width: 80px;
        }
    }

    @media (max-width: 767.98px) {
        .btn-responsive {
            display: block;
            width: 100%;
        }
    }
    
    /* Modal Testimoni Modern */
    #modalTestimoni .modal-content {
        background-color: #ffffff;
    }
    .star-rating {
        position: relative;
    }
    .star-rating input {
        display: none;
    }
    .star-rating label {
        color: #e4e5e9;
        font-size: 2.75rem;
        padding: 0 0.3rem;
        cursor: pointer;
        transition: color 0.2s ease, transform 0.2s ease;
    }
    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: #ffc107;
    }
    .star-rating label:hover {
        transform: scale(1.15);
    }
    .star-rating input:checked + label {
        animation: pop 0.3s ease;
    }
    @keyframes pop {
        50% { transform: scale(1.25); }
    }
    #contentTestimoni:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.2);
        border: 1px solid #4e73df;
        outline: none;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const starLabels = {
        '1': 'Buruk',
        '2': 'Kurang',
        '3': 'Cukup',
        '4': 'Bagus',
        '5': 'Sangat Memuaskan'
    };

    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const ratingLabel = document.getElementById('rating-label');
    
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            ratingLabel.textContent = starLabels[this.value];
            document.getElementById('error-rating').style.setProperty('display', 'none', 'important');
        });
        
        const label = input.nextElementSibling;
        label.addEventListener('mouseenter', function() {
            ratingLabel.textContent = starLabels[input.value];
        });
        label.addEventListener('mouseleave', function() {
            const checked = document.querySelector('input[name="rating"]:checked');
            ratingLabel.textContent = checked ? starLabels[checked.value] : '';
        });
    });

    const contentArea = document.getElementById('contentTestimoni');
    const charCount = document.getElementById('charCount');
    
    contentArea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
        this.classList.remove('is-invalid');
        document.getElementById('error-content').textContent = '';
    });

    const formTestimoni = document.getElementById('formTestimoni');
    if (formTestimoni) {
        formTestimoni.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const rating = document.querySelector('input[name="rating"]:checked');
            const content = contentArea.value.trim();
            let isValid = true;
            
            if (!rating) {
                document.getElementById('error-rating').textContent = 'Pilih rating bintang terlebih dahulu.';
                document.getElementById('error-rating').style.setProperty('display', 'block', 'important');
                isValid = false;
            }
            
            if (content.length < 10) {
                contentArea.classList.add('is-invalid');
                document.getElementById('error-content').textContent = 'Testimoni minimal 10 karakter.';
                isValid = false;
            }
            
            if (!isValid) return;
            
            const btnSubmit = document.getElementById('btnSubmitTestimoni');
            const originalText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Mengirim...';
            btnSubmit.disabled = true;
            
            fetch("{{ route('user.kantin.sewa.testimoni', $sewa->access_token) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    rating: rating.value,
                    content: content
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#modalTestimoni').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message || 'Terjadi kesalahan.'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan sistem.'
                });
            })
            .finally(() => {
                btnSubmit.innerHTML = originalText;
                btnSubmit.disabled = false;
            });
        });
    }
});
</script>
@endpush
