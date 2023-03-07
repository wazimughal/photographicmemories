@extends('adminpanel.admintemplate')
@push('title')
    <title>
        Venue groups| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-3">
                        <h1>View Venue Groups </h1>

                    </div>
                    <div class="col-sm-4">
                        @if ($user->group_id==config('constants.groups.admin'))
                        <a style="width:60%" href="{{ url('/admin/venuegroups/add') }}"
                            class="btn btn-block btn-success btn-lg">Add New <i class="fa fa-plus"></i></a>
                            @endif
                        </div>
                    <div class="col-sm-1">&nbsp;</div>
                    <div class="col-sm-4">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">View</li>
                        </ol>
                    </div>
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
                                <h3 class="card-title">Venue Groups</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                           
                                            <th>Email</th>
                                            <th>Venue Group Name</th>
                                            <th>Manager Name</th>
                                            <th>Mobile Number</th>
                                            <th>Address</th>
                                          
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                            if(count($venuegroupsData)>0)
                                            foreach ($venuegroupsData as $data){
                                                    // p($data['city']['name']);
                                                    // p($data['zipcode']['code']);
                                                    // p($data); die;
                                           
                                            ?>
                                        <tr id="row_{{ $data['id'] }}">
                                            {{-- <td><strong id="name_{{ $data['id'] }}">{{ $data['name'] }}</strong>
                                            </td> --}}
                                            <td id="email_{{ $data['id'] }}">{{ $data['email'] }}</td>
                                            <td id="vg_name_{{ $data['id'] }}">
                                                {{ $data['vg_name'] }}</td>
                                            <td id="vg_manager_name_{{ $data['id'] }}">
                                                {{ $data['vg_manager_name'] }}</td>
                                            <td id="vg_manager_phone_{{ $data['id'] }}">
                                                {{ $data['vg_manager_phone'] }}</td>
                                            <td id="address_{{ $data['id'] }}">
                                                {{ $data['address'] }} </td>
                                            {{-- <td id="vg_description_{{ $data['id'] }}">
                                                {{ $data['vg_description'] }}</td> --}}
                                            {{-- <td id="city_{{ $data['id'] }}">
                                                {{ $data['city'] }}</td> --}}
                                            
                                            <td>
                                                <button
                                                    onClick="do_action({{ $data['id'] }},'viewVenueGroupData',{{ $counter }})"
                                                    class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i>
                                                    View</button>
                                                @if ($user->group_id==config('constants.groups.admin'))
                                                <button onClick="do_action({{ $data['id'] }},'editvenuegroupForm',{{ $counter }})"
                                                    class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                                    Edit</button>
                                               
                                                
                                                
                                                    @if ($data['is_active'] == 1)
                                                    <button
                                                    onClick="do_action({{ $data['id'] }},'trash',{{ $counter }})"
                                                    type="button" class="btn btn-danger btn-block btn-sm"><i
                                                        class="fas fa-trash"></i>
                                                    Trash </button>
                                                    @elseif ($data['is_active'] == 2)
                                                        <button
                                                            onClick="do_action({{ $data['id'] }},'restore',{{ $counter }})"
                                                            type="button" class="btn btn-warning btn-block btn-sm"><i
                                                                class="fas fa-undo"></i>
                                                            Restore</button>
                                                        
                                                    @endif
                                                
                                                @endif
                                            </td>

                                          

                                        </tr>
                                        <?php 
                                            
                                              $counter ++;
                                        }else{
                                            echo '<tr><td class="text-center" colspan="6">No Record Found</td></tr>';
                                        }
                                        ?>




                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Email</th>
                                            <th>Venue Group Name</th>
                                            <th>Manager Name</th>
                                            <th>Mobile Number</th>
                                            <th>Address</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td colspan="8">
                                                <div class="text-right"> {{ $venuegroupsData->links() }}</div>
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
                        <h3 class="card-title"> Venue Group Panel</h3>
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
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('footer-js-css')

    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
        });
            



        // Edit to venuegroup 
        function do_action(id, action_name, counter_id=1 ) {

            var sendInfo = {
                action: action_name,
                counter: counter_id,
                id: id
            };
            if(action_name=='restore' || action_name=='trash'){
            if (!confirm('Are you sure, you want to perform this action?')){
                    return false;
            }
        }
            $.ajax({
                url: "{{ route('venuegroups.ajaxcall') }}/" + id,
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        if(action_name=='restore' || action_name=='trash'){
                            $('#row_'+data.id).html('');
                           
                        }
                        
                        $('#responseData').html(data.formdata);
                        $('.select2bs4').select2({
                            theme: 'bootstrap4'
                        });
                    } else {
                        $('#responseData').html('There is some error, Please try again Later');
                    }
                    if(action_name!='restore' || action_name=='trash')
                    $('#modal-xl-lead').modal('toggle')
                }
            });
            return false;
        }
        // Ajax to Update Lead Data
        function updatevenuegroup(id, counter_id = 1) {
            var formData = ($('#EditvenuegroupForm').formToJson());
            
            $.ajax({
                url: "{{ url('/admin/venuegroups/ajaxcall') }}/" + id,
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
                        $('#vg_manager_phone' + data.id).html(data.vg_manager_phone);
                        $('#vg_manager_name' + data.id).html(data.vg_manager_name);
                        $('#vg_name' + data.id).html(data.vg_name);
                        $('#vg_description' + data.id).html(data.vg_description);
                        $('#address_' + data.id).html(data.address);
                        $('#city_' + data.id).html(data.city);
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
       
    </script>
@endsection
