@extends('layouts.app')

@section('title', 'Form Sewa Kantin Baru')

@section('content')
<div class="container-fluid">

    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Form Sewa Kantin Baru</h1>
        <a href="{{ route('user.kantin.dashboard') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm"></i> Kembali ke Dashboard
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    {{-- Info penyewa otomatis --}}
    @if(!$penyewa)
        <div class="alert alert-info border-left-info shadow-sm py-2 px-3 mb-4">
            <i class="fas fa-info-circle mr-1"></i>
            Data penyewa Anda akan dibuat secara otomatis dari informasi akun saat pengajuan dikirim.
        </div>
    @endif

    <form id="form-booking" action="{{ route('user.kantin.booking.store') }}" method="POST">
        @csrf

        <div class="row">

            {{-- ════════════════════════════════════════════════
                 KOLOM KIRI — Pilih Unit + Info Unit
            ════════════════════════════════════════════════ --}}
            <div class="col-lg-5 mb-4">

                {{-- Bagian A: Pilih Unit --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-store mr-1"></i> Langkah 1 — Pilih Unit Kantin
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($units->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-store-slash fa-3x text-gray-200 mb-3"></i>
                                <p class="text-muted">Tidak ada unit kantin yang tersedia saat ini.</p>
                                <a href="{{ route('user.kantin.dashboard') }}" class="btn btn-sm btn-secondary">
                                    Kembali ke Dashboard
                                </a>
                            </div>
                        @else
                            <div class="form-group mb-0">
                                <label for="select-unit" class="font-weight-bold text-sm">Unit Tersedia</label>
                                <select id="select-unit" name="ruko_id" class="form-control @error('ruko_id') is-invalid @enderror">
                                    <option value="">-- Pilih Unit --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}"
                                            {{ old('ruko_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->kode_unit }}{{ $unit->no_unit ? ' - ' . $unit->no_unit : '' }}
                                            &nbsp;|&nbsp; Rp {{ number_format($unit->harga, 0, ',', '.') }}/thn
                                        </option>
                                    @endforeach
                                </select>
                                @error('ruko_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pilih unit untuk melihat detail dan melanjutkan pengisian.</small>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Bagian B: Info Singkat Unit (muncul via AJAX) --}}
                <div id="card-unit-info" class="card shadow border-left-primary" style="display:none;">
                    <div class="card-header py-3 d-flex align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle mr-1"></i> Informasi Unit
                        </h6>
                        <span id="unit-status-badge" class="badge badge-success">Tersedia</span>
                    </div>
                    <div class="card-body">

                        {{-- Thumbnail foto --}}
                        <div id="unit-photo-wrapper" class="mb-3 text-center" style="display:none;">
                            <img id="unit-photo" src="" alt="Foto Unit"
                                 class="img-fluid rounded shadow-sm"
                                 style="width:100%; height:180px; object-fit:cover;">
                        </div>
                        <div id="unit-no-photo" class="text-center mb-3" style="display:none;">
                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                 style="height:120px;">
                                <i class="fas fa-image fa-3x text-gray-300"></i>
                            </div>
                            <small class="text-muted">Belum ada foto unit</small>
                        </div>

                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="font-weight-bold text-xs text-gray-600 text-uppercase" style="width:40%">Kode Unit</td>
                                <td id="info-kode-unit" class="font-weight-bold">—</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-xs text-gray-600 text-uppercase">Kategori</td>
                                <td id="info-kategori">—</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-xs text-gray-600 text-uppercase">Harga / Tahun</td>
                                <td id="info-harga" class="text-success font-weight-bold">—</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-xs text-gray-600 text-uppercase">Status</td>
                                <td id="info-status">—</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Skeleton loader untuk info unit --}}
                <div id="card-unit-skeleton" class="card shadow" style="display:none;">
                    <div class="card-body">
                        <div class="skeleton" style="height:120px; border-radius:6px; margin-bottom:12px;
                             background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);
                             background-size:200% 100%; animation: shimmer 1.2s infinite;"></div>
                        @for($i = 0; $i < 4; $i++)
                            <div class="skeleton" style="height:16px; margin-bottom:8px; border-radius:4px;
                                 background: linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);
                                 background-size:200% 100%; animation: shimmer 1.2s infinite;
                                 width:{{ [80, 60, 70, 50][$i] }}%;"></div>
                        @endfor
                    </div>
                </div>

            </div>{{-- /KOLOM KIRI --}}

            {{-- ════════════════════════════════════════════════
                 KOLOM KANAN — Detail Sewa + Pembayaran + Submit
            ════════════════════════════════════════════════ --}}
            <div class="col-lg-7 mb-4" id="panel-detail-sewa" style="display:none;">

                {{-- Bagian C: Detail Sewa --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-calendar-alt mr-1"></i> Langkah 2 — Detail Sewa
                        </h6>
                    </div>
                    <div class="card-body">

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="date" id="tgl-mulai" name="tgl_mulai"
                                       class="form-control @error('tgl_mulai') is-invalid @enderror"
                                       value="{{ old('tgl_mulai', date('Y-m-d')) }}"
                                       min="{{ date('Y-m-d') }}" required>
                                @error('tgl_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Durasi Sewa</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control bg-light" value="1 Tahun" readonly>
                                <small class="text-muted">Durasi sewa minimum adalah 1 tahun.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label font-weight-bold">Tanggal Selesai</label>
                            <div class="col-sm-8">
                                <input type="text" id="tgl-selesai-display" class="form-control bg-light" readonly placeholder="Otomatis dihitung...">
                            </div>
                        </div>

                        {{-- Ringkasan Pembayaran --}}
                        <div class="alert alert-light border shadow-sm mt-3 mb-0" id="ringkasan-pembayaran" style="display:none;">
                            <div class="font-weight-bold mb-2 text-gray-700">
                                <i class="fas fa-calculator mr-1 text-primary"></i> Ringkasan Pembayaran
                            </div>
                            <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                <span class="text-sm text-gray-600">Total Sewa / Tahun</span>
                                <span id="rp-total" class="font-weight-bold text-gray-800">—</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <div>
                                    <span class="badge badge-warning mr-1">Termin 1</span>
                                    <span class="text-sm text-gray-600">Bayar saat mulai</span><br>
                                    <small id="tanggal-termin1" class="text-muted"></small>
                                </div>
                                <span id="rp-termin1" class="font-weight-bold text-warning align-self-center">—</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div>
                                    <span class="badge badge-secondary mr-1">Termin 2</span>
                                    <span class="text-sm text-gray-600">Bulan ke-6</span><br>
                                    <small id="tanggal-termin2" class="text-muted"></small>
                                </div>
                                <span id="rp-termin2" class="font-weight-bold text-secondary align-self-center">—</span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Bagian D: Metode Pembayaran --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-credit-card mr-1"></i> Langkah 3 — Metode Pembayaran
                        </h6>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label class="font-weight-bold d-block mb-2">Pilih Metode <span class="text-danger">*</span></label>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="metode-tunai" name="metode_pembayaran" value="tunai"
                                       class="custom-control-input @error('metode_pembayaran') is-invalid @enderror"
                                       {{ old('metode_pembayaran', 'tunai') === 'tunai' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="metode-tunai">
                                    <i class="fas fa-money-bill-wave mr-1 text-success"></i> Tunai
                                </label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="metode-qris" name="metode_pembayaran" value="qris"
                                       class="custom-control-input @error('metode_pembayaran') is-invalid @enderror"
                                       {{ old('metode_pembayaran') === 'qris' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="metode-qris">
                                    <i class="fas fa-qrcode mr-1 text-primary"></i> QRIS
                                </label>
                            </div>
                            @error('metode_pembayaran')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Info Tunai --}}
                        <div id="info-tunai" class="alert alert-success border-left-success shadow-sm py-2 px-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Pembayaran Tunai:</strong> Silakan temui Admin Kantin untuk proses pembayaran.
                            Tunjukkan nomor pengajuan Anda setelah form ini berhasil dikirim.
                        </div>

                        {{-- Info QRIS --}}
                        <div id="info-qris" class="text-center" style="display:none;">
                            <p class="text-sm text-muted mb-2">Scan QR Code berikut untuk pembayaran Termin 1:</p>
                            <img src="{{ asset('img/qr-kantin.png') }}"
                                 alt="QR Code Kantin"
                                 class="img-fluid rounded shadow-sm border"
                                 style="max-width:220px;"
                                 onerror="this.style.display='none'; document.getElementById('qr-fallback').style.display='block';">
                            <div id="qr-fallback" class="alert alert-warning small mt-2" style="display:none;">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Gambar QR Code belum tersedia. Hubungi admin untuk mendapatkan kode QR.
                            </div>
                            <div class="alert alert-info small mt-2 text-left">
                                <strong>Nominal Termin 1:</strong>
                                <span id="qris-nominal" class="font-weight-bold text-primary">—</span><br>
                                Setelah transfer, Admin akan melakukan verifikasi dalam 1×24 jam.
                            </div>
                        </div>

                        {{-- Nominal yang harus dibayar --}}
                        <div id="box-nominal-termin1" class="mt-3 p-3 bg-warning rounded text-center" style="display:none;">
                            <div class="text-xs text-gray-700 font-weight-bold text-uppercase">Nominal Termin 1 yang Harus Dibayar</div>
                            <div id="nominal-termin1-besar" class="h4 font-weight-bold text-gray-800 mt-1 mb-0">—</div>
                        </div>

                    </div>
                </div>

                {{-- Bagian E: Tombol Submit --}}
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="mb-1 font-weight-bold text-gray-700">
                                    <i class="fas fa-check-circle text-success mr-1"></i> Siap Mengajukan?
                                </p>
                                <small class="text-muted">
                                    Pastikan data yang Anda isi sudah benar sebelum mengirim pengajuan.
                                </small>
                            </div>
                            <button type="submit" id="btn-submit" class="btn btn-primary px-4 py-2 shadow-sm"
                                    onclick="return confirm('Apakah Anda yakin ingin mengajukan sewa ini?')">
                                <i class="fas fa-paper-plane mr-1"></i> Ajukan Sewa
                            </button>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt mr-1 text-info"></i>
                                Pengajuan akan diproses oleh Admin dalam maksimal 2×24 jam.
                            </small>
                        </div>
                    </div>
                </div>

            </div>{{-- /KOLOM KANAN --}}

        </div>{{-- /row --}}
    </form>

</div>

<style>
    @keyframes shimmer {
        0%   { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    #panel-detail-sewa { animation: fadeInUp 0.35s ease; }
    #card-unit-info    { animation: fadeInUp 0.3s ease; }
    @keyframes fadeInUp {
        from { opacity:0; transform: translateY(12px); }
        to   { opacity:1; transform: translateY(0); }
    }
</style>
@endsection

@push('scripts')
<script>
$(function () {

    // ── Konstanta route AJAX ──────────────────────────────────────
    const BASE_DETAIL_URL = '{{ url("user/kantin/unit") }}';
    // Contoh: /user/kantin/unit/{id}/detail

    let selectedHarga = 0;

    // ── Format Rupiah ─────────────────────────────────────────────
    function formatRupiah(angka) {
        return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
    }

    // ── Format tanggal tampil ─────────────────────────────────────
    function formatTanggal(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        const bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
        return d.getDate() + ' ' + bulan[d.getMonth()] + ' ' + d.getFullYear();
    }

    // ── Hitung ringkasan pembayaran ───────────────────────────────
    function hitungRingkasan() {
        if (selectedHarga <= 0) return;
        const tglMulaiVal = $('#tgl-mulai').val();
        if (!tglMulaiVal) return;

        const tglMulai = new Date(tglMulaiVal);

        // tgl_selesai = +1 tahun
        const tglSelesai = new Date(tglMulai);
        tglSelesai.setFullYear(tglSelesai.getFullYear() + 1);

        // termin 2 = +6 bulan dari mulai
        const tglTermin2 = new Date(tglMulai);
        tglTermin2.setMonth(tglTermin2.getMonth() + 6);

        const termin1 = Math.floor(selectedHarga / 2);
        const termin2 = selectedHarga - termin1;

        // Update tampilan
        $('#tgl-selesai-display').val(formatTanggal(tglSelesai.toISOString().split('T')[0]));

        $('#rp-total').text(formatRupiah(selectedHarga));
        $('#rp-termin1').text(formatRupiah(termin1));
        $('#rp-termin2').text(formatRupiah(termin2));
        $('#tanggal-termin1').text('Jatuh Tempo: ' + formatTanggal(tglMulaiVal));
        $('#tanggal-termin2').text('Jatuh Tempo: ' + formatTanggal(tglTermin2.toISOString().split('T')[0]));

        $('#qris-nominal').text(formatRupiah(termin1));
        $('#nominal-termin1-besar').text(formatRupiah(termin1));

        $('#ringkasan-pembayaran').fadeIn(200);
        $('#box-nominal-termin1').fadeIn(200);
    }

    // ── Event: Pilih Unit (AJAX) ─────────────────────────────────
    $('#select-unit').on('change', function () {
        const id = $(this).val();

        if (!id) {
            $('#card-unit-info').hide();
            $('#card-unit-skeleton').hide();
            $('#panel-detail-sewa').hide();
            selectedHarga = 0;
            return;
        }

        // Tampilkan skeleton
        $('#card-unit-info').hide();
        $('#card-unit-skeleton').fadeIn(150);

        $.ajax({
            url: BASE_DETAIL_URL + '/' + id + '/detail',
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (data) {
                $('#card-unit-skeleton').hide();

                // Isi info unit
                $('#info-kode-unit').text(data.kode_unit + (data.no_unit ? ' — ' + data.no_unit : ''));
                $('#info-kategori').text(data.kategori.nama);
                $('#info-harga').text(formatRupiah(data.harga) + ' / tahun');
                $('#info-status').html('<span class="badge badge-success">Tersedia</span>');

                // Foto
                const gambar = data.dokumentasi.filter(d => d.tipe === 'gambar');
                if (gambar.length > 0) {
                    $('#unit-photo').attr('src', gambar[0].url);
                    $('#unit-photo-wrapper').show();
                    $('#unit-no-photo').hide();
                } else {
                    $('#unit-photo-wrapper').hide();
                    $('#unit-no-photo').show();
                }

                $('#card-unit-info').fadeIn(250);

                // Simpan harga
                selectedHarga = parseInt(data.harga);

                // Tampilkan panel detail sewa
                $('#panel-detail-sewa').fadeIn(300);
                hitungRingkasan();
            },
            error: function (xhr) {
                $('#card-unit-skeleton').hide();
                selectedHarga = 0;
                $('#panel-detail-sewa').hide();

                const msg = (xhr.responseJSON && xhr.responseJSON.error)
                    ? xhr.responseJSON.error
                    : 'Gagal memuat detail unit. Silakan coba lagi.';

                toastr ? toastr.error(msg) : alert(msg);
            }
        });
    });

    // ── Event: Tanggal Mulai berubah ─────────────────────────────
    $('#tgl-mulai').on('change', function () {
        hitungRingkasan();
    });

    // ── Event: Metode Pembayaran ──────────────────────────────────
    $('input[name="metode_pembayaran"]').on('change', function () {
        if ($(this).val() === 'tunai') {
            $('#info-tunai').fadeIn(200);
            $('#info-qris').hide();
        } else {
            $('#info-qris').fadeIn(200);
            $('#info-tunai').hide();
        }
    });

    // ── Trigger state awal (jika ada old input) ───────────────────
    @if(old('ruko_id'))
        $('#select-unit').val('{{ old("ruko_id") }}').trigger('change');
    @endif

    @if(old('metode_pembayaran') === 'qris')
        $('#metode-qris').prop('checked', true).trigger('change');
    @endif

    // ── Trigger awal metode default --
    $('input[name="metode_pembayaran"]:checked').trigger('change');

    // ── Disable submit jika belum pilih unit ─────────────────────
    $('#form-booking').on('submit', function (e) {
        if (!$('#select-unit').val()) {
            e.preventDefault();
            alert('Silakan pilih unit terlebih dahulu.');
            $('#select-unit').focus();
        }
    });
});
</script>
@endpush
