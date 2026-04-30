<?php $__env->startSection('content'); ?>

<div class="container-fluid">

<h1 class="h3 mb-2 text-gray-800">Monitoring Transaksi</h1>

<p class="mb-4">
Super Admin dapat melihat semua transaksi dari sistem AC, Futsal, dan Ruko.
</p>

<div class="card shadow mb-4">

<div class="card-header py-3">
<h6 class="m-0 font-weight-bold text-primary">
Daftar Transaksi
</h6>
</div>

<div class="card-body">

    <form method="GET" class="mb-3">

<div class="row">

<div class="col-md-4">
<input 
type="text" 
name="search" 
class="form-control"
placeholder="Cari ID transaksi..."
value="<?php echo e(request('search')); ?>">
</div>

<div class="col-md-3">
<select name="sistem" class="form-control">

<option value="">Semua Sistem</option>

<option value="AC" <?php echo e(request('sistem') == 'AC' ? 'selected' : ''); ?>>
AC
</option>

<option value="Futsal" <?php echo e(request('sistem') == 'Futsal' ? 'selected' : ''); ?>>
Futsal
</option>

<option value="Ruko" <?php echo e(request('sistem') == 'Ruko' ? 'selected' : ''); ?>>
Ruko
</option>

</select>
</div>

<div class="col-md-3">
<select name="status" class="form-control">

<option value="">Semua Status</option>

<option value="menunggu" <?php echo e(request('status') == 'menunggu' ? 'selected' : ''); ?>>
Menunggu
</option>

<option value="verifikasi" <?php echo e(request('status') == 'verifikasi' ? 'selected' : ''); ?>>
Verifikasi
</option>

<option value="dibatalkan" <?php echo e(request('status') == 'dibatalkan' ? 'selected' : ''); ?>>
Dibatalkan
</option>

</select>
</div>

<div class="col-md-2">
<button class="btn btn-primary btn-block">
Filter
</button>
</div>

</div>

</form>

<div class="table-responsive">

<table class="table table-bordered" id="dataTable">

<thead>
<tr>
<th>No</th>
<th>Sistem</th>
<th>ID Transaksi</th>
<th>Total</th>
<th>Status</th>
<th>Tanggal Bayar</th>
<th>Aksi</th>
</tr>
</thead>

<tbody>

<?php $__currentLoopData = $transaksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

<tr>

<td><?php echo e($loop->iteration); ?></td>

<td>

<?php if($t->sistem == 'AC'): ?>
<span class="badge badge-primary">AC</span>
<?php elseif($t->sistem == 'Futsal'): ?>
<span class="badge badge-success">Futsal</span>
<?php else: ?>
<span class="badge badge-warning">Ruko</span>
<?php endif; ?>

</td>

<td><?php echo e($t->id); ?></td>

<td>
Rp <?php echo e(number_format($t->total,0,',','.')); ?>

</td>

<td>

<?php if($t->status == 'menunggu'): ?>
<span class="badge badge-secondary">Menunggu</span>
<?php elseif($t->status == 'verifikasi'): ?>
<span class="badge badge-success">Verifikasi</span>
<?php else: ?>
<span class="badge badge-danger">Dibatalkan</span>
<?php endif; ?>

</td>

<td>
<?php echo e($t->tgl_bayar ?? '-'); ?>

</td>

<td>

<a href="#" class="btn btn-info btn-sm">
Detail
</a>

</td>

</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</tbody>

</table>

</div>
</div>
</div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/dashboard/transaksi/index.blade.php ENDPATH**/ ?>