@extends('layouts.master')

@section('title')
    @lang('translation.chatbot')
@endsection

@section('css')
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Backend
        @endslot
        @slot('title')
            Chatbot
        @endslot
    @endcomponent

    <div class="mb-3">
        <a href="{{ route('chatbots.create') }}" class="btn btn-success">
            <i class="bx bx-plus"></i> Add Chatbot
        </a>
    </div>

    <table class="table table-bordered yajra-datatable">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Description</th>
                <th>Platform</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript">
        $(function() {

            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('chatbots.list') }}", // route to get Chatbot data
                pageLength: 10, // default rows per page
                lengthMenu: [ [10, 100, 500], [10, 100, 500] ], // dropdown options
                columns: [
                    // {data: 'DT_RowIndex', name: '', searchable: false, orderable: false}, // SN No
                    {data: 'DT_RowIndex', name: '', searchable: false, orderable: false},
                    {data: 'name', name: 'name'},
                    {data: 'description', name: 'description'},
                    {data: 'platform', name: 'platform'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

        });
    </script>
@endsection
