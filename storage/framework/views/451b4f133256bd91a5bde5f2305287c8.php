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
                        <li><a href="<?php echo e(route('dashboard.index')); ?>" key="t-default"><?php echo app('translator')->get('translation.Default'); ?></a></li>
                    </ul>
                </li>



                <li class="menu-title" key="t-backend"><?php echo app('translator')->get('translation.backend'); ?></li>
                <li>
                    <a href="<?php echo e(route('chatbots.index')); ?>" class="waves-effect">
                        <i class="bx bx-chat"></i>
                        <span key="t-chatbot"><?php echo app('translator')->get('translation.chatbot'); ?></span>
                    </a>
                </li>

                <li class="menu-title" key="t-pages"><?php echo app('translator')->get('translation.Pages'); ?></li>

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
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
<?php /**PATH /var/www/html/chatbotLaravel/resources/views/layouts/sidebar.blade.php ENDPATH**/ ?>