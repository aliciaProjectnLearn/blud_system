<!-- Modal Detail Booking (Bootstrap Style) -->
<div class="modal fade shadow" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true" x-cloak>
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white border-0">
                <h5 class="modal-title font-weight-bold" id="detailModalLabel">
                    <i class="fas fa-info-circle mr-2"></i> Rincian Booking Futsal
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body py-4">
                <div x-show="modalLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 font-weight-bold text-gray-500">Mendapatkan data terbaru...</p>
                </div>

                <div x-show="!modalLoading">
                    <!-- Lapangan Info -->
                    <div class="card bg-light border-0 rounded p-3 mb-4">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="bg-white p-3 rounded-circle shadow-sm">
                                    <i class="fas fa-futbol fa-2x text-primary"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="font-weight-bold text-primary mb-1" x-text="detail.lapangan.nama"></h5>
                                <p class="text-muted small mb-0" x-text="detail.lapangan.deskripsi"></p>
                            </div>
                            <div class="col-auto text-right">
                                <span class="badge badge-primary px-3 py-2" x-text="detail.lapangan.ukuran"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Jadwal Section -->
                        <div class="col-md-6 mb-4">
                            <h6 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">
                                <i class="fas fa-clock mr-2 text-info"></i> Jadwal Main
                            </h6>
                            <div class="pl-4">
                                <div class="mb-2">
                                    <small class="text-uppercase text-muted font-weight-bold">Tanggal</small>
                                    <p class="font-weight-bold text-gray-900 mb-0" x-text="detail.jadwal.tanggal"></p>
                                </div>
                                <div class="mb-2">
                                    <small class="text-uppercase text-muted font-weight-bold">Sesi Waktu</small>
                                    <p class="font-weight-bold text-gray-900 mb-0" x-text="detail.jadwal.jam"></p>
                                </div>
                                <div>
                                    <small class="text-uppercase text-muted font-weight-bold">Durasi</small>
                                    <p class="font-weight-bold text-gray-900 mb-0" x-text="detail.jadwal.durasi"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Pembayaran Section -->
                        <div class="col-md-6 mb-4">
                            <h6 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">
                                <i class="fas fa-credit-card mr-2 text-success"></i> Informasi Pembayaran
                            </h6>
                            <div class="pl-4">
                                <div class="mb-2">
                                    <small class="text-uppercase text-muted font-weight-bold">Metode</small>
                                    <p class="font-weight-bold text-gray-900 mb-0" x-text="detail.pembayaran.metode"></p>
                                </div>
                                <div class="mb-2">
                                    <small class="text-uppercase text-muted font-weight-bold">Status Bayar</small>
                                    <p class="font-weight-bold mb-0" 
                                       :class="detail.pembayaran.status === 'Lunas' ? 'text-success' : 'text-warning'" 
                                       x-text="detail.pembayaran.status"></p>
                                </div>
                                <div>
                                    <small class="text-uppercase text-muted font-weight-bold">Total Biaya</small>
                                    <h4 class="font-weight-bold text-success" x-text="detail.pembayaran.total"></h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Utama -->
                    <div class="text-center mt-3 p-3 border-top bg-gray-50">
                        <small class="text-muted text-uppercase d-block mb-2 font-weight-bold">Status Booking Saat Ini</small>
                        <span :class="{
                            'badge badge-lg px-4 py-2 font-weight-bold h5 mb-0 shadow-sm': true,
                            'badge-warning text-dark': detail.status.color === 'yellow',
                            'badge-primary text-white': detail.status.color === 'blue',
                            'badge-success text-white': detail.status.color === 'green',
                            'badge-danger text-white': detail.status.color === 'red',
                            'badge-secondary text-white': detail.status.color === 'gray'
                        }" x-text="detail.status.label"></span>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light border-row-0">
                <button type="button" class="btn btn-secondary border-0 px-4" data-dismiss="modal">Tutup</button>
                <button @click="downloadPDF(detail.id)" 
                        :disabled="downloading"
                        class="btn btn-primary shadow-sm border-0 px-4 d-flex align-items-center">
                    <template x-if="downloading">
                        <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
                    </template>
                    <template x-if="!downloading">
                        <i class="fas fa-download mr-2"></i>
                    </template>
                    <span x-text="downloading ? 'Sedang Mengunduh...' : 'Download PDF'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\blud_system\resources\views/user/futsal/partials/modal-detail.blade.php ENDPATH**/ ?>