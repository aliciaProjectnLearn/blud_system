@extends('layouts.app')

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
                        <div class="alert alert-light border d-flex align-items-center mb-4" style="border-radius: 12px;">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                style="width:50px; height:50px; flex-shrink:0;">
                                <i class="fas fa-wrench"></i>
                            </div>
                            <div>
                                <div class="text-xs text-uppercase font-weight-bold text-muted">Layanan Dipilih</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $layananTerpilih->nama_layanan }}
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('user.servis.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="layanan_servis_id" value="{{ $layananTerpilih->id }}">

                            <div class="form-section-title">Informasi Kendaraan</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Merek</label>
                                    <input type="text" name="merek_kendaraan" class="form-control" placeholder="Honda"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Kategori Kendaraan</label>
                                    <select name="tipe_kendaraan" class="form-control" required>
                                        <option value="">-- Pilih --</option>
                                        <option value="motor">Motor</option>
                                        <option value="mobil">Mobil</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Model/Nama Kendaraan</label>
                                    <input type="text" name="model_kendaraan" class="form-control"
                                        placeholder="Contoh: Vario 150" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Nomor Plat</label>
                                    <input type="text" name="nomor_plat" class="form-control" placeholder="B 1234 ABC"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Tahun</label>
                                    <input type="number" name="tahun_kendaraan" class="form-control" placeholder="2021">
                                </div>
                            </div>

                            <div class="form-section-title mt-4">Jadwal & Keluhan</div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="small font-weight-bold">Tanggal Booking</label>
                                    <input type="date" id="tanggal_booking" name="tanggal_booking" class="form-control"
                                        min="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="small font-weight-bold">Jam Kedatangan</label>
                                    <div id="slot-container" class="slot-grid">
                                        <div class="text-muted small">Pilih tanggal terlebih dahulu...</div>
                                    </div>
                                    <input type="hidden" name="jam_booking" id="selected_jam" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="small font-weight-bold">Catatan Keluhan</label>
                                    <textarea name="keluhan" class="form-control" rows="3" placeholder="Ceritakan masalah kendaraan Anda..."></textarea>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                                <a href="{{ route('user.servis.katalog') }}"
                                    class="btn btn-link text-muted font-weight-bold">Batal</a>
                                <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm"
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
            $('#tanggal_booking').on('change', function() {
                let tgl = $(this).val();
                let container = $('#slot-container');
                container.html('<div class="spinner-border spinner-border-sm text-primary"></div> Memuat slot...');

                $.ajax({
                    url: "{{ route('user.servis.slots') }}",
                    data: {
                        tanggal: tgl
                    },
                    success: function(res) {
                        container.empty();
                        res.forEach(function(s) {
                            let disabledClass = s.tersedia ? '' : 'disabled';
                            let info = s.tersedia ?
                                `<small class="d-block text-xs text-success">${3-s.terisi} slot</small>` :
                                '<small class="d-block text-xs text-danger">Penuh</small>';

                            container.append(`
                        <div class="slot-item ${disabledClass}" data-jam="${s.jam}">
                            <div class="font-weight-bold">${s.jam}</div>
                            ${info}
                        </div>
                    `);
                        });
                    }
                });
            });

            $(document).on('click', '.slot-item:not(.disabled)', function() {
                $('.slot-item').removeClass('active');
                $(this).addClass('active');
                $('#selected_jam').val($(this).data('jam'));
            });
        </script>
    @endpush
@endsection
