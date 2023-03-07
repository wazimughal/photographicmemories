@extends('adminpanel.admintemplate')
@push('title')
    <title>Add Venue Groups | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Add New Venue groups</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Add New Venue group</li>
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
                                <h3 class="card-title">Add New Venue groups</h3>
                            </div>
                            <div class="card-body">
                              <div class="row">
                                <div class="col-3">&nbsp;</div>
                                <div class="col-6">
                                 <!-- flash-message -->
                                <div class="flash-message">
                                  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                                    @if(Session::has('alert-' . $msg))
                              
                                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                                    @endif
                                  @endforeach
                                </div> <!-- end .flash-message -->
                                </div>
                                <div class="col-3">&nbsp;</div>
                              </div>
                                <form method="POST" action="{{ route('venuegroups.addsave') }}">
                                    @csrf
                                    
                                    
                                    
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                        <div class="input-group mb-3">
                                          <input type="text" name="vg_name" class="form-control @error('vg_name') is-invalid @enderror"
                                              placeholder="Group Venue Name" value="{{ old('vg_name') }}">
                                          <div class="input-group-append">
                                              <div class="input-group-text">
                                                  <span class="fas fa-building"></span>
                                              </div>
                                          </div>
                                          @error('vg_name')
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
                                          <input type="text" name="vg_manager_name" class="form-control @error('vg_manager_name') is-invalid @enderror"
                                              placeholder="Group Venue Manager Name" value="{{ old('vg_manager_name') }}">
                                          <div class="input-group-append">
                                              <div class="input-group-text">
                                                  <span class="fas fa-user"></span>
                                              </div>
                                          </div>
                                          @error('vg_manager_name')
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
                                          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                              placeholder="Email" value="{{ old('email') }}">
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
                                          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                              placeholder="Password" value="{{ old('password') }}">
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
                                          <input type="text" name="vg_manager_phone" class="form-control @error('vg_manager_phone') is-invalid @enderror"
                                              placeholder="Manager Phone" value="{{ old('vg_manager_phone') }}">
                                          <div class="input-group-append">
                                              <div class="input-group-text">
                                                  <span class="fas fa-phone"></span>
                                              </div>
                                          </div>
                                          @error('vg_manager_phone')
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
                                            <select id="city" onChange="changeCity()" name="city" class="form-control select2bs4 @error('city') is-invalid @enderror" placeholder="Select City">@php echo getCitiesOptions(); @endphp</select>
                                            </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    
                                    <div class="row form-group">
                                        <div class="col-3">&nbsp;</div>
                                        <div class="col-6">
                                        <div class="input-group mb-3">
                                          <input type="text" name="address" id="vg_address" class="form-control @error('address') is-invalid @enderror"
                                              placeholder="Venue Group Address" value="{{ old('address') }}">
                                          <div class="input-group-append">
                                              <div class="input-group-text">
                                                  <span class="fas fa-home"></span>
                                              </div>
                                          </div>
                                          @error('address')
                                              <div class="invalid-feedback">
                                                  {{ $message }}
                                              </div>
                                          @enderror
                                      </div>
                                        </div>
                                        <div class="col-3">&nbsp;</div>
                                    </div>
                                    
                                    {{-- New Row Button --}}
                                    <div class="row form-group">
                                        <div class="col-5">&nbsp;</div>
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-outline-success btn-block btn-lg"><i
                                                    class="fa fa-save"></i> Save</button>
                                        </div>
                                        <div class="col-5">&nbsp;</div>

                                    </div>
                                </form>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>

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
 {{-- For google Address --}}
 <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key={{config('constants.google_api_key')}}"></script> 

 <script>
    @php
        $address=['vg_address']   
        @endphp
    $(document).ready(function () {
        var autocomplete;
        @foreach ($address as $key=>$addr )
        autocomplete = new google.maps.places.Autocomplete((document.getElementById('{{$addr}}')), {
            types: ['geocode']
           
        });  
        @endforeach
      
    
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var near_place = autocomplete.getPlace();
        });
    });

    $(function() {
        $('.select2bs4').select2({
            theme: 'bootstrap4'
            });
            // Shorthand for $( document ).ready()
       
        });
        function changeCity() {
            selectOption = $('#city option:selected').text();
            
            if (selectOption == 'Other') {
                otherCity ='<div class="row form-group"><div class="col-3">&nbsp;</div><div class="col-6"><div class="input-group mb-3"><input  type="text" name="othercity" class="form-control" placeholder="Please enter City" required></div></div><div class="col-3">&nbsp;</div></div>';
                $('#othercity').html(otherCity);
            } else {
                $('#othercity').html('');
            }
        };
        function changezipcode() {
            selectOption = $('#zipcode option:selected').text();
            
            if (selectOption == 'Other') {
                otherZipCode ='<div class="row form-group"><div class="col-3">&nbsp;</div><div class="col-6"><div class="input-group mb-3"><input  type="text" name="otherzipcode" class="form-control" placeholder="Please enter Zip Code" required></div></div><div class="col-3">&nbsp;</div></div>';
                $('#otherzipcode').html(otherZipCode);
            } else {
                $('#otherzipcode').html('');
            }
        };
        </script>
 @endsection