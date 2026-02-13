<?= $this->extend('layouts/app'); ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <!-- jquery validation -->
        <div class="card card-primary p-0 mt-3">
            <div class="card-header">
                <h3 class="card-title">Page Not Found</h3>
            </div>
            <div class="card-body">
                <img src="<?php echo site_url('dist/img/2840523.jpg'); ?>" alt="ICEFAT Maintence Mode" class="error_404">
            </div>
        </div>
         <!-- /.card -->
    </div>
</div>
<!-- /.row -->
<?= $this->endSection(); ?>