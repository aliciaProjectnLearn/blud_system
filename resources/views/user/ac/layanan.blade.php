@extends('layouts.app')

@section('title', 'Layanan Servis AC')

@push('styles')
<style>
    /* Fix Layout Wrapper */
    .ac-page-container { width: 100%; padding: 0 5px; }

    /* Hero Section */
    .hero-ac {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 60%, #1a3a9c 100%);
        border-radius: 16px;
        padding: 30px 25px;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 25px;
        box-shadow: 0 8px 32px rgba(28,200,138,0.15);
    }
    .hero-ac h1 { font-size:1.6rem; font-weight:800; margin-bottom:8px; position: relative; z-index: 2; }
    .hero-ac p.lead { font-size:0.95rem; opacity:0.9; max-width:600px; position: relative; z-index: 2; }
    .hero-ac .hero-icon { position:absolute; right:30px; top:50%; transform:translateY(-50%); font-size:5rem; opacity:0.12; z-index: 1; }

    /* Stats bar */
    .stats-bar-ac { background:#fff; border-radius:12px; padding:15px; display:flex; box-shadow:0 2px 15px rgba(0,0,0,0.05); margin-bottom:25px; border: 1px solid #edf2f7; }
    .stats-bar-ac .stat-item { flex:1; text-align:center; border-right:1px solid #edf2f7; }
    .stats-bar-ac .stat-item:last-child{ border-right:none; }
    .stats-bar-ac .stat-num{ font-size:1.3rem; font-weight:800; color:#4e73df; display: block; }
    .stats-bar-ac .stat-lbl{ font-size:0.7rem; color:#718096; text-transform:uppercase; font-weight: 600; }

    /* Service Card */
    .service-card { border-radius:12px; border:1px solid #e2e8f0; transition: all 0.2s ease; background: #fff; }
    .service-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-color: #cbd5e0; }
    .price-badge { background: #4e73df; color:#fff; padding:5px 12px; border-radius:6px; font-weight:700; font-size: 0.9rem; }
    .service-desc { color:#4a5568; font-size: 0.88rem; line-height: 1.5; height: 65px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; }

    .gap-2 { gap: 0.5rem; }
</style>
@endpush

@section('content')
<div class="container-fluid ac-page-container">
    <div class="row">
        <div class="col-12">

            {{-- Hero --}}
            <div class="hero-ac">
                <h1>Solusi Servis AC Profesional</h1>
                <p class="lead">Booking teknisi berpengalaman untuk cuci AC, tambah freon, atau perbaikan komponen dengan harga transparan.</p>
                <div class="d-flex gap-2">
                    <a href="#layanan-list" class="btn btn-light text-primary font-weight-bold shadow-sm">Lihat Layanan</a>
                    <a href="{{ route('user.ac.index') }}" class="btn btn-outline-light font-weight-bold">Dashboard Saya</a>
                </div>
                <i class="fas fa-snowflake hero-icon"></i>
            </div>

            {{-- Stats --}}
            <div class="stats-bar-ac">
                <div class="stat-item">
                    <span class="stat-num">{{ $layanans->total() }}</span>
                    <span class="stat-lbl">Layanan</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num">{{ $kategoris->count() }}</span>
                    <span class="stat-lbl">Kategori</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num" style="color:#17a673">24/7</span>
                    <span class="stat-lbl">Booking</span>
                </div>
            </div>

            {{-- Grid Layanan --}}
            <div id="layanan-list" class="row">
                @forelse($layanans as $layanan)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card service-card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="font-weight-bold mb-0 text-dark">{{ $layanan->nama ?? $layanan->nama_layanan }}</h6>
                                    <span class="price-badge">Rp{{ number_format($layanan->harga_jasa ?? 0,0,',','.') }}</span>
                                </div>
                                <p class="service-desc">{{ $layanan->deskripsi ?? 'Pembersihan dan pengecekan komponen AC secara menyeluruh.' }}</p>
                                <div class="small text-muted mb-4">
                                    <i class="fas fa-tag mr-1"></i> {{ $layanan->kategori->nama ?? '-' }} | <i class="fas fa-bolt mr-1"></i> {{ $layanan->kapasitas_ac ?? '-' }}
                                </div>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <button class="btn btn-primary btn-sm btn-book px-4" data-id="{{ $layanan->id }}" data-name="{{ $layanan->nama ?? $layanan->nama_layanan }}">Booking</button>
                                    <span class="badge badge-light border text-muted">Garansi 14 Hari</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Layanan tidak ditemukan.</p>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $layanans->links() }}
            </div>

        </div>
    </div>
</div>

{{-- Modal Booking --}}
<div class="modal fade" id="bookingModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Konfirmasi Booking</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="bookingForm">
                @csrf
                <input type="hidden" name="layanan_id" id="layanan_id">
                <div class="modal-body">
                    <div id="bookingErrors" class="alert alert-danger d-none"></div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Layanan Dipilih</label>
                        <input type="text" id="layananName" class="form-control bg-light" readonly>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Tanggal Kunjungan</label>
                        <input type="date" name="tgl_kunjungan" class="form-control" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="2" required placeholder="Contoh: Jl. Merpati No. 123..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold">Merek AC</label>
                                <input type="text" name="merek_ac" class="form-control" placeholder="LG, Samsung, dll">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold">Jumlah Unit</label>
                                <input type="number" name="jumlah_unit" class="form-control" value="1" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold">Detail Keluhan</label>
                        <textarea name="detail_keluhan" class="form-control" rows="3" placeholder="Contoh: AC tidak dingin, berisik, atau ada air bocor..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Buat Pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function(){
        $('.btn-book').on('click', function(){
            $('#layanan_id').val($(this).data('id'));
            $('#layananName').val($(this).data('name'));
            $('#bookingErrors').addClass('d-none');
            $('#bookingModal').modal('show');
        });

        $('#bookingForm').on('submit', function(e){
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).text('Mengirim...');

            $.post('{{ route("user.ac.store") }}', $(this).serialize())
                .done(function(res){
                    $('#bookingModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Booking Anda telah diterima. Silakan tunggu konfirmasi dari teknisi kami.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4e73df'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                })
                .fail(function(xhr){
                    btn.prop('disabled', false).text('Buat Pesanan');
                    let msg = xhr.responseJSON?.message || 'Gagal membuat pesanan.';
                    $('#bookingErrors').removeClass('d-none').text(msg);
                });
        });
    });
</script>
@endpush
