@extends('adminpanel.admintemplate')
@push('title')
    <title>Edit Booking | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Booking</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Edit Booking</li>
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
                                <h3 class="card-title">Edit Booking</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-6">
                                        
                                        @if($errors->any())
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
                                                        {{ Session::get('alert-' . $msg) }} <a href="#" class="close"
                                                            data-dismiss="alert" aria-label="close">&times;</a></p>
                                                @endif
                                            @endforeach
                                        </div> <!-- end .flash-message -->
                                    </div>
                                    <div class="col-3">&nbsp;</div>
                                </div>
                                <form method="POST" action="{{ url('/admin/bookings/place') }}">
                                    @csrf
                                    {{-- List of items --}}
                                    <?php
                                    $total_items = 0;
                                    if(isset($id) && $id>0){
                                        echo '<input type="hidden" name="customer_id" value="'.$id.'">';
                                    }else{
                                        ?>
                                    <div class="row form-group">
                                        <div class="col-4">&nbsp;</div>
                                        <div class="col-4">
                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <select name="customer_id" class="form-control select2bs4"
                                                    placeholder="Select Venue Group">
                                                    @php
                                                        echo get_customer_options();
                                                    @endphp
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4">&nbsp;</div>
                                    </div>
                                    <?php
                                    }
                                    ($bookingData=$bookingData[0]);
                                    ?>

                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <label>Groom information</label>
                                            <div class="input-group mb-3">
                                                <input placeholder="First Name" type="text" name="groom_first_name"
                                                    required value="{{ $bookingData['groom_first_name'] }}"
                                                    class=" form-control @error('groom_first_name') is-invalid @enderror">
                                                @error('groom_first_name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-5">

                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <input required value="{{ $bookingData['groom_last_name'] }}" placeholder="Last Name"
                                                    type="text" name="groom_last_name"
                                                    class=" form-control @error('groom_last_name') is-invalid @enderror">
                                                @error('groom_last_name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input placeholder="Email Address" type="email" name="groom_email"
                                                    required value="{{ $bookingData['groom_email'] }}"
                                                    class=" form-control @error('groom_email') is-invalid @enderror">
                                                @error('groom_email')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input placeholder="Phone Number" type="text" name="groom_contact_number"
                                                    required value="{{ $bookingData['groom_contact_number'] }}"
                                                    class=" form-control @error('groom_contact_number') is-invalid @enderror">
                                                @error('groom_contact_number')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-10">
                                            <div class="input-group mb-3">
                                                <textarea placeholder="Billing Address (e.g street address, apt., city, state, and zip code) "
                                                    name="groom_billing_address" class=" form-control @error('groom_billing_address') is-invalid @enderror">{{ $bookingData['groom_billing_address'] }}</textarea>
                                                @error('groom_billing_address')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <label>Bride information</label>
                                            <div class="input-group mb-3">
                                                <input placeholder="First Name" type="text" name="bride_first_name"
                                                    required value="{{ $bookingData['bride_first_name'] }}"
                                                    class=" form-control @error('bride_first_name') is-invalid @enderror">
                                                @error('bride_first_name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-5">

                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <input required value="{{ $bookingData['bride_last_name'] }}"
                                                    placeholder="Last Name" type="text" name="bride_last_name"
                                                    class=" form-control @error('bride_last_name') is-invalid @enderror">
                                                @error('bride_last_name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input placeholder="Email Address" type="email" name="bride_email"
                                                    required value="{{ $bookingData['bride_email'] }}"
                                                    class=" form-control @error('bride_email') is-invalid @enderror">
                                                @error('bride_email')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input placeholder="Phone Number" type="text"
                                                    name="bride_contact_number" required
                                                    value="{{ $bookingData['bride_contact_number'] }}"
                                                    class=" form-control @error('bride_contact_number') is-invalid @enderror">
                                                @error('bride_contact_number')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-10">
                                            <div class="input-group mb-3">
                                                <textarea placeholder="Billing Address (e.g street address, apt., city, state, and zip code) "
                                                    name="bride_billing_address" class=" form-control @error('bride_billing_address') is-invalid @enderror">{{ $bookingData['bride_billing_address'] }}</textarea>
                                                @error('bride_billing_address')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            <label>Event Detail information</label>
                                            <div class="input-group date" id="reservationdatetime"
                                                data-target-input="nearest">
                                                <input name="event_date_time" placeholder="Event Date & Time (09/22/2022 2:25 AM)"
                                                    value="{{ $bookingData['event_date_time'] }}" type="text"
                                                    class="form-control datetimepicker-input @error('event_date_time') is-invalid @enderror"
                                                    data-target="#reservationdatetime" />
                                                <div class="input-group-append" data-target="#reservationdatetime"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="far fa-calendar-alt"></i>
                                                    </div>
                                                </div>
                                                @error('event_date_time')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-5">

                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <select name="venue_group_id" class="form-control select2bs4"
                                                    placeholder="Select Venue Group">
                                                    @php
                                                        echo get_venue_group_options($bookingData['venue_group_id']);
                                                    @endphp
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>

                                    {{-- Package Details --}}
                                    <?php $packages=get_packages();
                                    $total_items=count($packages);
                                    //p($bookingData['package']);
                                    $manual_package=true;
                                    foreach ($packages as $key => $packageData) {
                                        //echo $bookingData['package_id'].'=='.$packageData['id'].'<br>';
                                      if($bookingData['package_id']==$packageData['id']){
                                        $manual_package=false;
                                        
                                      }
                                      
                                    ?>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-2">
                                            <div class="form-group clearfix">
                                                <div class="icheck-primary d-inline">
                                                    {{-- <input type="hidden" name="package_id" value="{{$packageData['id']}}"> --}}
                                                    <input value="{{ phpslug($packageData['id']) }}" type="radio"
                                                        id="package_name_{{ $packageData['id'] }}" name="package_id"
                                                        @php
                                                            if($bookingData['package_id']==$packageData['id']) echo 'checked'
                                                        @endphp
                                                        >
                                                    <label
                                                        for="package_name_{{ $packageData['id'] }}">{{ $packageData['name'] }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-1"><span> {{ $packageData['price'] }} USD</span></div>
                                        <div class="col-7">
                                            <div class="input-group mb-3">
                                                <span> {{ $packageData['description'] }}</span>
                                            </div>
                                        </div>

                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <?php } ?>

                                    {{-- If Manual Package was added then list this one --}}
                                    @if ($manual_package)
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-2">
                                            <div class="form-group clearfix">
                                                <div class="icheck-primary d-inline">
                                                    <input value="{{ phpslug($bookingData['package']['id']) }}" type="radio"
                                                        id="package_name_{{ $bookingData['package']['id'] }}" name="package_id" checked>
                                                    <label
                                                        for="package_name_{{ $bookingData['package']['id'] }}">{{ $bookingData['package']['name'] }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-1"><span> {{ $bookingData['package']['price'] }} USD</span></div>
                                        <div class="col-7">
                                            <div class="input-group mb-3">
                                                <span> {{ $bookingData['package']['description'] }}</span>
                                            </div>
                                        </div>

                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    @endif
                                    <div id="more_packages"></div>
                                    <div class="row form-group">
                                        <div class="col-11">&nbsp;</div>
                                        <div class="col-1" id="btn_manual_pgk">
                                            <div style="width: 90px; float:right;"
                                                onclick="addmore_items('more_packages')"
                                                class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i>
                                                Manually</div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-2"><strong>Who is Paying?</strong></div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <div class="btn-group">
                                                    <input type="hidden" name="who_is_paying" id="who_is_paying"
                                                        value="{{$bookingData['who_is_paying']}}">
                                                    <button value="customer" onclick=select_who_is_paying('customer')
                                                        id="customer" type="button" style="width: 150px"
                                                        class="active btn btn-primary">Customer</button>
                                                    <button value="venue_group" onclick=select_who_is_paying('venue_group')
                                                        id="venue_group" type="button" style="width: 150px"
                                                        class="btn btn-primary">Venue Group</button>
                                                    <button value="both" onclick=select_who_is_paying('both')
                                                        id="both" type="button" style="width: 150px"
                                                        class="btn btn-primary">Siplit in Both</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2">&nbsp;</div>
                                    </div>
                                    <div id="user_payment_inputs"></div>
                                    <div class="row form-group">
                                        <div class="col-4">&nbsp;</div>
                                        <div class="col-4">
                                            <div class="input-group mb-3">
                                                <div class="form-group clearfix">
                                                    <label>Payment Source : </label>&nbsp;
                                                    <div class="icheck-primary d-inline">
                                                        <input value="{{ phpslug('credit_card') }}" type="radio"
                                                            id="payment_source1" name="payment_source" 
                                                            @php
                                                                if($bookingData['payment_source']=='credit_card') echo 'checked';
                                                            @endphp
                                                            >
                                                        <label for="payment_source1">Credit Card </label>
                                                    </div> &nbsp;
                                                    <div class="icheck-primary d-inline">
                                                        <input value="{{ phpslug('zelle') }}" type="radio"
                                                            id="payment_source2" name="payment_source"
                                                            
                                                            @php
                                                                if($bookingData['payment_source']=='zelle') echo 'checked';
                                                            @endphp
                                                            >
                                                        <label for="payment_source2">Zelle</label>
                                                    </div>
                                                    <div class="icheck-primary d-inline">
                                                        <input value="{{ phpslug('cheque') }}" type="radio"
                                                            id="payment_source3" name="payment_source"
                                                            @php
                                                                if($bookingData['payment_source']=='cheque') echo 'checked';
                                                            @endphp
                                                            >
                                                        <label for="payment_source3">Cheque</label>
                                                    </div>

                                                </div>
                                                @error('payment_source')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-5">
                                            @php //p(get_photographer_options()); @endphp
                                            <div class="input-group mb-3">
                                                <select placeholder="Select Photographer" type="text"
                                                    name="photographer_id[]" required class=" select2bs4 form-control">
                                                    @php echo get_photographer_options(); @endphp
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <input placeholder="Photographer Expense" type="text"
                                                    name="photographer_expense[]" required
                                                    value="{{ old('photographer_expense[]') }}"
                                                    class=" form-control @error('photographer_expense[]') is-invalid @enderror">
                                                @error('photographer_expense[]')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div id="photographer_list"></div>
                                    <div class="row form-group">
                                        <div class="col-11">&nbsp;</div>
                                        <div class="col-1">
                                            <div style="width: 130px; float:right;"
                                                onclick="addmore_photographers()"
                                                class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i>Photographer</div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-10">
                                            <div class="input-group mb-3">
                                                <textarea placeholder="Any Special Notes for this order.."
                                                    name="order_notes" class=" form-control @error('order_notes') is-invalid @enderror">{{ $bookingData['order_notes'] }}</textarea>
                                                @error('order_notes')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-5">&nbsp;</div>
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-outline-success btn-block btn-lg"><i
                                                    class="fa fa-save"></i> Save</button>
                                        </div>
                                        <div class="col-5">&nbsp;</div>

                                    </div>
                                </form>
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
@endsection
@section('footer-js-css')
    <!-- Select2 -->
    <script src="{{ url('adminpanel/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- date-range-picker -->
    <script src="{{ url('adminpanel/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        var ctr = 1;
        var counter = {{ $total_items }};
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });

            //Date and time picker
            $('#reservationdatetime').datetimepicker({
                icons: {
                    time: 'far fa-clock'
                }
            });
        });
        // Select Booking Type Single/Multi
        function select_who_is_paying(user) {
            if (user == 'customer') {
                $('#user_payment_inputs').hide('slow');
                $('#user_payment_inputs').html('');
                $('#who_is_paying').val(user);
                return true;
            } else if (user == 'venue_group') {
                $('#user_payment_inputs').hide('slow');
                $('#user_payment_inputs').html('');
                $('#who_is_paying').val(user);
            } else {

                multi_unit_html =
                    '<div class="row form-group"><div class="col-2">&nbsp;</div><div class="col-3"><label>Customer</label><div class="input-group mb-2"><input type="number" name="customer_to_pay" placeholder="How much?" required class="form-control"></div></div>';
                multi_unit_html +=
                    '<div class="col-1">&nbsp;</div><div class="col-3"><label>Venue Group</label><div class="input-group mb-2"><input type="number" name="venue_group_to_pay" placeholder="How much?" required class="form-control"></div></div></div>';

                //multi_unit_html +='<div class="row form-group"><div class="col-4">&nbsp;</div><div class="col-3"><div id="listof_floors"><label>List All Floors</label><div class="input-group mb-2"><input type="text" name="list_of_floors[]" placeholder="Floor?" required class="form-control"></div></div><div style="width: 90px; float:right;" onclick="addmore_floors()" class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i> Add more</div></div> <div class="col-3">&nbsp;</div></div>';

                $('#user_payment_inputs').html(multi_unit_html);
                $('#user_payment_inputs').show('slow');

            }
            $('#who_is_paying').val(user);
            $('#customer').removeClass('active');

        }
        // Add more Items manually
        function addmore_items(cat_id) {
            counter++;
            itemHTML = '<div class="row form-group"><div class="col-1">&nbsp;</div>';
            itemHTML +=
                '<div class="col-2"><div class="input-group mb-3"> <div class="icheck-primary d-inline"><input type="radio" value="manual_package" name="package_id" id="package_name_' +
                counter + '" checked><label for="package_name_' + counter +
                '"><input placeholder="Package Name" type="text" name="package_name" required  class=" form-control" ><label></div></div></div>';
            itemHTML +=
                '<div class="col-1"><div class="input-group mb-3"><input placeholder="Price" type="text" value="200" name="package_price" required  class=" form-control" ></div></div>';
            itemHTML +=
                '<div class="col-7"><div class="input-group mb-3"><textarea placeholder="Description" type="number" name="description"  class="form-control" ></textarea></div></div>';
            itemHTML += '<div class="col-1"></div></div>'
            itemHTML222 =
                '<div class="col-1"><div style="width:20px; cursor:pointer; padding:10px; color:red;"><i onclick=$("#manual_item_' +
                counter + '").remove() class="fas fa-minus"></i></div></div></div>';

            $('#' + cat_id).append('<div id="manual_item_' + counter + '">' + itemHTML + '</div>');
            $('#btn_manual_pgk').remove();

        }
        // Ajax to Update Lead Data
        function addmore_photographers() {
            var sendInfo = {
                action: 'show_photographer',
            };

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
