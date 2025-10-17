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
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> --}}
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                responsive: true
            });

            const analytics = @json($analytics);
            const responseRate = @json($responseRate);
            const topBotMessages = @json($topBotMessages);
            const allBotMessages = @json($allBotMessages);
            const tableData = @json($tableData);

            // 1️⃣ Total vs Bot vs User Messages
            new ApexCharts(document.querySelector("#messagesChart"), {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: 'Messages',
                    data: [analytics.total_messages, analytics.bot_messages, analytics.user_messages]
                }],
                xaxis: {
                    categories: ['Total', 'Bot', 'User']
                },
                colors: ['#007bff', '#28a745', '#ffc107']
            }).render();

            // 2️⃣ Average User Messages per Conversation
            new ApexCharts(document.querySelector("#averageUserChart"), {
                chart: {
                    type: 'donut',
                    height: 350
                },
                series: [analytics.average_user_messages_per_conversation, analytics.total_messages - analytics.average_user_messages_per_conversation],
                labels: ['Avg User Messages', 'Other Messages'],
                colors: ['#17a2b8', '#6c757d']
            }).render();

            // 3️⃣ Response Rate per Bot Message
            new ApexCharts(document.querySelector("#responseRateChart"), {
                chart: {
                    type: 'pie',
                    height: 350
                },
                series: responseRate.map(r => r.answered),
                labels: responseRate.map(r => r.label)
            }).render();

            // 4️⃣ Top Bot Messages by Replies
            new ApexCharts(document.querySelector("#topBotChart"), {
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: true
                    }
                },
                series: [{
                    name: 'Replies',
                    data: topBotMessages.map(r => r.replies)
                }],
                xaxis: {
                    categories: topBotMessages.map(r => r.label)
                },
                colors: ['#28a745']
            }).render();

            // 5️⃣ Conversation Length Distribution
            const convLengths = tableData.map(row => Object.keys(row).filter(k => k !== 'conversation_id' && row[k] !== 'N/A').length);
            const convIds = tableData.map(row => row.conversation_id);
            new ApexCharts(document.querySelector("#conversationLengthChart"), {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: 'Messages',
                    data: convLengths
                }],
                xaxis: {
                    categories: convIds
                },
                colors: ['#17a2b8'],
                title: {
                    text: 'Messages per Conversation'
                }
            }).render();

            // 6️⃣ Heatmap: Conversations vs Bot Messages
            const heatmapData = allBotMessages.map((msg, i) => ({
                name: msg,
                data: tableData.map(row => row[msg] !== 'N/A' ? 1 : 0)
            }));
            new ApexCharts(document.querySelector("#heatmapChart"), {
                chart: {
                    type: 'heatmap',
                    height: 350
                },
                series: heatmapData,
                colors: ['#00E396']
            }).render();
        });
    </script>
</body>

</html>
