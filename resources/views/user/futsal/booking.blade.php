@extends('layouts.app')

@section('title', 'Booking Lapangan Futsal')

@section('content')
<div class="container-fluid" x-data="bookingForm()" x-init="init()">

    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-futbol text-primary mr-2"></i> Booking Lapangan Futsal
        </h1>
        <a href="{{ route('user.dashboard') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Kembali ke Dashboard Utama
        </a>
    </div>

    {{-- Membership Info Banner --}}
    @if($membership)
    <div class="alert alert-success border-left-success shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-id-card fa-2x text-success mr-3"></i>
            <div>
                <strong>Membership Aktif</strong> — {{ $membership->paket->nama_paket ?? 'Paket Membership' }}<br>
                <small>Sisa kuota: <strong>{{ $membership->sisa_kuota }}</strong> jam dari
                    <strong>{{ $membership->total_kuota }}</strong> jam total</small>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info border-left-info shadow-sm mb-4" role="alert">
        <i class="fas fa-info-circle mr-2"></i>
        Anda belum memiliki membership aktif. Pembayaran hanya tersedia dengan metode <strong>Reguler</strong>.
    </div>
    @endif

    <div class="row">

        {{-- Form Booking --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-plus mr-1"></i> Form Booking
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.futsal.booking.store') }}" method="POST" @submit.prevent="submitForm">
                        @csrf

                        {{-- Validation Errors --}}
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        {{-- Jenis Booking --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700">
                                <i class="fas fa-tags text-primary mr-1"></i> Jenis Booking
                            </label>
                            <div class="d-flex jenis-booking-wrap" style="gap:12px;">
                                <label class="payment-card flex-fill" :class="{'payment-card--selected': form.type === 'regular'}">
                                    <input type="radio" name="type" value="regular" x-model="form.type" class="d-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock fa-lg text-primary mr-3"></i>
                                        <div>
                                            <div class="font-weight-bold">Regular</div>
                                            <small class="text-muted">Booking per jam (maks 3 jam)</small>
                                        </div>
                                    </div>
                                </label>
                                <label class="payment-card flex-fill" :class="{'payment-card--selected': form.type === 'event'}">
                                    <input type="radio" name="type" value="event" x-model="form.type" class="d-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt fa-lg text-warning mr-3"></i>
                                        <div>
                                            <div class="font-weight-bold">Event</div>
                                            <small class="text-muted">Booking multi-hari (Rp 800rb/hari)</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Pilih Lapangan --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-gray-700">
                                <i class="fas fa-map-marker-alt text-primary mr-1"></i> Lapangan
                            </label>
                            <select class="form-control @error('lapangan_id') is-invalid @enderror"
                                    name="lapangan_id"
                                    id="lapangan_id"
                                    x-model="form.lapangan_id"
                                    @change="onLapanganChange"
                                    required>
                                <option value="">-- Pilih Lapangan --</option>
                                @foreach($lapangans as $lap)
                                <option value="{{ $lap->id }}" {{ old('lapangan_id') == $lap->id ? 'selected' : '' }}>
                                    {{ $lap->nama }}
                                </option>
                                @endforeach
                            </select>
                            @error('lapangan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Form Event --}}
                        <div x-show="form.type === 'event'" x-cloak>
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700">Tanggal Mulai Event</label>
                                <input type="date" class="form-control"
                                    x-model="form.start_datetime" :min="today"
                                    :required="form.type === 'event'">
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700">Tanggal Selesai Event</label>
                                <input type="date" class="form-control"
                                    x-model="form.end_datetime" :min="form.start_datetime"
                                    :required="form.type === 'event'">
                            </div>
                            <div class="alert alert-warning border-left-warning shadow-sm">
                                <i class="fas fa-info-circle mr-1"></i>
                                Booking event menggunakan pembayaran <strong>Cash/Reguler</strong>.
                                Harga: <strong>Rp 800.000/hari</strong>
                                <br>
                                <span x-show="form.start_datetime && form.end_datetime">
                                    × <span x-text="hitungHari()"></span> hari = 
                                    <strong class="text-danger" x-text="'Rp ' + formatRupiah(hitungHari() * 800000)"></strong>
                                </span>
                            </div>
                        </div>

                        {{-- Pilih Tanggal --}}
                        <div class="form-group" x-show="form.type === 'regular'" :required="form.type === 'regular'" x-cloak>
                            <label class="font-weight-bold text-gray-700">
                                <i class="fas fa-calendar text-primary mr-1"></i> Tanggal Main
                            </label>
                            <input type="date"
                                   class="form-control @error('tanggal') is-invalid @enderror"
                                   name="tanggal"
                                   id="tanggal"
                                   x-model="form.tanggal"
                                   :min="today"
                                   @change="onTanggalChange"
                                   value="{{ old('tanggal') }}"
                                   :required="form.type === 'regular'">
                            @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Pilih Jam Mulai --}}
                        <div class="form-group" x-show="form.type === 'regular'" :required="form.type === 'regular' && (slots.length > 0 || slotLoading)" x-cloak>
                            <label class="font-weight-bold text-gray-700">
                                <i class="fas fa-clock text-primary mr-1"></i> Jam Mulai
                            </label>

                            {{-- Loading State --}}
                            <div x-show="slotLoading" class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <span class="ml-2 text-muted">Memuat slot tersedia...</span>
                            </div>

                            {{-- Slot Radio Buttons --}}
                            <div x-show="!slotLoading && slots.length > 0" class="slot-grid">
                                <template x-for="slot in slots" :key="slot.id">
                                    <label class="slot-card"
                                           :class="{ 'slot-card--selected': form.jam_mulai_id == slot.id, 'slot-card--booked': slot.is_booked }">
                                        <input type="radio"
                                               name="jam_mulai_id"
                                               :value="slot.id"
                                               x-model="form.jam_mulai_id"
                                               @change="onSlotChange(slot)"
                                               class="d-none">
                                        <div class="slot-time">
                                            <i class="fas fa-clock"></i>
                                            <span x-text="slot.jam_mulai_display || formatTime(slot.jam_mulai)"></span>
                                        </div>
                                        <small class="text-muted" x-text="'s/d ' + (slot.jam_selesai_display || formatTime(slot.jam_selesai))"></small>
                                    </label>
                                </template>
                            </div>

                            @error('jam_mulai_id')
                            <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Info jika tidak ada slot --}}
                        <div x-show="form.type === 'regular' && !slotLoading && slots.length === 0 && form.lapangan_id && form.tanggal" x-cloak>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Tidak ada slot tersedia untuk lapangan dan tanggal yang dipilih.
                            </div>
                        </div>

                        {{-- Pesan jika ada booking event --}}
                        <div x-show="eventPesan" x-cloak>
                            <div class="alert alert-danger">
                                <i class="fas fa-calendar-times mr-2"></i>
                                <span x-text="eventPesan"></span>
                            </div>
                        </div>

                        {{-- Durasi Main --}}
                        <div class="form-group" x-show="form.type === 'regular' && form.jam_mulai_id" x-cloak>
                            <label class="font-weight-bold text-gray-700">
                                <i class="fas fa-hourglass-half text-primary mr-1"></i> Durasi Main
                            </label>
                            <div class="row">
                                <div class="col-md-4">
                                    <select class="form-control @error('durasi_main') is-invalid @enderror"
                                            name="durasi_main"
                                            id="durasi_main"
                                            x-model="form.durasi_main"
                                            @change="onDurasiChange"
                                            :required="form.type === 'regular'"> ```
                                        <option value="1">1 Jam</option>
                                        <option value="2">2 Jam</option>
                                        <option value="3">3 Jam</option>
                                    </select>
                                    @error('durasi_main')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-8 d-flex align-items-center" x-show="jamSelesai" x-cloak>
                                    <span class="text-muted">
                                        <i class="fas fa-arrow-right text-success mr-1"></i>
                                        Selesai pukul: <strong class="text-success" x-text="jamSelesai"></strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Jenis Pembayaran --}}
                        <div class="form-group" x-show="form.type === 'regular' && form.jam_mulai_id" x-cloak>
                            <label class="font-weight-bold text-gray-700">
                                <i class="fas fa-credit-card text-primary mr-1"></i> Jenis Pembayaran
                            </label>
                            <div class="payment-options">
                                {{-- Reguler --}}
                                <label class="payment-card"
                                       :class="{ 'payment-card--selected': form.jenis_pembayaran === 'reguler' }">
                                    <input type="radio" name="jenis_pembayaran" value="reguler"
                                           x-model="form.jenis_pembayaran" class="d-none" checked>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-money-bill-wave fa-lg text-success mr-3"></i>
                                        <div>
                                            <div class="font-weight-bold">Reguler</div>
                                            <small class="text-muted">Bayar tunai / transfer ke kasir</small>
                                        </div>
                                    </div>
                                </label>

                                {{-- Membership --}}
                                @if($membership)
                                <label class="payment-card"
                                       :class="{ 'payment-card--selected': form.jenis_pembayaran === 'membership', 'payment-card--disabled': !isMembershipEnough }"
                                       :title="!isMembershipEnough ? 'Kuota membership tidak mencukupi' : ''">
                                    <input type="radio" name="jenis_pembayaran" value="membership"
                                           x-model="form.jenis_pembayaran"
                                           :disabled="!isMembershipEnough"
                                           class="d-none">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-id-card fa-lg text-info mr-3"></i>
                                        <div>
                                            <div class="font-weight-bold">Membership</div>
                                            <small class="text-muted">Gunakan kuota membership aktif</small>
                                            <br>
                                            <small x-show="!isMembershipEnough" class="text-danger">
                                                <i class="fas fa-exclamation-circle"></i> Kuota tidak cukup
                                            </small>
                                        </div>
                                    </div>
                                </label>
                                @else
                                <div class="payment-card payment-card--disabled">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-id-card fa-lg text-muted mr-3"></i>
                                        <div>
                                            <div class="font-weight-bold text-muted">Membership</div>
                                            <small class="text-muted">Tidak tersedia (belum memiliki membership aktif)</small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @error('jenis_pembayaran')
                            <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Hidden form fields --}}
                        <input type="hidden" name="lapangan_id" x-bind:value="form.lapangan_id">
                        <input type="hidden" name="tanggal" x-bind:value="form.tanggal">
                        <input type="hidden" name="jam_mulai_id" x-bind:value="form.jam_mulai_id">
                        <input type="hidden" name="durasi_main" x-bind:value="form.durasi_main">
                        <input type="hidden" name="jenis_pembayaran" x-bind:value="form.type === 'event' ? 'reguler' : form.jenis_pembayaran">
                        <input type="hidden" name="type" x-bind:value="form.type">
                        <input type="hidden" name="start_datetime" x-bind:value="form.start_datetime">
                        <input type="hidden" name="end_datetime" x-bind:value="form.end_datetime">

                        {{-- Submit Button --}}
                        <div class="border-top pt-3 mt-3" x-show="(form.type === 'regular' && form.jam_mulai_id) || (form.type === 'event' && form.start_datetime && form.end_datetime)" x-cloak>
                            <button type="submit"
                                    class="btn btn-primary btn-block btn-lg shadow"
                                    :disabled="submitting || !isFormValid"
                                    @click="console.log('isFormValid', isFormValid, 'form:', form)"
                                    id="btn-submit-booking">
                                <span x-show="!submitting">
                                    <i class="fas fa-check-circle mr-1"></i> Konfirmasi & Buat Booking
                                </span>
                                <span x-show="submitting">
                                    <span class="spinner-border spinner-border-sm mr-2"></span> Memproses...
                                </span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- Ringkasan Booking --}}
        <div class="col-lg-4">

            {{-- Ringkasan Card --}}
            <div class="card shadow mb-4" x-show="showSummary" x-cloak>
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-receipt mr-1"></i> Ringkasan Booking
                    </h6>
                </div>
                <div class="card-body">
                    <div class="summary-item">
                        <span class="text-muted small">Lapangan</span>
                        <span class="font-weight-bold" x-text="summary.lapangan || '-'"></span>
                    </div>
                    <div class="summary-item">
                        <span class="text-muted small">Tanggal</span>
                        <span class="font-weight-bold" x-text="summary.tanggal || '-'"></span>
                    </div>
                    <div class="summary-item">
                        <span class="text-muted small">Jam Mulai</span>
                        <span class="font-weight-bold" x-text="summary.jamMulai || '-'"></span>
                    </div>
                    <div class="summary-item">
                        <span class="text-muted small">Jam Selesai</span>
                        <span class="font-weight-bold text-success" x-text="jamSelesai || '-'"></span>
                    </div>
                    <div class="summary-item">
                        <span class="text-muted small">Durasi</span>
                        <span class="font-weight-bold" x-text="form.durasi_main + ' Jam'"></span>
                    </div>
                    <hr>
                    <div class="summary-item">
                        <span class="text-muted small">Jenis Pembayaran</span>
                        <span class="badge"
                              :class="form.jenis_pembayaran === 'membership' ? 'badge-info' : 'badge-success'"
                              x-text="form.jenis_pembayaran === 'membership' ? 'Membership' : 'Reguler'">
                        </span>
                    </div>
                    @if($membership)
                    <div class="summary-item" x-show="form.jenis_pembayaran === 'membership'" x-cloak>
                        <span class="text-muted small">Kuota Terpakai</span>
                        <span class="font-weight-bold text-warning" x-text="form.durasi_main + ' Jam'"></span>
                    </div>
                    <div class="summary-item" x-show="form.jenis_pembayaran === 'membership'" x-cloak>
                        <span class="text-muted small">Sisa Kuota Setelah</span>
                        <span class="font-weight-bold text-info"
                              x-text="({{ $membership->sisa_kuota }} - parseInt(form.durasi_main)) + ' Jam'">
                        </span>
                    </div>
                    @endif
                    <div class="mt-3 p-2 rounded" style="background:#f8f9fc;">
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Booking akan berstatus <strong>Menunggu</strong> hingga dikonfirmasi oleh admin.
                        </small>
                    </div>
                </div>
            </div>

            {{-- Panduan Booking Card --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-question-circle mr-1"></i> Panduan Booking
                    </h6>
                </div>
                <div class="card-body">
                    <ol class="pl-3 mb-0 small text-gray-700">
                        <li class="mb-2">Pilih lapangan yang ingin Anda gunakan</li>
                        <li class="mb-2">Pilih tanggal main</li>
                        <li class="mb-2">Pilih slot jam yang tersedia</li>
                        <li class="mb-2">Tentukan durasi bermain (1–3 jam)</li>
                        <li class="mb-2">Pilih metode pembayaran</li>
                        <li>Klik <strong>Konfirmasi & Buat Booking</strong></li>
                    </ol>
                    <hr>
                    <small class="text-muted">
                        <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                        Slot yang tersedia hanya menunjukkan jadwal yang belum terisi.
                        Booking akan dikonfirmasi oleh admin.
                    </small>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    /* Mobile: Jenis Booking stack vertical */
@media (max-width: 575.98px) {
    /* Jenis Booking: stack jadi vertical */
    .jenis-booking-wrap {
        flex-direction: column !important;
    }

    .jenis-booking-wrap .payment-card {
        width: 100%;
    }

    /* Payment card lebih compact di mobile */
    .payment-card {
        padding: 12px 14px;
    }

    /* Slot grid lebih rapat */
    .slot-card {
        min-width: 90px;
        padding: 10px 12px;
    }

    .slot-time {
        font-size: 0.9rem;
    }

    /* Durasi: full width di mobile */
    .col-md-4 {
        margin-bottom: 8px;
    }
}


    [x-cloak] { display: none !important; }

    /* Slot Grid */
    .slot-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 8px;
    }

    .slot-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 12px 18px;
        border: 2px solid #e3e6f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #fff;
        min-width: 110px;
        text-align: center;
        user-select: none;
    }
    .slot-card:hover {
        border-color: #4e73df;
        background: #f0f4ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(78,115,223,0.15);
    }
    .slot-card--selected {
        border-color: #4e73df !important;
        background: #4e73df !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(78,115,223,0.35);
        transform: translateY(-2px);
    }
    .slot-card--selected small {
        color: rgba(255,255,255,0.8) !important;
    }
    .slot-time {
        font-size: 1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .slot-card--booked {
        opacity: 0.4;
        background-color: #f8f9fc !important;
        border-color: #e3e6f0 !important;
        cursor: not-allowed !important;
        pointer-events: none; /* User tidak bisa klik */
        filter: grayscale(1);
    }
    .slot-card--booked .slot-time,
    .slot-card--booked small {
        color: #b7b9cc !important;
    }

    /* Payment Options */
    .payment-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 8px;
    }
    .payment-card {
        display: block;
        padding: 14px 18px;
        border: 2px solid #e3e6f0;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #fff;
    }
    .payment-card:hover:not(.payment-card--disabled) {
        border-color: #4e73df;
        background: #f0f4ff;
    }
    .payment-card--selected {
        border-color: #4e73df !important;
        background: #f0f4ff !important;
    }
    .payment-card--disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f8f9fc;
    }

    /* Summary Items */
    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        border-bottom: 1px dashed #e3e6f0;
    }
    .summary-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
{{-- Alpine.js CDN --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
{{-- axios --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
function bookingForm() {
    return {
        // Sisa kuota membership dari PHP (jika ada)
        membershipKuota: {{ $membership ? $membership->sisa_kuota : 0 }},
        hasMembership: {{ $membership ? 'true' : 'false' }},
        eventPesan: '',

        form: {
            type: '{{ old('type', 'regular') }}',
            lapangan_id: '{{ old('lapangan_id', '') }}',
            tanggal: '{{ old('tanggal', '') }}',
            jam_mulai_id: '{{ old('jam_mulai_id', '') }}',
            durasi_main: '{{ old('durasi_main', 1) }}',
            jenis_pembayaran: '{{ old('jenis_pembayaran', 'reguler') }}',
            start_datetime: '{{ old('start_datetime', '') }}',
            end_datetime: '{{ old('end_datetime', '') }}',
        },

        today: '',
        slots: [],
        slotLoading: false,
        selectedSlot: null,
        submitting: false,

        // Computed: Ringkasan
        get showSummary() {
            return this.form.lapangan_id && this.form.tanggal && this.form.jam_mulai_id;
        },

        get summary() {
            const lapEl = document.getElementById('lapangan_id');
            const lapNama = lapEl ? lapEl.options[lapEl.selectedIndex]?.text : '-';

            let jamMulaiTampil = '-';
            if (this.selectedSlot) {
                jamMulaiTampil = this.form.type === 'regular'
                ? this.selectedSlot.jam_mulai_display
                : this.formatTime(this.selectedSlot.jam_mulai);
            }
        
            return {
                lapangan: lapNama !== '-- Pilih Lapangan --' ? lapNama : '-',
                tanggal: this.form.tanggal ? this.formatDate(this.form.tanggal) : '-',
                jamMulai: jamMulaiTampil,
            };
        },

        get jamSelesai() {
            if (!this.selectedSlot || !this.form.durasi_main) return '';
            let [h, m] = this.selectedSlot.jam_mulai.split(':').map(Number);
            let date = new Date();
            if (this.form.type === 'regular') {
                m += 10;
            }
            date.setHours(h, m, 0);
            date.setHours(date.getHours() + parseInt(this.form.durasi_main));
            return date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');
        },

        get isMembershipEnough() {
            return this.membershipKuota >= parseInt(this.form.durasi_main);
        },

        get isFormValid() {
            if (this.form.type === 'event') {
                return this.form.lapangan_id && this.form.start_datetime && this.form.end_datetime;
            }
            return this.form.lapangan_id &&
                   this.form.tanggal &&
                   this.form.jam_mulai_id &&
                   this.form.durasi_main &&
                   this.form.jenis_pembayaran;
        },

        init() {
            // Set min date = today
            const now = new Date();
            this.today = now.toISOString().split('T')[0];

            // Setup axios CSRF
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Jika ada old input, muat ulang slot
            if (this.form.lapangan_id && this.form.tanggal) {
                this.fetchSlots();
            }
        },

        onLapanganChange() {
            this.form.jam_mulai_id = '';
            this.selectedSlot = null;
            this.slots = [];
            if (this.form.lapangan_id && this.form.tanggal) {
                this.fetchSlots();
            }
        },

        onTanggalChange() {
            this.form.jam_mulai_id = '';
            this.selectedSlot = null;
            this.slots = [];
            if (this.form.lapangan_id && this.form.tanggal) {
                this.fetchSlots();
            }
        },

        onSlotChange(slot) {
            this.selectedSlot = slot;
        },

        onDurasiChange() {
            // Jika jenis pembayaran membership dan kuota tidak cukup, reset ke reguler
            if (this.form.jenis_pembayaran === 'membership' && !this.isMembershipEnough) {
                this.form.jenis_pembayaran = 'reguler';
            }
        },

        async fetchSlots() {
            this.slotLoading = true;
            this.slots = [];
            this.eventPesan = '';
            try {
                const response = await axios.get('{{ route('user.futsal.booking.check') }}', {
                    params: {
                        lapangan_id: this.form.lapangan_id,
                        tanggal: this.form.tanggal,
                    }
                });
                if (response.data.event) {
                    this.eventPesan = response.data.pesan;
                } else if (response.data.success) {
                    this.slots = response.data.slots;

                    // Restore old selected slot jika ada
                    const oldId = '{{ old('jam_mulai_id', '') }}';
                    if (oldId) {
                        const found = this.slots.find(s => s.id == oldId);
                        if (found) {
                            this.form.jam_mulai_id = oldId;
                            this.selectedSlot = found;
                        }
                    }
                }
            } catch (error) {
                console.error('Gagal memuat slot:', error);
            } finally {
                this.slotLoading = false;
            }
        },

        submitForm(e) {
            if (!this.isFormValid || this.submitting) return;
            this.submitting = true;
            e.target.submit();
        },

        formatTime(time) {
            if (!time) return '-';
            return time.substring(0, 5);
        },

        formatDate(dateStr) {
            if (!dateStr) return '-';
            const d = new Date(dateStr);
            return d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        },

        hitungHari() {
            if (!this.form.start_datetime || !this.form.end_datetime) return 0;
            const start = new Date(this.form.start_datetime);
            const end = new Date(this.form.end_datetime);
            const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            return Math.max(1, diff);
        },

        formatRupiah(angka) {
            return 'Rp ' + angka.toLocaleString('id-ID');
        },
    };
}
</script>
@endpush
