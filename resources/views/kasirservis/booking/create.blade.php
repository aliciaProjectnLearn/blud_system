@extends('layouts.app')

@section('title', 'Buat Booking Baru')

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
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card booking-card mb-4">
                    <div class="booking-header">
                        <h5 class="mb-1 font-weight-bold"><i class="fas fa-calendar-plus mr-2"></i> Buat Booking Baru (Kasir)</h5>
                        <p class="mb-0 opacity-75 small">Lengkapi data pelanggan dan kendaraan untuk menambahkan booking baru.</p>
                    </div>
                    <div class="card-body p-4">

                        @if($errors->any())
                            <div class="alert alert-danger mb-4" style="border-radius: 12px;">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('kasir.booking.store') }}" method="POST">
                            @csrf

                            <div class="form-section-title">Informasi Pribadi</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Nama Lengkap</label>
                                    <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required placeholder="Nama Lengkap">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Nomor WhatsApp</label>
                                    <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" required placeholder="0812...">
                                </div>
                            </div>

                            <div class="form-section-title">Informasi Kendaraan & Layanan</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Pilihan Layanan</label>
                                    <select name="layanan_servis_id" id="layanan_servis_id" class="form-control" required>
                                        <option value="">-- Pilih Layanan --</option>
                                        @foreach($layanans as $l)
                                            <option value="{{ $l->id }}" data-tipe="{{ strtolower($l->tipe_kendaraan) }}" {{ old('layanan_servis_id') == $l->id ? 'selected' : '' }}>
                                                {{ $l->nama_layanan }} ({{ ucfirst($l->tipe_kendaraan) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Merek Kendaraan</label>
                                    <select name="merek_kendaraan_id" id="merek_kendaraan_id" class="form-control" required>
                                        <option value="">-- Pilih Merek --</option>
                                        @foreach($mereks as $m)
                                            <option value="{{ $m->id }}" data-tipe="{{ strtolower($m->tipe) }}" {{ old('merek_kendaraan_id') == $m->id ? 'selected' : '' }}>
                                                {{ $m->nama }} ({{ ucfirst($m->tipe) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Model/Nama Kendaraan</label>
                                    <select name="model_kendaraan_id" id="model_kendaraan_id" class="form-control" required disabled>
                                        <option value="">-- Pilih Model --</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="small font-weight-bold">Nomor Plat</label>
                                    <input type="text" name="nomor_plat" class="form-control" placeholder="B 1234 ABC" required value="{{ old('nomor_plat') }}" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="small font-weight-bold">Tahun Keluaran</label>
                                    <select name="tahun_kendaraan" class="form-control" required>
                                        <option value="">-- Tahun --</option>
                                        @for($i = date('Y'); $i >= 1990; $i--)
                                            <option value="{{ $i }}" {{ old('tahun_kendaraan') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="form-section-title mt-4">Jadwal & Keluhan</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Tanggal Booking</label>
                                    <input type="date" id="tanggal_booking" name="tanggal_booking" class="form-control" value="{{ old('tanggal_booking') }}" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="small font-weight-bold">Jam Kedatangan</label>
                                    <div id="slot-container" class="slot-grid">
                                        <div class="text-muted small">Pilih tanggal terlebih dahulu...</div>
                                    </div>
                                    <input type="hidden" name="jam_booking" id="selected_jam" value="{{ old('jam_booking') }}" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="small font-weight-bold">Catatan Keluhan</label>
                                    <textarea name="keluhan" class="form-control" rows="3" placeholder="Ceritakan masalah kendaraan..."></textarea>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex flex-column-reverse flex-sm-row justify-content-between align-items-center">
                                <a href="{{ route('kasir.booking.index') }}"
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
@endsection

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
                url: "{{ route('kasir.booking.slots') }}",
                data: { tanggal: tgl },
                success: function(res) {
                    container.empty();
                    let slots = res.slots;

                    if (slots.length === 0) {
                        container.html(
                            '<div class="text-muted small">Tidak ada slot tersedia.</div>'
                        );
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
        });

        // Simpan semua opsi original merek kendaraan
        const originalMereks = $('#merek_kendaraan_id option').clone();

        $('#layanan_servis_id').on('change', function() {
            let tipe = $(this).find(':selected').data('tipe');
            let selectedMerek = $('#merek_kendaraan_id').val();
            
            // Reset model selection
            $('#model_kendaraan_id').empty().append('<option value="">-- Pilih Model --</option>').prop('disabled', true);
            
            // Bersihkan dropdown merek dan isi kembali berdasarkan tipe layanan
            $('#merek_kendaraan_id').empty();
            
            originalMereks.each(function() {
                let mType = $(this).data('tipe');
                if (!mType || mType === tipe) {
                    $('#merek_kendaraan_id').append($(this).clone());
                }
            });

            // Restore selected value if it's still available in the filtered list
            if (selectedMerek && $('#merek_kendaraan_id option[value="' + selectedMerek + '"]').length > 0) {
                $('#merek_kendaraan_id').val(selectedMerek);
            } else {
                $('#merek_kendaraan_id').val('');
            }
        });

        // Trigger change event to filter on page load if needed
        if ($('#layanan_servis_id').val()) {
            $('#layanan_servis_id').trigger('change');
        }

        $(document).on('change', '#merek_kendaraan_id', function() {
            let merekId = $(this).val();
            let modelSelect = $('#model_kendaraan_id');
            modelSelect.empty().append('<option value="">-- Pilih Model --</option>').prop('disabled', true);

            if (merekId) {
                modelSelect.append('<option value="">Memuat model...</option>');
                $.ajax({
                    url: "{{ route('kasir.booking.kendaraan.model', ':id') }}".replace(':id', merekId),
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
