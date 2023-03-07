<?php

if(!function_exists('p')){
    function p($data){ 
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}
if(!function_exists('get_formatted_date')){
    function get_formatted_date($data,$formate){
        $formattedDate= date($formate, strtotime($date));
        return $formattedDate; 
    }
}

if(!function_exists('get_session_value')){
    function get_session_value($key=NULL){

        $userData=session()->get('userData'); 
        if($key==NULL)
        return $userData;
        return $userData[$key];
    }
}
if(!function_exists('getLeadWithVenuebyID')){
    function getLeadWithVenuebyID($id=NULL){
            if($id>0){
                $leadsData=App\Models\adminpanel\Users::with('getVenueGroup')
                //->where('group_id', '=', config('constants.groups.subscriber'))
                ->where('id', '=', $id)->get();
                
                
            }else{
               return array();
            }
            

            $data=($leadsData->toArray());
            $data=$data[0];
            $venueGroupData=getVenueGrpupById($data['get_venue_group']['venue_group_id']);
            $data['get_venue_group_detail']=$venueGroupData[0];
            return $data;
    }
}
if(!function_exists('getBookingbyID')){
    function getBookingbyID($id=NULL){
            if($id>0){
                $bookingData=App\Models\adminpanel\orders::with('customer')
                ->with('venue_group')
                ->with('photographers')
                ->where('id',$id)
                ->orderBy('created_at', 'desc')->get()->toArray();
                
                
            }else{
               return array();
            }
            if(isset($bookingData[0]))
            return $bookingData[0];

            else 
            return array();
    }
}
if(!function_exists('phpslug')){
    function phpslug($string)
    {
        $slug = preg_replace('/[-\s]+/', '_', strtolower(trim($string)));
        return $slug;
    }
}
if(!function_exists('getParamsByTestID')){
    function getParamsByTestID($id){
        $testParams = App\Models\adminpanel\LabTestsParams::where('lab_test_id',$id)->orderBy('created_at', 'desc')->get();
        if($testParams)
        return $testParams->toArray();
        
        return array();
    }
}
if(!function_exists('getGroups')){
    function getGroups(){
        $userGroups = App\Models\adminpanel\Groups::orderBy('created_at', 'desc')->where('id','!=',config('constants.groups.admin'))->get();
        if($userGroups)
        return $userGroups->toArray();
        
        return array();
    }
}
if(!function_exists('getUsersByGroupId')){
    function getUsersByGroupId($group_id){
        $userData = App\Models\adminpanel\Users::orderBy('created_at', 'desc')->where('group_id','=',$group_id)->get();
        if($userData){
            $userData=$userData->toArray();
            return $userData;
        }
        return array();
    }
}
if(!function_exists('getTypesOfLeads')){
    function getTypesOfLeads(){
        $leads=config('constants.lead_types');
        return $leads;
    }
}
if(!function_exists('getLeadByType')){
    function getLeadByType($lead_type, $status=0){
        $userData = App\Models\adminpanel\Users::orderBy('created_at', 'desc')->where('lead_type','=',$lead_type)->where('status','=',$status)->get();
        if($userData){
            $userData=$userData->toArray();
            return $userData;
        }
        return array();
    }
}
if(!function_exists('getAllGroups')){
    function getAllGroups(){
        $userGroups = App\Models\adminpanel\Groups::orderBy('created_at', 'desc')->get();
        if($userGroups)
        return $userGroups->toArray();
        
        return array();
    }
}

if(!function_exists('getVenueGrpupById')){
    function getVenueGrpupById($id){
        $Organizations = App\Models\adminpanel\Venue_groups::orderBy('created_at', 'desc')->with('ownerinfo')->where('id',$id)->get();
        if($Organizations)
        return $Organizations->toArray();
        
        return array();
    }
}
// Add City in cities table if already city not exist 
if(!function_exists('getOtherCity')){
    function getOtherCity($name){
        $nameSlug=phpslug($name);
        $cityData = App\Models\adminpanel\cities::where('slug',$nameSlug)->get();
        $cityData=$cityData->toArray();
        if(!empty($cityData)){
            return $cityData[0]['id'];
        }
        $cityId = DB::table('cities')->insertGetId(array('name'=>strtolower($name),'slug'=>phpslug($name),'is_active'=>1),'id');
        return $cityId;
        
    }
}
// Add zipcode in zipcode table if already zipcode not exist 
if(!function_exists('getOtherZipCode')){
    function getOtherZipCode($code){
        
        $zipcodeData = App\Models\adminpanel\zipcode::where('code',phpslug($code))->get();
        $zipcodeData=$zipcodeData->toArray();
        if(!empty($zipcodeData)){
            return $zipcodeData[0]['id'];
        }
        $zipcodeId = DB::table('zipcode')->insertGetId(array('code'=>strtolower(phpslug($code)),'is_active'=>1),'id');
        return $zipcodeId;
        
    }
}
if(!function_exists('log_activity')){
    function log_activity($data){
        
        $activityID = DB::table('activities_log')->insertGetId($data,'id');
        return $activityID;
        
    }
}
if(!function_exists('getAllTestsByOrg')){
    function getAllTestsByOrg($org_id){
        $allTestData = App\Models\adminpanel\LabTests::with('getParams')->where('organization_id',$org_id)->orderBy('created_at', 'desc')->get();
        if($allTestData)
        return $allTestData->toArray();
        
        return array();
    }
}
if(!function_exists('getAdvisedTestsNames')){
    function getAdvisedTestsNames($ids){
        //return $ids;
        $allTestData = App\Models\adminpanel\LabTests::whereIn('id',$ids)->orderBy('created_at', 'desc')->get('test_name');
        if($allTestData)
        return $allTestData->toArray();
        
        return array();
    }
}

// Get Test Report by Test ID
// if(!function_exists('getReportByTestId')){
//     function getReportByTestId($id){
//         //return array($id);
//         $testReport = App\Models\adminpanel\LabTestsParams::where('lab_test_id',$id)->with('ParamResult')->with('LabTest')->get();
//         if($testReport)
//         return $testReport->toArray();
        
//         return array();
//     }
// }

if(!function_exists('customer_status_badge')){
    function customer_status_badge($key){
        $msg='<span class=" text-center badge badge-info">'.customer_status($key).'</span>';

        if($key==0)
        $msg='<span class="text-center badge badge-info"> '.customer_status($key).'</span>';
        elseif($key==1)
        $msg='<span class="text-center badge badge-success">'.customer_status($key).'</span>';
        elseif($key==2)
        $msg='<span class="text-center badge badge-danger"> '.customer_status($key).'</span>';
        elseif($key==3)
        $msg='<span class="text-center badge badge-warning">'.customer_status($key).'</span>';
        elseif($key==4)
        $msg='<span class="text-center badge badge-primary">'.customer_status($key).'</span>';
        elseif($key==5)
        $msg='<span class="text-center badge badge-secondary">'.customer_status($key).'</span>';

        return $msg;
        
    }
}
if(!function_exists('customer_status_msg')){
    function customer_status_msg($key){
        $msg='<div class=" text-center alert-info"> Customer Agreement is Pending</div>';

        if($key==0)
        $msg='<div class="text-center alert-info customer_booking_status_msg"> Customer Agreement is Pending</div>';
        elseif($key==1)
        $msg='<div class="text-center alert-success customer_booking_status_msg"> Customer approved the agreement</div>';
        elseif($key==2)
        $msg='<div class="text-center alert-danger customer_booking_status_msg"> Customer Rejected the agreement</div>';
        elseif($key==3)
        $msg='<div class="text-center alert-warning customer_booking_status_msg">Pending Customer Deposit</div>';
        elseif($key==4)
        $msg='<div class="text-center alert-primary customer_booking_status_msg">Customer Booking is on Hold</div>';
        elseif($key==5)
        $msg='<div class="text-center alert-secondary customer_booking_status_msg">Customer Booking is Confirmed</div>';

        return $msg;
        
    }
}
if(!function_exists('customer_status')){
    function customer_status($key){
        $customer_status[]='Pending Agreement';
        $customer_status[]='Approved';
        $customer_status[]='Rejected';
        $customer_status[]='Pending Customer Deposit';
        $customer_status[]='On Hold';
        $customer_status[]='Confirmed';
        return $customer_status[$key];
    }
}
// Get Options of customer Operation
if(!function_exists('customer_status_options')){
    function customer_status_options($selectID=NULL){
        $customer_status[]='Pending Agreement';
        $customer_status[]='Approved';
        $customer_status[]='Rejected';
        $customer_status[]='Pending Customer Deposit';
        $customer_status[]='On Hold';
        $customer_status[]='Confirmed';
        $options='';
        foreach($customer_status as $key=>$value){
            $selected='';
            if($selectID==$key) $selected='selected';
            $options .='<option '.$selected.' value="'.$key.'">'.$value.'</option>';
        }
        return $options;   
    }
}
if(!function_exists('booking_status_for_msg')){
    function booking_status_for_msg($booking_status){
       
        $msg='invalid';
        if ($booking_status==config('constants.booking_status.pencil')){
            $msg='Pencil, We need to add this pencil to booking';
        }
        elseif ($booking_status==config('constants.booking_status.awaiting_for_photographer')){
            $msg='We are Waiting for photographer response, Photographer will have to accept or Reject the Invitation';
        }
        elseif ($booking_status==config('constants.booking_status.declined_by_photographer')){
            $msg='All the photographer declined the requests, we need to assign new photographer';
        }
        elseif ($booking_status==config('constants.booking_status.pending_customer_agreement')){
            $msg='Customer agreement is pending !';
        }
        elseif ($booking_status==config('constants.booking_status.pending_customer_deposit')){
            $msg='Customer need to deposit, ask the customer to deposit !';
        }
        elseif ($booking_status==config('constants.booking_status.on_hold')){
            $msg='Booking is on hold, Please review it';
        }
        elseif ($booking_status==config('constants.booking_status.confirmed')){
            $msg='Everyth is perfect. Booking is confirmed!';
        }
        
        return $msg;
    }
}
if(!function_exists('get_photographer_status_title')){
    function get_photographer_status_title($key){
        $photographer_status=[
            'Awaited',
            'Accepted',
            'Declined',
            'Cancelled',
            'Removed',
        ];
        if(isset($photographer_status[$key]))
        return $photographer_status[$key];

        return 'Invalid';

    }
}
if(!function_exists('get_booking_status')){
    function get_booking_status($booking_id){
        
        $photographer_status = DB::table('bookings_users')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->where('group_id',config('constants.groups.photographer'))
            ->where('booking_id',$booking_id)
            ->get()->toArray();
        
        if(empty($photographer_status))
        return config('constants.booking_status.awaiting_for_photographer'); 
        //config('constants.photographer_assigned.awaiting') =0
        //config('constants.photographer_assigned.accepted') =1
        //config('constants.photographer_assigned.declined') =2
        //config('constants.photographer_assigned.cancelled') =3
        //config('constants.photographer_assigned.removed') =4

        $status=config('constants.booking_status.pending_customer_agreement'); 
        $awaiting_photographer=0;
        $accepted_photographer=0;
        $declined_photographer=0;
        $cancelled_photographer=0;

        foreach($photographer_status as $data){

            if($data->status==config('constants.photographer_assigned.awaiting')){  //0
                $awaiting_photographer=$data->total;
                continue;
            
            }elseif($data->status==config('constants.photographer_assigned.accepted')){ //1
               
                $accepted_photographer=$data->total;
                continue;
            
            }elseif($data->status==config('constants.photographer_assigned.declined') && $data->total>0 ){
                $declined_photographer=$data->total;
                continue;
            }elseif($data->status==config('constants.photographer_assigned.declined') && $data->total>0 ){
                $cancelled_photographer=$data->total;
                continue;  
            }
            
        }

        if($awaiting_photographer>0 || $accepted_photographer<1  ){
            return config('constants.booking_status.awaiting_for_photographer'); 
        }else if($declined_photographer>0){
            return config('constants.booking_status.awaiting_for_photographer'); 
        }else if($declined_photographer>0){
            return config('constants.booking_status.awaiting_for_photographer'); 
        }
        else{
            return config('constants.booking_status.pending_customer_agreement'); 
        }
       
    }
}
// assign new photographer
if(!function_exists('assign_photographer_to_booking')){
    function assign_photographer_to_booking($event_date, $data){
       // p($data);
        $where_photographer=[
            ['user_id', '=', $data['user_id']],
            ['status', '=', config('constants.photographer_assigned.accepted')],
            ['group_id','=',config('constants.groups.photographer')],
            ['active', '=', 1]
        ];
        $photographersData = App\Models\adminpanel\bookings_users::where($where_photographer)
                            ->with('booking')->get()->toArray();
                       
                            $retData['result']=true;
                            
                            //echo 'user_id:'.$data['user_id'].'<br>';
                            //echo 'count :'.count($photographersData);
           foreach($photographersData as $photographer){
            $booking_date_of_event='';
            if(!empty($photographer['booking']))
            $booking_date_of_event=date(config('constants.date_formate'),$photographer['booking']['date_of_event']);

                if($booking_date_of_event==$event_date){
                    $retData['result']=false;
                    $retData['msg']=$photographer['user_id'].' Photographer already booked for this date '.$event_date;
                    return $retData;
                    
                }elseif($photographer['user_id']==$data['user_id'] && $photographer['booking_id']==$data['booking_id']){
                    $retData['result']=false;
                    $retData['msg']=$photographer['user_id'].' Photographer already assigned for this booking id '.$data['booking_id'];
                    return $retData;
                }
                
           } 
           
           $retData['msg']=$data['user_id'].' assigned to the booking id '.$data['booking_id'];
           DB::table('bookings_users')->insert($data);
           return $retData;
          
    }
}
// Get users Email Address
if(!function_exists('get_users_email_address')){
    function get_users_email_address($where=[], $mergingArray=[]){
        if(empty($where)){
            $where=[
                ['group_id', '=', config('constants.groups.admin')],
                ['is_active', '=', 1]
            ];
        }

        $usersData=App\Models\adminpanel\users::where($where)
        ->orderBy('id', 'desc')->get('email');
        
        

        if(!empty($usersData))
        foreach($usersData as $data){
            $mergingArray[]=$data->email;
        }
        return $mergingArray;
        
    }
}
if(!function_exists('get_photographer_options')){
    function get_photographer_options($selectID=NULL){
    
        $photographersData = App\Models\adminpanel\users::where('is_active',1)
        ->where('group_id', '=', config('constants.groups.photographer'))
        ->orderBy('id', 'desc')->get();
        if($photographersData)
         $photographersData=$photographersData->toArray();
         $options='';
         //p($photographersData);

        foreach($photographersData as $key=>$data){
            $selected='';
            if(is_array($selectID)){
                if(in_array($data['id'],$selectID))
                $selected='selected';
            }
            elseif($selectID==$data['id']){
                $selected='selected';
            } 
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        
     return $options;   
    }
}
// Get Options of _photographer_
if(!function_exists('get_photographer_options_with_count')){
    function get_photographer_options_with_count($selectID=NULL){
    
        $photographersData = App\Models\adminpanel\users::where('is_active',1)
        ->where('group_id', '=', config('constants.groups.photographer'))
        ->orderBy('id', 'desc')->get();
        if($photographersData)
         $photographersData=$photographersData->toArray();
         $options='';
         //p($photographersData);

        foreach($photographersData as $key=>$data){
            $selected='';
            if($selectID==$data['id']) $selected='selected';
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        $retData['options']=$options;
        $retData['total']=count($photographersData);
     return $retData;   
    }
}
// Get Options of _photographer_
if(!function_exists('get_customer_options')){
    function get_customer_options($selectID=NULL){
    
        $customersData = App\Models\adminpanel\users::where('is_active',1)
        ->where('group_id', '=', config('constants.groups.customer'))
        ->orderBy('id', 'desc')->get();
        if($customersData)
         $customersData=$customersData->toArray();
         $options='';
         
        foreach($customersData as $key=>$data){
            $selected='';
            if(is_array($selectID)){
                if(in_array($data['id'],$selectID))
                $selected='selected';
            }
            elseif($selectID==$data['id']){
                $selected='selected';
            } 
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        
     return $options;   
    }
}
// Get Options of States
if(!function_exists('getStatesOptions')){
    function getStatesOptions($selectID=NULL){
    
        $statesData = App\Models\adminpanel\states::where('is_active',1)->orderBy('id', 'desc')->get();
        if($statesData)
         $statesData=$statesData->toArray();
         $options='';
         
        foreach($statesData as $key=>$data){
            $selected='';
            if($selectID==$data['id']) $selected='selected';
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        $options .='<option  value="other">Other</option>';
     return $options;   
    }
}
// Get Options of Prodcut Categories
if(!function_exists('getpackageCatOptions')){
    function getpackageCatOptions($selectID=NULL){
    
        $categoryData = App\Models\adminpanel\packages_categories::where('is_active',1)->orderBy('id', 'asc')->get();
        if($categoryData)
         $categoryData=$categoryData->toArray();
         $options='';
         
        foreach($categoryData as $key=>$data){
            $selected='';
            if($selectID==$data['id']) $selected='selected';
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        //$options .='<option  value="other">Other</option>';
     return $options;   
    }
}
// Get Options of Cities
if(!function_exists('getCitiesOptions')){
    function getCitiesOptions($selectID=NULL){
        
        $cityData = App\Models\adminpanel\cities::where('is_active',1)->orderBy('id', 'asc')->get();
        if($cityData)
         $cityData=$cityData->toArray();
         $options='';
         $selectID= phpslug($selectID);
        foreach($cityData as $key=>$data){
            $selected='';
            if($selectID==phpslug($data['name'])) $selected='selected';
            $options .='<option '.$selected.' value="'.($data['name']).'">'.$data['name'].'</option>';
        }
       // $options .='<option  value="other">Other</option>';
     return $options;   
    }
}
// Get Options of Cities
if(!function_exists('getZipCodeOptions')){
    function getZipCodeOptions($selectID=NULL){
    
        $zipcodeData = App\Models\adminpanel\zipcode::where('is_active',1)->orderBy('id', 'asc')->get();
        if($zipcodeData)
         $zipcodeData=$zipcodeData->toArray();
         $options='';
         
        foreach($zipcodeData as $key=>$data){
            $selected='';
            if($selectID==$data['id']) $selected='selected';
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['code'].'</option>';
        }
        $options .='<option  value="other">Other</option>';
     return $options;   
    }
}
// Get Options of Relationship with groom/bride
if(!function_exists('relation_with_event_options')){
    function relation_with_event_options($selectID=NULL){
        $relations[]='Father';
        $relations[]='Mother';
        $relations[]='Groom';
        $relations[]='Bride';
        $relations[]='Brother';
        $relations[]='Sister';
        $relations[]='Other family member';
        $relations[]='Hall manager';
        $options='';
        foreach($relations as $key=>$value){
            $selected='';
            if($selectID==$key) $selected='selected';
            $options .='<option '.$selected.' value="'.$key.'">'.$value.'</option>';
        }
        return $options;   
    }
}
// Get Options of Relationship with groom/bride
if(!function_exists('relation_with_event')){
    function relation_with_event($id){
        $relations[]='Father';
        $relations[]='Mother';
        $relations[]='Groom';
        $relations[]='Bride';
        $relations[]='Brother';
        $relations[]='Sister';
        $relations[]='Other family member';
        $relations[]='Hall manager';
        return $relations[$id];

    }
}
// Get Packages
if(!function_exists('get_packages')){
    function get_packages(){
    
        $photographic_packages_data = App\Models\adminpanel\PhotographicPackages::where('is_active',1)->orderBy('id', 'asc')->get();
        if($photographic_packages_data)
         $photographic_packages_data=$photographic_packages_data->toArray();
          return $photographic_packages_data;
    }
}
// Get Packages
if(!function_exists('get_package_by_id')){
    function get_package_by_id($id){
    
        $photographic_package_data = App\Models\adminpanel\PhotographicPackages::where('id',$id)->orderBy('id', 'asc')->get()->toArray();
         return $photographic_package_data[0];
    }
}
// Get Options of Venue Groups
if(!function_exists('get_packages_options')){
    function get_packages_options($selectID=NULL){
    
        $packages_data =  App\Models\adminpanel\PhotographicPackages::where('is_active',1)->orderBy('id', 'asc')->get();
        if($packages_data)
         $packages_data=$packages_data->toArray();
         $options='';
         
        foreach($packages_data as $key=>$data){
            $selected='';
            if($selectID==$data['id']) $selected='selected';
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'&nbsp;&nbsp;&nbsp; ('.$data['price'].'USD)</option>';
        }
        //$options .='<option value="other">Other</option>';
     return $options;   
    }
}
// Get Options of States
if(!function_exists('get_booking_status_options')){
    function get_booking_status_options($selectID=NULL){
    $quote_status[0]=['id'=>config('constants.booking_status.pencil'),'slug'=>'pencil','title'=>'Pencil'];
    $quote_status[1]=['id'=>config('constants.booking_status.awaiting_for_photographer'),'slug'=>'awaiting_for_photographer','title'=>'Awaited Photographer Response'];
    $quote_status[2]=['id'=>config('constants.booking_status.declined_by_photographer'),'slug'=>'declined_by_photographer','title'=>'Declined by Photographer'];
    $quote_status[3]=['id'=>config('constants.booking_status.pending_customer_agreement'),'slug'=>'pending_customer_agreement','title'=>'Pending Customer Agreement'];
    $quote_status[4]=['id'=>config('constants.booking_status.pending_customer_deposit'),'slug'=>'pending_customer_deposit','title'=>'Pending Customer Deposit'];
    $quote_status[5]=['id'=>config('constants.booking_status.on_hold'),'slug'=>'on_hold','title'=>'Booking on Hold'];
    $quote_status[6]=['id'=>config('constants.booking_status.confirmed'),'slug'=>'confirmed','title'=>'Confimed Booking'];
    
         $options='';
         
        foreach($quote_status as $key=>$data){
            $selected='';
            if(is_array($selectID)){
                if(in_array($data['id'],$selectID))
                $selected='selected';
            }
            elseif($selectID==$data['id']){
                $selected='selected';
            } 
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['title'].'</option>';
        }
        
     return $options;   
    }
}
// get Venue Group Name 
if(!function_exists('get_venue_group_name_by_id')){
    function get_venue_group_name_by_id($vg_id){

        $venue_group_data = App\Models\adminpanel\Users::where('id',$vg_id)->first('vg_name');
        return $venue_group_data->vg_name;
    }
}
// Get Options of Venue Groups
if(!function_exists('get_venue_group_options')){
    function get_venue_group_options($selectID=NULL){
    
        $venue_group_data = App\Models\adminpanel\Users::where('is_active',1)->where('group_id',config('constants.groups.venue_group_hod'))->orderBy('id', 'asc')->get();
        
        if($venue_group_data)
         $venue_group_data=$venue_group_data->toArray();
         $options='';
         //p($venue_group_data);
        foreach($venue_group_data as $key=>$data){
            $selected='';
            if(is_array($selectID)){
                if(in_array($data['id'],$selectID))
                $selected='selected';
            }
            elseif($selectID==$data['id']){
                $selected='selected';
            } 
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['vg_name'].'</option>';
        }
        $selected='';
        if($selectID=='other')
        $selected='selected';
        $options .='<option '.$selected.' value="other">Other</option>';
     return $options;   
    }
}
if(!function_exists('get_user_by_id')){
    function get_user_by_id($id){
        //return array($id);
        $userData = App\Models\adminpanel\Users::where('id',$id)->get()->toArray();
        
        return $userData[0];
    }
}
if(!function_exists('pencilBy')){
    function pencilBy($key){
        if($key==config('constants.pencileBy.office'))
        return 'Office';
        else if($key==config('constants.pencileBy.venue_group'))
        return 'Venue Group';
        else if($key==config('constants.pencileBy.website'))
        return 'Website';
        else if($key==config('constants.pencileBy.customer'))
        return 'Customer';

        return 'Invalid';
        
    }
}
if(!function_exists('booking_status')){
    function booking_status($key){
        if($key==config('constants.booking_status.pencil'))
        return 'New Pencil';
        if($key==config('constants.booking_status.awaiting_for_photographer'))
        return 'Awaiting Photographer';
        if($key==config('constants.booking_status.declined_by_photographer'))
        return 'Photographer Declined';
        if($key==config('constants.booking_status.pending_customer_agreement'))
        return 'Pending Agreement ';
        if($key==config('constants.booking_status.pending_customer_deposit'))
        return 'Pending Deposit';
        if($key==config('constants.booking_status.on_hold'))
        return 'On Hold';
        if($key==config('constants.booking_status.confirmed'))
        return 'Confirmed';
        
        return 'Invalid';
        
    }
}
if(!function_exists('current_booking_status')){
    function current_booking_status($customer_status=0,$photographer_status=0){
        $class=$msg='';
        if($customer_status==0)
        $class='badge-info';
        elseif($customer_status==1)
        $class='badge-success';
        elseif($customer_status==2)
        $class='badge-danger';
        elseif($customer_status==3)
        $class='badge-warning';
        elseif($customer_status==4)
        $class='badge-primary';
        elseif($customer_status==5)
        $class='badge-secondary';

        $msg .='<span class=" text-center badge '.$class.'">Customer:'.customer_status($customer_status).'</span><br>';
        
        if($customer_status==1 && $photographer_status==1){
            return $msg ='<span class=" text-center badge badge-success">Confirmed Booking</span><br>';
        }else if($photographer_status==1){
            $msg .='<span class=" text-center badge badge-success">Photographer:Accepted</span><br>';
        }else{
            $msg .='<span class=" text-center badge badge-info">Photographer:awaited</span><br>';
        }
        

        return $msg;
        
    }
}

if(!function_exists('booking_email_body')){
    function booking_email_body($bookingsMailData,$show_package_details=true){
        $groom_data=$bride_data=$vg_data=$photographerData=$packageData='';

        if(!empty($bookingsMailData['package']) && count($bookingsMailData['package'])>0 && $show_package_details==true){
            $packageData='<tr><td colspan=2> <hr></td></tr> <tr><td colspan=2><strong>Package Details</strong></td></tr>
                <tr><td> Package Name :</td><td>'.($bookingsMailData['package']['name']).'</td></tr>
                <tr><td> Description :</td><td>'.($bookingsMailData['package']['description']).'</td></tr>
                <tr><td> Price :</td><td>$'.($bookingsMailData['package']['price']).'</td></tr>';

                if(!empty($bookingsMailData['extra_price']))
                    $packageData .='<tr><td> Additional Charges :</td><td>$'.($bookingsMailData['extra_price']).'</td></tr>';
                
                if(!empty($bookingsMailData['extra_charge_desc']))
                    $packageData .='<tr><td>Reason for Addi. Charges :</td><td>$'.($bookingsMailData['extra_charge_desc']).'</td></tr>';
                
                if(!empty($bookingsMailData['overtime_rate_per_hour']))
                    $packageData .='<tr><td>Over time Rate/hour :</td><td>$'.($bookingsMailData['overtime_rate_per_hour']).'/Hour</td></tr>';
                
                if(!empty($bookingsMailData['overtime_hours']))
                    $packageData .='<tr><td>Over Time Worked :</td><td>$'.($bookingsMailData['overtime_hours']).' Hour</td></tr>';
                
                $packageData .='<tr><td colspan=2> <hr></td></tr> <tr><td colspan=2><strong>Who is Paying for Event</strong></td></tr>';
                if(($bookingsMailData['who_is_paying']==0))
                    $packageData .='<tr><td colspan=2>Customer</td></tr>';
                elseif(($bookingsMailData['who_is_paying']==1))
                    $packageData .='<tr><td colspan=2>Venue Group</td></tr>';
                if(($bookingsMailData['who_is_paying']==2)){
                    $packageData .='<tr><td>Customer :</td><td>$'.($bookingsMailData['customer_to_pay']==''?0:$bookingsMailData['customer_to_pay']).'</td></tr>';
                    $packageData .='<tr><td>Venue Group :</td><td>$'.($bookingsMailData['venue_group_to_pay']==''?0:$bookingsMailData['venue_group_to_pay']).'</td></tr>';
                    
                }
                    
                
                
        }
        if(!empty($bookingsMailData['photographer']) && count($bookingsMailData['photographer'])>0){
            $photographerData='<tr><td colspan=2> <hr></td></tr> <tr><td colspan=2><strong>Photographer Details</strong></td></tr>';
            $k=1;
            foreach($bookingsMailData['photographer'] as $photographer){
                $photographerData .='<tr><td><strong> Photographer '.$k++.' :</strong></td><td>&nbsp;</td></tr>
                <tr><td> Name :</td><td>'.($photographer['userinfo'][0]['name']).'</td></tr>
                <tr><td> Email :</td><td>'.($photographer['userinfo'][0]['email']).'</td></tr>
                <tr><td> Phone :</td><td>'.($photographer['userinfo'][0]['phone']).'</td></tr>';
            }
        }
        if(!empty($bookingsMailData['venue_group']) && count($bookingsMailData['venue_group'])>0){
            $vg_data='<tr><td colspan=2> <hr></td></tr> <tr><td colspan=2><strong>Venue Group Details</strong></td></tr>
            <tr><td> Venue Name :</td><td>'.($bookingsMailData['venue_group']['userinfo'][0]['vg_name']).'</td></tr>
            <tr><td> Address :</td><td>'.$bookingsMailData['venue_group']['userinfo'][0]['address'].'</td></tr>
            <tr><td> Manager Name :</td><td>'.$bookingsMailData['venue_group']['userinfo'][0]['vg_manager_name'].'</td></tr>
            <tr><td> Manager Phone :</td><td>'.$bookingsMailData['venue_group']['userinfo'][0]['vg_manager_phone'].'</td></tr>
            ';
            if(!empty($bookingsMailData['venue_group']['userinfo'][0]['city']))
            $vg_data .='<tr><td>City :</td><td>'.($bookingsMailData['venue_group']['userinfo'][0]['city']).' </td></tr>';
        }elseif(isset($bookingsMailData['other_venue_group']) && !empty($bookingsMailData['other_venue_group'])){
            $vg_data='<tr><td colspan=2> <hr></td></tr> <tr><td colspan=2><strong>Venue Group Details</strong></td></tr>
            <tr><td> Other Venue Name :</td><td>'.($bookingsMailData['other_venue_group']).'</td></tr>';
        }

        if(!empty($bookingsMailData['groom_name'])){
            $groom_data='<tr><td colspan=2> <hr></td></tr> <tr><td colspan=2><strong>Groom Details</strong></td></tr>
            <tr><td> Groom Name :</td><td>'.$bookingsMailData['groom_name'].'</td></tr>';
            if(!empty($bookingsMailData['groom_home_phone']))
            $groom_data .='<tr><td>Home Phone :</td><td>'.($bookingsMailData['groom_home_phone']).' </td></tr>';
            if(!empty($bookingsMailData['groom_mobile']))
            $groom_data .='<tr><td>Mobile :</td><td>'.($bookingsMailData['groom_mobile']).' </td></tr>';
            if(!empty($bookingsMailData['groom_billing_address']))
            $groom_data .='<tr><td>Groom Billing Address :</td><td>'.($bookingsMailData['groom_billing_address']).' </td></tr>';
        }
        if(!empty($bookingsMailData['bride_name'])){
            $bride_data='<tr><td colspan=2> <hr></td></tr> <tr><td colspan=2><strong>Bride Details</strong></td></tr>
            <tr><td> Bride Name :</td><td>'.$bookingsMailData['bride_name'].'</td></tr>';
            if(!empty($bookingsMailData['bride_home_phone']))
            $bride_data .='<tr><td>Home Phone :</td><td>'.($bookingsMailData['bride_home_phone']).' </td></tr>';
            if(!empty($bookingsMailData['bride_mobile']))
            $bride_data .='<tr><td>Mobile :</td><td>'.($bookingsMailData['bride_mobile']).' </td></tr>';
            if(!empty($bookingsMailData['bride_billing_address']))
            $bride_data .='<tr><td>Bride Billing Address :</td><td>'.($bookingsMailData['bride_billing_address']).' </td></tr>';
        }

        $retData='<table width="100%"  style="text-align:center;">
        <td colspan=2><strong>Customer Details</strong></td></tr>
        <tr><td width="35%"> Name :</td><td width="65%">'.$bookingsMailData['customer']['userinfo'][0]['name'].'</td></tr>
        <tr><td> Email :</td><td>'.$bookingsMailData['customer']['userinfo'][0]['email'].'</td></tr>
        <tr><td> Phone :</td><td>'.$bookingsMailData['customer']['userinfo'][0]['phone'].'</td></tr>
        <tr><td> Relationship with Event :</td><td>'.relation_with_event($bookingsMailData['customer']['userinfo'][0]['relation_with_event']).'</td></tr>
        <tr><td> Event Date :</td><td><strong>'.date(config('constants.date_formate'),$bookingsMailData['date_of_event']).'<strong></td></tr>'
        .$groom_data.$bride_data.$packageData.$vg_data.$photographerData.'
        
</table>';
return $retData;
    }
}
// Count All records
if(!function_exists('get_record_count')){
    function get_record_count(){

        $retData=[
            'office'=>0,
            'web'=>0,
            'hall'=>0,
            'customer_pencil'=>0,
            'customer'=>0,
            'admin'=>0,
            'venue_groups'=>0,
            'photographers'=>0,
            'pencil' => 0,
            'awaiting_for_photographer' => 0,
            'declined_by_photographer' => 0,
            'pending_customer_agreement' => 0, 
            'pending_customer_deposit' => 0, 
            'on_hold' => 0, 
            'confirmed' => 0, 
            'completed' => 0, 
            'total_bookings'=>0,
            'total_users'=>0,
            'total_pencils'=>0,
            'total_packages'=>0,
            'photographer_awaited'=>0,
            'photographer_scheduled'=>0,
            ];

        // total Packages Count
        $package_info = DB::table('packages')
                        ->select('is_active', DB::raw('count(*) as total'))
                        ->groupBy('is_active')
                        ->where('is_active',1)
                        ->get()->toArray();
                        $package_info=$package_info[0];
        
        if(isset($package_info) && !empty($package_info))
        $retData['total_packages']=$package_info->total;                 



                if(get_session_value('group_id')==config('constants.groups.admin')|| get_session_value('group_id')==config('constants.groups.customer')){
                      // Pencil Info
                    $pencil_where=[
                        ['status', '=', config('constants.booking_status.pencil')],
                    ];
                    if(get_session_value('group_id')==config('constants.groups.customer')){
                        $pencil_where[]=['customer_id','=',get_session_value('id')];
                    }
                    $pencil_info = DB::table('bookings')
                    ->select('pencile_by', DB::raw('count(*) as total'))
                    ->groupBy('pencile_by')
                    ->orderBy('pencile_by', 'asc')
                    ->where($pencil_where)
                    ->get()->toArray();
                    foreach($pencil_info as $key=>$pencil_by){
                    
                        if($pencil_by->pencile_by==config('constants.pencileBy.office')){
                            $retData['office']=$pencil_by->total;
                        }elseif($pencil_by->pencile_by==config('constants.pencileBy.website')){
                            $retData['web']=$pencil_by->total;
                        }elseif($pencil_by->pencile_by==config('constants.pencileBy.venue_group')){
                            $retData['hall']=$pencil_by->total;
                        }elseif($pencil_by->pencile_by==config('constants.pencileBy.customer')){
                            $retData['customer_pencil']=$pencil_by->total;
                        }
                        
                    }
                    $retData['total_pencils']=$retData['office']+$retData['web']+$retData['hall']+$retData['customer_pencil'];
                }else{

                    $pencil_where[]=['user_id','=',get_session_value('id')];
                    //$pencil_info = DB::table('bookings_users')
                    $pencil_info = App\Models\adminpanel\bookings_users::
                    joinRelationship('bookings', function ($join) {
                        $where_bookings[]=['bookings.is_active','=','1'];
                        $where_bookings[]=['bookings.status','=',config('constants.booking_status.pencil')];
                        $join->where($where_bookings);
                    })
                    ->select('user_id', DB::raw('count(*) as total'))
                    ->groupBy('user_id')
                    ->orderBy('user_id', 'asc')
                    ->where($pencil_where)
                    ->get(); 
                   
                    if(count($pencil_info)>0)
                    $retData['total_pencils']=$pencil_info[0]->total;
                    //p($pencil_info); die;
                }
            
                     // Booking Info
        if(get_session_value('group_id')==config('constants.groups.admin')|| get_session_value('group_id')==config('constants.groups.customer')){
        
        $booking_where=[
            ['status', '>', config('constants.booking_status.pencil')],
        ];
        if(get_session_value('group_id')==config('constants.groups.customer')){
            $booking_where[]=['customer_id','=',get_session_value('id')];
        }

        $booking_info = DB::table('bookings')
                 ->select('status', DB::raw('count(*) as total'))
                 ->groupBy('status')
                 ->where($booking_where)
                 ->orderBy('status', 'asc')
                 ->get()->toArray();
   
                
                  foreach($booking_info as $key=>$booking){
                    
                    if($booking->status==config('constants.booking_status.pencil')){
                        $retData['total_bookings']=$retData['total_bookings']+$booking->total;
                        
                    }
                    elseif($booking->status==config('constants.booking_status.awaiting_for_photographer')){
                        $retData['awaiting_for_photographer']=$booking->total;
                         $retData['total_bookings']=$retData['total_bookings']+$booking->total;
                    }
                    elseif($booking->status==config('constants.booking_status.declined_by_photographer')){
                        $retData['declined_by_photographer']=$booking->total;
                         $retData['total_bookings']=$retData['total_bookings']+$booking->total;
                    }
                    elseif($booking->status==config('constants.booking_status.pending_customer_agreement')){
                        $retData['pending_customer_agreement']=$booking->total;
                         $retData['total_bookings']=$retData['total_bookings']+$booking->total;
                    }
                    elseif($booking->status==config('constants.booking_status.pending_customer_deposit')){
                        $retData['pending_customer_deposit']=$booking->total;
                         $retData['total_bookings']=$retData['total_bookings']+$booking->total;
                    }
                    elseif($booking->status==config('constants.booking_status.on_hold')){
                        $retData['on_hold']=$booking->total;
                        $retData['total_bookings']=$retData['total_bookings']+$booking->total;
                    }
                    elseif($booking->status==config('constants.booking_status.confirmed')){
                        $retData['confirmed']=$booking->total;
                        $retData['total_bookings']=$retData['total_bookings']+$booking->total;
                    }
                    elseif($booking->status==config('constants.booking_status.complete')){
                        $retData['completed']=$booking->total;
                        
                    }
                   
                   
                  }
        }
               
               // Users count
               $user_info = DB::table('users')
               ->select('group_id', DB::raw('count(*) as total'))
               ->groupBy('group_id')
               ->where('is_active',1)
               ->orderBy('group_id', 'asc')
               ->get();
                    foreach($user_info as $key=>$userData){

                        $retData['total_users']=$retData['total_users']+$userData->total;

                        if($userData->group_id==config('constants.groups.admin'))
                        $retData['admin']=$userData->total;
                        elseif($userData->group_id==config('constants.groups.venue_group_hod'))
                        $retData['venue_groups']=$userData->total;
                        elseif($userData->group_id==config('constants.groups.photographer'))
                        $retData['photographers']=$userData->total;
                        elseif($userData->group_id==config('constants.groups.customer'))
                        $retData['customer']=$userData->total;
                     
                    }
                   
                // Awaited and Scheduled for Photographer
                if(get_session_value('group_id')==config('constants.groups.photographer') || get_session_value('group_id')==config('constants.groups.venue_group_hod')){
                    $photographer_booking_where[]=['user_id','=',get_session_value('id')];

                    if(get_session_value('group_id')==config('constants.groups.photographer'))
                    $photographer_booking_where[]=['slug','=','new_photographer_assigned'];
                    
                    if(get_session_value('group_id')==config('constants.groups.venue_group_hod'))
                    $photographer_booking_where[]=['slug','=','new_venue_group'];
                    
                   
                    $photographer_booking_info = App\Models\adminpanel\bookings_users::joinRelationship('bookings', function ($join) {
                        $where[]=['bookings.is_active','=','1'];
                        $where[]=['bookings.status','>',config('constants.booking_status.pencil')];
                        $join->where($where);
                    })
                    ->select('bookings_users.status', DB::raw('count(*) as total'))
                    ->groupBy('bookings_users.status')
                    ->orderBy('bookings_users.status', 'asc')
                    ->where($photographer_booking_where)
                    ->get()->toArray(); 
                  // p($photographer_booking_info); 
                    foreach($photographer_booking_info as $key=>$photographer_booking_data){
                        if($photographer_booking_data['status']==0)
                        $retData['photographer_awaited']=$photographer_booking_data['total'];
                        if($photographer_booking_data['status']==1)
                        $retData['photographer_scheduled']=$photographer_booking_data['total'];

                        $retData['total_bookings']=$retData['total_bookings']+$photographer_booking_data['total']; 
                    }
                
                } 

                    
               
                 return $retData;
    }
}
?>
  