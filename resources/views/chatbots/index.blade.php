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

  

    <div class="row">
     <div class="col-xl-12">
        <div class="card">
        <div class="card-body">
            <div class="card-header d-flex align-items-center justify-content-between p-0 bg-transparent mb-4">
              <h4 class="card-title">Chatbot</h4>
               <a href="javascript: void(0);" class="btn btn-primary waves-effect waves-light btn-sm" data-bs-toggle="modal" data-bs-target="#addchatbot">Add Chatbot<i class="mdi mdi-plus ms-1"></i></a>
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
        </div>
      </div>
    </div>
  </div>



   <!-- Static add chatbot Modal -->
            <div class="modal fade" id="addchatbot" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Add Chatbot</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="{{ route('chatbots.store') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group mb-4">
                                    <label>Chatbot Name<span>*</span></label>
                                    <input type="text" class="form-control" placeholder="Name of Chatbot" name="name">
                                </div>
                                <div class="form-group">
                                    <label>Chatbot Type<span>*</span></label>
                                    <select class="form-control select-form" name="platform">
                                        <option>select chatbot</option>
                                        <option value="Web">Web</option>
                                        <option value="RASA">RASA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="close" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary ms-3">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>



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
