

<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.chatbot'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Backend
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Chatbot
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

  

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
                        <div class="modal-body">
                            <div class="form-group">
                                <lable>Chatbot Name<span>*</span></lable>
                                <input type="text" class="form-control" placeholder="Name">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary ms-3">Submit</button>
                        </div>
                    </div>
                </div>
            </div>



<?php $__env->stopSection(); ?>


      


<?php $__env->startSection('script'); ?>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript">
        $(function() {
            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "<?php echo e(route('chatbots.list')); ?>", // route to get Chatbot data
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\chatbotLaravel\resources\views/chatbots/index.blade.php ENDPATH**/ ?>