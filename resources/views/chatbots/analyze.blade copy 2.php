@extends('layouts.inner-layouts')

@section('content')
    <style>
        body[data-layout="horizontal"] .page-content {
            margin-top: 10px !important;
            padding: 30px 0px 30px !important;
        }
    </style>


    <div class="analyze-section">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav-conversations" type="button" role="tab" aria-selected="true">conversations' data</button>
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-metrics" type="button" role="tab" aria-selected="false">metrics</button>
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-analytics" type="button" role="tab" aria-selected="false">flow analytics</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-conversations" role="tabpanel" tabindex="0">
                <div class="card-section-table">
                    <div class="card-body">
                        {{-- <h4 class="card-title">Datatable</h4> --}}
                        <table id="datatable" class="table table-bordered table-striped nowrap w-100">
                            <thead>
                                <tr>
                                    <th>Conversation ID</th>
                                    <th>Bot Message</th>
                                    <th>User Reply</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tableData as $row)
                                    <tr>
                                        <td>{{ $row['conversation_id'] }}</td>
                                        <td>{{ $row['bot_message'] }}</td>
                                        <td class="{{ $row['user_reply'] === 'N/A' ? 'text-danger fw-bold' : '' }}">
                                            {{ $row['user_reply'] ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-metrics" role="tabpanel" tabindex="0">
                <div class="card-section-table">
                    <div class="card-body">
                        <h3>Generic Analytics</h3>
                        <ul>
                            <li>Total Conversations: {{ $analytics['total_conversations'] }}</li>
                            <li>Total Messages: {{ $analytics['total_messages'] }}</li>
                            <li>Bot Messages: {{ $analytics['bot_messages'] }}</li>
                            <li>User Messages: {{ $analytics['user_messages'] }}</li>
                            <li>Average User Messages per Conversation: {{ $analytics['average_user_messages_per_conversation'] }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-analytics" role="tabpanel" tabindex="0">
                <div class="card-section-table">
                    <div class="card-body">
                        <p>Analytics content goes here...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- DataTables CSS & JS CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "responsive": true
            });
        });
    </script>
@endsection
