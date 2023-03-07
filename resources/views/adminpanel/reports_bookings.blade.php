@extends('adminpanel.admintemplate')
@push('title')
    <title>
        Bookings| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-2">
                        <h1>View Bookings </h1>

                    </div>
                    {{-- <div class="col-sm-2"><a style="width:60%" href="{{ url('/admin/bookings/add') }}"
                            class="btn btn-block btn-success btn-lg">Add New <i class="fa fa-plus"></i></a></div> --}}
                    <div class="col-sm-2">&nbsp;</div>
                    <div class="col-sm-6">
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
                                <h3 class="card-title">bookings</h3>
                            </div>
                            <!-- /.card-header -->
                            @php
                                //p($_GET);
                                $venue_groups_id=$customers_id=$photographers_id=$bookings_status=array();

                                if(isset($_GET['customers_id']) && !empty($_GET['customers_id']))
                                $customers_id=$_GET['customers_id'];

                                if(isset($_GET['venue_groups_id']) && !empty($_GET['venue_groups_id']))
                                $venue_groups_id=$_GET['venue_groups_id'];

                                if(isset($_GET['photographers_id']) && !empty($_GET['photographers_id']))
                                $photographers_id=$_GET['photographers_id'];

                                if(isset($_GET['bookings_status']) && !empty($_GET['bookings_status']))
                                $bookings_status=$_GET['bookings_status'];
                                // p($photographers_id);
                            @endphp
                            <div class="card-body">

                                <form id="search_form" method="GET" action="{{ route('reports.bookings') }}">
                                    @csrf
                                    <input type="hidden" name="action" value="search_form">
                                    <input type="hidden" id="export_xls" name="export" value="noexport">
                                    @if (isset($_GET['page']) && $_GET['page']>0)
                                    <input type="hidden" name="page" value="{{($_GET['page']+1)}}">    
                                    @endif
                                    
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <td>
                                            <span>Select Photographer</span>
                                            <select name="photographers_id[]" class="form-control select2bs4" multiple="multiple" data-placeholder="Select Photographer" style="width: 100%;">
                                                {!!get_photographer_options($photographers_id)!!}
                                            </select>
                                        </td>
                                        <td>
                                            <span>Select Venue Group</span>
                                            <select name="venue_groups_id[]" class="form-control select2bs4" multiple="multiple" data-placeholder="Select Venue Group" style="width: 100%;">
                                                {!!get_venue_group_options($venue_groups_id)!!}
                                            </select>
                                        </td>
                                        <td>
                                            <span>Select Customer</span>
                                            <select name="customers_id[]" class="form-control select2bs4" multiple="multiple" data-placeholder="Select Customer" style="width: 100%;">
                                            {!!get_customer_options($customers_id)!!}
                                            </select>
                                        </td>
                                        <td>
                                            <span>Select Status</span>
                                            <select name="bookings_status[]" class="form-control select2bs4" multiple="multiple" data-placeholder="Select Quote Status" style="width: 100%;">
                                                {!!get_booking_status_options($bookings_status)!!}
                                                </select>

                                        </td>
                                        <td><button onclick="$('#search_form').submit()" style="margin-top: 24px;" type="button" class="btn btn-block btn-primary"><i class="fa fa-search"></i>Search</button></td>
                                        <td><button onclick="$('#export_xls').val('export_xls');$('#search_form').submit()" style="margin-top: 24px;" type="button" class="btn btn-block btn-success"><i class="fa fa-download"></i> Excel</button></td>
                                    </tr>
                                </table>
                                </form>
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            
                                            <th>Event Date</th>
                                            <th>Venue Group</th>
                                            <th>Customer</th>
                                            <th>Groom </th>
                                            <th>Bride </th>
                                            <th> By</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                            
                                            foreach ($pencilData as $data){
                                        
                                            ?>
                                            @foreach ($data['pencils'] as $pencil)
                                                
                                           
                                        <tr id="row_{{ $pencil['id'] }}">
                                          
                                           
                                            <td id="date_of_event_{{ $pencil['id'] }}">{{ date(config('constants.date_formate'),$pencil['date_of_event'])}}</td>
                                            <td id="venue_group_{{ $pencil['id'] }}">
                                                {{ (isset($pencil['venue_group']['userinfo'][0]['vg_name']))?$pencil['venue_group']['userinfo'][0]['vg_name']: $pencil['other_venue_group'];}}</td>
                                           
                                            <td id="customer_name_{{ $pencil['id'] }}">
                                                {{ $pencil['customer']['userinfo'][0]['name'] }}</td>
                                                <td id="groom_name_{{ $pencil['id'] }}">
                                                    Name:{{ $pencil['groom_name'] }}<br>
                                                     Ph:{{ $pencil['groom_mobile'] }}<br>
                                                     Add:{{ $pencil['groom_billing_address'] }}<br>
                                            
                                                </td>
                                                <td id="bride_name_{{ $pencil['id'] }}">
                                                    Name:{{ $pencil['bride_name'] }}<br>
                                                     Ph:{{ $pencil['bride_mobile'] }}<br>
                                                     Add:{{ $pencil['bride_billing_address'] }}<br>
                                            
                                                </td>
                                            <td id="photographer_name_{{ $pencil['id'] }}">@php echo pencilBy($pencil['pencile_by'])@endphp</td>
                                            <td id="photographer_name_{{ $pencil['id'] }}">{{booking_status($pencil['status'])}}</td>

                                            <td>
                                                {{-- @if ($user->group_id==config('constants.groups.admin'))
                                                <a href="{{url('admin/bookings/edit')}}/{{$pencil['id']}}" class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                                    Edit</a>  
                                                @elseif ($user->group_id==config('constants.groups.photographer'))
                                                <a href="{{route('booking.photos',$pencil['id'])}}" class="btn btn-info btn-block btn-sm"><i class="fas fa-upload"></i>
                                                    Upload Photos</a>                                                  
                                                @endif --}}
                                                
                                                
                                                    <a href="{{url('admin/bookings/view')}}/{{$pencil['id']}}" 
                                                    class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i>
                                                    View</a>
                                                {{-- <button
                                                     onClick="do_action({{ $pencil['id'] }},'delete_booking')"
                                                    type="button" class="btn btn-danger btn-block btn-sm"><i
                                                        class="fas fa-trash"></i>
                                                    Delete</button> --}}
                                            </td>

                                        

                                        </tr>
                                        @endforeach
                                        <?php 
                                            
                                              $counter ++;
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Event Date</th>
                                            <th>Venue Group</th>
                                            <th>Customer</th>
                                            <th>Groom </th>
                                            <th>Bride </th>
                                            <th> By</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        @php
                                        $query=$req->all();
                                        @endphp
                                        <tr>
                                            <td colspan="10">
                                                <div class="text-right"> {{ $pencilData->appends($query)->links() }}</div>
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
                        <h3 class="card-title"> bookings Panel</h3>
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
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('footer-js-css')
    <!-- DataTables  & Plugins -->
    <script src="{{ url('adminpanel/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ url('adminpanel/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
            
        });
        function do_action(id, action_name='') {
    //var formData = ($('#'+action_name).formToJson());
    
    var sendInfo = {
        //data: formData,
        action:action_name,
        id: id
    };

    $.ajax({
        url: "{{ url('/admin/bookings/ajaxcall/') }}/" + id,
        data: sendInfo,
        contentType: 'application/json',
        error: function() {
            alert('There is Some Error, Please try again !');
        },
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error == 'No') {
                if(action_name=='trash_booking' || action_name=='restor_booking' || action_name=='delete_booking')
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
        }
    });

}
    
    </script>
@endsection
