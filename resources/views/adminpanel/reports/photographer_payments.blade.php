@extends('adminpanel.admintemplate')
@push('title')
    <title>Photographers| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-2">
                        <h1>Photographers </h1>
                        
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
                                <h3 class="card-title">Photographers</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                @if ($user->group_id==config('constants.groups.admin'))
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-4">
                                        <input class="form-control" onkeyup="search_quote()" type="text" id="qsearch"
                                            name="qsearch" placeholder="Type Photographer Name or Email to Search">
                                    </div>
                                </div>
                                @endif
                                <table id="photographer_table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Address</th>
                                            <th>Booking From</th>
                                            <th>Booking To</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
                                            $photographer_id_array=array();

                                            if(count($usersData)>0)
                                            foreach ($usersData as $data){

                                             $photographer_id_array[]=$data['id'];
                                            ?>
                                            <form id="download_photographer_balance_{{$data['id']}}" method="GET" action="{{route('reports.photographer.payments.export',$data['id'])}}">
                                                @csrf
                                                <input type="hidden" name="action" value="download_photographer_balance">
                                                <input type="hidden" name="photographer_id" value="{{$data['id']}}"> 

                                             <tr id="row_{{ $data['id'] }}">
                                            <td id="name_{{ $data['id'] }}">{{$data['name']}}</td>
                                            <td id="photographer_{{ $data['id'] }}">
                                                {{$data['email']}}
                                               
                                            </td>
                                           
                                            <td id="address_{{ $data['id'] }}">
                                                {{$data['address']}}</td>
                                            <td id="row_from_date_{{ $data['id'] }}">
                                                <div class="input-group date" id="from_date_{{ $data['id'] }}" data-target-input="nearest">
                                                    <input id="input_from_date_{{ $data['id'] }}"  type="text"  name="from_date" placeholder="From date" class="form-control datetimepicker-input" data-target="#from_date_{{ $data['id'] }}"/>
                                                    <div class="input-group-append" data-target="#from_date_{{ $data['id'] }}" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>  
                                            </td>
                                            <td id="row_to_date_{{ $data['id'] }}">
                                                <div class="input-group date" id="to_date_{{ $data['id'] }}" data-target-input="nearest">
                                                    <input id="input_to_date_{{ $data['id'] }}" type="text"  name="to_date" placeholder="To Date" class="form-control datetimepicker-input" data-target="#to_date_{{ $data['id'] }}"/>
                                                    <div class="input-group-append" data-target="#to_date_{{ $data['id'] }}" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div> 
                                            </td>
                                             <td>
                                                <button onclick="$('#download_photographer_balance_{{$data['id']}}').submit()" type="button" class="btn btn-block btn-primary"><i class="fa fa-download"></i> Photographer Balance Excel</button>
                                            </td>

                                        

                                        </tr>
                                            </form>
                                        <?php 
                                            
                                              $counter ++;
                                        }
                                        else{
                                            echo '<tr><td class="text-center" colspan="6">No Record Found</td></tr>';
                                        }
                                        ?>




                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Venue Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Booking From</th>
                                            <th>Booking To</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr>
                                            <td colspan="11">
                                                <div class="text-right"> {{ $usersData->links() }}</div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                {{-- Pagination --}}

                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('head-js-css')
    <link rel="stylesheet"
        href="{{ url('adminpanel/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('adminpanel/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('footer-js-css')
    
   
    <script>
         $(function() {
            @foreach ($photographer_id_array as $val)
            $('#from_date_{{$val}}').datetimepicker({
                format: 'L'
            });
            $('#to_date_{{$val}}').datetimepicker({
                format: 'L'
            });    
            @endforeach
            

        });

            function search_quote() {

                searchval = $('#qsearch').val();
                if (searchval.length < 4 && searchval.length > 0) {
                return false;
                }
                if(searchval=='')
                window.location='';

                var sendInfo = {
                action: 'qsearch_photographer',
                qsearch: searchval
                };
                
                $.ajax({
                    url: "{{ route('photographers.ajaxcall',1) }}",
                    data: sendInfo,
                    contentType: 'application/json',
                    error: function(err) {
                    //alert(err);
                    //alert(JSON.stringify(err));
                    alert('There is Some Error, Please try again .. !');
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.error == 'No') {
                        $('#photographer_table').html(data.response);

                        } else {
                            $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: data.title,
                            subtitle: 'record',
                            body: data.msg
                            });
                        }

                        photographer_ids=data.photographer_ids;
                        photographer_idsArray = photographer_ids.split(',');
                        for (let i = 0; i < photographer_idsArray.length; i++) {
                            $('#to_date_'+photographer_idsArray[i]).datetimepicker({
                                format: 'L'
                            }); 
                            $('#from_date_'+photographer_idsArray[i]).datetimepicker({
                                format: 'L'
                            });
                        }
                    }
                });

            }  
       function sumit_form(id){
       to_date= $('#input_to_date_'+id).val();
       from_date= $('#input_from_date_'+id).val();
       _token= $('#token_'+id).val();
        window.location="{{route('reports.photographer.payments.export')}}/"+id+"?from_date="+from_date+"&to_date="+to_date+"&photographer_id="+id+"&action=download_photographer_balance&_token="+_token;
       }
    </script>
@endsection
