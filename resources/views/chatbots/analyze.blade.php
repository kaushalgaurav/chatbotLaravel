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
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Office</th>
                                    <th>Age</th>
                                    <th>Start date</th>
                                    <th>Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Tiger Nixon</td>
                                    <td>System Architect</td>
                                    <td>Edinburgh</td>
                                    <td>61</td>
                                    <td>2011/04/25</td>
                                    <td>$320,800</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-metrics" role="tabpanel" tabindex="0">
                <div class="card-section-table">
                    <div class="card-body">
                        <p>Metrics content goes here...</p>
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
