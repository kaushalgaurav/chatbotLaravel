<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" key="t-menu"><?php echo app('translator')->get('translation.Menu'); ?></li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards"><?php echo app('translator')->get('translation.Dashboards'); ?></span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="index" key="t-default"><?php echo app('translator')->get('translation.Default'); ?></a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="waves-effect">
                    <span class="badge rounded-pill bg-danger float-end" key="t-hot"><?php echo app('translator')->get('translation.hot'); ?></span>
                        <i class="bx bx-layout"></i>
                        <span key="t-layouts"><?php echo app('translator')->get('translation.Layouts'); ?></span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li>
                            <a href="javascript: void(0);" class="has-arrow"
                                key="t-vertical"><?php echo app('translator')->get('translation.Vertical'); ?></a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="layouts-light-sidebar"
                                        key="t-light-sidebar"><?php echo app('translator')->get('translation.Light_Sidebar'); ?></a></li>
                                <li><a href="layouts-compact-sidebar"
                                        key="t-compact-sidebar"><?php echo app('translator')->get('translation.Compact_Sidebar'); ?></a></li>
                                <li><a href="layouts-icon-sidebar"
                                        key="t-icon-sidebar"><?php echo app('translator')->get('translation.Icon_Sidebar'); ?></a></li>
                                <li><a href="layouts-boxed" key="t-boxed-width"><?php echo app('translator')->get('translation.Boxed_Width'); ?></a>
                                </li>
                                <li><a href="layouts-preloader" key="t-preloader"><?php echo app('translator')->get('translation.Preloader'); ?></a>
                                </li>
                                <li><a href="layouts-colored-sidebar"
                                        key="t-colored-sidebar"><?php echo app('translator')->get('translation.Colored_Sidebar'); ?></a></li>
                                <li><a href="layouts-scrollable" key="t-scrollable"><?php echo app('translator')->get('translation.Scrollable'); ?></a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow"
                                key="t-horizontal"><?php echo app('translator')->get('translation.Horizontal'); ?></a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="layouts-horizontal" key="t-horizontal"><?php echo app('translator')->get('translation.Horizontal'); ?></a>
                                </li>
                                <li><a href="layouts-hori-topbar-light"
                                        key="t-topbar-light"><?php echo app('translator')->get('translation.Topbar_Light'); ?></a></li>
                                <li><a href="layouts-hori-boxed-width"
                                        key="t-boxed-width"><?php echo app('translator')->get('translation.Boxed_Width'); ?></a></li>
                                <li><a href="layouts-hori-preloader"
                                        key="t-preloader"><?php echo app('translator')->get('translation.Preloader'); ?></a></li>
                                <li><a href="layouts-hori-colored-header"
                                        key="t-colored-topbar"><?php echo app('translator')->get('translation.Colored_Header'); ?></a></li>
                                <li><a href="layouts-hori-scrollable"
                                        key="t-scrollable"><?php echo app('translator')->get('translation.Scrollable'); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="menu-title" key="t-backend"><?php echo app('translator')->get('translation.backend'); ?></li>
                <li>
                    <a href="yajra-datatable" class="waves-effect">
                        <i class="bx bx-list-ul"></i>
                        <span key="t-yajra-datatable"><?php echo app('translator')->get('translation.yajra-datatable'); ?></span>
                    </a>
                </li>

                <li class="menu-title" key="t-pages"><?php echo app('translator')->get('translation.Pages'); ?></li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-user-circle"></i>
                        <span key="t-authentication"><?php echo app('translator')->get('translation.Authentication'); ?></span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="auth-login" key="t-login"><?php echo app('translator')->get('translation.Login'); ?></a></li>
                        <li><a href="auth-login-2" key="t-login-2"><?php echo app('translator')->get('translation.Login'); ?> 2</a></li>
                        <li><a href="auth-register" key="t-register"><?php echo app('translator')->get('translation.Register'); ?></a></li>
                        <li><a href="auth-register-2" key="t-register-2"><?php echo app('translator')->get('translation.Register'); ?> 2</a></li>
                        <li><a href="auth-recoverpw" key="t-recover-password"><?php echo app('translator')->get('translation.Recover_Password'); ?></a>
                        </li>
                        <li><a href="auth-recoverpw-2" key="t-recover-password-2"><?php echo app('translator')->get('translation.Recover_Password'); ?>
                                2</a></li>
                        <li><a href="auth-lock-screen" key="t-lock-screen"><?php echo app('translator')->get('translation.Lock_Screen'); ?></a></li>
                        <li><a href="auth-lock-screen-2" key="t-lock-screen-2"><?php echo app('translator')->get('translation.Lock_Screen'); ?> 2</a>
                        </li>
                        <li><a href="auth-confirm-mail" key="t-confirm-mail"><?php echo app('translator')->get('translation.Confirm_Mail'); ?></a></li>
                        <li><a href="auth-confirm-mail-2" key="t-confirm-mail-2"><?php echo app('translator')->get('translation.Confirm_Mail'); ?> 2</a>
                        </li>
                        <li><a href="auth-email-verification"
                                key="t-email-verification"><?php echo app('translator')->get('translation.Email_verification'); ?></a></li>
                        <li><a href="auth-email-verification-2"
                                key="t-email-verification-2"><?php echo app('translator')->get('translation.Email_verification'); ?> 2</a></li>
                        <li><a href="auth-two-step-verification"
                                key="t-two-step-verification"><?php echo app('translator')->get('translation.Two_step_verification'); ?></a></li>
                        <li><a href="auth-two-step-verification-2"
                                key="t-two-step-verification-2"><?php echo app('translator')->get('translation.Two_step_verification'); ?> 2</a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-file"></i>
                        <span key="t-utility"><?php echo app('translator')->get('translation.Utility'); ?></span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="pages-starter" key="t-starter-page"><?php echo app('translator')->get('translation.Starter_Page'); ?></a></li>
                        <li><a href="pages-maintenance" key="t-maintenance"><?php echo app('translator')->get('translation.Maintenance'); ?></a></li>
                        <li><a href="pages-comingsoon" key="t-coming-soon"><?php echo app('translator')->get('translation.Coming_Soon'); ?></a></li>
                        <li><a href="pages-404" key="t-error-404"><?php echo app('translator')->get('translation.Error_404'); ?></a></li>
                        <li><a href="pages-500" key="t-error-500"><?php echo app('translator')->get('translation.Error_500'); ?></a></li>
                    </ul>
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-share-alt"></i>
                        <span key="t-multi-level"><?php echo app('translator')->get('translation.Multi_Level'); ?></span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li><a href="javascript: void(0);" key="t-level-1-1"><?php echo app('translator')->get('translation.Level_1.1'); ?></a></li>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow"
                                key="t-level-1-2"><?php echo app('translator')->get('translation.Level_1.2'); ?></a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="javascript: void(0);" key="t-level-2-1"><?php echo app('translator')->get('translation.Level_2.1'); ?></a>
                                </li>
                                <li><a href="javascript: void(0);" key="t-level-2-2"><?php echo app('translator')->get('translation.Level_2.2'); ?></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
<?php /**PATH C:\xampp\htdocs\chatbotLaravel\resources\views\extra\extra-layouts\sidebar.blade.php ENDPATH**/ ?>