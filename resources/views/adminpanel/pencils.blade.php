@extends('adminpanel.admintemplate')
@push('title')
    <title>
        Pencils| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-2">
                        <h1>View Pencils </h1>

                    </div>
                    <div class="col-sm-2"><a style="width:60%" href="{{ url('/admin/pencils/add') }}"
                            class="btn btn-block btn-success btn-lg">Add New <i class="fa fa-plus"></i></a></div>
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
                                <h3 class="card-title">Pencils</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Groom Name (Mobile)</th>
                                            <th>Groom Billing Address</th>
                                            <th>Bride Name (Mobile)</th>
                                            <th>Bride Billing Address</th>
                                            <th>Event Date</th>
                                            <th>Venue Group</th>
                                            <th>Customer</th>
                                            <th>By</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                            
                                            foreach ($pencilData as $pencil){
                                        //         $pencil=$pencil->toArray();
                                        //        echo  $pencil['customer']['userinfo'][0]['name'];
                                        //    p($pencil);
                                        //     die;
                                            ?>
                                        <tr id="row_{{ $pencil['id'] }}">
                                            <td><strong id="groom_name_{{ $pencil['id'] }}">{{ $pencil['groom_name'] }} ({{ $pencil['groom_mobile'] }})</strong></td>
                                            <td id="groom_billing_address_{{ $pencil['id'] }}">{{ $pencil['groom_billing_address'] }}
                                            </td>
                                            
                                            <td><strong id="bride_name_{{ $pencil['id'] }}">{{ $pencil['bride_name'] }} ({{ $pencil['bride_mobile'] }})</strong></td>
                                            <td id="bride_billing_address_{{ $pencil['id'] }}">{{ $pencil['bride_billing_address'] }}
                                            </td>
                                            <td id="date_of_event_{{ $pencil['id'] }}">{{ $pencil['date_of_event']}}</td>
                                            <td id="venue_group_{{ $pencil['id'] }}">
                                               @if (isset($pencil['venue_group']))
                                               {{ $pencil['venue_group']['userinfo'][0]['name'] }}
                                               @else
                                               {{$pencil['other_venue_group']}}
                                               @endif
                                               
                                            </td>
                                           
                                            <td id="customer_name_{{ $pencil['id'] }}">
                                                {{ $pencil['customer']['userinfo'][0]['name'] }}</td>
                                            <td id="photographer_name_{{ $pencil['id'] }}">@php echo pencilBy($pencil['pencile_by'])@endphp</td>
                                            <td id="photographer_name_{{ $pencil['id'] }}"> {{  booking_status($pencil['status'])}}</td>

                                            <td>
                                                @if ($user->group_id==config('constants.groups.admin'))
                                                <a href="{{url('admin/bookings/add')}}/{{$pencil['id']}}" class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i> Booking</a>  
                                                @endif
                                                
                                                <a href="{{url('admin/pencils/edit')}}/{{$pencil['id']}}" class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                                    Edit</a>
                                                    <a href="{{url('admin/pencils/view')}}/{{$pencil['id']}}" 
                                                    class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i>
                                                    View</a>
                                                <button
                                                onClick="do_action({{ $pencil['id'] }},'delete_pencil')"
                                                    type="button" class="btn btn-danger btn-block btn-sm"><i
                                                        class="fas fa-trash"></i>
                                                    Delete</button>
                                                
                                            </td>

                                        

                                        </tr>
                                        <?php 
                                            
                                              $counter ++;
                                        }
                                        ?>




                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Groom Name (Mobile)</th>
                                            <th>Groom Billing Address</th>
                                            <th>Bride Name (Mobile)</th>
                                            <th>Bride Billing Address</th>
                                            <th>Event Date</th>
                                            <th>Venue Group</th>
                                            <th>Customer</th>
                                            <th> By</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td colspan="10">
                                                <div class="text-right"> {{ $pencilData->links() }}</div>
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
                        <h3 class="card-title"> pencils Panel</h3>
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

@section('footer-js-css')
   
    <script>
        
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
                if(action_name=='delete_pencil')
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
