<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ICEFAT Carbon Calculator Tool v0.6</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="<?php echo site_url('fonts/font.css'); ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo site_url('plugins/fontawesome-free/css/all.min.css'); ?>">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?php echo site_url('plugins/icheck-bootstrap/icheck-bootstrap.min.css'); ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo site_url('dist/css/adminlte.css'); ?>">
    <link rel="stylesheet" href="<?php echo site_url('dist/css/custom.css'); ?>">
</head>

<body class="hold-transition login-page layout-top-nav">
    
    <?= $this->include('layouts/login_navbar'); ?>
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="<?php site_url('/');?>" class="h6"><b>ICEFAT</b>Carbon Calculator</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg"><b>Sign In</b></p>

                <form action="<?php echo site_url('login/authenticate'); ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="input-group mb-3">
                        <input value="app4icefat" type="text" id="username" name="username" class="form-control" placeholder="Username" required />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user-tie"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input value="5r8U(A{7wXXa" type="password" class="form-control" placeholder="Password" name="password" id="password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <?php if (session()->getFlashdata('error')): ?>
                        <p style="color: red;"><?php echo session()->getFlashdata('error'); ?></p>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-8">
                            
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="<?php echo site_url('plugins/jquery/jquery.min.js'); ?>"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo site_url('plugins/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo site_url('dist/js/adminlte.min.js'); ?>"></script>
</body>

</html>