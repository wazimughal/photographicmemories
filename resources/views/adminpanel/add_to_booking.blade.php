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
                        <h1>Add New booking</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Add New booking</li>
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
                                <h3 class="card-title">Add New booking</h3>
                            </div>
                            <div class="card-body">


                                <!-- /.row -->

                                <div class="row">

                                    <div class="col-md-8">
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Event/Booking Information</strong>
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div class="tab-content">
                                                    <div>
                                                        <div class="row">
                                                            <div class="col-3">&nbsp;</div>
                                                            <div class="col-6">

                                                                @if ($errors->any())
                                                                    {{ implode('', $errors->all('<div>:message</div>')) }}
                                                                @endif
                                                                <!-- flash-message -->
                                                                <div class="flash-message">
                                                                    @if ($errors->any())
                                                                        {{ implode('', $errors->all('<div>:message</div>')) }}
                                                                    @endif

                                                                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                                                                        @if (Session::has('alert-' . $msg))
                                                                            <p class="alert alert-{{ $msg }}">
                                                                                {{ Session::get('alert-' . $msg) }} <a
                                                                                    href="#" class="close"
                                                                                    data-dismiss="alert"
                                                                                    aria-label="close">&times;</a></p>
                                                                        @endif
                                                                    @endforeach
                                                                </div> <!-- end .flash-message -->
                                                            </div>
                                                            <div class="col-3">&nbsp;</div>
                                                        </div>
                                                        <form class="form-horizontal" method="POST"
                                                            action="{{ route('bookings.save_booking_edit_data', $id) }}">
                                                            @csrf

                                                            <input type="hidden" name="customer_id"
                                                                value="{{ $bookingData['customer']['user_id'] }}">
                                                            @if (isset($bookingData['venue_group']['user_id']) && $bookingData['venue_group']['user_id'] > 0)
                                                                <input type="hidden" name="selected_venue_group_id"
                                                                    value="{{ $bookingData['venue_group']['user_id'] }}">
                                                            @endif

                                                            <div class="form-group row">
                                                                <div class="col-sm-6">
                                                                    <label class="col-form-label">Event Date</label>
                                                                    <div class="input-group date" id="reservationdate"
                                                                        data-target-input="nearest">
                                                                        <input name="date_of_event"
                                                                            placeholder="Event Date (09/22/2022)"
                                                                            value="{{ $bookingData['date_of_event'] }}"
                                                                            type="text"
                                                                            class="form-control datetimepicker-input @error('date_of_event') is-invalid @enderror"
                                                                            data-target="#reservationdate" />
                                                                        <div class="input-group-append"
                                                                            data-target="#reservationdate"
                                                                            data-toggle="datetimepicker">
                                                                            <div class="input-group-text"><i
                                                                                    class="far fa-calendar-alt"></i>
                                                                            </div>
                                                                        </div>
                                                                        @error('date_of_event')
                                                                            <div class="invalid-feedback">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label class="col-form-label">Event Time</label>

                                                                    <div class="input-group date" id="timepicker"
                                                                        data-target-input="nearest">
                                                                        <input name="time_of_event"
                                                                            value="{{ $bookingData['time_of_event'] }}"
                                                                            type="text"
                                                                            class="form-control datetimepicker-input"
                                                                            data-target="#timepicker" />
                                                                        <div class="input-group-append"
                                                                            data-target="#timepicker"
                                                                            data-toggle="datetimepicker">
                                                                            <div class="input-group-text"><i
                                                                                    class="far fa-clock"></i></div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <?php if($assigne_photographers){  $k=1;?>
                                                            {{-- <div class="form-group row">
                                                                <div class="col-sm-12">
                                                                    <div class="table-responsive">
                                                                        <table class="table">
                                                                            <?php //foreach ($assigne_photographers as $key=>$photographer){
                                                                            ?>

                                                                            <tr>
                                                                                <td style="width:20%">{{$k++}}</td>
                                                                                <td>{{ $photographer['userinfo'][0]['firstname'] }}
                                                                                    {{ $photographer['userinfo'][0]['lastname'] }}
                                                                                </td>
                                                                                <td style="width:200px">
                                                                                    {!! ($photographer['status']==0)?'<span  class=" btn-primary btn-sm">Waiting Response</span>':'' !!}
                                                                                    {!! ($photographer['status']==1)?'<span  class=" success btn-sm">Accpted</span>':'' !!}
                                                                                    {!! ($photographer['status']==2)?'<span  class=" btn-danger btn-sm">Declined</span>':'' !!}
                                                                                    
                                                                                </td>
                                                                                <div
                                                                            </tr>

                                                                            <?php //}
                                                                            ?>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div> --}}
                                                            <div class="row form-group">
                                                                <div class="col-12">
                                                                    <div class="alert alert-warning alert-dismissible">
                                                                        <button type="button" class="close"
                                                                            data-dismiss="alert"
                                                                            aria-hidden="true">&times;</button>
                                                                        <h5><i class="icon fas fa-exclamation-triangle"></i>
                                                                            Alert!</h5>
                                                                        If you changed the assigned photographer, Previous
                                                                        Photographers will be removed and new one assigned.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php }?>

                                                            <div class="row form-group">
                                                                <div class="col-6">
                                                                    @php
                                                                        $photographer = get_photographer_options_with_count();
                                                                        $total_photographers = $photographer['total'];
                                                                    @endphp
                                                                    <div class="input-group mb-3" style="margin-top:2rem;">
                                                                        <select placeholder="Select Photographer"
                                                                            type="text" name="photographer_id[]"
                                                                            class=" select2bs4 form-control">
                                                                            <option value="">Select Photographer
                                                                            </option>
                                                                            @php echo $photographer['options']; @endphp
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="input-group mb-3" style="margin-top:2rem;">
                                                                        <input placeholder="Photographer Expense"
                                                                            type="text" name="photographer_expense[]"
                                                                            value="{{ old('photographer_expense[]') }}"
                                                                            class=" form-control @error('photographer_expense[]') is-invalid @enderror">
                                                                        @error('photographer_expense[]')
                                                                            <div class="invalid-feedback">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if ($total_photographers > 1)
                                                                <div id="photographer_list"></div>
                                                                <div id="photographer_btn" class="row form-group">
                                                                    <div class="col-11">&nbsp;</div>
                                                                    <div class="col-1">
                                                                        <div style="width: 130px; float:right;"
                                                                            onclick="addmore_photographers()"
                                                                            class="btn btn-success btn-block btn-sm"><i
                                                                                class="fas fa-plus"></i>Photographer</div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div id="other_venue_group"></div>
                                                            <div class="form-group row">
                                                                <div class="col-sm-6">
                                                                    @if (isset($bookingData['venue_group']))
                                                                        <div class="input-group">
                                                                            <select class="form-control select2bs4"
                                                                                onchange="changeVenueGroup()"
                                                                                name="venue_group_id" id="venue_group_id"
                                                                                placeholder="Select Venue Group">
                                                                                @php
                                                                                    echo get_venue_group_options($bookingData['venue_group']['userinfo'][0]['id']);
                                                                                @endphp
                                                                            </select>
                                                                        </div>
                                                                    @else
                                                                        <div class="input-group">

                                                                            <input type="text" disabled readonly
                                                                                class="form-control"
                                                                                value="{{ $bookingData['other_venue_group'] }}">
                                                                             

                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="input-group mb-3">
                                                                        <select name="package_id" id="venue_group_id"
                                                                            class="form-control select2bs4"
                                                                            placeholder="Select Venue Group">
                                                                            <option selected="selected"> Select Package
                                                                            </option>
                                                                            @php
                                                                                $package_id = null;
                                                                                if ($bookingData['package_id'] > 0) {
                                                                                    $package_id = $bookingData['package_id'];
                                                                                }
                                                                                echo get_packages_options($package_id);
                                                                            @endphp
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-sm-6">
                                                                    <div class="input-group mb-2"><input type="number"
                                                                            name="extra_price"
                                                                            value="{{ $bookingData['extra_price'] }}"
                                                                            placeholder="Extra Cost (if any)"
                                                                            class="form-control"></div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="input-group mb-2"><input
                                                                            name="extra_charge_desc"
                                                                            value="{{ $bookingData['extra_charge_desc'] }}"
                                                                            placeholder="Reason for extra charge (if any)"
                                                                            class="form-control"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-sm-6">
                                                                    <div class="input-group mb-2"><input type="number"
                                                                            value="{{ $bookingData['overtime_hours'] }}"
                                                                            name="overtime_hours"
                                                                            placeholder="Over Time: Number of Hours(08)"
                                                                            class="form-control"></div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="input-group mb-2"><input type="number"
                                                                            value="{{ $bookingData['overtime_rate_per_hour'] }}"
                                                                            name="overtime_rate_per_hour"
                                                                            placeholder="Rate Per Hour"
                                                                            class="form-control"></div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                {{-- <div class="col-sm-6">
                                                                    <div class="form-group">

                                                                        <select required name="paying_via"
                                                                            class="form-control select2bs4"style="width: 100%;">
                                                                            <option
                                                                                {{ $bookingData['paying_via'] == '' ? 'selected="selected"' : '' }}>
                                                                                Select Paying source ?</option>
                                                                            <option
                                                                                {{ $bookingData['paying_via'] == 0 ? 'selected="selected"' : '' }}
                                                                                value="0">Cheque</option>
                                                                            <option
                                                                                {{ $bookingData['paying_via'] == 1 ? 'selected="selected"' : '' }}
                                                                                value="1">Credit Card</option>
                                                                            <option
                                                                                {{ $bookingData['paying_via'] == 2 ? 'selected="selected"' : '' }}
                                                                                value="2">Zelle</option>

                                                                        </select>
                                                                    </div>
                                                                </div> --}}
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">

                                                                        <select id="who_is_paying" name="who_is_paying"
                                                                            onchange="select_who_is_paying()"
                                                                            class="form-control select2bs4" required
                                                                            style="width: 100%;">
                                                                            <option
                                                                                {{ $bookingData['who_is_paying'] == '' ? 'selected="selected"' : '' }}>
                                                                                Select Who is Paying ?</option>
                                                                            <option
                                                                                {{ $bookingData['who_is_paying'] == 1 ? 'selected="selected"' : '' }}
                                                                                value="1">Venue Group</option>
                                                                            <option
                                                                                {{ $bookingData['who_is_paying'] == 0 ? 'selected="selected"' : '' }}
                                                                                value="0">Customer</option>
                                                                            <option
                                                                                {{ $bookingData['who_is_paying'] == 2 ? 'selected="selected"' : '' }}
                                                                                value="2">Split in Both</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">&nbsp;</div>
                                                            </div>
                                                            <div id="both_paying" class="form-group row"
                                                                style="{{ $bookingData['who_is_paying'] != 2 ? 'display: none' : '' }}">
                                                                <div class="col-sm-6">
                                                                    <label>Customer
                                                                    </label>
                                                                    <div class="input-group mb-2"><input type="number"
                                                                            name="customer_to_pay"
                                                                            value="{{ $bookingData['customer_to_pay'] }}"
                                                                            placeholder="How much?" class="form-control">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <label>Venue Group</label>
                                                                    <div class="input-group mb-2"><input type="number"
                                                                            value="{{ $bookingData['venue_group_to_pay'] }}"
                                                                            name="venue_group_to_pay"
                                                                            placeholder="How much?" class="form-control">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row form-group">
                                                                <div class="col-5">&nbsp;</div>
                                                                <div class="col-2">
                                                                    <button type="submit"
                                                                        class="btn btn-outline-success btn-block btn-lg"><i
                                                                            class="fa fa-save"></i> Save</button>
                                                                </div>
                                                                <div class="col-5">&nbsp;</div>

                                                            </div>

                                                        </form>
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
                                                                <div id="file_{{$data['id']}}" class="col-3 text-center" style="position: relative;">
                                                                  <label class="">{{$data['name']}}</label>
                                                                  <i onclick="removeFile({{$data['id']}})" style="position: absolute; top:15px; right:0px; cursor:pointer" class="fas fa-times"></i>
                                                                  <a href="{{$data['path']}}" target="_blank"><img class="w-100 shadow-1-strong rounded mb-4 img-thumbnail" src="{{$thumb_img}}" width="200" alt="Uploaded Image"></a>
                                                                </div>
                      
                      
                                                                <?php 
                                                                }
                                                            ?>
                                                       
                                                      </div>
                                                        <div class="col-1">&nbsp;</div>
                                                      </div>
                                                      </div>
                                                    <div class="row form-group">
                                                        <div class="col-1">&nbsp;</div>
                                                        <div class="col-10 card card-default">
                                                            <div class="card-header">
                                                                <h3 class="card-title">Upload Documents: <small>  <strong>Click!</strong> in
                                                                            box and upload files.</small></h3>
                                                            </div>
                                                            <form action="{{ url('/admin/bookings/upload-documents/') . '/' .$bookingData['id'] }}"
                                                                method="post" enctype="multipart/form-data" id="image-upload"
                                                                class="dropzone ">
                                                                @csrf
                                                                <div>
                                                                    <h4 class="form-label">Upload Multiple Files By Click On Box</h4>
                                                                </div>
                    
                    
                                                            </form>
                                                            <div class="card-footer">
                                                                You can select multiple files (e.g images, .docx , .xls ,.csv, .pdf ) and upload
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="col-1">&nbsp;</div>
                    
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        
                                        {{-- This section is for Invoices --}}
                                        @if ($bookingData['status']>0)
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Payment Received </strong>
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div class="tab-content">
                                                    <div class="row">
                                                        <div class="col-3">&nbsp;</div>
                                                        <div class="col-6">

                                                            @if ($errors->any())
                                                                {{ implode('', $errors->all('<div>:message</div>')) }}
                                                            @endif
                                                            <!-- flash-message -->
                                                            <div class="flash-message">
                                                                @if ($errors->any())
                                                                    {{ implode('', $errors->all('<div>:message</div>')) }}
                                                                @endif

                                                                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                                                                    @if (Session::has('alert-' . $msg))
                                                                        <p class="alert alert-{{ $msg }}">
                                                                            {{ Session::get('alert-' . $msg) }} <a
                                                                                href="#" class="close"
                                                                                data-dismiss="alert"
                                                                                aria-label="close">&times;</a></p>
                                                                    @endif
                                                                @endforeach
                                                            </div> <!-- end .flash-message -->
                                                        </div>
                                                        <div class="col-3">&nbsp;</div>
                                                    </div>
                                                    
                                                    <?php $recievedAmount=0; if($bookingData['invoices']){  $k=1;?>
                                                        <div class="form-group row">
                                                            <div class="col-sm-12">
                                                                <div class="table-responsive">
                                                                    <table class="table">
                                                                        <?php foreach ($bookingData['invoices'] as $key=>$invoice){ 
                                                                            $recievedAmount=$recievedAmount+$invoice['paid_amount'];?>
                
                                                                        <tr>
                                                                            <td>{{ $k++}}</td>
                                                                            <td>{{ $invoice['slug']}}</td>
                                                                            <td>{{ $invoice['payee_name']}}</td>
                                                                            <td>{{ $invoice['paid_amount']}}</td>
                                                                            <td>{{ date('d/m/Y H:i:s',strtotime($invoice['created_at'])) }}</td>
                                                                            <td style="width:200px">
                                                                                <span style="width:200px"  class=" btn-success btn-sm">Received</span>
                                                                            </td>
                                                                            <div </tr>
                
                                                                                <?php }?>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                
                                                        <?php }?>
                                                    <form action="{{route('bookings.add_invoice',$id)}}" method="POST">
                                                        @csrf
                                                        <div class="form-group row">
                                                            <div class="col-sm-3">
                                                                <label>Who is Paying?</label>
                                                                <select id="paying" name="payee_uid" required
                                                                            class="form-control select2bs4" required
                                                                            style="width: 100%;">
                                                                            
                                                                            <option value="{{$bookingData['customer']['userinfo'][0]['id']}}-customer">Customer</option>
                                                                            @if (isset($bookingData['venue_group']) && $bookingData['venue_group']['user_id'] > 0)
                                                                            <option value="{{$bookingData['venue_group']['userinfo'][0]['id']}}-venue_group">Venue Group</option>
                                                                            @endif
                                                                        </select>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <label>Payee Name
                                                                </label>
                                                                <div class="input-group mb-2"><input type="text"
                                                                        name="payee_name" value="{{ old('payee_name') }}"
                                                                        placeholder="Name of Payee" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <label>Amout</label>
                                                                <div class="input-group mb-2"><input type="number"
                                                                        name="paid_amount"
                                                                        value="{{ old('paid_amount') }}"
                                                                        placeholder="Amount" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <label>Description</label>
                                                                <div class="input-group mb-2"><textarea
                                                                        name="description"
                                                                        value="{{ old('description') }}"
                                                                        placeholder="Description about Payment" class="form-control"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row form-group">
                                                            <div class="col-4">&nbsp;</div>
                                                            <div class="col-4">
                                                                <button type="submit"
                                                                    class="btn btn-outline-success btn-block btn-lg"><i
                                                                        class="fa fa-save"></i> Add Payment</button>
                                                            </div>
                                                            <div class="col-4">&nbsp;</div>

                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- /.col -->

                                    <div class="col-md-4">
                                        @if ($bookingData['status']>0)
                                        <div class="card-header alert-secondary">
                                            <h3 class="card-title">Deposit Need</h3>
                                        </div>
                                        <div class="table-responsive">
                                            <form action="" method="POST">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <th style="width:20%">Amount?</th>
                                                        <td><input type="text" name="deposit_needed" class="form-control"> <br>
                                                            <button type="button" class="btn btn-success float-right"><i
                                                                class="far fa-credit-card"></i> Send </button>
                                                            </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            </form>
                                        </div>
                                       
                                        <div class="card-header alert-secondary">
                                            <h3 class="card-title">Amount Due</h3>
                                        </div>
                                        @php
                                            $packageDetails = get_package_by_id($bookingData['package_id']);
                                        @endphp
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
                                                        <td>{{ ($bookingData['extra_price']>0)?'$'.$bookingData['extra_price']:0 }} <br>
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
                                                        <td>${{ (isset($recievedAmount))?$recievedAmount:$recievedAmount=0;  }}</td>
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
                                                <tbody>
                                                    <tr>
                                                        <th style="width:50%">Name</th>
                                                        <td>{{ $bookingData['customer']['userinfo'][0]['firstname'] }}
                                                            {{ $bookingData['customer']['userinfo'][0]['lastname'] }}</td>
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
                                                    @if ($bookingData['preferred_photographer_id']>0)
                                                    <tr>
                                                        <th>Preffered Photographer</th>
                                                        <td>@php
                                                            //echo $bookingData['preferred_photographer_id'];
                                                            $Preferred_photographer = get_user_by_id($bookingData['preferred_photographer_id']);
                                                            echo $Preferred_photographer['name'];
                                                        @endphp</td>
                                                    </tr>    
                                                    @endif
                                                    
                                                    <tr>
                                                        <th>Change Password</th>
                                                        <td>
                                                            <input type="password" name="password" class="form-control "
                                                                placeholder="Password" required value="12345678"><br>
                                                            <button type="button" class="btn btn-success float-right"><i
                                                                    class="far fa-credit-card"></i> Save </button>
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-header alert-secondary">
                                            <h3 class="card-title">Groom Info</h3>
                                        </div>
                                        <table class="table">
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
                                                    <td>
                                                        <textarea placeholder="Billing Address (e.g street address, apt., city, state, and zip code) "
                                                            name="groom_billing_address" class=" form-control ">{{ $bookingData['groom_billing_address'] }}</textarea><br>
                                                        <button type="button" class="btn btn-success float-right"><i
                                                                class="far fa-credit-card"></i> Save </button>
                                                    </td>
                                                </tr>


                                            </tbody>
                                        </table>
                                        <div class="card-header alert-secondary">
                                            <h3 class="card-title">Bride Info</h3>
                                        </div>
                                        <table class="table">
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
                                                    <td>
                                                        <textarea placeholder="Billing Address (e.g street address, apt., city, state, and zip code) "
                                                            name="groom_billing_address" class=" form-control ">{{ $bookingData['bride_billing_address'] }}</textarea><br>
                                                        <button type="button" class="btn btn-success float-right"><i
                                                                class="far fa-credit-card"></i> Save </button>
                                                    </td>
                                                </tr>


                                            </tbody>
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

