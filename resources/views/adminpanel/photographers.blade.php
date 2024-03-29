@extends('adminpanel.admintemplate')
@push('title')
    <title>
        photographers| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <h1>View Photographers </h1>

                    </div>
                    <div class="col-sm-2"><a style="width:60%" href="{{ url('/admin/photographers/add') }}"
                            class="btn btn-block btn-success btn-lg">Add New <i class="fa fa-plus"></i></a></div>
                    <div class="col-sm-1">&nbsp;</div>
                    
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">


                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">photographers</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile Number</th>
                                            <th>Address</th>
                                            <th>City</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                           
                                            foreach ($photographersData as $data){
                                     
                                           
                                            ?>
                                        <tr id="row_{{ $data['id'] }}">
                                            <td><strong id="name_{{ $data['id'] }}">{{ $data['name'] }}</strong>
                                            </td>
                                            <td id="email_{{ $data['id'] }}">{{ $data['email'] }}</td>
                                            <td id="phone_{{ $data['id'] }}">
                                                {{ $data['phone'] }}</td>
                                                 <td id="address_{{ $data['id'] }}">
                                                    {{ $data['address'] }}</td>
                                                    <td id="city_{{ $data['id'] }}">
                                                        {{ $data['City']['name'] }}</td>
                                           
                                            
                                            <td>

                                                <button onClick="editphotographerForm({{ $data['id'] }},{{ $counter }})"
                                                    class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                                    Edit</button>
                                                <button onClick="view_photographer({{ $data['id'] }},{{ $counter }})"
                                                    class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i>
                                                    View</button>
                                                {{-- <button
                                                    onClick="changeStatus({{ $data['id'] }},{{ $counter }},'delete')"
                                                    type="button" class="btn btn-danger btn-block btn-sm"><i
                                                        class="fas fa-trash"></i>
                                                    Delete</button>
                                                <div style="margin-top: 5px;" id="status_action_btn_{{ $data['id'] }}">
                                                    @if ($data['is_active'] == 1)
                                                        <button
                                                            onClick="changeStatus({{ $data['id'] }},{{ $counter }},'trash')"
                                                            type="button" class="btn btn-warning btn-block btn-sm"><i
                                                                class="fas fa-chart-line"></i>
                                                            Trash</button>
                                                        
                                                    @endif
                                                </div> --}}
                                            </td>

                                            </td>

                                        </tr>
                                        <?php 
                                            
                                              $counter ++;
                                        }
                                        ?>




                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Mobile Number</th>
                                            <th>Address</th>
                                           <th>City</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td colspan="8">
                                                <div class="text-right"> {{ $photographersData->links() }}</div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                {{-- Pagination --}}

                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


    <div class="modal fade" id="modal-xl-lead">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"> photographers Panel</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <div id="responseData">
                            This is the Body of modal
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
@endsection

@section('head-js-css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2/css/select2.min.css') }}">
    
@endsection

