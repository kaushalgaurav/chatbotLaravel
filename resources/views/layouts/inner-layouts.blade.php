<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title> @yield('title') | Skote - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico') }}">
    @include('layouts.head-css')
  
</head>

@section('body')

    <body data-topbar="dark" data-layout="horizontal">
    @show

    <!-- Begin page -->
    <div id="layout-wrapper">
          <header class="header-section">
            <nav class="navbar navbar-expand-lg p-0">
                <a href="{{ route('root') }}" class="logo">
                    <span class="logo-sm">
                        <img src="{{ URL::asset('build/images/logo/logo.svg') }}" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ URL::asset('build/images/logo/nimbli_ai_logo.png') }}" alt="" height="25">
                    </span>
                </a>
                     <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"><i class="fa fa-bars"></i>
                        </span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav headerSlot">
                            <li class="nav-item">
                                <a class="nav-link active" href="#">Create</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">Review</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Share</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Analytics</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Dashboard</a>
                            </li>
                        </ul>
                        <form class="d-flex header-right">
                            <div class="input-group">
                                <button class="btn btn-primary" type="button" id="button-addon2">Publish</button>
                            </div>
                    </div>
            </nav>
        </header>

        <div class="main-content">
            <div class="page-content">
                <!-- Start content -->
                    @yield('content')
                 <!-- content -->
            </div>
            @include('layouts.footer')
        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    @include('layouts.vendor-scripts')

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable();
        } );
    </script>
</body>

</html>
