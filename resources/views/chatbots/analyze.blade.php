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
                        <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    @foreach($tableData as $botMessage => $userResponses)
                                        <th>{{ $botMessage }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $maxRows = max(array_map('count', $tableData));
                                @endphp
                                @for($i = 0; $i < $maxRows; $i++)
                                    <tr>
                                        @foreach($tableData as $userResponses)
                                            <td>{{ $userResponses[$i] ?? '' }}</td>
                                        @endforeach
                                    </tr>
                                @endfor
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
