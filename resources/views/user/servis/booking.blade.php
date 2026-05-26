@extends('layouts.publik')

@section('title', 'Booking Jadwal Servis')

@push('styles')
    <style>
        .booking-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .booking-header {
            background: linear-gradient(90deg, #4e73df, #224abe);
            color: white;
            padding: 25px;
            border: none;
        }

        .form-section-title {
            font-size: 0.9rem;
            font-weight: 800;
            color: #4e73df;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #eee;
        }

        .slot-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
        }

        .slot-item {
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .slot-item:hover:not(.disabled) {
            border-color: #4e73df;
            background: #f8f9fc;
        }

        .slot-item.active {
            background: #4e73df;
            border-color: #4e73df;
            color: white;
        }

        .slot-item.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8f9fc;
        }

        select.form-control {
            max-width: 100%;
            text-overflow: ellipsis;
        }

        .slot-item.slot-error {
            border-color: #e74a3b !important;
            box-shadow: 0 0 0 2px rgba(231,74,59,0.2);
        }

        .required-star {
            color: #e74a3b;
            font-weight: bold;
            margin-left: 2px;
        }

        .form-control.is-invalid-custom {
            border-color: #e74a3b;
            box-shadow: 0 0 0 0.2rem rgba(231,74,59,0.2);
        }

        #client-error-alert {
            border-radius: 12px;
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card booking-card mb-4">
                    <div class="booking-header">
                        <h5 class="mb-1 font-weight-bold"><i class="fas fa-calendar-check mr-2"></i> Konfirmasi Booking</h5>
                        <p class="mb-0 opacity-75 small">Lengkapi data kendaraan dan pilih jadwal kedatangan Anda.</p>
                    </div>
                    <div class="card-body p-4">

                        @if($errors->has('no_hp'))
                            <div class="alert alert-danger d-flex align-items-start mb-4" style="border-radius: 12px;" role="alert">
                                <i class="fas fa-times-circle fa-lg mr-3 mt-1 flex-shrink-0"></i>
                                <div>
                                    <strong>Booking Ditolak!</strong><br>
                                    {{ $errors->first('no_hp') }}
                                </div>
                            </div>
                        @elseif($errors->any())
                            <div class="alert alert-danger mb-4" style="border-radius: 12px;">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <strong>Terdapat kesalahan, mohon periksa kembali:</strong>
                                </div>
                                <ul class="mb-0 pl-4">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @elseif($bookingAktif ?? null)
                            <div class="alert alert-warning d-flex align-items-start mb-4" style="border-radius: 12px;" role="alert">
                                <i class="fas fa-exclamation-triangle fa-lg mr-3 mt-1 flex-shrink-0"></i>
                                <div>
                                    <strong>Booking Aktif Terdeteksi!</strong><br>
                                    Kamu masih memiliki booking servis aktif dengan kode
                                    <strong>{{ $bookingAktif->kode_booking }}</strong>
                                    (Status: <span class="badge badge-warning">{{ strtoupper($bookingAktif->status) }}</span>).
                                    Selesaikan booking tersebut sebelum membuat booking baru.
                                    @if($bookingAktif->access_token)
                                        <br><a href="{{ route('user.servis.token.show', $bookingAktif->access_token) }}" class="font-weight-bold">
                                            <i class="fas fa-external-link-alt mr-1"></i>Lihat Detail Booking
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div id="client-error-alert" class="alert alert-danger mb-4">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <strong>Harap lengkapi semua field yang wajib diisi:</strong>
                            </div>
                            <ul id="client-error-list" class="mb-0 pl-4"></ul>
                        </div>

                        <div class="alert alert-light border d-flex align-items-center mb-4" style="border-radius: 12px;">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                style="width:50px; height:50px; flex-shrink:0;">
                                <i class="fas fa-wrench"></i>
                            </div>
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted">Layanan Dipilih</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $layananTerpilih->nama_layanan }}</div>
                            </div>
                        </div>

                        <form action="{{ route('user.servis.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="layanan_servis_id" value="{{ $layananTerpilih->id }}">

                            <div class="form-section-title">Informasi Pribadi</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Nama Lengkap <span class="required-star">*</span></label>
                                    <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama') }}" required placeholder="Nama Lengkap">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Nomor WhatsApp <span class="required-star">*</span></label>
                                    <input type="text" name="no_hp" id="no_hp" class="form-control" value="{{ old('no_hp') }}" required placeholder="0812...">
                                </div>
                            </div>

                            <div class="form-section-title">Informasi Kendaraan</div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="small font-weight-bold">Merek Kendaraan <span class="required-star">*</span></label>
                                    <select name="merek_kendaraan_id" id="merek_kendaraan_id" class="form-control" required>
                                        <option value="">-- Pilih Merek --</option>
                                        @foreach($mereks as $m)
                                            <option value="{{ $m->id }}" {{ old('merek_kendaraan_id') == $m->id ? 'selected' : '' }}>
                                                {{ $m->nama }} ({{ ucfirst($m->tipe) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="small font-weight-bold">Model/Nama Kendaraan <span class="required-star">*</span></label>
                                    <select name="model_kendaraan_id" id="model_kendaraan_id" class="form-control" required disabled>
                                        <option value="">-- Pilih Model --</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="small font-weight-bold">Nomor Plat <span class="required-star">*</span></label>
                                    <input type="text" name="nomor_plat" id="nomor_plat" class="form-control" placeholder="B 1234 ABC" required
                                        oninput="this.value = this.value.toUpperCase()">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="small font-weight-bold">Tahun Keluaran <span class="required-star">*</span></label>
                                    <select name="tahun_kendaraan" id="tahun_kendaraan" class="form-control" required>
                                        <option value="">-- Pilih Tahun --</option>
                                        @for($i = date('Y'); $i >= 1990; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="form-section-title mt-4">Jadwal & Keluhan</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Tanggal Booking <span class="required-star">*</span></label>
                                    <input type="date" id="tanggal_booking" name="tanggal_booking" class="form-control" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="small font-weight-bold">Jam Kedatangan <span class="required-star">*</span></label>
                                    <div id="slot-container" class="slot-grid">
                                        <div class="text-muted small">Pilih tanggal terlebih dahulu...</div>
                                    </div>
                                    <input type="hidden" name="jam_booking" id="selected_jam">
                                    <div id="jam-error" class="text-danger small mt-1" style="display:none;">
                                        <i class="fas fa-exclamation-circle mr-1"></i>Jam kedatangan wajib dipilih.
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="small font-weight-bold">Catatan Keluhan <span class="required-star">*</span></label>
                                    <textarea name="keluhan" id="keluhan" class="form-control" rows="3" placeholder="Ceritakan masalah kendaraan Anda..." required></textarea>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex flex-column-reverse flex-sm-row justify-content-between align-items-center">
                                <a href="{{ route('user.servis.katalog') }}"
                                    class="btn btn-link text-muted font-weight-bold mt-2 mt-sm-0 w-100 w-sm-auto text-center">Batal</a>
                                <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm w-100 w-sm-auto"
                                    style="border-radius:10px;">
                                    <i class="fas fa-check-circle mr-2"></i> Buat Janji Servis
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                let now = new Date();
                let minDate;
                if (now.getHours() >= 16) {
                    let besok = new Date(now);
                    besok.setDate(besok.getDate() + 1);
                    minDate = besok.toISOString().split('T')[0];
                } else {
                    minDate = now.toISOString().split('T')[0];
                }
                $('#tanggal_booking').attr('min', minDate);
            })();

            // ===== VALIDASI FORM CLIENT-SIDE =====
            $('form').on('submit', function(e) {
                let errors = [];
                let valid = true;

                // Reset visual error state
                $('.form-control').removeClass('is-invalid-custom');
                $('#jam-error').hide();
                $('#slot-container').removeClass('slot-error');

                if (!$.trim($('#nama').val())) {
                    errors.push('Nama Lengkap wajib diisi.');
                    $('#nama').addClass('is-invalid-custom');
                    valid = false;
                }
                if (!$.trim($('#no_hp').val())) {
                    errors.push('Nomor WhatsApp wajib diisi.');
                    $('#no_hp').addClass('is-invalid-custom');
                    valid = false;
                }
                if (!$('#merek_kendaraan_id').val()) {
                    errors.push('Merek Kendaraan wajib dipilih.');
                    $('#merek_kendaraan_id').addClass('is-invalid-custom');
                    valid = false;
                }
                if (!$('#model_kendaraan_id').val()) {
                    errors.push('Model/Nama Kendaraan wajib dipilih.');
                    $('#model_kendaraan_id').addClass('is-invalid-custom');
                    valid = false;
                }
                if (!$.trim($('#nomor_plat').val())) {
                    errors.push('Nomor Plat wajib diisi.');
                    $('#nomor_plat').addClass('is-invalid-custom');
                    valid = false;
                }
                if (!$('#tahun_kendaraan').val()) {
                    errors.push('Tahun Keluaran wajib dipilih.');
                    $('#tahun_kendaraan').addClass('is-invalid-custom');
                    valid = false;
                }
                if (!$('#tanggal_booking').val()) {
                    errors.push('Tanggal Booking wajib diisi.');
                    $('#tanggal_booking').addClass('is-invalid-custom');
                    valid = false;
                }
                if (!$('#selected_jam').val()) {
                    errors.push('Jam Kedatangan wajib dipilih.');
                    $('#jam-error').show();
                    $('#slot-container').addClass('slot-error');
                    valid = false;
                }
                if (!$.trim($('#keluhan').val())) {
                    errors.push('Catatan Keluhan wajib diisi.');
                    $('#keluhan').addClass('is-invalid-custom');
                    valid = false;
                }

                if (!valid) {
                    e.preventDefault();
                    let list = $('#client-error-list');
                    list.empty();
                    $.each(errors, function(i, msg) {
                        list.append('<li>' + msg + '</li>');
                    });
                    $('#client-error-alert').fadeIn(200);
                    $('html, body').animate({ scrollTop: $('#client-error-alert').offset().top - 20 }, 400);
                    return false;
                }
            });

            // Hapus error visual saat field diperbaiki
            $(document).on('input change', '.form-control', function() {
                $(this).removeClass('is-invalid-custom');
            });

            $('#tanggal_booking').on('change', function() {
                let tgl = $(this).val();
                let container = $('#slot-container');
                container.html(
                    '<div class="text-center py-3">' +
                    '<span class="spinner-border spinner-border-sm text-primary mr-2"></span>' +
                    'Memuat jadwal tersedia...</div>'
                );
                $('#selected_jam').val('');

                $.ajax({
                    url: "{{ route('user.servis.slots') }}",
                    data: { tanggal: tgl },
                    success: function(res) {
                        container.empty();
                        let slots = res.slots;

                        if (slots.length === 0) {
                            container.html('<div class="text-muted small">Tidak ada slot tersedia.</div>');
                            return;
                        }

                        if (res.is_hari_ini && !res.ada_yang_tersedia) {
                            container.html(
                                '<div class="alert alert-warning py-2 px-3 small">' +
                                '<i class="fas fa-clock mr-1"></i>' +
                                'Semua jadwal untuk hari ini sudah tidak tersedia. ' +
                                'Silakan pilih tanggal besok atau setelahnya.' +
                                '</div>'
                            );
                            return;
                        }

                        slots.forEach(function(s) {
                            let disabledClass = s.tersedia ? '' : 'disabled';
                            let info = '';

                            if (s.sudah_lewat) {
                                info = '<small class="d-block text-xs text-muted">Sudah lewat</small>';
                            } else if (!s.tersedia) {
                                info = '<small class="d-block text-xs text-danger">Penuh</small>';
                            } else {
                                info = '<small class="d-block text-xs text-success">' +
                                       (3 - s.terisi) + ' slot</small>';
                            }

                            container.append(
                                '<div class="slot-item ' + disabledClass + '" data-jam="' + s.jam + '">' +
                                '<div class="font-weight-bold">' + s.jam + '</div>' +
                                info +
                                '</div>'
                            );
                        });
                    },
                    error: function() {
                        container.html(
                            '<div class="text-danger small">' +
                            '<i class="fas fa-exclamation-circle mr-1"></i>' +
                            'Gagal memuat jadwal. Silakan coba lagi.' +
                            '</div>'
                        );
                    }
                });
            });

            $(document).on('click', '.slot-item:not(.disabled)', function() {
                $('.slot-item').removeClass('active');
                $(this).addClass('active');
                $('#selected_jam').val($(this).data('jam'));
                // Hapus error jam saat slot dipilih
                $('#jam-error').hide();
                $('#slot-container').removeClass('slot-error');
            });

            $('#merek_kendaraan_id').on('change', function() {
                let merekId = $(this).val();
                let modelSelect = $('#model_kendaraan_id');
                modelSelect.empty().append('<option value="">-- Pilih Model --</option>').prop('disabled', true);

                if (merekId) {
                    modelSelect.append('<option value="">Memuat model...</option>');
                    $.ajax({
                        url: "{{ route('user.servis.kendaraan.model', ':id') }}".replace(':id', merekId),
                        success: function(models) {
                            modelSelect.empty().append('<option value="">-- Pilih Model --</option>');
                            if (models && models.length > 0) {
                                models.forEach(function(m) {
                                    modelSelect.append('<option value="' + m.id + '">' + m.nama_model + '</option>');
                                });
                                modelSelect.prop('disabled', false);
                            } else {
                                modelSelect.append('<option value="">Tidak ada model tersedia</option>');
                            }
                        },
                        error: function() {
                            modelSelect.empty().append('<option value="">-- Pilih Model --</option>');
                            alert('Gagal memuat model kendaraan. Silakan coba lagi.');
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection