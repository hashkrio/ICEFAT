<?php
$segments = request()->getUri()->getSegments();
$segment1 = isset($segments[0]) ? $segments[0] : '';
$segment2 = isset($segments[1]) ? $segments[1] : '';
?>
<?php if(auth()->loggedIn()) : ?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="<?php echo MAIN_WEBSITE_URL; ?>" class="brand-link">
    <img src="<?php echo site_url('dist/img/icefat-logo-white.png'); ?>" alt="ICEFAT Logo" class="brand-image elevation-3">
    <span class="brand-text font-weight-light">ICEFAT</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="<?php echo site_url('/'); ?>" class="nav-link <?php echo ($segment1 == "") ? 'active' : '' ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
            </p>
          </a>
        </li>
        <li class="nav-header">LISTS</li>
        <li class="nav-item">
          <a href="<?php echo site_url('material/quantity'); ?>" class="nav-link <?php echo ($segment1 == "material" && $segment2 == "quantity") ? 'active' : '' ?>">
            <i class="nav-icon fas fa-chart-pie"></i>
            <p>
              By Material Quantities
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo site_url('crate/design'); ?>" class="nav-link <?php echo ($segment1 == "crate" && $segment2 == "design") ? 'active' : '' ?>">
            <i class="nav-icon fas fa-edit"></i>
            <p>
              By Crate Design
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo site_url('design/regression'); ?>" class="nav-link <?php echo ($segment1 == "design" && $segment2 == "regression") ? 'active' : '' ?>">
            <i class="nav-icon fas fa-table"></i>
            <p>
              Design Regressions
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo site_url('transportation/emission'); ?>" class="nav-link <?php echo ($segment1 == "transportation" && $segment2 == "emission") ? 'active' : '' ?>">
            <i class="nav-icon fas fa-columns"></i>
            <p>
              Transport Emission Factors
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo site_url('crate/type'); ?>" class="nav-link <?php echo ($segment1 == "crate" && $segment2 == "types") ? 'active' : '' ?>">
            <i class="nav-icon fas fa-file"></i>
            <p>
              Crate Types
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo site_url('model/option'); ?>" class="nav-link <?php echo ($segment1 == "model" && $segment2 == "option") ? 'active' : '' ?>">
            <i class="nav-icon fas fa-ellipsis-h"></i>
            <p>
              Model Options
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?php echo site_url('users'); ?>" class="nav-link <?php echo ($segment1 == "users") ? 'active' : '' ?>">
            <i class="nav-icon fas fa-ellipsis-h"></i>
            <p>
              Users
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
<?php endif; ?>