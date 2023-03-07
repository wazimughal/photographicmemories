@extends('adminpanel.admintemplate')
@push('title')
    <title>Edit Pencil | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit Pencil</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Edit Pencil</li>
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
                                <h3 class="card-title">Edit pencil</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-3">&nbsp;</div>
                                    <div class="col-6">

                                        @if ($errors->any())
                                            {!! implode('', $errors->all('<div class="alert-danger">:message</div>')) !!}
                                        @endif
                                        <!-- flash-message -->
                                        <div class="flash-message">
                                           
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
                                @php
                                    //p($pencilData);
                                @endphp
                                <form method="POST" action="{{ route('pencils.save_pencil_edit_data',$id) }}">
                                    @csrf
                               <input type="hidden" name="customer_id" value="{{ $pencilData['customer']['user_id'] }}">
                               @if (isset($pencilData['venue_group']['user_id']) && $pencilData['venue_group']['user_id']>0)
                               <input type="hidden" name="selected_venue_group_id" value="{{ $pencilData['venue_group']['user_id']}}">    
                               @endif
                               @if (isset($pencilData['preferred_photographer_id']) && $pencilData['preferred_photographer_id']>0)
                               <input type="hidden" name="selected_preferred_photographer_id" value="{{ $pencilData['preferred_photographer_id']}}">    
                               @endif
                               
                               
                                
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="text" name="firstname"
                                                    class="form-control @error('firstname') is-invalid @enderror"
                                                    placeholder="First name" required value="{{ $pencilData['customer']['userinfo'][0]['firstname'] }}">
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
                                                    placeholder="Last name" required value="{{ $pencilData['customer']['userinfo'][0]['lastname'] }}">
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
                                            <label>Leave it empty, if you don't want to change email .[ {{ $pencilData['customer']['userinfo'][0]['email'] }} ]</label>
                                            <div class="input-group mb-3">
                                                <input type="email" name="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    placeholder="Email" >
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
                                                <input type="text" required name="phone"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    placeholder="Phone" value="{{ $pencilData['customer']['userinfo'][0]['phone'] }}">
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
                                                    @php echo relation_with_event_options($pencilData['customer']['userinfo'][0]['relation_with_event']);@endphp
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
                                                <select name="preferred_photographer_id" class="form-control select2bs4"
                                                    placeholder="Preffered Photographer">
                                                    <option value="" selected>No Preffrence</option>
                                                    @php echo get_photographer_options($pencilData['preferred_photographer_id']); @endphp
                                                    
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
                                                <input placeholder="Groom Name" type="text" name="groom_name" 
                                                    value="{{ $pencilData['groom_name']}}"
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
                                                <input  value="{{ $pencilData['groom_home_phone']}}"
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
                                                     value="{{ $pencilData['groom_mobile'] }}"
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
                                                     value="{{ $pencilData['groom_email'] }}"
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
                                                    name="groom_billing_address" class=" form-control @error('groom_billing_address') is-invalid @enderror">{{ $pencilData['groom_billing_address']}}</textarea>
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
                                                <input placeholder="Bride Name" type="text" name="bride_name" 
                                                    value="{{ $pencilData['bride_name'] }}"
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
                                                <input  value="{{ $pencilData['bride_home_phone'] }}"
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
                                                     value="{{ $pencilData['bride_mobile'] }}"
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
                                                     value="{{ $pencilData['bride_email'] }}"
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
                                                    name="bride_billing_address" class=" form-control @error('bride_billing_address') is-invalid @enderror">{{ $pencilData['bride_billing_address'] }}</textarea>
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
                                                    value="{{date(config('constants.date_formate'),$pencilData['date_of_event'])  }}" type="text"
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
                                                    $selected_vg='other';
                                                    if(isset($pencilData['venue_group']['userinfo'][0]['id']) && $pencilData['venue_group']['userinfo'][0]['id']!=''){
                                                        $selected_vg=$pencilData['venue_group']['userinfo'][0]['id'];
                                                    }
                                                    

                                                        echo get_venue_group_options($selected_vg);
                                                    @endphp
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-1">&nbsp;</div>
                                    </div>
                                    <div id="other_venue_group">
                                        @if ($selected_vg=='other')
                                        <div class="row form-group"><div class="col-1">&nbsp;</div><div class="col-10"><div class="input-group mb-3"><textarea placeholder="Name and Address of Venue Group" name="other_venue_group" class=" form-control" required>{{ $pencilData["other_venue_group"] }}</textarea></div></div><div class="col-1">&nbsp;</div></div>
                                            
                                        @endif
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-1">&nbsp;</div>
                                        <div class="col-10">
                                            <div class="input-group mb-3">
                                                <textarea placeholder="Any Special Note" name="notes_by_pencil"
                                                    class=" form-control @error('notes_by_pencil') is-invalid @enderror">{{ $pencilData['notes_by_pencil'] }}</textarea>
                                                @error('notes_by_pencil')
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

        function changeVenueGroup() {
            selectOption = $('#venue_group_id option:selected').text();

            if (selectOption == 'Other') {
                otherVenueGroup =
                    '<div class="row form-group"><div class="col-1">&nbsp;</div><div class="col-10"><div class="input-group mb-3"><textarea placeholder="Name and Address of Venue Group" name="other_venue_group" class=" form-control" required>{{ $pencilData["other_venue_group"] }}</textarea></div></div><div class="col-1">&nbsp;</div></div>';
                $('#other_venue_group').html(otherVenueGroup);
            } else {
                $('#other_venue_group').html('');
            }
        };
    </script>
@endsection
