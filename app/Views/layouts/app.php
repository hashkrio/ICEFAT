<!DOCTYPE html>
<html lang="en">

<?= $this->include('layouts/header'); ?>

<body class="hold-transition sidebar-collapse layout-top-nav">
    <div class="wrapper">

    <?php if(auth()->loggedIn() && auth()->user()->inGroup('superadmin')) : ?>
        <?= $this->include('layouts/navbar'); ?>
        <?= $this->include('layouts/sidebar'); ?>
    <?php elseif(auth()->loggedIn() && auth()->user()->inGroup('admin')) : ?>
        <?= $this->include('layouts/navbar'); ?>
        <?= $this->include('layouts/admin_sidebar'); ?>
    <?php else: ?>
        <?= $this->include('layouts/user_navbar'); ?>
    <?php endif; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            
            <?= $this->include('layouts/content_header'); ?>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?= $this->include('layouts/control_sidebar'); ?>
        
        <?= $this->include('layouts/footer'); ?>
    </div>
    <!-- ./wrapper -->

    <?= $this->include('layouts/required_js'); ?>
</body>
<?= $this->renderSection('script') ?>
</html>