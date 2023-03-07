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
                    <div class="col-sm-4">
                        <h1>{{ $booking_title }}</h1>

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
                                <h3 class="card-title">{{ $booking_title }}</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
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
                                <form id="search_form" method="GET" action="{{ route('bookings.type','all') }}">
                                    @csrf
                                    <input type="hidden" name="action" value="search_form">
                                    <input type="hidden" id="export_xls" name="export" value="noexport">
                                    @if (isset($_GET['page']) && $_GET['page'] > 0)
                                        <input type="hidden" name="page" value="{{ $_GET['page'] + 1 }}">
                                    @endif
									<div class="wrapper" style="background: #f8f8f8; padding: 20px 10px; margin-bottom: 2%;">
                                    <div class="row">
                                        
                                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 border-right">
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
                                            <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 border-right">
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
                                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 border-right">
                                                <label>Photographer</label>
                                                <div class="input-group date" id="venue_group_ids" data-target-input="nearest">
                                                    <select name="photographers_id[]" class="form-control select2bs4" multiple="multiple" data-placeholder="Select Photographer" style="width: 100%;">
                                                        {!!get_photographer_options($photographers_id)!!}
                                                    </select>
                                                    @error('venue_groups_id')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 border-right">
                                                <label>Venue Group</label>
                                                <div class="input-group date" id="venue_group_ids" data-target-input="nearest">
                                                    <select name="venue_groups_id[]" class="form-control select2bs4" multiple="multiple" data-placeholder="Select Venue Group" style="width: 100%;">
                                                        {!!get_venue_group_options($venue_groups_id)!!}
                                                    </select>
                                                    @error('venue_groups_id')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            {{-- <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 border-right">
                                                <label>Customers</label>
                                                <div class="input-group date" id="customers_ids" data-target-input="nearest">
                                                    <select name="customers_id[]" class="form-control select2bs4" multiple="multiple" data-placeholder="Select Customer" style="width: 100%;">
                                                        {!!get_customer_options($customers_id)!!}
                                                    </select>
                                                    @error('customers_id')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div> --}}
                                           
									<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2"><!--buttons-->
                                       <div class="row">
									   <div class="col-lg-6">
											<button onclick="$('#search_form').submit()" style="margin-top: 32px;"
                                                    type="button" class="btn btn-block btn-primary"><i
                                                        class="fa fa-search"></i>Search</button>
										</div>
                                            <div class="col-lg-6">
												<a href="{{ route('bookings.type','all') }}" style="margin-top: 32px;"
                                                    type="button" class="btn btn-block btn-secondary"><i
                                                        class="fa fa-undo"></i> Cancel</a>
											</div>
										</div>
										</div><!--buttons main-->
                                     </div><!--/row-->
									</div><!--top_container-->

                                    
                                </form>
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-4">
                                        <input class="form-control" onkeyup="search_quote()" type="text" id="qsearch"
                                            name="qsearch" placeholder="Type Customer or Venue Group name to search">
                                    </div>
                                </div>
                                <table id="booking_table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>

                                            <th>Event Date</th>
                                            <th>Venue Group</th>
                                            <th>Customer</th>
                                            <th>Groom</th>
                                            <th>Bride</th>
                                            <th>By</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                            if(count($pencilData)>0)
                                            foreach ($pencilData as $pencil){
                                        //         $pencil=$pencil->toArray();
                                        //        echo  $pencil['customer']['userinfo'][0]['name'];
                                        //    p($pencil);
                                        //     die;
                                            ?>
                                        <tr id="row_{{ $pencil['id'] }}">
                                            <td id="date_of_event_{{ $pencil['id'] }}">
                                                {{ date(config('constants.date_formate'), $pencil['date_of_event']) }}</td>
                                            <td id="venue_group_{{ $pencil['id'] }}">
                                                {{ isset($pencil['venue_group']['userinfo'][0]['vg_name']) ? $pencil['venue_group']['userinfo'][0]['vg_name'] : $pencil['other_venue_group'] }}
                                            </td>

                                            <td id="customer_name_{{ $pencil['id'] }}">
                                                {{ $pencil['customer']['userinfo'][0]['name'] }}</td>
                                            <td id="groom_name_{{ $pencil['id'] }}">
                                                Name:{{ $pencil['groom_name'] }} <br>
                                                Ph:{{ $pencil['groom_mobile'] }}<br>
                                                Add:{{ $pencil['groom_billing_address'] }}
                                            </td>
                                            <td id="bride_name_{{ $pencil['id'] }}">
                                                Name:{{ $pencil['bride_name'] }} <br>
                                                Ph:{{ $pencil['bride_mobile'] }}<br>
                                                Add:{{ $pencil['bride_billing_address'] }}
                                            </td>
                                            <td id="photographer_name_{{ $pencil['id'] }}">@php echo pencilBy($pencil['pencile_by'])@endphp</td>
                                            <td id="photographer_name_{{ $pencil['id'] }}">
                                                {{-- {!! customer_status_badge($pencil['customer_approved']) !!} --}}
                                                {!! current_booking_status($pencil['customer_approved'],$pencil['photographer_status']) !!}
                                            </td>

                                            <td>
                                                @if ($pencil['is_active'] != 2)
                                                    <a href="{{ route('bookings.view', $pencil['id']) }}"
                                                        class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i>
                                                        View</a>
                                                @endif
                                                <div style="margin-top: 5px;">
                                                    @if ($pencil['is_active'] == 2)
                                                        <button onClick="do_action({{ $pencil['id'] }},'restor_booking')"
                                                            type="button" class="btn btn-warning btn-block btn-sm"><i
                                                                class="fas fa-chart-line"></i>
                                                            Restor</button>
                                                    @elseif($pencil['is_active'] == 1)
                                                        <button onClick="do_action({{ $pencil['id'] }},'trash_booking')"
                                                            type="button" class="btn btn-warning btn-block btn-sm"><i
                                                                class="fas fa-chart-line"></i>
                                                            Trash</button>
                                                    @endif

                                                </div>
                                            </td>



                                        </tr>
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
    <!-- DataTables  & Plugins -->

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

       

        function search_quote() {

            searchval = $('#qsearch').val();
            if (searchval.length < 4 && searchval.length > 0) {
                return false;
            }
            if(searchval=='')
            window.location='';

            var sendInfo = {
                action: 'qsearch_bookings',
                type: '{{ $type }}',
                qsearch: searchval
            };

            $.ajax({
                url: "{{ route('bookings.ajaxcall', 1) }}",
                data: sendInfo,
                contentType: 'application/json',
                error: function(err) {
                    //alert(err);
                    //alert(JSON.stringify(err));
                    alert('There is Some Error, Please try again .. !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        // alert('asdasd');;
                        $('#booking_table').html(data.response);


                    } else {
                        $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: data.title,
                            subtitle: 'record',
                            body: data.msg
                        });
                    }

                    //alert('i am here');
                }
            });

        }

        function do_action(id, action_name = '') {
            //var formData = ($('#'+action_name).formToJson());

            var sendInfo = {
                //data: formData,
                action: action_name,
                id: id
            };
            if (!confirm('Are you sure, you want to perform this action?')) {
                return false;
            }

            $.ajax({
                url: "{{ route('bookings.ajaxcall') }}/" + id+"?time={{time()}}",
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        if (action_name == 'trash_booking' || action_name == 'restor_booking')
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
