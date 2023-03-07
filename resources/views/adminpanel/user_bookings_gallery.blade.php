@extends('adminpanel.admintemplate')
@push('title')
    <title>
        Bookings| {{ config('constants.app_name') }}</title>
@endpush
@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <h1>{{$booking_title}} </h1>

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
                                <h3 class="card-title">{{$booking_title}}</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                            <section class="content">
                                <div class="container-fluid">
                                    <!-- Small boxes (Stat box) -->
                                    <div class="row">
                                        <?php 
                                            $counter = 1;
                                            //p($pencilData->toArray());
                                            if(count($pencilData)>0)
                                            foreach ($pencilData as $data){
                                        
                                            ?>
                                             @foreach ($data['bookings'] as $pencil)
                                        <div class="col-lg-3 col-6">
                                            <div class="small-box bg-secondary">
                                                <div class="inner">
                                                    <h3>Event:{{date(config('constants.date_formate'), $pencil['date_of_event'])}}</h3>
                                                    <p>Venue:{{ (isset($pencil['venue_group']['userinfo'][0]['vg_name']))?$pencil['venue_group']['userinfo'][0]['vg_name']: $pencil['other_venue_group'];}}</p><br>
                                                    {{ (isset($pencil['venue_group']['userinfo'][0]['vg_phone']))?'<p>Ph:'.$pencil['venue_group']['userinfo'][0]['vg_phone'].'</p><br>':'';}}
                                                    
                                                </div>
                                                <div class="icon">
                                                    <i class="ion ion-images"></i>
                                                </div>
                                                <a href="{{route('booking.photos',$pencil['id'])}}" class="small-box-footer">Open Gallery <i
                                                        class="fas fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        @endforeach
                                        <?php 
                                            
                                              $counter ++;
                                        }else{
                                            echo '<div class="col-12 align-center">No Record Found</div>';
                                        }
                                        ?>

                                    </div>
                                </div>
                            </section>
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