@section('footer-js-css')
   
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
        });
    
        function view_photographer(id) {
            var sendInfo = {
                action: 'view_photographer',
                id: id
            };
            $.ajax({
                url: "{{ url('/admin/photographers/ajaxcall') }}/" + id,
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        $('#responseData').html(data.res);

                    } else {
                        $('#responseData').html('There is some error, Please try again Later');
                    }
                    $('#modal-xl-lead').modal('toggle')
                }
            });
            return false;
        }
   
        function updateForm(id, counter_id = 1) {
            var sendInfo = {
                action: 'updateLeadForm',
                counter: counter_id,
                status: status,
                id: id
            };
            $.ajax({
                url: "{{ url('/admin/photographers/ajaxcall') }}/" + id,
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        $('#responseData').html(data.formdata);
                        $('.select2bs4').select2({
                            theme: 'bootstrap4'
                        });
                    } else {
                        $('#responseData').html('There is some error, Please try again Later');
                    }
                    $('#modal-xl-lead').modal('toggle')
                }
            });
            return false;
        }
        // Ajax to Update Lead Data
        function updateLead(id, counter_id = 1) {
            var formData = ($('#EditLeadForm').formToJson());
            // console.log(formData);
            $.ajax({
                url: "{{ url('/admin/photographers/ajaxcall') }}/" + id,
                data: formData,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        console.log(data);
                        $('#name_' + data.id).html(data.name);
                        $('#venue_group_name_' + data.id).html(data.venue_group_name);
                        $('#lead_type_title_' + data.id).html(data.lead_type_tile);
                        // $('#row_' + data.id).removeClass('odd');
                        // $('#row_' + data.id).removeClass('even');
                        // $('#row_' + data.id).addClass('alert-info');
                        // // Close modal and success Message
                        $('#modal-xl-lead').modal('toggle')


                        $(document).Toasts('create', {
                            class: 'bg-success',
                            title: data.title,
                            subtitle: 'record',
                            body: data.msg
                        });


                    } else {
                        $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: data.name,
                            subtitle: 'record',
                            body: data.msg
                        });
                    }
                }
            });
            return false;
        }

        
        function editphotographerForm(id, counter_id = 1) {
     

            var sendInfo = {
                action: 'editphotographerForm',
                counter: counter_id,
                id: id
            };
            $.ajax({
                url: "{{ url('/admin/photographers/ajaxcall') }}/" + id,
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        $('#responseData').html(data.formdata);
                        $('.select2bs4').select2({
                            theme: 'bootstrap4'
                        });
                    } else {
                        $('#responseData').html('There is some error, Please try again Later');
                    }
                    $('#modal-xl-lead').modal('toggle')
                }
            });
            return false;
        }
        // Ajax to Update Lead Data
        function updatephotographer(id, counter_id = 1) {
            var formData = ($('#EditphotographerForm').formToJson());
            // console.log(formData);
            $.ajax({
                url: "{{ url('/admin/photographers/ajaxcall') }}/" + id,
                data: formData,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        console.log(data);
                        
                        $('#name_' + data.id).html(data.name);
                        $('#phone_' + data.id).html(data.phone_);
                        $('#unitnumber_' + data.id).html(data.unitnumber);
                        $('#homeaddress_' + data.id).html(data.homeaddress);
                        $('#zipcode_' + data.id).html(data.zipcode);
                        $('#city_' + data.id).html(data.city);

                        
                        // $('#row_' + data.id).removeClass('odd');
                        // $('#row_' + data.id).removeClass('even');
                        // $('#row_' + data.id).addClass('alert-info');
                        // // Close modal and success Message
                        $('#modal-xl-lead').modal('toggle')


                        $(document).Toasts('create', {
                            class: 'bg-success',
                            title: data.title,
                            subtitle: 'record',
                            body: data.msg
                        });


                    } else {
                        $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: data.name,
                            subtitle: 'record',
                            body: data.msg
                        });
                    }
                }
            });
            return false;
        }
    
     
        $(function() {
       
            $('.current_status').on('change', function() {
                var status = $(this).val();
                var id = $(this).attr('dataid');
                var counter_id = $(this).attr('datacounter');
                counter_id = 1;

             
                    alertmsg = 'move in Cancelled'

                if (confirm("Are you sure you want to " + alertmsg + " this?")) {

                    var sendInfo = {
                        action: 'changestatus',
                        counter: counter_id,
                        status: status,
                        alertmsg: alertmsg,
                        id: id
                    };

                    $.ajax({
                        url: "{{ url('/admin/photographers/ajaxcall/') }}/" + id,
                        data: sendInfo,
                        contentType: 'application/json',
                        error: function() {
                            alert('There is Some Error, Please try again !');
                        },
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            if (data.error == 'No') {
                                // Close modal and success Message
                                $('#status' + id).html(data.status_btn);
                                // $('#status_action_btn_' + id).html(data.status_action_btn);

                                $(document).Toasts('create', {
                                    class: 'bg-success',
                                    title: data.title,
                                    subtitle: 'record',
                                    body: data.msg
                                });


                            } else {
                                $(document).Toasts('create', {
                                    class: 'bg-danger',
                                    title: data.title,
                                    subtitle: 'record',
                                    body: data.msg
                                });
                            }
                            console.log(data);
                            //alert('i am here');
                        }

                    });

                }

            });
        });

        function changeStatus(id, counter_id, action) {

            if (action == 'trash')
                alertMsg = 'Are you sure you want to Trash this?';
            else if (action == 'delete')
                alertMsg = 'Are you sure you want to Delete this?';

            if (confirm(alertMsg)) {

                var sendInfo = {
                    action: action,
                    counter: counter_id,
                    id: id
                };

                $.ajax({
                    url: "{{ url('/admin/photographers/ajaxcall/') }}/" + id,
                    data: sendInfo,
                    contentType: 'application/json',
                    error: function() {
                        alert('There is Some Error, Please try again !');
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error == 'No') {
                            $('#row_' + id).remove();
                            $(document).Toasts('create', {
                                class: 'bg-success',
                                title: data.title,
                                subtitle: 'record',
                                body: data.msg
                            });


                        } else {
                            $(document).Toasts('create', {
                                class: 'bg-danger',
                                title: data.title,
                                subtitle: 'record',
                                body: data.msg
                            });
                        }
                        console.log(data);
                        //alert('i am here');
                    }

                });

            }

        }
    </script>
@endsection
