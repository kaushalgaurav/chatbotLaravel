<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <!-- LOGO -->

    <button type="button" class=" fs-14 hdKopy vertical-menu-btn-icon" id="vertical-menu-btn">
        <svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="arrow-left-to-line" class="svg-inline--fa fa-arrow-left-to-line " role="img" xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 448 512">
            <path fill="currentColor"
                d="M0 424c0 13.3 10.7 24 24 24s24-10.7 24-24L48 88c0-13.3-10.7-24-24-24S0 74.7 0 88L0 424zM135.6 238.5c-4.8 4.5-7.6 10.9-7.6 17.5s2.7 12.9 7.6 17.5l136 128c9.7 9.1 24.8 8.6 33.9-1s8.6-24.8-1-33.9L212.5 280l83.5 0 128 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-128 0-83.5 0 91.9-86.5c9.7-9.1 10.1-24.3 1-33.9s-24.3-10.1-33.9-1l-136 128z">
            </path>
        </svg>
    </button>


    <div class="navbar-brand-box">
        <a href="<?php echo e(route('root')); ?>" class="logo logo-dark">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('build/images/logo/logo.svg')); ?>" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="<?php echo e(URL::asset('build/images/logo/nimbli_ai_logo.png')); ?>" alt="" height="25">
            </span>
        </a>

        <a href="<?php echo e(route('root')); ?>" class="logo logo-light">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('build/images/logo/logo-light.svg')); ?>" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="<?php echo e(URL::asset('build/images/logo/nimbli_ai_logowht.png')); ?>" alt="" height="25">
            </span>
        </a>
    </div>





    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li>
                    <a href="<?php echo e(route('chatbots.index')); ?>" class="waves-effect">
                        <i class="mdi mdi-robot-outline"></i>
                        <span key="t-chatbot"><?php echo app('translator')->get('translation.Dashboards'); ?></span>
                    </a>
                </li>
                
                

                
            </ul>
        </div>

        <!-- Sidebar -->
        <div class="dropdown d-inline-block user-dropdown-fixed">
            <button type="button" class="btn header-item waves-effect w-100 text-left" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="rounded-circle header-profile-user" src="<?php echo e(isset(Auth::user()->avatar) ? asset(Auth::user()->avatar) : asset('build/images/users/avatar-1.jpg')); ?>" alt="Header Avatar">
                <span class="d-none d-xl-inline-block ms-1 color-white" key="t-henry"><?php echo e(ucfirst(Auth::user()->name)); ?></span>
                <i class="mdi mdi-chevron-down d-none d-xl-inline-block color-white"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <!-- item-->
                <a class="dropdown-item" href="javascript:void(0)"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile"><?php echo app('translator')->get('translation.Profile'); ?></span></a>
                <a class="dropdown-item" href="javascript:void(0)"><i class="bx bx-wallet font-size-16 align-middle me-1"></i> <span key="t-my-wallet"><?php echo app('translator')->get('translation.My_Wallet'); ?></span></a>
                <a class="dropdown-item d-block" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target=".change-password"><span class="badge bg-success float-end">11</span><i
                        class="bx bx-wrench font-size-16 align-middle me-1"></i> <span key="t-settings"><?php echo app('translator')->get('translation.Settings'); ?></span></a>
                <a class="dropdown-item" href="javascript:void(0)"><i class="bx bx-lock-open font-size-16 align-middle me-1"></i> <span key="t-lock-screen"><?php echo app('translator')->get('translation.Lock_screen'); ?></span></a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                        class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout"><?php echo app('translator')->get('translation.Logout'); ?></span></a>
                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                    <?php echo csrf_field(); ?>
                </form>
            </div>
        </div>
        <!-- End Sidebar -->
    </div>



</div>
<!-- Left Sidebar End -->
<?php /**PATH C:\xampp\htdocs\chatbotLaravel\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>