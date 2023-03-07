@extends('adminpanel.admintemplate')
@push('title')
    <title>View Pencil | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>View Pencil</h1>
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
                                <h3 class="card-title">View Pencil</h3>
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
                                                @if ($user->group_id==config('constants.groups.admin'))
                                                <div class="row">
                                                    <div class="col-10">&nbsp;</div>
                                                    <div class="col-2">
                                                        <a href="{{route('pencils.pencils_edit_form',$bookingData['id'])}}" class="btn btn-danger pull-right btn-block btn-sm"><i class="fas fa-edit"></i>
                                                            Edit</a>  
                                                    </div>
                                                </div>
                                                
                                                @endif
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div class="tab-content">
                                                
                                                        <div class="row invoice-info">
                                                            <div class="col-sm-12 invoice-col">
                                                                Event Details <br>
                                                                <b>Event Date</b> : {{ date(config('constants.date_formate'),$bookingData['date_of_event']) }}<br>
                                                                {{ $bookingData['time_of_event'] != '' ? 'Event Date :' . $bookingData['time_of_event'].'<br>' : '' }}
                                                                @if (isset($bookingData['venue_group']))
                                                                    Venue Group:
                                                                    {{ $bookingData['venue_group']['userinfo'][0]['vg_name'] }}<br>
                                                                @else
                                                                    Venue Group: {{ $bookingData['other_venue_group'] }}<br>
                                                                   
                                                                @endif
                                                                <br>
                                                                {{-- Package : {{ $packageDetails['name'] }} <br>
                                                                Price : ${{ $packageDetails['price'] }} <br>
                                                                Total Cost: $@php echo $totalCost=$packageDetails['price']+ $bookingData['extra_price']+$overtime;@endphp --}}
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row invoice-info">
                                                            <div class="col-sm-6 invoice-col">
                                                                <div class="card-header alert-secondary">
                                                                    <h3 class="card-title">Groom Info</h3>
                                                                </div>
                                                                <table >
                                                                <tbody>
                                                                    
                                                                    <tr>
                                                                        <td><strong>Name: </strong>{{ $bookingData['groom_name'] }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Email: </strong>{{ $bookingData['groom_email'] }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Home Phone: </strong>{{ $bookingData['groom_home_phone'] }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Mobile No.: </strong>{{ $bookingData['groom_mobile'] }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>Billing Address: </strong>{{ $bookingData['groom_billing_address'] }}</td>
                                                                    </tr>
                                                                </tbody>
                                                                </table>
                                                            </div>
                                                            <!-- /.col -->
                                                            <div class="col-sm-6 invoice-col">
                                                                <div class="card-header alert-secondary">
                                                                    <h3 class="card-title">bride Info</h3>
                                                                </div>
                                                                <table >
                                                                    <tbody>
                                                                        
                                                                        <tr>
                                                                            <td><strong>Name: </strong>{{ $bookingData['bride_name'] }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Email: </strong>{{ $bookingData['bride_email'] }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Home Phone: </strong>{{ $bookingData['bride_home_phone'] }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Mobile No.: </strong>{{ $bookingData['bride_mobile'] }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Billing Address: </strong>{{ $bookingData['bride_billing_address'] }}</td>
                                                                        </tr>
                                                                    </tbody>
                                                                    </table>
                                                            </div>
                                                            

                                                        </div>


                                                    <!-- /.tab-pane -->

                                                </div>
                                                <!-- /.tab-content -->
                                            </div><!-- /.card-body -->
                                        </div>
                                        <!-- /.card -->
                                        @if ($user->group_id==config('constants.groups.admin'))
                                        {{-- This section is for Comments for admin --}}
                                        <div class="card">
                                            <div class="card-header p-2">
                                                <strong> Notes Section (For Admin only) </strong>
                                            </div><!-- /.card-header -->
                                            <div class="card-body">
                                                <div id="submit_pencil_comments_replace">
                                                    @php
                                                       // p($bookingData['comments']);
                                                    @endphp
                                                    @foreach ($bookingData['pencil_comments'] as $key=>$comment )
                                                    <div class="row border">
                                                    <div class="col-12">
                                                        <strong>{{$comment['user']['name']}} ({{$comment['slug']}})  </strong> {{date(config('constants.date_and_time'),strtotime($comment['created_at']))}}<br>
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
                                                    <form method="post" id="submit_pencil_comments" >
                                                        <input type="hidden" name="group_id" value="{{$user->group_id}}">
                                                        <input type="hidden" name="action" value="submit_pencil_comments">
                                                        <input type="hidden" name="slug" value="{{$userData['get_groups']['slug']}}">
                                                        <input type="hidden" name="for_section" value="pencil_comments">
                                                            <div class="form-group">
                                                                <label for="inputDescription">Comment</label>
                                                                <textarea id="comments" name="comment" placeholder="Write comment about the Pencil" class="form-control" rows="4"></textarea></br>
                                                                <button
                                                                    onclick="do_action({{ $bookingData['id'] }},'submit_pencil_comments')"
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
@section('footer-js-css')
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
            $('#_loader').show();
            $.ajax({
                url: "{{ route('bookings.ajaxcall',$id) }}" ,
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

