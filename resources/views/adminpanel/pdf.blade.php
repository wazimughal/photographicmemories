<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Customer Invoice</title>
    <style>
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #5D6975;
            text-decoration: underline;
        }

        body {
            position: relative;
            width: 19cm;
            height: 27.7cm;
            margin: 0 auto;
            color: #001028;
            background: #FFFFFF;
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-family: Arial;
        }

        header {
            padding: 10px 0;
            margin-bottom: 30px;
        }

        #logo {
            text-align: center;
            margin-bottom: 10px;
            background: #5D6975;
        }

        #logo img {
            xwidth: 260px;
        }

        h1 {
            border-top: 1px solid #5D6975;
            border-bottom: 1px solid #5D6975;
            color: #5D6975;
            font-size: 2.4em;
            line-height: 1.4em;
            font-weight: normal;
            text-align: center;
            margin: 0 0 20px 0;
            background: url(dimension.png);
        }

        #project {
            float: left;
        }

        #project span {
            color: #5D6975;
            text-align: right;
            width: 52px;
            margin-right: 10px;
            display: inline-block;
            font-size: 0.8em;
        }

        #company {
            float: right;
            text-align: right;
        }

        #project div,
        #company div {
            white-space: nowrap;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }

        table tr:nth-child(2n-1) td {
            background: #F5F5F5;
        }

        table th,
        table td {
            text-align: center;
        }

        table th {
            padding: 5px 20px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            font-weight: normal;
        }

        table .service,
        table .desc {
            text-align: left;
        }
        
        table td {
            padding: 5px;
            /* text-align: right; */
        }

        table td.service,
        table td.desc {
            vertical-align: top;
        }
      
        table td.unit,
        table td.qty,
        table td.total {
            font-size: 12px;
        }

        table td.grand {
            border-top: 1px solid #5D6975;
            ;
        }

        #notices .notice {
            color: #5D6975;
            font-size: 1.2em;
        }

        footer {
            color: #5D6975;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #C1CED9;
            padding: 8px 0;
            text-align: center;
        }
        .service{width:10%; text-align: center; font-size: 12px; font-family: Arial, Helvetica, sans-serif;}
        .qty{width: 14%; text-align: center;font-size: 12px; font-family: Arial, Helvetica, sans-serif;}
        .desc{width: 39%; text-align: center;font-size: 12px; font-family: Arial, Helvetica, sans-serif;}
        .total{width: 5%; text-align: center;font-size: 12px; font-family: Arial, Helvetica, sans-serif;}
    </style>
</head>

