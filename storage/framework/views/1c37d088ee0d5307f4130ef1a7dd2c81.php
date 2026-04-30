<?php $__env->startSection('title', 'Histori Booking Futsal'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid" x-data="futsalHistory()">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('user.futsal.dashboard')); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Histori Booking</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Histori Booking Futsal</h1>
        </div>
        <div class="d-flex align-items-center">
            <a href="<?php echo e(route('user.futsal.history')); ?>" class="btn btn-sm btn-light border shadow-sm mr-2 text-primary">
                <i class="fas fa-sync-alt"></i> Refresh
            </a>
            <a href="<?php echo e(route('user.futsal.index')); ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Booking Baru
            </a>
        </div>
    </div>

    <!-- DataTales Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Seluruh Booking</h6>

            <!-- Filter Buttons (Standard HTML Form) -->
            <form action="<?php echo e(route('user.futsal.history')); ?>" method="GET" class="form-inline">
                <select name="status" class="form-control form-control-sm mr-2">
                    <option value="all">Semua Status</option>
                    <option value="menunggu" <?php echo e(request('status') == 'menunggu' ? 'selected' : ''); ?>>Menunggu</option>
                    <option value="dikonfirmasi" <?php echo e(request('status') == 'dikonfirmasi' ? 'selected' : ''); ?>>Dikonfirmasi</option>
                    <option value="selesai" <?php echo e(request('status') == 'selesai' ? 'selected' : ''); ?>>Selesai</option>
                    <option value="dibatalkan" <?php echo e(request('status') == 'dibatalkan' ? 'selected' : ''); ?>>Dibatalkan</option>
                </select>
                <input type="date" name="start_date" value="<?php echo e(request('start_date')); ?>" class="form-control form-control-sm mr-2">
                <span class="mr-2 text-xs">s/d</span>
                <input type="date" name="end_date" value="<?php echo e(request('end_date')); ?>" class="form-control form-control-sm mr-2">
                <button type="submit" class="btn btn-sm btn-primary shadow-sm mr-1">
                    <i class="fas fa-filter fa-sm text-white-50"></i> Filter
                </button>
                <a href="<?php echo e(route('user.futsal.history')); ?>" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-undo fa-sm"></i> Reset
                </a>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center" width="100%" cellspacing="0">
                    <thead class="bg-light text-gray-800">
                        <tr>
                            <th>Nama Lapangan</th>
                            <th>Tanggal Main</th>
                            <th>Waktu Sesi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="font-weight-bold"><?php echo e($item->lapangan->nama ?? 'N/A'); ?></td>
                            <td>
                                <?php if($item->type === 'event'): ?>
                                    <?php echo e(\Carbon\Carbon::parse($item->start_datetime)->format('d M Y')); ?><br>s/d<br><?php echo e(\Carbon\Carbon::parse($item->end_datetime)->format('d M Y')); ?>

                                <?php else: ?>
                                    <?php echo e(\Carbon\Carbon::parse($item->start_datetime)->format('d M Y')); ?>

                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($item->type === 'event'): ?>
                                    <span class="badge badge-warning">Event</span>
                                <?php else: ?>
                                    <?php echo e($item->start_datetime->format('H:i')); ?> - <?php echo e($item->end_datetime->format('H:i')); ?>

                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $status = strtolower($item->booking->status ?? '');
                                    $badge = match($status) {
                                        'menunggu' => 'badge-warning text-dark',
                                        'dikonfirmasi' => 'badge-primary',
                                        'selesai' => 'badge-success',
                                        'dibatalkan' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                ?>
                                <span class="badge font-weight-bold <?php echo e($badge); ?>"><?php echo e(ucfirst($status ?: 'Menunggu')); ?></span>
                            </td>
                            <td>
                                <button @click="openDetail(<?php echo e($item->id); ?>)" class="btn btn-outline-info btn-sm shadow-sm">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="py-5">
                                <i class="fas fa-search-minus fa-3x text-gray-200 mb-3"></i>
                                <h5 class="text-gray-400 font-weight-bold">Tidak ada data ditemukan</h5>
                                <p class="text-muted small">Coba sesuaikan filter pencarian Anda.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Links -->
            <div class="d-flex justify-content-end mt-4">
                <?php echo e($history->appends(request()->query())->links()); ?>

            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <?php echo $__env->make('user.futsal.partials.modal-detail', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    [x-cloak] { display: none !important; }
    .breadcrumb-item + .breadcrumb-item::before {
        content: "\f105";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        font-size: 10px;
        padding-right: 8px;
        color: #d1d3e2;
    }
    /* Pagination style fix for SB Admin 2 */
    .pagination {
        margin-bottom: 0;
    }
</style>
<?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function futsalHistory() {
    return {
        modalLoading: false,
        downloading: false,
        detail: {
            id: null,
            lapangan: { nama: '', ukuran: '', deskripsi: '' },
            jadwal: { tanggal: '', jam: '', durasi: '' },
            status: { label: '', color: '' },
            pembayaran: { jenis: '', total: '', status: '', metode: '' }
        },
        async openDetail(id) {
            this.modalLoading = true;
            $('#detailModal').modal('show');
            try {
                const response = await axios.get("<?php echo e(url('user/futsal/api/booking')); ?>/" + id);
                this.detail = response.data;
                this.detail.id = id;
            } catch (error) {
                console.error("Detail error:", error);
                $('#detailModal').modal('hide');
            } finally {
                this.modalLoading = false;
            }
        },
        async downloadPDF(id) {
            if (this.downloading) return;
            this.downloading = true;
            try {
                const response = await axios.get("<?php echo e(url('user/futsal/booking')); ?>/" + id + "/download-invoice", {
                    responseType: 'blob'
                });
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'Invoice-Futsal-' + id + '.pdf');
                document.body.appendChild(link);
                link.click();
                link.remove();
            } catch (error) {
                console.error("Download error:", error);
                alert("Gagal mengunduh PDF. Silakan coba lagi.");
            } finally {
                this.downloading = false;
            }
        }
    }
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/user/futsal/history.blade.php ENDPATH**/ ?>