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
                        <h1>Upload Photos to this Booking</h1>
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
                                <h3 class="card-title">Upload Photos to this Booking</h3>
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
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">&times;</button>
                                            <h5><i class="icon fa fa-user"></i>
                                                Booking Status!</h5>
                                            {{ booking_status_for_msg($bookingData['status']) }}
                                        </div>
                                    </div>
                                </div>
                                @if ($bookingData['status'] > 0)

                                    <div class="card">
                                        <div class="card-header p-2">
                                            <strong> Upload the Photos </strong>
                                            @if ($user->group_id == config('constants.groups.admin'))
                                                <div class="row">
                                                    <div class="col-9">&nbsp;</div>
                                                    <div class="col-3">
                                                        <form id="photo_gallery_status" action="" method="POST">
                                                            <input type="hidden" name="action"
                                                                value="photo_gallery_status">
                                                            <div class="form-group clearfix">
                                                                <div class="icheck-success d-inline">
                                                                    <input type="radio"
                                                                        {{ $bookingData['gallery_status'] == 1 ? 'checked' : '' }}
                                                                        onclick="do_action({{ $bookingData['id'] }},'photo_gallery_status','active_event2')"
                                                                        value="{{ base64_encode(1) }}" name="active_event"
                                                                        id="active_event1">
                                                                    <label for="active_event1"> Activate
                                                                    </label>
                                                                </div>
                                                                <div class="icheck-success d-inline">
                                                                    <input type="radio"
                                                                        {{ $bookingData['gallery_status'] == 0 ? 'checked' : '' }}
                                                                        onclick="do_action({{ $bookingData['id'] }},'photo_gallery_status','active_event1')"
                                                                        value="{{ base64_encode(0) }}" name="active_event"
                                                                        id="active_event2">
                                                                    <label for="active_event2"> De-activate
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>
                                            @endif
                                        </div><!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="tab-content">
                                                <div class="row form-group">
                                                    <div class="col-1">&nbsp;</div>
                                                    <div class="col-10">
                                                        <div class="row form-group">
                                                            @if ($bookingData['gallery_status'] == 1 ||
                                                                $user->group_id == config('constants.groups.admin') ||
                                                                $user->group_id == config('constants.groups.photographer'))
                                                                <?php
                                                   $imagesTypes=array('jpg','JPG','jpeg','JPEG','png','PNG','gif','GIF');
                                                   $excelTypes=array('xls','xlsx');
                                                   $docTypes=array('doc','docx');
                                                   $pdfTypes=array('doc','PDF');
                                                   
                                                   //p($bookingData['gallery']); die;
                                                   if(count($bookingData['gallery'])>0)
                                                      foreach($bookingData['gallery'] as $data){

                                                        if(in_array($data['otherinfo'],$imagesTypes))
                                                          $thumb_img=$data['path'];
                                                        else if(in_array($data['otherinfo'],$excelTypes))
                                                          $thumb_img=url('adminpanel/dist/img/xls.jpeg');
                                                        else if(in_array($data['otherinfo'],$docTypes))
                                                          $thumb_img=url('adminpanel/dist/img/doxx.png');
                                                        else if(in_array($data['otherinfo'],$pdfTypes))
                                                        $thumb_img=url('adminpanel/dist/img/pdf.png');
                                                        else 
                                                            $thumb_img='';
                                                        
                                                          ?>
                                                                <div id="file_{{ $data['id'] }}" class="col-3 text-center"
                                                                    style="position: relative;">
                                                                    <label class="">{{ $data['name'] }}</label>
                                                                    @if ($user->group_id == config('constants.groups.photographer') ||
                                                                        $user->group_id == config('constants.groups.admin'))
                                                                        <i onclick="removeFile({{ $data['id'] }},'booking_photos')"
                                                                            style="position: absolute; top:15px; right:0px; cursor:pointer"
                                                                            class="fas fa-times"></i>
                                                                    @endif
                                                                    <a download="myimage" href="{{ $data['path'] }}" target="_blank"><img
                                                                            class="w-100 shadow-1-strong rounded mb-4 img-thumbnail"
                                                                            src="{{ $thumb_img }}" width="200"
                                                                            alt="Uploaded Image"></a><br>
                                                                    <div class="btn btn-flat btn-info align-center"
                                                                        onclick="open_template_editor('{{ $data['path'] }}')">
                                                                        Edit</div>
                                                                    <a  href="{{ $data['path'] }}" download="{{ $data['name'] }}" class="btn btn-flat btn-info align-center"
                                                                        >
                                                                        Download</a>
                                                                </div>


                                                                <?php 
                                                        }else{
                                                            echo '<div class="col-12 text-center alert-danger">Waiting for Photographer to upload photos. No Photo uploaded Yet!</div>';
                                                        }
                                                    ?>
                                                            @else
                                                                <div class="col-12 text-center alert-danger">In-active by
                                                                    the Office admin,Please contact the office. Thanks</div>
                                                            @endif
                                                        </div>
                                                        <div class="col-1">&nbsp;</div>
                                                    </div>
                                                </div>

                                                @if ($user->group_id == config('constants.groups.photographer') ||
                                                    $user->group_id == config('constants.groups.admin'))
                                                    <div class="row form-group">
                                                        <div class="col-1">&nbsp;</div>
                                                        <div class="col-10 card card-default">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Upload Event Photos: <small>
                                                                        <strong>Click!</strong> in
                                                                        box and upload photos.</small></h3>
                                                            </div>
                                                            <?php
                                                            $ts = time();
                                                            $user_id = Auth::user()->id;
                                                            $date = date('Y-m-d');
                                                            $booking_id = $id;
                                                            ?>
                                                            <form action="{{ route('booking.add_photos', ['id' => $id]) }}"
                                                                method="post" enctype="multipart/form-data"
                                                                id="datanodeupload" class="dropzone ">
                                                                <input type="file" name="file" style="display: none;">
                                                                <input type="hidden" name="dataTS" id="dataTS"
                                                                    value="{{ $ts }}">
                                                                <input type="hidden" name="dataDATE" id="dataDATE"
                                                                    value="{{ $date }}">
                                                                <input type="hidden" name="booking_id" id="booking_id"
                                                                    value="{{ $booking_id }}">
                                                                @csrf
                                                                <div>
                                                                    <h4 class="form-label">Upload Multiple photos By Click
                                                                        On Box</h4>
                                                                </div>


                                                            </form>
                                                            <div class="card-footer">
                                                                You can select multiple files (e.g images, .jpg , .png ,.gif
                                                                ) and upload

                                                            </div>
                                                        </div>
                                                        <div class="col-1">&nbsp;</div>

                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                @endif
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
                                                                @if ($user->group_id != config('constants.groups.photographer'))
                                                                    Package : {{ $packageDetails['name'] }} <br>
                                                                    Price : ${{ $packageDetails['price'] }} <br>
                                                                    Total Cost: $@php echo $totalCost=$packageDetails['price']+ $bookingData['extra_price']+$overtime;@endphp
                                                                @endif
                                                            </div>

                                                        </div>


                                                    </div>
                                                    <!-- /.tab-pane -->

                                                </div>
                                                <!-- /.tab-content -->
                                            </div><!-- /.card-body -->
                                        </div>
                                        <!-- /.card -->

                                        {{-- This section is for Upload Documents --}}
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Upload Documents  </strong>
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
                                                                    <a download href="{{ $data['path'] }}"><img download
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

                                        @if ($user->group_id==config('constants.groups.photographer') || $user->group_id==config('constants.groups.admin'))
                                        {{-- This section is for photographer Comments --}}
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Notes Section (for Photographer) </strong>
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div id="submit_photographer_comment_replace">
                                                    @php
                                                        // p($bookingData['comments']);
                                                    @endphp
                                                    @foreach ($bookingData['photographer_comments'] as $key => $comment)
                                                        <div class="row border">
                                                            <div class="col-12">
                                                                <strong>{{ $comment['user']['name'] }}
                                                                    ({{ $comment['slug'] }})
                                                                </strong>
                                                                {{ date('d/m/Y H:i:s', strtotime($comment['created_at'])) }}<br>
                                                                {{ $comment['comment'] }}
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                </div>
                                                @php
                                                    $userData = get_session_value();
                                                    //p($userData);
                                                @endphp
                                                <div class="tab-content">
                                                    <form method="post" id="submit_photographer_comment">
                                                        <input type="hidden" name="group_id"
                                                            value="{{ $user->group_id }}">
                                                        <input type="hidden" name="action"
                                                            value="submit_photographer_comment">
                                                        <input type="hidden" name="slug"
                                                            value="{{ $userData['get_groups']['slug'] }}">
                                                        <input type="hidden" name="for_section"
                                                            value="photographer_section">
                                                        <div class="form-group">
                                                            <label for="inputDescription">Comment</label>
                                                            <textarea id="comments" name="comment" placeholder="Write comment about the Booking" class="form-control"
                                                                rows="4"></textarea></br>
                                                            <button
                                                                onclick="do_action({{ $bookingData['id'] }},'submit_photographer_comment')"
                                                                type="button" class="btn btn-success float-right"><i
                                                                    class="far fa-credit-card"></i> Send</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                    </div>

                                    <!-- /.col -->

                                    <div class="col-md-4">
                                        @if ($bookingData['status'] > 0)
                                            <?php $recievedAmount = 0; ?>
                                            <div class="card-header alert-secondary">
                                                <h3 class="card-title">Package Detail</h3>
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
                                                        @if ($user->group_id != config('constants.groups.photographer'))
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
                                                                    <p>Staff Worked {{ $bookingData['overtime_hours'] }}
                                                                        hours
                                                                        extra as over time .</p>
                                                                </td>
                                                            </tr>
                                                        @endif
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
    <script src="https://pixoeditor.com/editor/scripts/bridge.m.js"></script>
    <style>
        .pixo-modal {
            z-index: 99999 !important;
        }
    </style>
