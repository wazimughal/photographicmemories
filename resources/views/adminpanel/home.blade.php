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

                        if(isset($pencils_info[$i]) && !empty($pencils_info[$i])){

                        $pencilData= $pencils_info[$i];
                        if($pencilData->status==config('constants.pencil_status.pending')){
                            $boxDataArr['class']='bg-info';
                            $boxDataArr['total']=$pencilData->total;
                            $boxDataArr['type']=$pencilData->status;
                            $boxDataArr['title']='Pending pencils';
                            $boxDataArr['more']='/admin/pencil/pending';
                            }
                        elseif($pencilData->status==config('constants.pencil_status.approved')){
                            $boxDataArr['class']='bg-success';
                            $boxDataArr['total']=$pencilData->total;
                            $boxDataArr['type']=$pencilData->status;
                            $boxDataArr['title']='Approved pencils';
                            $boxDataArr['more']='/admin/pencil/approved';
                            }
                        elseif($pencilData->status==config('constants.pencil_status.cancelled')){
                            $boxDataArr['class']='bg-danger';
                            $boxDataArr['total']=$pencilData->total;
                            $boxDataArr['type']=$pencilData->status;
                            $boxDataArr['title']='Cancelled pencils';
                            $boxDataArr['more']='/admin/pencil/cancelled';
                            }
                        elseif($pencilData->status==config('constants.pencil_status.trashed')){
                            $boxDataArr['class']='bg-info';
                            $boxDataArr['total']=$pencilData->total;
                            $boxDataArr['type']=$pencilData->status;
                            $boxDataArr['title']='Trashed pencils';
                            $boxDataArr['more']='/admin/pencil/trash';
                            }
                        }
                        else{ // ELSE portion if array is empty
                            
                            if($i==config('constants.pencil_status.pending')){
                            $boxDataArr['class']='bg-info';
                            $boxDataArr['total']=0;
                            $boxDataArr['type']=$i;
                            $boxDataArr['title']='Pending pencils';
                            $boxDataArr['more']='/admin/pencil/pending';
                            }
                        elseif($i==config('constants.pencil_status.approved')){
                            $boxDataArr['class']='bg-success';
                            $boxDataArr['total']=0;
                            $boxDataArr['type']=$i;
                            $boxDataArr['title']='Approved pencils';
                            $boxDataArr['more']='/admin/pencil/approved';
                            }
                        elseif($i==config('constants.pencil_status.cancelled')){
                            $boxDataArr['class']='bg-danger';
                            $boxDataArr['total']=0;
                            $boxDataArr['type']=$i;
                            $boxDataArr['title']='Cancelled pencils';
                            $boxDataArr['more']='/admin/pencil/cancelled';
                            }
                        elseif($i==config('constants.pencil_status.trashed')){
                            $boxDataArr['class']='bg-info';
                            $boxDataArr['total']=0;
                            $boxDataArr['type']=$i;
                            $boxDataArr['title']='Trashed pencils';
                            $boxDataArr['more']='/admin/pencil/trash';
                            }
                            
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
                    @php
                    $boxDataArr=array();
                    $k=1;
                        for($i=0; $i<4; $i++){

                        if(isset($user_info[$i]) && !empty($user_info[$i])){

                        $userData= $user_info[$i];
                        if($userData->group_id==config('constants.groups.admin')){
                            $boxDataArr['class']='bg-info';
                            $boxDataArr['total']=$userData->total;
                            $boxDataArr['type']=$userData->group_id;
                            $boxDataArr['title']='Admin';
                            $boxDataArr['more']='/admin/pencil/pending';
                            }
                        elseif($userData->group_id==config('constants.groups.venue_group_hod')){
                            $boxDataArr['class']='bg-success';
                            $boxDataArr['total']=$userData->total;
                            $boxDataArr['type']=$userData->group_id;
                            $boxDataArr['title']='Venue Groups';
                            $boxDataArr['more']='/admin/pencil/approved';
                            }
                        elseif($userData->group_id==config('constants.groups.customer')){
                            $boxDataArr['class']='bg-danger';
                            $boxDataArr['total']=$userData->total;
                            $boxDataArr['type']=$userData->group_id;
                            $boxDataArr['title']='Customers';
                            $boxDataArr['more']='/admin/pencil/cancelled';
                            }
                        elseif($userData->group_id==config('constants.groups.photographer')){
                            $boxDataArr['class']='bg-info';
                            $boxDataArr['total']=$userData->total;
                            $boxDataArr['type']=$userData->group_id;
                            $boxDataArr['title']='Photographers';
                            $boxDataArr['more']='/admin/pencil/trash';
                            }
                        }
                        else{ // ELSE portion if array is empty
                            
                            if($k==config('constants.groups.admin')){
                            $boxDataArr['class']='bg-info';
                            $boxDataArr['total']=0;
                            $boxDataArr['type']=$i;
                            $boxDataArr['title']='admin';
                            $boxDataArr['more']='/admin/pencil/pending';
                            }
                        elseif($k==config('constants.groups.venue_group_hod')){
                            $boxDataArr['class']='bg-success';
                            $boxDataArr['total']=0;
                            $boxDataArr['type']=$i;
                            $boxDataArr['title']='Venue Group';
                            $boxDataArr['more']='/admin/pencil/approved';
                            }
                        elseif($i==config('constants.groups.customer')){
                            $boxDataArr['class']='bg-danger';
                            $boxDataArr['total']=0;
                            $boxDataArr['type']=$i;
                            $boxDataArr['title']='Customer';
                            $boxDataArr['more']='/admin/pencil/cancelled';
                            }
                        elseif($k==config('constants.groups.photographer')){
                            $boxDataArr['class']='bg-info';
                            $boxDataArr['total']=0;
                            $boxDataArr['type']=$i;
                            $boxDataArr['title']='Photographer';
                            $boxDataArr['more']='/admin/pencil/trash';
                            }
                          $k++;  
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
                    <!-- ./col -->

                    

                   
               
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
