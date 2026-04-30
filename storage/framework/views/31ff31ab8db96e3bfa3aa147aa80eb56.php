<!-- Slot Availability Summary -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-light d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-clock mr-1"></i> Status Slot Tanggal: <?php echo e(\Carbon\Carbon::parse($tanggalSlot)->translatedFormat('d F Y')); ?>

        </h6>
        <?php
            $currentRoute = request()->route()->getName();
            $resetRoute = $currentRoute;
        ?>
        <?php if(request('tanggal')): ?>
            <a href="<?php echo e(route($resetRoute)); ?>" class="btn btn-sm btn-outline-secondary">Reset Tanggal</a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="row">
            <?php
                $workHours = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'];
            ?>
            <?php $__currentLoopData = $workHours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hour): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    // Normalisasi string jam dari DB (bisa 08:00:00) ke 08:00
                    $usage = 0;
                    foreach($slotUsage as $dbHour => $count) {
                        if(strpos($dbHour, $hour) === 0) {
                            $usage = $count;
                            break;
                        }
                    }
                    $isFull = $usage >= 3;
                ?>
                <div class="col-6 col-md-3 col-xl-1-5 mb-3">
                    <div class="border rounded p-2 text-center <?php echo e($isFull ? 'bg-danger-soft border-danger' : 'bg-light'); ?>">
                        <div class="small font-weight-bold"><?php echo e($hour); ?></div>
                        <div class="mt-1">
                            <?php if($isFull): ?>
                                <span class="badge badge-danger">Penuh (3/3)</span>
                            <?php else: ?>
                                <span class="badge badge-success">Tersedia (<?php echo e($usage); ?>/3)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>

<?php if (! $__env->hasRenderedOnce('33057c15-51b1-4b6a-8ac3-ad4dab82c056')): $__env->markAsRenderedOnce('33057c15-51b1-4b6a-8ac3-ad4dab82c056'); ?>
<?php $__env->startPush('styles'); ?>
<style>
    .bg-danger-soft {
        background-color: #fff5f5;
    }
    .col-xl-1-5 {
        flex: 0 0 12.5%;
        max-width: 12.5%;
    }
    @media (max-width: 1200px) {
        .col-xl-1-5 {
            flex: 0 0 25%;
            max-width: 25%;
        }
    }
    @media (max-width: 576px) {
        .col-xl-1-5 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
</style>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\blud_system\resources\views/adminservis/booking/partials/slot_summary.blade.php ENDPATH**/ ?>