@endsection
@section('footer-js-css')
    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ url('adminpanel/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- dropzonejs -->
    <script src="{{ url('adminpanel/plugins/dropzone/min/dropzone.min.js') }}"></script>
    <script src="{{ asset('js/file_upload.js') }}" defer></script>

    <script>
        var home_url = "{{ env('APP_URL') }}";
        var deleteAction = '{{ route('file-delete') }}';
        var generalTS = document.getElementById('dataTS').value;
        var generalDATE = document.getElementById('dataDATE').value;
        var token = '{!! csrf_token() !!}';



        function open_template_editor(img_url) {

            new window.Pixo.Bridge({
                apikey: "20s311f70o0w",
                type: "modal",
                onSave: (data) => {
                    //alert(data.toImage());
                    data.download();
                    //alert(JSON.stringify({ template: data.toJSON() }));
                    // we will use the localStorage to store
                    // the template, however you should save the
                    // JSON somewhere to your back-end
                    localStorage.setItem(
                        "template",
                        JSON.stringify({
                            template: data.toJSON()
                        })
                    );
                    document.body.appendChild(
                        document.createTextNode("Template saved to localStorage")
                    );
                }
            }).edit(img_url);


        }

        function removeFile(id, slug = 'booking_photos') {
            if (confirm('Are you sure? you want to delete this file?')) {

                var sendInfo = {
                    action: 'delteFile',
                    id: id,
                    booking_id: {{ $id }},
                    slug: slug
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
        }

        function do_action(id, action_name = '', element_id = '') {
            var formData = ($('#' + action_name).formToJson());

            var sendInfo = {
                data: formData,
                action: action_name,
                id: id
            };
            if (action_name == 'photo_gallery_status') {
                if (!confirm('Are you sure? you want to active/inactive the Booking Gallary?')) {
                    $("#" + element_id).prop('checked', true);
                    return false;
                }



            }
            if (action_name == 'submit_comment') {
                if ($('#comments').val() == '')
                    return false;
            }
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

                    $('#' + action_name + '_replace').append(data.response);
                    $('#comments').val('');
                    $('#_loader').hide();
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
