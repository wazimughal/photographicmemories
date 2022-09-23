@extends('adminpanel.admintemplate')
@push('title')
    <title>Add booking | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>View booking</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">View booking</li>
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
                                <h3 class="card-title">View booking</h3>
                            </div>
                            <div class="card-body">


                                <!-- /.row -->
                                @php
                                    $packageDetails = get_package_by_id($bookingData['package_id']);
                                    $overtime = $bookingData['overtime_hours'] * $bookingData['overtime_rate_per_hour'];
                                @endphp
                                   <div class="row form-group">
                                    <div class="col-12">
                                        <div class="alert alert-info alert-dismissible">
                                            <button type="button" class="close"
                                                data-dismiss="alert"
                                                aria-hidden="true">&times;</button>
                                            <h5><i class="icon fa fa-user"></i>
                                                Booking Status!</h5>
                                                {{booking_status_for_msg($bookingData['status'])}}
                                            </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-8">
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Event/Booking Information</strong>
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div class="tab-content">
                                                    <div>

                                                        <div class="row invoice-info">
                                                            <div class="col-sm-4 invoice-col">
                                                                From
                                                                <address>
                                                                    <strong>{{ config('constants.app_name') }}</strong><br>
                                                                    Phone: {{ $user->phone }}<br>
                                                                    Email: {{ $user->email }}<br>
                                                                    {{ $user->address }}<br>
                                                                </address>
                                                            </div>
                                                            <!-- /.col -->
                                                            <div class="col-sm-4 invoice-col">
                                                                To
                                                                <address>
                                                                    <strong>{{ $bookingData['customer']['userinfo'][0]['name'] }}</strong><br>
                                                                    {{ $bookingData['customer']['userinfo'][0]['phone'] }}<br>
                                                                    {{ $bookingData['customer']['userinfo'][0]['email'] }}<br>
                                                                    {{ $bookingData['customer']['userinfo'][0]['address'] }}
                                                                </address>
                                                            </div>
                                                            <div class="col-sm-4 invoice-col">
                                                                Event Details <br>
                                                                <b>Event Date</b> : {{ $bookingData['date_of_event'] }}<br>
                                                                {{ $bookingData['time_of_event'] != '' ? 'Event Date :' . $bookingData['time_of_event'] : '' }}
                                                                @if (isset($bookingData['venue_group']))
                                                                    <br>Venue Group:
                                                                    {{ $bookingData['venue_group']['userinfo'][0]['name'] }}
                                                                @else
                                                                    Venue Group: {{ $bookingData['other_venue_group'] }}
                                                                    <br>
                                                                @endif
                                                                Package : {{ $packageDetails['name'] }} <br>
                                                                Price : ${{ $packageDetails['price'] }} <br>
                                                                Total Cost: $@php echo $totalCost=$packageDetails['price']+ $bookingData['extra_price']+$overtime;@endphp
                                                            </div>

                                                        </div>


                                                    </div>
                                                    <!-- /.tab-pane -->

                                                </div>
                                                <!-- /.tab-content -->
                                            </div><!-- /.card-body -->
                                        </div>
                                        <!-- /.card -->
                                        @if ($bookingData['status'] > 0)
                                            <div class="card">
                                                <div class="card-header p-2">
                                                    <strong> Payment Received </strong>
                                                </div><!-- /.card-header -->
                                                <div class="card-body">
                                                    <div class="tab-content">
                                                        <?php $recievedAmount=0; if($bookingData['invoices']){  $k=1;?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-12">
                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <?php foreach ($bookingData['invoices'] as $key=>$invoice){ 
                                                                            $recievedAmount=$recievedAmount+$invoice['paid_amount'];?>

                                                                        <tr>
                                                                            <td>{{ $k++ }}</td>
                                                                            <td>{{ $invoice['slug'] }}</td>
                                                                            <td>{{ $invoice['payee_name'] }}</td>
                                                                            <td>{{ $invoice['paid_amount'] }}</td>
                                                                            <td>{{ date('d/m/Y H:i:s', strtotime($invoice['created_at'])) }}
                                                                            </td>
                                                                            <td style="width:200px">
                                                                                <span style="width:200px"
                                                                                    class=" btn-success btn-sm">Received</span>
                                                                            </td>
                                                                            <div </tr>

                                                                                <?php }?>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <?php }else{?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-12 text-center">
                                                                No Payment Received Yet!
                                                            </div>
                                                        </div>
                                                        <?php }?>

                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        {{-- This section is for Upload Documents --}}
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Upload Documents </strong>
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div class="tab-content">
                                                    <div class="row form-group">
                                                        <div class="col-1">&nbsp;</div>
                                                        <div class="col-10">
                                                            <div class="row form-group">
                                                                <?php
                                                           $imagesTypes=array('jpg','jpeg','png','gif');
                                                           $excelTypes=array('xls','xlsx');
                                                           $docTypes=array('doc','docx');
                                                           //p($bookingData['files']); die;
                                                           if(!empty($bookingData['files'])){
                                                              foreach($bookingData['files'] as $data){
                                                                if(in_array($data['otherinfo'],$imagesTypes))
                                                                  $thumb_img=$data['path'];
                                                                else if(in_array($data['otherinfo'],$excelTypes))
                                                                  $thumb_img=url('adminpanel/dist/img/xls.jpeg');
                                                                else if(in_array($data['otherinfo'],$docTypes))
                                                                  $thumb_img=url('adminpanel/dist/img/doxx.png');
                                                                else if($data['otherinfo']=='pdf')
                                                                $thumb_img=url('adminpanel/dist/img/pdf.png');
                                                                  ?>
                                                                <div id="file_{{ $data['id'] }}"
                                                                    class="col-3 text-center" style="position: relative;">
                                                                    <label class="">{{ $data['name'] }}</label>
                                                                    <a href="{{ $data['path'] }}" target="_blank"><img
                                                                            class="w-100 shadow-1-strong rounded mb-4 img-thumbnail"
                                                                            src="{{ $thumb_img }}" width="200"
                                                                            alt="Uploaded Image"></a>
                                                                </div>


                                                                <?php 
                                                                }
                                                            }
                                                            else{
                                                                    echo 'No File uploaded';
                                                                }
                                                            ?>

                                                            </div>
                                                            <div class="col-1">&nbsp;</div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        {{-- This section is for Comments --}}
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Notes Section </strong>
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div id="submit_comment_replace">
                                                    @php
                                                       // p($bookingData['comments']);
                                                    @endphp
                                                    @foreach ($bookingData['comments'] as $key=>$comment )
                                                    <div class="row border">
                                                    <div class="col-12">
                                                        <strong>{{$comment['user']['name']}} ({{$comment['slug']}})  </strong> {{date('d/m/Y H:i:s',strtotime($comment['created_at']))}}<br>
                                                        {{$comment['comment']}}
                                                    </div>
                                                    </div>
                                                    @endforeach
                                                  
                                                </div>
                                                @php
                                                    $userData=get_session_value();
                                                    //p($userData);
                                                @endphp
                                                <div class="tab-content">
                                                    <form method="post" id="submit_comment" >
                                                        <input type="hidden" name="group_id" value="{{$user->group_id}}">
                                                        <input type="hidden" name="action" value="submit_comment">
                                                        <input type="hidden" name="slug" value="{{$userData['get_groups']['slug']}}">
                                                            <div class="form-group">
                                                                <label for="inputDescription">Comment</label>
                                                                <textarea id="comments" name="comment" placeholder="Write comment about the Booking" class="form-control" rows="4"></textarea></br>
                                                                <button
                                                                    onclick="do_action({{ $bookingData['id'] }},'submit_comment')"
                                                                    type="button" class="btn btn-success float-right"><i
                                                                        class="far fa-credit-card"></i> Send</button>
                                                            </div>
                                                        </form>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- /.col -->

                                    <div class="col-md-4">
                                        @if ($bookingData['status'] > 0)
                                            <div class="card-header alert-secondary">
                                                <h3 class="card-title">Deposit Requests</h3>
                                            </div>
                                            <?php $recievedAmount=0; if($bookingData['deposite_requests']){  ?>
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <?php foreach ($bookingData['deposite_requests'] as $key=>$ask_for_deposit){ ?>

                                                            <tr>

                                                                <td>Deposit Needed</td>
                                                                <td>{{ $ask_for_deposit['amount'] }} USD</td>
                                                                <td>{{ date('d/m/Y ', strtotime($ask_for_deposit['created_at'])) }}
                                                                </td>
                                                                <td style="width:100px">
                                                                    <span style="width:200px"
                                                                        class=" btn-success btn-sm">Sent!</span>
                                                                </td>
                                                                <div </tr>

                                                                    <?php }?>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php }else{?>
                                            <div class="form-group row">
                                                <div class="col-sm-12 text-center">
                                                    No Payment Request!
                                                </div>
                                            </div>
                                            <?php }?>
                                            <div class="card-header alert-secondary">
                                                <h3 class="card-title">Amount Due</h3>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tbody>
                                                        <tr>
                                                            <th style="width:50%">Package Name:</th>
                                                            <td>{{ $packageDetails['name'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Description:</th>
                                                            <td>{{ $packageDetails['description'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th style="width:50%">Price:</th>
                                                            <td>${{ $packageDetails['price'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Extra Charge</th>
                                                            <td>{{ $bookingData['extra_price'] > 0 ? '$' . $bookingData['extra_price'] : 0 }}
                                                                <br>
                                                                <p>({{ $bookingData['extra_charge_desc'] }})</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Over time:</th>
                                                            <td>${{ $overtime = $bookingData['overtime_hours'] * $bookingData['overtime_rate_per_hour'] }}
                                                                (${{ $bookingData['overtime_rate_per_hour'] }}/Hour)
                                                                <br>
                                                                <p>Staff Worked {{ $bookingData['overtime_hours'] }} hours
                                                                    extra as over time .</p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Total Cost:</th>
                                                            <td>$@php echo $totalCost=$packageDetails['price']+ $bookingData['extra_price']+$overtime;@endphp</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Total Received:</th>
                                                            <td>${{ isset($recievedAmount) ? $recievedAmount : ($recievedAmount = 0) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Due Amount:</th>
                                                            <td>${{ $due_amount = $totalCost - $recievedAmount }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                            </div>
                                            <div class="card-header alert-secondary">
                                                <h3 class="card-title">Assigned Photographers</h3>
                                            </div>

                                            <?php if($assigne_photographers){  $k=1;?>
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <?php foreach ($assigne_photographers as $key=>$photographer){ ?>

                                                            <tr>
                                                                <td>{{ $photographer['userinfo'][0]['firstname'] }}
                                                                    {{ $photographer['userinfo'][0]['lastname'] }}
                                                                </td>
                                                                <td style="width:200px">
                                                                    {!! $photographer['status'] == 0
                                                                        ? '<span style="width:200px"  class=" btn-primary btn-sm">Waiting Response</span>'
                                                                        : '' !!}
                                                                    {!! $photographer['status'] == 1 ? '<span style="width:200px" class=" btn-success btn-sm">Accpted</span>' : '' !!}
                                                                    {!! $photographer['status'] == 2 ? '<span style="width:200px" class=" btn-danger btn-sm">Declined</span>' : '' !!}

                                                                </td>
                                                                <div </tr>

                                                                    <?php }?>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <?php }else{?>
                                            <div class="form-group row">
                                                <div class="col-sm-12">
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <tr>
                                                                <td class="text-center"> No photographer Assigned Yet !
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php }?>
                                        @endif
                                        <div class="card-header alert-secondary">
                                            <h3 class="card-title">Customer Info</h3>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <form method="post" id="customer_update">
                                                    <input type="hidden" name="action" value="customer_update">
                                                    <input type="hidden" name="uid"
                                                        value="{{ $bookingData['customer']['userinfo'][0]['id'] }}">
                                                    <tbody>
                                                        <tr>
                                                            <th style="width:50%">Name</th>
                                                            <td>{{ $bookingData['customer']['userinfo'][0]['firstname'] }}
                                                                {{ $bookingData['customer']['userinfo'][0]['lastname'] }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Email</th>
                                                            <td>{{ $bookingData['customer']['userinfo'][0]['email'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Phone</th>
                                                            <td>{{ $bookingData['customer']['userinfo'][0]['phone'] }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Relationship with Event</th>
                                                            <td>@php echo relation_with_event($bookingData['customer']['userinfo'][0]['relation_with_event']); @endphp</td>
                                                        </tr>
                                                        @if ($bookingData['preferred_photographer_id'] > 0)
                                                            <tr>
                                                                <th>Preffered Photographer</th>
                                                                <td>@php
                                                                    //echo $bookingData['preferred_photographer_id'];
                                                                    $Preferred_photographer = get_user_by_id($bookingData['preferred_photographer_id']);
                                                                    echo $Preferred_photographer['name'];
                                                                @endphp</td>
                                                            </tr>
                                                        @endif

                                                        {{-- <tr>
                                                        <th>Password</th>
                                                        <td>
                                                            <input type="password" readonly disabled name="password" class="form-control "
                                                                placeholder="Password" required value="9889778546"><br>
                                                            
                                                        </td>
                                                    </tr> --}}
                                                    </tbody>
                                                </form>

                                            </table>
                                        </div>
                                        <div class="card-header alert-secondary">
                                            <h3 class="card-title">Groom Info</h3>
                                        </div>
                                        <table class="table">
                                            <form method="post" id="groom_update">
                                                <input type="hidden" name="action" value="groom_update">
                                                <tbody>
                                                    <tr>
                                                        <th style="width:50%">Name</th>
                                                        <td>{{ $bookingData['groom_name'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email</th>
                                                        <td>{{ $bookingData['groom_email'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Home Phone</th>
                                                        <td>{{ $bookingData['groom_home_phone'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Mobile No.</th>
                                                        <td>{{ $bookingData['groom_mobile'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Billing Address</th>
                                                        <td>{{ $bookingData['groom_billing_address'] }}</td>
                                                    </tr>
                                                </tbody>
                                            </form>
                                        </table>
                                        <div class="card-header alert-secondary">
                                            <h3 class="card-title">Bride Info</h3>
                                        </div>
                                        <table class="table">
                                            <form method="post" id="bride_update">
                                                <input type="hidden" name="action" value="bride_update">
                                                <tbody>
                                                    <tr>
                                                        <th style="width:50%">Name</th>
                                                        <td>{{ $bookingData['bride_name'] }} </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email</th>
                                                        <td>{{ $bookingData['bride_email'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Home Phone</th>
                                                        <td>{{ $bookingData['bride_home_phone'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Mobile No.</th>
                                                        <td>{{ $bookingData['bride_mobile'] }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Billing Address</th>
                                                        <td>{{ $bookingData['bride_billing_address'] }}</td>
                                                    </tr>
                                                </tbody>
                                            </form>
                                        </table>



                                        {{-- <button type="button" class="btn btn-success float-right"><i
                                                class="far fa-credit-card"></i> Submit
                                            Payment
                                        </button>
                                        <button type="button" class="btn btn-primary float-right"
                                            style="margin-right: 5px;">
                                            <i class="fas fa-download"></i> Generate PDF
                                        </button> --}}



                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->




                                <!-- /.row -->
                            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('head-js-css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- dropzonecss -->
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/dropzone/min/dropzone.min.css') }}">
@endsection
@section('footer-js-css')
    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ url('adminpanel/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- dropzonejs -->
    <script src="{{ url('adminpanel/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script>
        function do_action(id, action_name = '') {
            var formData = ($('#' + action_name).formToJson());
            
            var sendInfo = {
                data: formData,
                action: action_name,
                id: id
            };

            if(action_name=='submit_comment'){
                if($('#comments').val()=='')
                return false;
            }

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
                   
                    $('#'+action_name+'_replace').append(data.response);
                    $('#comments').val('');
                    //console.log('result :'+action_name);
                    if (data.error == 'No') {
                        $('#file_' + id).remove();
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
