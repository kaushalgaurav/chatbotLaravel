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
    <style>
        .header-section {
            background: #ffffff;
            transition: all .3s ease;
            height: 76px;
            padding: 16px 0px;
            box-shadow: 0 0 10px 0 rgb(0 0 0 / 10%);
            box-shadow: 0px 1px 4px 0px rgb(0 0 0 / 12%);
        }

        nav.navbar {
            padding: 0px;
        }

        img.nav-logo {
            max-height: 55px;
        }

        .header-section a.navbar-brand {
            padding: 0px;
            margin: 0px;
        }

        span.navbar-toggler-icon {
            /* background: red; */
            color: #fff;
            padding-top: 4px;
            font-size: 20px;
        }

        button.navbar-toggler {
            background: black;
            padding: 2px 10px;
        }

        .navbar-toggler:focus {
            text-decoration: none;
            outline: 0;
            box-shadow: none;
        }

        ul.navbar-nav.headerSlot {
            display: flex;
            justify-content: end;
            width: 100%;
            align-items: center;
            padding: 0px;
            /* margin-left: 85px; */
        }

        ul.navbar-nav li.nav-item {
            margin-left: 20px;
        }

        ul.navbar-nav li.nav-item a.nav-link.fixed {
            color: #000;
        }

        ul.navbar-nav li.nav-item a.nav-link:hover {
            /* background: #fff !important; */
            color: #3751ff;
            letter-spacing: .4px;
        }

        ul.navbar-nav li.nav-item a.nav-link {
            font-size: 15px;
            font-weight: 500;
            padding: 9px 15px;
            border-radius: 30px;
            /* text-transform: uppercase; */
            text-decoration: none;
            transition: all .3s ease;
        }

        .header-right {
            width: 50%;
            justify-content: flex-end;
        }

        .header-right .input-group {
            text-align: right;
            width: 100%;
            display: flex;
            justify-content: end;
        }

        .rightSIde a.nav-link {
            background: #3751FF;
            color: #fff !important;
            padding: 8px 25px !important;
        }

        .rightSIde a.nav-link.account {
            background: none;
            color: #1E266D !important;
            border: 0px;
        }
    </style>
</head>

@section('body')

    <body data-topbar="dark" data-layout="horizontal">
    @show

    <!-- Begin page -->
    <div id="layout-wrapper">
        <header class="header-section">
            <nav class="navbar navbar-expand-lg p-0">
                <div class="container">
                    <a href="{{ route('root') }}" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ URL::asset('build/images/logo.svg') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ URL::asset('build/images/daksh-wht.png') }}" alt="" height="17">
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
                </div>
            </nav>
        </header>
        <div class="main-content">
            <div class="page-content">
                <!-- Start content -->
                <div class="container-fluid">
                    @yield('content')
                </div> <!-- content -->
            </div>
            @include('layouts.footer')
        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    @include('layouts.vendor-scripts')
</body>

</html>
