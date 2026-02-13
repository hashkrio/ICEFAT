<!-- Navbar -->
<nav class="main-header navbar navbar-expand-md navbar-transparennt fixed-top">
    <?= $this->include('layouts/main_header_logo'); ?>

    <!-- <div class="container"> -->

        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon">
            <i class="fas fa-bars"></i>
            </span>
        </button>

        <div class="collapse navbar-collapse order-3 collapse" id="navbarCollapse">
            <!-- Left navbar links -->
            <ul class="navbar-nav px-5">
                <li class="nav-item d-sm-inline-block">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-sm-inline-block">
                    <a href="javascript:void(0);" class="nav-link"><b>Hello, <?=auth()->user()->username?></b></a>
                </li>
            </ul>


            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <?= $this->include('layouts/site_links'); ?>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="<?php echo site_url('login/logout'); ?>" class="nav-link">Logout</a>
                </li>
            </ul>
        </div>
    <!-- </div> -->
  </nav>
  <!-- /.navbar -->