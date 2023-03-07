<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Event:{{ date(config('constants.date_formate'), $bookingData['date_of_event']) }} </title>

    <style>
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            position: relative;
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            color: #555555;
            background: #FFFFFF;
            font-family: verdana;
            font-size: 14px;
            font-family: verdana;
            max-width: 700px;
        }

        header {
            padding: 10px 0;
            margin-bottom: 20px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
            margin-top: 8px;
        }

        #logo img {
            height: 70px;
        }

        #company {
            float: right;
            text-align: right;
        }


        #details {
            margin-bottom: 50px;
        }

        #client {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
            float: left;
            width: 50%;
        }

        #client .to {
            color: #777777;
        }

        h2.name {
            font-size: 1.4em;
            font-weight: normal;
            margin: 0;
        }

        #invoice {
            float: right;
            text-align: right;
            width: 48%;
        }

        #invoice h1 {
            color: #0087C3;
            font-size: 2.4em;
            line-height: 1em;
            font-weight: normal;
            margin: 0 0 10px 0;
        }
        #invoice h2 {
            color: #D08E3E;
            font-size: 16px;
            line-height: 1em;
            font-weight: 700;
            margin: 0 0 10px 0;
        }

        #invoice .date {
            font-size: 1.1em;
            color: #777777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 20px;
            background: #EEEEEE;
            text-align: center;
            border-bottom: 1px solid #FFFFFF;
        }

        table th {
            white-space: nowrap;
            font-weight: normal;
        }

        table td {
            text-align: right;
        }

        table td h3 {
            color: #57B223;
            font-size: 1.2em;
            font-weight: normal;
            margin: 0 0 0.2em 0;
        }

        table .no {
            color: #FFFFFF;
            font-size: 1.6em;
            background: #57B223;
        }

        table .desc {
            text-align: left;
        }

        table .unit {
            background: #DDDDDD;
            text-align: left;
        }

        table .qty {text-align: left;}

        table .total {
            background: #57B223;
            color: #FFFFFF;
            text-align: right
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
        }

        table tbody tr:last-child td {
            border: none;
        }

        table tfoot td {
            padding: 10px 20px;
            background: #FFFFFF;
            border-bottom: none;
            font-size: 1.2em;
            white-space: nowrap;
            border-top: 1px solid #AAAAAA;
        }

        table tfoot tr:first-child td {
            border-top: none;
        }

        table tfoot tr:last-child td {
            color: #57B223;
            font-size: 1.4em;
            border-top: 1px solid #57B223;

        }

        table tfoot tr td:first-child {
            border: none;
        }

        #thanks {
            font-size: 2em;
            margin-bottom: 50px;
        }

        #notices {
            padding-left: 6px;
            border-left: 6px solid #0087C3;
        }

        #notices .notice {
            font-size: 1.2em;
        }

        footer {
            color: #777777;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #AAAAAA;
            padding: 8px 0;
            text-align: center;
        }
        #logo span{
                color: #54595F;
                font-size: 22px;
                font-weight: 700;
                margin-left: -8px;
            }
            h3{
                margin:5px 0 0 0 !important;
            }
            .booking_status{  padding: 5px; text-align: center; margin: 10px 0; background: #DDDDDD;}
    </style>
</head>
@php

        
        $overtime_cost =$bookingData['overtime_hours']*$bookingData['overtime_rate_per_hour'];
      //$logo_url='https://office.oodlerexpress.com/adminpanel/dist/img/2.png';
      $logo_url='http://127.0.0.1:8000/adminpanel/dist/img/logo_photographic.png';
      //$logo_url=storage_path('app/public/2.png');
    @endphp
<body>
    <header class="clearfix">
        <div id="logo" style="color: #CF8E2F; font-size:35px; font-weight: 700;">
          {{-- Photographic <span>Memories</span> --}}
            <img alt="Thephotographic Memories" src="{{$logo_url}}"
                width="100px" height="100px">
        </div>
        <div id="company">
            <h2 class="name">{{config('constants.app_name')}}</h2>
            <div>{{config('constants.address')}}</div>
            <div>{{config('constants.phone')}}</div>
            <div><a href="mailto:{{config('constants.admin_email')}}">{{config('constants.admin_email')}}</a></div>
        </div>
        </div>
    </header>
    <main>
        <div class="booking_status">{{booking_status_for_msg($bookingData['status'])}}</div>
        <div id="details" class="clearfix">
            <div id="client">
                <h3>Customer:</h3>
                <div class="name">Name:{{$bookingData['customer']['userinfo'][0]['name']}}</div>
                @if($bookingData['customer']['userinfo'][0]['phone']!='')
                <div class="address">Ph:{{$bookingData['customer']['userinfo'][0]['phone']}}</div>
                @endif
                
                @if($bookingData['customer']['userinfo'][0]['email']!='')
                <a href="mailto:{{$bookingData['customer']['userinfo'][0]['email']}}">{{$bookingData['customer']['userinfo'][0]['email']}}</a>
                @endif
                
                <h3>Cost:</h3>
                <div><span>Package: </span> {{ $bookingData['package']['name'] }}</div>
                <div><span>Price: </span> ${{ $bookingData['package']['price'] }} </div>
                @if ($bookingData['overtime_hours']>0)
                <div><span>Over Time: </span> {{ $bookingData['overtime_hours'] }} hours</div>    
                @endif
                @if ($bookingData['overtime_rate_per_hour']>0)
                <div><span>Rate/Hour: </span> ${{ $bookingData['overtime_rate_per_hour'] }} hours</div>    
                @endif
                
                <div><span>Total Cost: </span>$@php echo $totalCost=$bookingData['package']['price']+ $bookingData['extra_price']+$overtime_cost;
                    $customer_to_pay=$bookingData['extra_price']+$overtime_cost;
                    $venue_to_pay=0;
                    @endphp
                    </div>
                @if ($bookingData['who_is_paying'] == 1)
                    {{-- This is for Venue Group --}}
                    <span>Total Payment Will be Paid by Venue Group</span>
                @elseif ($bookingData['who_is_paying'] == 0)
                    <span>Total Payment Will be Paid by Customer</span>
                @elseif ($bookingData['who_is_paying'] == 2)
                    <div><span>Customer to pay: </span> ${{ $customer_to_pay=$customer_to_pay+$bookingData['customer_to_pay'] }}</div>
                    <div><span>Venue to pay:</span> ${{ $venue_to_pay=$bookingData['venue_group_to_pay'] }}</div>
                @endif
                
                @php
                if ($bookingData['groom_name'] != ''){
                    echo '<h3>Groom Detail:</h3>';
                    echo '<div><span>Name:</span>'.$bookingData['groom_name'].'</div>';
                }
                if ($bookingData['groom_mobile'] != '')
                echo '<div><span> Mob:</span>'.$bookingData['groom_mobile'].'</div>';
                if ($bookingData['groom_email'] != '')
                echo '<a href="maileto:'.$bookingData['groom_email'].'">'.$bookingData['groom_email'].'</a>';
                if ($bookingData['groom_billing_address'] != '')
                echo '<div><span>Add:</span>'.$bookingData['groom_billing_address'].'</div>';
                
                if ($bookingData['bride_name'] != ''){
                    echo '<h3>Bride Detail:</h3>';
                    echo '<div><span>Name:</span>'.$bookingData['bride_name'].'</div>';
                }
                if ($bookingData['bride_mobile'] != '')
                echo '<div><span> Mob:</span>'.$bookingData['bride_mobile'].'</div>';
                if ($bookingData['bride_email'] != '')
                echo '<a href="maileto:'.$bookingData['bride_email'].'">'.$bookingData['bride_email'].'</a>';
                if ($bookingData['bride_billing_address'] != '')
                echo '<div><span>Add:</span>'.$bookingData['bride_billing_address'].'</div>';
                @endphp
              </div>
            <div id="invoice">
                
                <h2>Date of Event: {{ date(config('constants.date_formate'), $bookingData['date_of_event']) }}</h2>
                @if ($bookingData['time_of_event'] != '')
                <div class="date"><span> Event Time: </span>{{ $bookingData['time_of_event'] }}
                @endif
                <h3 class="name">Venue:{{($bookingData['other_venue_group']!='')?$bookingData['other_venue_group']:$bookingData['venue_group']['userinfo'][0]['vg_name']}}</h3>
                @if($bookingData['venue_group']['userinfo'][0]['vg_manager_name']!='')
                <div class="address">Manager:{{$bookingData['venue_group']['userinfo'][0]['vg_manager_name']}}</div>
                @endif
                @if($bookingData['venue_group']['userinfo'][0]['vg_manager_phone']!='')
                <div class="address">Ph:{{$bookingData['venue_group']['userinfo'][0]['vg_manager_phone']}}</div>
                @endif
                @if($bookingData['venue_group']['userinfo'][0]['city']!='')
                <div class="address">City:{{$bookingData['venue_group']['userinfo'][0]['city']}}</div>
                @endif
                @if($bookingData['venue_group']['userinfo'][0]['email']!='')
                <a href="mailto:{{$bookingData['venue_group']['userinfo'][0]['email']}}">{{$bookingData['venue_group']['userinfo'][0]['email']}}</a>
                @endif
                <h3>PHOTOGRAPHER  </h3>
                <div> @php
                  if($bookingData['photographer'])
                  foreach ($bookingData['photographer'] as $key => $photographer) {
                    echo $photographer['userinfo'][0]['name'].'<br>';
                  }
               @endphp</div>
                @if($bookingData['collected_by_photographer']==1)
                 Collect Payment: <strong>YES</strong> <br>
                  How Much? : ${{$bookingData['photographer_to_collect_amount']}}
                @endif
            </div>
        </div>
        @php
                if ($bookingData['who_is_paying'] == 2 && $invoice_of == 'customer_invoices') {
                    $total_amount_to_pay = $bookingData['customer_to_pay'];
                } elseif ($bookingData['who_is_paying'] == 2 && $invoice_of == 'venue_invoices') {
                    $total_amount_to_pay = $bookingData['venue_group_to_pay'];
                }
        @endphp
       
        <table border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2"><strong>Total COST</strong></td>
                    <td><strong>${{$totalCost}}</strong></td>
                </tr>
                <tr>
                    <th class="no">#</th>
                    {{-- <th class="desc">DESCRIPTION</th> --}}
                    <th colspan="2" class="unit">DATE</th>
                    <th class="qty">PAYEE NAME</th>
                    <th class="total">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
               
              <?php 
            $recievedAmount=0;
            $k=1;
            if(isset($total_amount_to_pay) && $total_amount_to_pay>0)
            $totalCost=$total_amount_to_pay;
//p($bookingData);
            foreach ($bookingData['invoices'] as $key=>$invoice){ 
               
                $recievedAmount=$recievedAmount+$invoice['paid_amount'];
                $totalCost
                ?>
                
                <tr>
                    <td class="no">{{$k++}}</td>
                    {{-- <td class="desc">
                        <h3>Payment Received</h3>
                    </td> --}}
                    <td colspan="2" class="unit">{{ date(config('constants.date_formate'), strtotime($invoice['created_at'])) }}</td>
                    <td  class="qty">{{ $invoice['payee_name'] }}</td>
                    <td class="total">${{ $invoice['paid_amount'] }}</td>
                </tr>
                <?php }?>
                
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2">TOTAL PAID AMOUNT</td>
                    <td>${{ !isset($recievedAmount) ? ($recievedAmount = 0) : $recievedAmount }}</td>
                </tr>
                {{-- <tr>
                    <td colspan="2"></td>
                    <td colspan="2">DELIVERY COST</td>
                    <td>${{$totalCost}}</td>
                </tr> --}}
                <tr>
                    <td colspan="2"></td>
                    <td colspan="2" style="color:red">DUE AMOUNT</td>
                    <td style="color:red">${{ $due_amount =$totalCost- $recievedAmount }}</td>
                </tr>
            </tfoot>
        </table>
        {{-- <div id="thanks">Thank you!</div> --}}
        {{-- <div id="notices">
            <div>NOTICE:</div>
            <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
        </div> --}}
    </main>
    <footer>
        Invoice was created on a computer and is valid without the signature and seal.
    </footer>
</body>

</html>