Dropzone.autoDiscover = false;

var myDropzone = new Dropzone('#image-upload', {
    thumbnailWidth: 200,
    maxFilesize: 1,
    acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf,.doc,.docx,.xls,.csv"
});

        var ctr = 1;
        var counter = 1;
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
            //Timepicker
            $('#timepicker').datetimepicker({
                format: 'LT'
            })
            //Date picker
            $('#reservationdate').datetimepicker({
                format: 'L'
            });
            //Date and time picker
            $('#reservationdatetime').datetimepicker({
                icons: {
                    time: 'far fa-clock'
                }
            });
        });
        // Select Who is Paying
        function select_who_is_paying() {
            who_is_paying = $('#who_is_paying').val();
            if (who_is_paying == '2')
                $('#both_paying').show('slow');
            else
                $('#both_paying').hide('slow');
        }
        // Add more Items manually
        function addmore_items(cat_id) {
            counter++;
            itemHTML = '<div class="row form-group"><div class="col-1">&nbsp;</div>';
            itemHTML +=
                '<div class="col-2"><div class="input-group mb-3"><input placeholder="Title" type="text" name="title_for_extra_price" required  class="form-control" ></div></div>';
            itemHTML +=
                '<div class="col-1"><div class="input-group mb-3"><input placeholder="Price" type="text" value="200" name="extra_price" required  class=" form-control" ></div></div>';
            itemHTML +=
                '<div class="col-7"><div class="input-group mb-3"><textarea placeholder="Add descriptions of addional charges/taxes" name="extra_charge_desc"  class="form-control" ></textarea></div></div>';
            itemHTML += '<div class="col-1"></div></div>'
            itemHTML222 =
                '<div class="col-1"><div style="width:20px; cursor:pointer; padding:10px; color:red;"><i onclick=$("#manual_item_' +
                counter + '").remove() class="fas fa-minus"></i></div></div></div>';

            $('#' + cat_id).append('<div id="manual_item_' + counter + '">' + itemHTML + '</div>');
            $('#btn_manual_pgk').remove();

        }
        // Ajax to Update Lead Data
        var total_photographers = {{ $total_photographers }}
        photographer_counter = 1

        function addmore_photographers() {
            var sendInfo = {
                action: 'show_photographer',
            };
            photographer_counter++;
            console.log('photographer_counter' + photographer_counter);
            $.ajax({
                url: "{{ url('/admin/bookings/ajaxcall') }}/1",
                data: sendInfo,
                contentType: 'application/json',
                error: function() {
                    alert('There is Some Error, Please try again !');
                },
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error == 'No') {
                        console.log(data);

                        $('#photographer_list').append(data.photographer_list);
                        $('.select2bs4').select2({
                            theme: 'bootstrap4'
                        });
                        if (photographer_counter == total_photographers)
                            $('#photographer_btn').remove();

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

        function changeVenueGroup() {
            selectOption = $('#venue_group_id option:selected').text();

            if (selectOption == 'Other') {
                otherVenueGroup =
                    '<div class="row form-group"><div class="col-1">&nbsp;</div><div class="col-10"><div class="input-group mb-3"><textarea placeholder="Name and Address of Venue Group" name="other_venue_group" class=" form-control" required></textarea></div></div><div class="col-1">&nbsp;</div></div>';
                $('#other_venue_group').html(otherVenueGroup);
            } else {
                $('#other_venue_group').html('');
            }
        };


function removeFile(id) {


if (confirm('Are you sure? you want to delete this file?')) {

    var sendInfo = {
        action: 'delteFile',
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
    </script>
@endsection