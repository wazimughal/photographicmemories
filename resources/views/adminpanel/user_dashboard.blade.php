@extends('adminpanel.admintemplate')

@push('title')
    <title>Dashboard 1</title>
@endpush


@section('main-section')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"> @can('adminTest')
                                test
                            @endcan </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Small boxes (Stat box) -->
                <div class="row">
                    @php
                    $boxDataArr=array();
                        for($i=0; $i<3; $i++){

                     // ELSE portion if array is empty
                         if($i==0){
                            $boxDataArr['class']='bg-info';
                            $boxDataArr['total']=0;
                            $boxDataArr['type']=$i;
                            $boxDataArr['title']='Pending bookings';
                            $boxDataArr['more']='/admin/bookings/pending';
                            }
                        elseif($i==1){
                            $boxDataArr['class']='bg-success';
                            $boxDataArr['total']=0;
                            $boxDataArr['type']=$i;
                            $boxDataArr['title']='Approved bookings';
                            $boxDataArr['more']='/admin/bookings/approved';
                            }
                        elseif($i==2){
                            $boxDataArr['class']='bg-danger';
                            $boxDataArr['total']=0;
                            $boxDataArr['type']=$i;
                            $boxDataArr['title']='Cancelled bookings';
                            $boxDataArr['more']='/admin/bookings/cancelled';
                            }
                        elseif($i==3){
                            $boxDataArr['class']='bg-info';
                            $boxDataArr['total']=0;
                            $boxDataArr['type']=$i;
                            $boxDataArr['title']='Trashed bookings';
                            $boxDataArr['more']='/admin/bookings/trash';
                            }
                            
                        
                         echo '<div class="col-lg-3 col-6">
                        <!-- small box -->
                        <div class="small-box '.$boxDataArr['class'].'">
                            <div class="inner">
                                <h3>'.$boxDataArr['total'].'</h3>

                                <p>'.$boxDataArr['title'].'</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="'.$boxDataArr['more'].'" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>';   
                        }
                    @endphp
                    
               
                </div>
                <!-- /.row -->
                <!-- Main row -->

                <!-- /.row (main row) -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
