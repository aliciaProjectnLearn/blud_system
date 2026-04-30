<?php $__env->startSection('content'); ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Daftar Pelanggan Blud Sistem</h1>
                    <p class="mb-4">
                        Dengan menu ini, Super admin dapat memantau dan mengetahui jumlah pelanggan dalam sistem.
                    </p>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Pelanggan</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="d-flex justify-content-between mb-3">
                                    <form method="GET">
                                        <input type="text" name="search" class="form-control" value="<?php echo e($search ?? ''); ?>"
                                            placeholder="Cari nama...">
                                    </form>

                                    
                                    <div>
                            </div>
                        </div>
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Email</th>                                    
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($loop->iteration); ?></td>
                                            <td><?php echo e($user->name); ?></td>
                                            <td><?php echo e($user->email); ?></td>
                                            <td>
                                                <a href="<?php echo e(route('users.edit', $user->id)); ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="<?php echo e(route('users.show',$user->id)); ?>" 
                                                    class="btn btn-info btn-sm">
                                                    Detail
                                                </a>
                                                <form id="delete-form-<?php echo e($user->id); ?>" 
                                                    action="<?php echo e(route('users.destroy', $user->id)); ?>" 
                                                    method="POST" 
                                                    style="display:inline-block">

                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="confirmDelete(<?php echo e($user->id); ?>)">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
<!-- /.container-fluid -->

<?php $__env->startPush('scripts'); ?>

<script>

function confirmDelete(id){

    Swal.fire({
    title: 'Yakin?',
    text: "Data user akan dihapus!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal'
    }).then((result) => {

        if(result.isConfirmed){
        document.getElementById('delete-form-'+id).submit();
        }
    })
}

</script>

<script>
$(document).ready(function() {

let table = $('#dataTable').DataTable({
pageLength: 3
})

$('#searchUser').on('keyup', function(){
table.search(this.value).draw()
})

})
</script>

<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\blud_system\resources\views/dashboard/users/pelanggan.blade.php ENDPATH**/ ?>