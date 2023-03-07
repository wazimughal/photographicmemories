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
                        <h1>View Pencils  </h1>

                    </div>
                    <div class="col-sm-4"><a style="width:60%" href="{{ route('pencils.pencils_form') }}"
                            class="btn btn-block btn-success btn-lg">Add New <i class="fa fa-plus"></i></a></div>
                    
                   
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
                                <form id="search_form" method="GET" action="{{ route('admin.pencil.types','all') }}">
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
												<a href="{{ route('admin.pencil.types','all') }}" style="margin-top: 32px;"
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
                                <table id="example1" class="table table-bordered table-striped">
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
                                            foreach ($pencilData as $data){
                                        
                                            ?>
                                            
                                             @foreach ($data->pencils as $pencil)
                                             <tr id="row_{{ $pencil['id'] }}">
                                            <td id="date_of_event_{{ $pencil['id'] }}">{{date(config('constants.date_formate', $pencil['date_of_event']))}}</td>
                                            <td id="venue_group_{{ $pencil['id'] }}">
                                               @if (isset($pencil['venue_group']))
                                               {{ $pencil['venue_group']['userinfo'][0]['vg_name'] }}
                                               @else
                                               {{$pencil['other_venue_group']}}
                                               @endif
                                               
                                            </td>
                                           
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
                                            <td id="photographer_name_{{ $pencil['id'] }}"> {{  booking_status($pencil['status'])}}</td>

                                            <td>
                                                <a href="{{url('admin/pencils/view')}}/{{$pencil['id']}}" 
                                                    class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i>
                                                    View</a>
                                                {{--     
                                                <a href="{{url('admin/pencils/edit')}}/{{$pencil['id']}}" class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                                    Edit</a>
                                                    
                                                    @if ($pencil['is_active']==1)
                                                    <button
                                                    onClick="do_action({{ $pencil['id'] }},'trash_booking')"
                                                        type="button" class="btn btn-danger btn-block btn-sm"><i
                                                            class="fas fa-trash"></i>
                                                        Delete</button>
                                                    @elseif ($pencil['is_active']==2)
                                                    <button
                                                        onClick="do_action({{ $pencil['id'] }},'restore_booking')"
                                                    type="button" class="btn btn-warning btn-block btn-sm"><i
                                                        class="fas fa-undo"></i>
                                                    Restore</button>        
                                                    @endif --}}
                                                
                                            </td>

                                        

                                        </tr>
                                        @endforeach
                                        <?php 
                                            
                                              $counter ++;
                                        }
                                        else{
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
                                            <th>By</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td colspan="11">
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
        $(function() {
           
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
                action: 'qsearch_pencil',
                type: '{{ $type }}',
                qsearch: searchval
            };

            $.ajax({
                url: "{{ route('pencils.ajaxcall', 1) }}",
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
                        $('#example1').html(data.response);


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
