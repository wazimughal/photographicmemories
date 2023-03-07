@extends('adminpanel.admintemplate')
@push('title')
    <title>Customers| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-2">
                        <h1>Customers </h1>
                        
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
                                <h3 class="card-title">Customers</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row" style="margin-bottom: 15px;">
                                    <div class="col-4">
                                        <input class="form-control" onkeyup="search_quote()" type="text" id="qsearch"
                                            name="qsearch" placeholder="Type Customer Name or Email to Search">
                                    </div>
                                </div>
                                <table id="customer_table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $counter = 1;
               
                                            if(count($usersData)>0)
                                            foreach ($usersData as $data){
                                        
                                            ?>
                                             <tr id="row_{{ $data['id'] }}">
                                            <td id="date_of_event_{{ $data['id'] }}">{{$data['name']}}</td>
                                            <td id="venue_group_{{ $data['id'] }}">
                                                {{$data['email']}}
                                               
                                            </td>
                                           
                                            <td id="phone_{{ $data['id'] }}">
                                                {{$data['phone']}}</td>

                                        
                                           
                                            <td>
                                                <a href="{{route('reports.customer.payments.export',$data['id'])}}" 
                                                    class="btn btn-success btn-block btn-sm"><i class="fas fa-download"></i>
                                                    Download Payments Excel</a>
                                                
                                                
                                            </td>

                                        

                                        </tr>
                                       
                                        <?php 
                                            
                                              $counter ++;
                                        }
                                        else{
                                            echo '<tr><td class="text-center" colspan="5">No Record Found</td></tr>';
                                        }
                                        ?>




                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
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
            function search_quote() {

                searchval = $('#qsearch').val();
                if (searchval.length < 4 && searchval.length > 0) {
                return false;
                }
                if(searchval=='')
                window.location='';

                var sendInfo = {
                action: 'qsearch_customer',
                qsearch: searchval
                };

                $.ajax({
                    url: "{{ route('venuegroups.ajaxcall',1) }}",
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
                        $('#customer_table').html(data.response);

                        } else {
                            $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: data.title,
                            subtitle: 'record',
                            body: data.msg
                            });
                        }

                        //alert('i am here');
                    }
                });

            }  
       
    </script>
@endsection