<body>
    @php
        $packageDetails = get_package_by_id($bookingData['package_id']);
        $overtime = $bookingData['overtime_hours'] * $bookingData['overtime_rate_per_hour'];
      
    @endphp

    <header class="clearfix">
        @if(config('app.url')!='http://localhost/')
        <div id="logo">
              <img src="{{ url('adminpanel/dist/img/logo_photographic.png') }}" alt="Thephotographic Memories">
        </div>
        @endif
        <h1>INVOICE: Booking-{{ date('dmY') }}{{ $bookingData['id'] }}</h1>
        <div id="company" class="clearfix">

            <div><span>Event Date:</span> {{ date(config('constants.date_formate'), $bookingData['date_of_event']) }}</div>
            @if ($bookingData['time_of_event'] != '')
                <div><span> Event Time: </span>{{ $bookingData['time_of_event'] }}
            @endif

            @if (isset($bookingData['venue_group']))
                <div><span>Venue:</span> {{ $bookingData['venue_group']['userinfo'][0]['vg_name'] }}</div>
            @else
                <div><span>Venue:</span> {{ $bookingData['other_venue_group'] }}</div>
            @endif

            <div><span>Package: </span> {{ $packageDetails['name'] }}</div>
            <div><span>Price: </span> ${{ $packageDetails['price'] }} </div>
            <div><span>Total Cost: </span>$@php echo $totalCost=$packageDetails['price']+ $bookingData['extra_price']+$overtime;@endphp</div>
            @if ($bookingData['who_is_paying'] == 1)
                {{-- This is for Venue Group --}}
                <h6>Total Payment Will be Paid by Venue Group</h6>
            @elseif ($bookingData['who_is_paying'] == 0)
                <h6>Total Payment Will be Paid by Venue Group</h6>
            @elseif ($bookingData['who_is_paying'] == 2)
                <div><span>Customer </span> ${{ $bookingData['customer_to_pay'] }}</div>
                <div><span>Venue:</span> ${{ $bookingData['venue_group_to_pay'] }}</div>
            @endif

        </div>
        <div id="project">

            @if ($invoice_of == 'venue_invoices')
                <div><span>Venue Name:</span> {{ $bookingData['venue_group']['userinfo'][0]['name'] }}</div>
                {{ $bookingData['venue_group']['userinfo'][0]['phone'] != '' ? $bookingData['venue_group']['userinfo'][0]['phone'] . '<br>' : '' }}
                @if (isset($bookingData['venue_group']))
                    <div><span>Venue Group</span>
                        {{ $bookingData['venue_group']['userinfo'][0]['vg_name'] }}
                    @else
                        {{ $bookingData['other_venue_group'] }}
                @endif
        </div>
        <div><span>Email</span> <a
                href="mailto:{{ $bookingData['venue_group']['userinfo'][0]['email'] }}">{{ $bookingData['venue_group']['userinfo'][0]['email'] }}</a>
        </div>
        <div><span>ADDRESS</span>{{ $bookingData['venue_group']['userinfo'][0]['address'] }}</div>
    @else
        <div><span>Name</span>{{ $bookingData['customer']['userinfo'][0]['name'] }}</div>
        <div><span>Phone</span>{{ $bookingData['customer']['userinfo'][0]['phone'] }}</div>
        <div><span>Email</span><a
                href="mailto:{{ $bookingData['customer']['userinfo'][0]['email'] }}">{{ $bookingData['customer']['userinfo'][0]['email'] }}</a>
        </div>
        <div><span>ADDRESS</span>{{ $bookingData['customer']['userinfo'][0]['address'] }}</div>
        @endif
        <div><span>DATE</span>{{ date('d/m/Y') }}</div>
        {{--  Event Detail is starting --}}






        <?php
        if ($bookingData['who_is_paying'] == 2 && $invoice_of == 'customer_invoices') {
            $total_amount_to_pay = $bookingData['customer_to_pay'];
        } elseif ($bookingData['who_is_paying'] == 2 && $invoice_of == 'venue_invoices') {
            $total_amount_to_pay = $bookingData['venue_group_to_pay'];
        }
        ?>
        </div>
    </header>
    <?php
    // if ($bookingData['who_is_paying'] == 2 && $invoice_of == 'customer_invoices') {
    //     $total_amount_to_pay = $bookingData['customer_to_pay'];
    // } elseif ($bookingData['who_is_paying'] == 2 && $invoice_of == 'venue_invoices') {
    //     $total_amount_to_pay = $bookingData['venue_group_to_pay'];
    // }
    ?>
    <main>
        <table>
            <thead>
                <tr>
                    <th class="service">DATE</th>
                    <th class="desc">DESCRIPTION</th>
                    <th class="unit">NAME</th>
                    <th class="qty">USER</th>
                    <th class="total">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @if ($invoice_of == 'invoices')
                    <tr>
                       
                        <td class="service">22/11/2022</td>
                       <td class="desc">{{ $packageDetails['description'] }}</td>
                        <td >(Package:){{ $packageDetails['name'] }}</td>
                        <td>-</td>
                        <td>${{ $packageDetails['price'] }}</td>
                    </tr>
                    <tr>
                       
                        <td class="service">22/11/2022</td>
                        <td class="desc">{{ $bookingData['extra_charge_desc'] }}</td>
                        <td>Extra Charge</td>
                        <td>-</td>
                        <td>{{ $bookingData['extra_price'] > 0 ? '$' . $bookingData['extra_price'] : 0 }}</td>
                    </tr>
                    <tr>
                        
                        <td class="service">22/11/2022</td>
                        <td class="desc">
                            <p>Staff Worked {{ $bookingData['overtime_hours'] }} hours
                                extra as over time .</p>
                        </td>
                        <td>Over time (${{ $bookingData['overtime_rate_per_hour'] }}/Hour)</td>
                        <td>-</td>
                        <td>${{ $overtime = $bookingData['overtime_hours'] * $bookingData['overtime_rate_per_hour'] }}
                            
                        </td>
                    </tr>
                    <tr>
                    
                        <td colspan="4">Total Cost:</td>
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
                    <td class="service">{{ date('d/m/Y', strtotime($invoice['created_at'])) }}</td>
                    <td class="desc">{{ $invoice['description'] }}</td>
                    <td class="unit">{{ $invoice['payee_name'] }}</td>
                    <td class="qty">{{ $invoice['slug'] == 'customer' ? 'Customer' : 'Venue Group' }}</td>
                    <td class="total">{{ $invoice['paid_amount'] }}</td>
                     </tr>

                        <?php }?>

                <tr>
                    

                    <td colspan="4" class="grand total">Total Received:</td>
                    <td class="grand total">${{ !isset($recievedAmount) ? ($recievedAmount = 0) : $recievedAmount }}</td>
                </tr>
                <tr>
                    
                    <td colspan="4">Due Amount:</td>
                    <td>${{ $due_amount = $totalCost - $recievedAmount }}</td>
                </tr>
               
            </tbody>
        </table>
        <div id="notices">
            <div>NOTE:</div>
            <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
        </div>
    </main>
    <footer>
        Invoice was created on a computer and is valid without the signature and seal. &copy;<div>
            {{ config('constants.app_name') }}</div>
    </footer>
</body>

</html>
