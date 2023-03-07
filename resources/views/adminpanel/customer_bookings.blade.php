@extends('adminpanel.admintemplate')
@push('title')
    <title>
        Customer Bookings| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <h1>{{$booking_title}} </h1>

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
                                <h3 class="card-title">{{$booking_title}}</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form id="search_form" method="GET" action="{{ route($route,$type) }}">
                                    @csrf
                                    <input type="hidden" name="action" value="search_form">
                                    <input type="hidden" id="export_xls" name="export" value="noexport">
                                    @if (isset($_GET['page']) && $_GET['page'] > 0)
                                        <input type="hidden" name="page" value="{{ $_GET['page'] + 1 }}">
                                    @endif
									<div class="wrapper" style="background: #f8f8f8; padding: 20px 10px; margin-bottom: 2%;">
                                    <div class="row">
                                        
                                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 border-right">
                                                <label>From</label>
                                                <div class="input-group date" id="from_date" data-target-input="nearest">
                                                    <input id="input_from_date" type="text"
                                                        value="{{ isset($_GET['from_date']) ? $_GET['from_date'] : '' }}"
                                                        name="from_date" placeholder="From date"
                                                        class="form-control datetimepicker-input"
                                                        data-target="#from_date" />
                                                    <div class="input-group-append" data-target="#from_date"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                    @error('from_date')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 border-right">
                                                <label>To</label>
                                                <div class="input-group date" id="to_date" data-target-input="nearest">
                                                    <input id="input_to_date" type="text"
                                                        value="{{ isset($_GET['to_date']) ? $_GET['to_date'] : '' }}"
                                                        name="to_date" placeholder="To Date"
                                                        class="form-control datetimepicker-input" data-target="#to_date" />
                                                    <div class="input-group-append" data-target="#to_date"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                    @error('to_date')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                           
									<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3"><!--buttons-->
                                       <div class="row">
									   <div class="col-lg-6">
											<button onclick="$('#search_form').submit()" style="margin-top: 32px;"
                                                    type="button" class="btn btn-block btn-primary"><i
                                                        class="fa fa-search"></i>Search</button>
										</div>
                                            <div class="col-lg-6">
												<a href="{{ route($route,$type) }}" style="margin-top: 32px;"
                                                    type="button" class="btn btn-block btn-secondary"><i
                                                        class="fa fa-undo"></i> Cancel</a>
											</div>
										</div>
										</div><!--buttons main-->
                                     </div><!--/row-->
									</div><!--top_container-->

                                    
                                </form>
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Event Date</th>
                                            <th>Venue Group</th>
                                            <th>Customer</th>
                                            <th>Groom</th>
                                            <th>Bride</th>
                                            {{-- <th>Customer Status</th>
                                            <th>Update Status</th> --}}
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                            //p($pencilData->toArray());
                                            if(count($pencilData)>0)
                                            foreach ($pencilData as $data){
                                        
                                            ?>
                                            @foreach ($data['bookings'] as $pencil)
                                                
                                           
                                        <tr id="row_{{ $pencil['id'] }}">
                                           
                                            
                                            <td id="date_of_event_{{ $pencil['id'] }}">{{date(config('constants.date_formate'), $pencil['date_of_event'])}}</td>
                                            <td id="venue_group_{{ $pencil['id'] }}">
                                                {{ (isset($pencil['venue_group']['userinfo'][0]['vg_name']))?$pencil['venue_group']['userinfo'][0]['vg_name']: $pencil['other_venue_group'];}}</td>
                                           
                                            <td id="customer_name_{{ $pencil['id'] }}">
                                                {{ $pencil['customer']['userinfo'][0]['name'] }}</td>
                                            <td id="groom_name_{{ $pencil['id'] }}">
                                                Name:{{ ($pencil['groom_name']) }}<br>
                                                Ph:{{ $pencil['groom_mobile'] }}<br>
                                                Add:{{ $pencil['groom_billing_address'] }}
                                            </td>
                                            <td id="bride_name_{{ $pencil['id'] }}">
                                                Name:{{ ($pencil['bride_name']) }}<br>
                                                Ph: {{ $pencil['bride_mobile'] }}<br>
                                                Add: {{ $pencil['bride_billing_address'] }}
                                            </td>

                                            {{-- <td id="customer_status_{{ $pencil['id'] }}">
                                                {!!customer_status_badge($pencil['customer_approved'])!!}
                                            </td>
                                            <td>
                                                <select data_booking_id="{{$pencil['id']}}" datacounter="{{ $counter++ }}" dataid="{{ $pencil['status'] }}" class="select2bs4 form-control update_customer_status">
                                                    <option {{($pencil['customer_approved']==0)?'Selected':''}} value="0">Pending</option>
                                                    <option {{($pencil['customer_approved']==1)?'Selected':''}} value="1">Approve</option>
                                                    <option {{($pencil['customer_approved']==2)?'Selected':''}} value="2">Reject</option>
                                                </select>
                                                 
                                            </td> --}}
                                            <td>
                                                    <a href="{{route('bookings.view',$pencil['id'])}}" 
                                                    class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i>
                                                    View</a>
                                                
                                            </td>

                                        

                                        </tr>
                                        @endforeach
                                        <?php 
                                            
                                              $counter ++;
                                        }else{
                                            echo '<tr><td class="text-center" colspan="8">No Record Found</td></tr>';
                                        }
                                        ?>




                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Event Date</th>
                                            <th>Venue Group</th>
                                            <th>Customer</th>
                                            <th>Groom</th>
                                            <th>Bride</th>
                                            {{-- <th>Customer Status</th>
                                            <th>Update Status</th> --}}
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
           $('#from_date').datetimepicker({
               format: 'L'
           });
           $('#to_date').datetimepicker({
               format: 'L'
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
        url: "{{ route('bookings.ajaxcall') }}/" + id,
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

}// This is to change photographer Status
$(function() {
       
       $('.update_customer_status').on('change', function() {
           var status = $(this).val();
           var id = $(this).attr('dataid');
           var booking_id = $(this).attr('data_booking_id');
           var counter_id = $(this).attr('datacounter');
           
           alertmsg='take action of'
           if (status == '0'){
            alertmsg = 'move in waiting';
            current_status="Waiting Response";
           }else if (status == '1'){
            alertmsg = 'accept';
            current_status="Accepted";
           }else if (status == '2'){
            alertmsg = 'move in declined';
            current_status="Declined";
           }
            

           if (confirm("Are you sure you want to " + alertmsg + " this?")) {

               var sendInfo = {
                   action: 'update_customer_status',
                   counter: counter_id,
                   booking_id: booking_id,
                   status: status,
                   alertmsg: alertmsg,
                   current_status: current_status,
                   id: id
               };
                $('#_loader').show();
               $.ajax({
                   url: "{{ route('bookings.ajaxcall') }}/" + id,
                   data: sendInfo,
                   contentType: 'application/json',
                   error: function() {
                       alert('There is Some Error, Please try again !');
                   },
                   type: 'GET',
                   dataType: 'json',
                   success: function(data) {
                       if (data.error == 'No') {
                       
                        $('#customer_status_'+data.booking_id).html(data.customer_status_msg);

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
                       $('#_loader').hide();
                   }

               });

           }

       });
   });
// End to change photographer status
    
    </script>
@endsection
