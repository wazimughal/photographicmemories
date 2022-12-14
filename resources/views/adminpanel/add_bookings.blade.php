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
                                                        {{ Session::get('alert-' . $msg) }} <a href="#" class="close"
                                                            data-dismiss="alert" aria-label="close">&times;</a></p>
                                                @endif
                                            @endforeach
                                        </div> <!-- end .flash-message -->
                                    </div>
                                    <div class="col-3">&nbsp;</div>
                                </div>
                                <form method="POST" action="{{ url('/admin/bookings/add') }}/{{$id}}">
                                    @csrf
                                    <input type="hidden" name="customer_id" value="{{ $bookingData['customer']['user_id'] }}">
                                    @if (isset($bookingData['venue_group']['user_id']) && $bookingData['venue_group']['user_id']>0)
                                    <input type="hidden" name="selected_venue_group_id" value="{{ $bookingData['venue_group']['user_id']}}">    
                                    @endif
                                    @if (isset($bookingData['preferred_photographer_id']) && $bookingData['preferred_photographer_id']>0)
                                    <input type="hidden" name="selected_preferred_photographer_id" value="{{ $bookingData['preferred_photographer_id']}}">    
                                    @endif

                                    {{-- List of items --}}
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="text" name="firstname"
                                                    class="form-control @error('firstname') is-invalid @enderror"
                                                    placeholder="First name" required value="{{ $bookingData['customer']['userinfo'][0]['firstname'] }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-user"></span>
                                                    </div>
                                                </div>
                                                @error('firstname')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="text" name="lastname"
                                                    class="form-control @error('lastname') is-invalid @enderror"
                                                    placeholder="Last name" required value="{{ $bookingData['customer']['userinfo'][0]['lastname'] }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-user"></span>
                                                    </div>
                                                </div>
                                                @error('lastname')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="email" name="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    placeholder="Email" required value="{{ $bookingData['customer']['userinfo'][0]['email'] }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-envelope"></span>
                                                    </div>
                                                </div>
                                                @error('email')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="password" name="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="Password" required>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-envelope"></span>
                                                    </div>
                                                </div>
                                                @error('password')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="text" required name="phone"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    placeholder="Phone" value="{{ $bookingData['customer']['userinfo'][0]['phone'] }}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-phone"></span>
                                                    </div>
                                                </div>
                                                @error('phone')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>


                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <select name="relation_with_event" required class="form-control select2bs4"
                                                    placeholder="Relationship with Event">
                                                    @php echo relation_with_event_options($bookingData['customer']['userinfo'][0]['relation_with_event']);@endphp
                                                </select>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-building"></span>
                                                    </div>
                                                </div>
                                                @error('relation_with_event')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            <label>Preffered Photographer</label>
                                            <div class="input-group mb-3">
                                                <select name="preferred_photographer_id" required class="form-control select2bs4"
                                                    placeholder="Preffered Photographer">
                                                    <option value="No" selected>No Preffrence</option>
                                                    @php echo get_photographer_options($bookingData['preferred_photographer_id']); @endphp
                                                    
                                                </select>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span class="fas fa-building"></span>
                                                    </div>
                                                </div>
                                                @error('preferred_photographer_id')
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
                                            <label>Groom information</label>
                                            <div class="input-group mb-3">
                                                <input placeholder="Groom Name" type="text" name="groom_name" required
                                                    value="{{ $bookingData['groom_name']}}"
                                                    class=" form-control @error('groom_name') is-invalid @enderror">
                                                @error('groom_name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-5">

                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <input required value="{{ $bookingData['groom_home_phone']}}"
                                                    placeholder="Groom Home Phone No." type="text"
                                                    name="groom_home_phone"
                                                    class=" form-control @error('groom_home_phone') is-invalid @enderror">
                                                @error('groom_home_phone')
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
                                                <input placeholder="Groom Contact No." type="text" name="groom_mobile"
                                                    required value="{{ $bookingData['groom_mobile'] }}"
                                                    class=" form-control @error('groom_mobile') is-invalid @enderror">
                                                @error('groom_mobile')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
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
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-10">
                                            <div class="input-group mb-3">
                                                <textarea placeholder="Billing Address (e.g street address, apt., city, state, and zip code) "
                                                    name="groom_billing_address" class=" form-control @error('groom_billing_address') is-invalid @enderror">{{ $bookingData['groom_billing_address']}}</textarea>
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
                                                <input placeholder="Bride Name" type="text" name="bride_name" required
                                                    value="{{ $bookingData['bride_name'] }}"
                                                    class=" form-control @error('bride_name') is-invalid @enderror">
                                                @error('bride_name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="col-5">

                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <input required value="{{ $bookingData['bride_home_phone'] }}"
                                                    placeholder="Bride Home Phone No." type="text"
                                                    name="bride_home_phone"
                                                    class=" form-control @error('bride_home_phone') is-invalid @enderror">
                                                @error('bride_home_phone')
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
                                                <input placeholder="Bride Contact No." type="text" name="bride_mobile"
                                                    required value="{{ $bookingData['bride_mobile'] }}"
                                                    class=" form-control @error('bride_mobile') is-invalid @enderror">
                                                @error('bride_mobile')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
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
                                            <div class="input-group date" id="reservationdate"
                                                data-target-input="nearest">
                                                <input name="date_of_event" placeholder="Event Date (09/22/2022)"
                                                    value="{{ $bookingData['date_of_event'] }}" type="text"
                                                    class="form-control datetimepicker-input @error('date_of_event') is-invalid @enderror"
                                                    data-target="#reservationdate" />
                                                <div class="input-group-append" data-target="#reservationdate"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="far fa-calendar-alt"></i>
                                                    </div>
                                                </div>
                                                @error('date_of_event')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <label>Venue Group/Hall</label>
                                            <div class="input-group mb-3">
                                                <select onchange="changeVenueGroup()" name="venue_group_id"
                                                    id="venue_group_id" class="form-control select2bs4"
                                                    placeholder="Select Venue Group">
                                                    @php
                                                        echo get_venue_group_options($bookingData['venue_group']['userinfo'][0]['id']);
                                                    @endphp
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div id="other_venue_group"></div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        
                                        <div class="col-5" >
                                                <div class="input-group mb-3" style="margin-top:2rem;">
                                                    <div class="form-group clearfix">
                                                        <label>Deposit Needed?  : </label>&nbsp;
                                                        <div class="icheck-primary d-inline">
                                                            <input value="YES" type="radio" id="desposite_needed1" name="payment_source" checked="">
                                                            <label for="desposite_needed1">YES</label>
                                                        </div> &nbsp;
                                                        <div class="icheck-primary d-inline">
                                                            <input value="NO" type="radio" id="deposit_needed2" name="payment_source">
                                                            <label for="deposit_needed2">NO</label>
                                                        </div>
                                                    </div>
                                                                                                
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <label>Packages</label>
                                            <div class="input-group mb-3">
                                                <select name="venue_group_id"
                                                    id="venue_group_id" class="form-control select2bs4"
                                                    placeholder="Select Venue Group">
                                                    @php
                                                    $package_id=NULL;
                                                    if($bookingData['package_id']>0)
                                                    $package_id=$bookingData['package_id'];
                                                        echo get_packages_options($package_id);
                                                    @endphp
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>

                                    {{-- Package Details --}}
                                 
                                    {{-- $packages=get_packages();
                                    $total_items=count($packages);
                                    foreach ($packages as $key => $packageData)
                                       --}}
                                    {{-- <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-2">
                                            <div class="form-group clearfix">
                                                <div class="icheck-primary d-inline">
                                                    <input value="{{ phpslug($packageData['id']) }}" type="radio"
                                                        id="package_name_{{ $packageData['id'] }}" name="package_id"
                                                        checked>
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
                                    </div> --}}
                                 
                                    <div id="more_packages"></div>
                                    <div class="row form-group">
                                        <div class="col-11">&nbsp;</div>
                                        <div class="col-1" id="btn_manual_pgk">
                                            <div style="width: 90px; float:right;"
                                                onclick="addmore_items('more_packages')"
                                                class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i>
                                                Additional Charge</div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-2">&nbsp;</div>
                                        <div class="col-2"><strong>Who is Paying?</strong></div>
                                        <div class="col-5">
                                            <div class="input-group mb-3">
                                                <div class="btn-group">
                                                    <input type="hidden" value="{{old('who_is_paying')}}" name="who_is_paying" id="who_is_paying"
                                                        value="customer">
                                                    <button value="customer"  onclick=select_who_is_paying('customer')
                                                        id="customer" type="button" style="width: 150px"
                                                        class=" btn btn-primary @if (old('who_is_paying')=='customer' || old('who_is_paying')=='')
                                                        active
                                                        @endif
                                                        " >Customer</button>
                                                    <button value="venue_group" onclick=select_who_is_paying('venue_group')
                                                        id="venue_group" type="button" style="width: 150px"
                                                        class="btn btn-primary @if (old('who_is_paying')=='venue_group')
                                                        active
                                                        @endif ">Venue Group</button>
                                                    <button value="both" onclick=select_who_is_paying('both')
                                                        id="both" type="button" style="width: 150px"
                                                        class="btn btn-primary @if (old('who_is_paying')=='both')
                                                        active
                                                        @endif ">Siplit in Both</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2">&nbsp;</div>
                                    </div>
                                    <div id="user_payment_inputs" @if (old('who_is_paying')=='both') style="display:block" @endif>
                                        @if (old('who_is_paying')=='both') <div class="row form-group"><div class="col-2">&nbsp;</div><div class="col-3"><label>Customer</label><div class="input-group mb-2"><input type="number" name="customer_to_pay" placeholder="How much?" required="" class="form-control"></div></div><div class="col-1">&nbsp;</div><div class="col-3"><label>Venue Group</label><div class="input-group mb-2"><input type="number" name="venue_group_to_pay" placeholder="How much?" required="" class="form-control"></div></div></div> @endif

                                    </div>
                                    <div class="row form-group">
                                        <div class="col-4">&nbsp;</div>
                                        <div class="col-4">
                                            <div class="input-group mb-3">
                                                <div class="form-group clearfix">
                                                    <label>Payment Source : </label>&nbsp;
                                                    <div class="icheck-primary d-inline">
                                                        <input value="{{ phpslug('Credit Card') }}" type="radio"
                                                            id="payment_source1" name="payment_source" checked>
                                                        <label for="payment_source1">Credit Card </label>
                                                    </div> &nbsp;
                                                    <div class="icheck-primary d-inline">
                                                        <input value="{{ phpslug('Zelle') }}" type="radio"
                                                            id="payment_source2" name="payment_source">
                                                        <label for="payment_source2">Zelle</label>
                                                    </div>
                                                    <div class="icheck-primary d-inline">
                                                        <input value="{{ phpslug('Cheque') }}" type="radio"
                                                            id="payment_source3" name="payment_source">
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
                                            @php
                                                $photographer=(get_photographer_options_with_count());
                                                $total_photographers=$photographer['total'];
                                            @endphp
                                            <div class="input-group mb-3" style="margin-top:2rem;">
                                                <select placeholder="Select Photographer" type="text"
                                                    name="photographer_id[]" required class=" select2bs4 form-control">
                                                    @php echo $photographer['options']; @endphp
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="input-group mb-3" style="margin-top:2rem;">
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
                                        <div class="col-2">
                                            <label>Collect Payment</label>
                                            <div class="input-group mb-3">
                                            <div class="icheck-primary d-inline">
                                                <input value="1" type="radio"
                                                    id="collect_payment1" name="collected_by_photographere[]">
                                                <label for="collect_payment1">YES</label>
                                            </div>
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                @if ($total_photographers>1)
                                <div id="photographer_list"></div>
                                <div id="photographer_btn" class="row form-group">
                                    <div class="col-11">&nbsp;</div>
                                    <div class="col-1">
                                        <div style="width: 130px; float:right;" onclick="addmore_photographers()"
                                            class="btn btn-success btn-block btn-sm"><i
                                                class="fas fa-plus"></i>Photographer</div>
                                    </div>
                                </div>
                                @endif
                                    
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-10">
                                            <div class="input-group mb-3">
                                                <textarea placeholder="Any Special Note" name="notes_by_booking"
                                                    class=" form-control @error('notes_by_booking') is-invalid @enderror">{{ old('notes_by_booking') }}</textarea>
                                                @error('notes_by_booking')
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
        var counter = 1;
        $(function() {
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            });
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
        // Select booking Type Single/Multi
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
                '<div class="col-2"><div class="input-group mb-3"><input placeholder="Title" type="text" name="title_for_charges" required  class="form-control" ></div></div>';
            itemHTML +=
                '<div class="col-1"><div class="input-group mb-3"><input placeholder="Price" type="text" value="200" name="add_price" required  class=" form-control" ></div></div>';
            itemHTML +=
                '<div class="col-7"><div class="input-group mb-3"><textarea placeholder="Add descriptions of addional charges/taxes" type="number" name="description"  class="form-control" ></textarea></div></div>';
            itemHTML += '<div class="col-1"></div></div>'
            itemHTML222 =
                '<div class="col-1"><div style="width:20px; cursor:pointer; padding:10px; color:red;"><i onclick=$("#manual_item_' +
                counter + '").remove() class="fas fa-minus"></i></div></div></div>';

            $('#' + cat_id).append('<div id="manual_item_' + counter + '">' + itemHTML + '</div>');
            $('#btn_manual_pgk').remove();

        }
        // Ajax to Update Lead Data
        var total_photographers={{$total_photographers;}}
        photographer_counter=1
        function addmore_photographers() {
            var sendInfo = {
                action: 'show_photographer',
            };
            photographer_counter++;
            console.log('photographer_counter'+photographer_counter);
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
                        if(photographer_counter==total_photographers)
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
    </script>
@endsection
