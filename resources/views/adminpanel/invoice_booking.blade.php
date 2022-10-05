@extends('adminpanel.admintemplate')
@push('title')
    <title>Invoice Booking | {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Invoice Booking</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Invoice Booking</li>
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
                                <h3 class="card-title">Invoice Booking</h3>
                            </div>
                            <div class="card-body">

                                @php
                                    $packageDetails = get_package_by_id($bookingData['package_id']);
                                    $overtime = $bookingData['overtime_hours'] * $bookingData['overtime_rate_per_hour'];
                                @endphp
                             
                                <div class="row">

                                    <div class="col-md-12">
                                 
                                                
                                                        <div class="row alert-secondary" >
                                                            <div class="col-10" >
                                                             
                                                                <a href="{{ url('admin/dashboard') }}" class="brand-link">
                                                                    <img src="{{ url('adminpanel/dist/img/logo_photographic.png') }}" alt="Thephotographic Memories" width="100%">
                                                                </a>
                                                                {{-- <i class="fas fa-globe"></i> Thephotographic Memories. --}}
                                                                
                                                            </div>
                                                            <div class="col-2" style="color: :#fff; padding:50px"><small class="float-right" style="color:#fff;">Date: {{date('d/m/Y')}}</small></div>
                                                        </div>
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
                                                                    @if($invoice_of=='venue_invoices')
                                                                    <strong>{{ $bookingData['venue_group']['userinfo'][0]['name'] }}</strong><br>
                                                                    {{ ($bookingData['venue_group']['userinfo'][0]['phone']!='')?$bookingData['venue_group']['userinfo'][0]['phone'].'<br>':'' }}
                                                                    @if (isset($bookingData['venue_group']))
                                                                    Venue Group:
                                                                    {{ $bookingData['venue_group']['userinfo'][0]['vg_name'] }}<br>
                                                                    @else
                                                                    Venue Group: {{ $bookingData['other_venue_group'] }}<br>
                                                                    @endif
                                                                    {{ $bookingData['venue_group']['userinfo'][0]['email'] }}<br>
                                                                    {{ $bookingData['venue_group']['userinfo'][0]['address'] }}
                                                                    @else
                                                                    <strong>{{ $bookingData['customer']['userinfo'][0]['name'] }}</strong><br>
                                                                    {{ $bookingData['customer']['userinfo'][0]['phone'] }}<br>
                                                                    {{ $bookingData['customer']['userinfo'][0]['email'] }}<br>
                                                                    {{ $bookingData['customer']['userinfo'][0]['address'] }}
                                                                    @endif
                                                                </address>
                                                            </div>
                                                            <div class="col-sm-4 invoice-col">
                                                                Event Details <br>
                                                                <b>Event Date</b> : {{ date('d/m/Y',$bookingData['date_of_event']) }}
                                                                {{ $bookingData['time_of_event'] != '' ? 'Event Time :' . $bookingData['time_of_event'] : '' }}
                                                                @if (isset($bookingData['venue_group']))
                                                                    <br>Venue Group:
                                                                    {{ $bookingData['venue_group']['userinfo'][0]['name'] }}
                                                                @else
                                                                   <br>Venue Group: {{ $bookingData['other_venue_group'] }}
                                                                @endif
                                                                Package : {{ $packageDetails['name'] }} <br>
                                                                Price : ${{ $packageDetails['price'] }} <br>
                                                                Total Cost: $@php echo $totalCost=$packageDetails['price']+ $bookingData['extra_price']+$overtime;@endphp <br>
                                                                @if($bookingData['who_is_paying'] == 1) 
                                                                {{-- This is for Venue Group --}}
                                                                Total Payment Will be Paid by Venue Group
                                                                @elseif ($bookingData['who_is_paying'] == 0)
                                                                Total Payment Will be Paid by Venue Group
                                                                @elseif ($bookingData['who_is_paying'] == 2)
                                                                Customer To Pay: {{$bookingData['customer_to_pay']}} <br>
                                                                Venue To Pay: {{$bookingData['venue_group_to_pay']}}
                                                                @endif

                                                                <?php
                                                                if($bookingData['who_is_paying']==2 && $invoice_of=='customer_invoices'){
                                                                    $total_amount_to_pay=$bookingData['customer_to_pay'];
                                                                }elseif($bookingData['who_is_paying']==2 && $invoice_of=='venue_invoices'){
                                                                    $total_amount_to_pay =$bookingData['venue_group_to_pay'];
                                                                }
                                                                ?>
                                                            </div>

                                                        </div>

                                                    <!-- /.tab-pane -->

                                        
                                        <div class="row">
                                            <div class="col-12 table-responsive">
                                              <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                  <th>No.</th>
                                                  <th>Date</th>
                                                  <th>User</th>
                                                  <th>Description</th>
                                                  <th>Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    @if ($invoice_of=='invoices')
                                                        
                                                   
                                                    <tr>
                                                        <td>1</td>
                                                        <td>22/11/2022</td>
                                                        <td>(Package:){{ $packageDetails['name'] }}</td>
                                                        <td>{{ $packageDetails['description'] }}</td>
                                                        <td>${{ $packageDetails['price'] }}</td>
                                                      </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>22/11/2022</td>
                                                        <td>Extra Charge</td>
                                                        <td>{{ $bookingData['extra_charge_desc'] }}</td>
                                                        <td>{{ $bookingData['extra_price'] > 0 ? '$' . $bookingData['extra_price'] : 0 }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>22/11/2022</td>
                                                        <td>Over time:</td>
                                                        <td><p>Staff Worked {{ $bookingData['overtime_hours'] }} hours
                                                                extra as over time .</p></td>
                                                        <td>${{ $overtime = $bookingData['overtime_hours'] * $bookingData['overtime_rate_per_hour'] }}
                                                            (${{ $bookingData['overtime_rate_per_hour'] }}/Hour)
                                                          </td>
                                                    </tr>
                                                    <tr>
                                                        <td>4</td>
                                                        <td colspan="3">Total Cost:</td>
                                                        <td>$@php echo $totalCost=$packageDetails['price']+ $bookingData['extra_price']+$overtime;@endphp</td>
                                                    </tr>
                                                 
                                                    <tr>
                                                        
                                                        <td colspan="5" class="text-center alert-secondary"><strong> Payment Received:</strong></td>
                                                        </tr>
                                                        @endif
                                                    <?php 
                                                    $recievedAmount=0;
                                                    $k=5;
                                                    if(isset($total_amount_to_pay) && $total_amount_to_pay>0)
                                                    $totalCost=$total_amount_to_pay;

                                                    foreach ($bookingData[$invoice_of] as $key=>$invoice){ 
                                                        $recievedAmount=$recievedAmount+$invoice['paid_amount'];
                                                        $totalCost
                                                        ?>

                                                    <tr>
                                                        <td>{{ $k++ }}</td>
                                                        <td>{{ date('d/m/Y', strtotime($invoice['created_at'])) }}</td>
                                                        <td>{{ ($invoice['slug']=='customer')?'Customer':'Venue Group'; }}</td>
                                                        <td>{{ $invoice['payee_name'] }}</td>
                                                        <td>{{ $invoice['paid_amount'] }}</td>
                                                        <div </tr>

                                                            <?php }?>

                                                            <tr>
                                                                <td>{{ $k++ }}</td>
        
                                                                <td colspan="3">Total Received:</td>
                                                                <td>${{(!isset($recievedAmount))?$recievedAmount=0:$recievedAmount}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>{{ $k++ }}</td>
                                                                <td colspan="3">Due Amount:</td>
                                                                <td>${{ $due_amount = $totalCost - $recievedAmount }}</td>
                                                            </tr>
                                                </tbody>
                                              </table>
                                            </div>
                                            <!-- /.col -->
                                          </div>
                                          <div class="row">
                                            <!-- accepted payments column -->
                                            <div class="col-6">
                                              <p class="lead">Payment Methods:</p>
                                              <img src="{{url('adminpanel/dist/img/credit/visa.png')}}" alt="Visa">
                                              <img src="{{url('adminpanel/dist/img/credit/mastercard.png')}}" alt="Mastercard">
                                              <img src="{{url('adminpanel/dist/img/credit/american-express.png')}}" alt="American Express">
                                              <img src="{{url('adminpanel/dist/img/credit/paypal2.png')}}" alt="Paypal">
                            
                                              <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                                                The above payment methods can be used to pay the payment to Photographic Memories.
                                              </p>
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-6">
                                              <p class="lead">Amount Due {{date('d/m/Y')}}</p>
                            
                                              <div class="table-responsive">
                                                <table class="table">
                                                  <tbody><tr>
                                                    <th style="width:50%">Total Cost:</th>
                                                    <td>${{$totalCost}}</td>
                                                  </tr>
                                                  <tr>
                                                    <th>Payment Received:</th>
                                                    <td>${{$recievedAmount}}</td>
                                                  </tr>
                                                  <tr>
                                                    <th>Due:</th>
                                                    <td>${{ $due_amount = $totalCost - $recievedAmount }}</td>
                                                  </tr>
                                                </tbody></table>
                                              </div>
                                            </div>
                                            <!-- /.col -->
                                          </div>
                                        
                                    </div>

                                   
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
