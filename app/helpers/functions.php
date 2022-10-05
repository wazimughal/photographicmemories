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
        
        $photographersData = App\Models\adminpanel\bookings_users::where('user_id',$data['user_id'])
                            ->where('status','<=',config('constants.photographer_assigned.accepted'))
                            ->where('group_id',config('constants.groups.photographer'))
                            ->with('booking')->get()->toArray();
                            //p($photographersData); 
                            $retData['result']=true;

                            //echo 'user_id:'.$data['user_id'].'<br>';
                            //echo 'count :'.count($photographersData);
           foreach($photographersData as $photographer){
                if($photographer['booking']['date_of_event']==$event_date){
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
// Get Options of _photographer_
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
            if($selectID==$data['id']) $selected='selected';
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
            if($selectID==$data['id']) $selected='selected';
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
         
        foreach($cityData as $key=>$data){
            $selected='';
            if($selectID==$data['id']) $selected='selected';
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
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

// Get Options of Venue Groups
if(!function_exists('get_venue_group_options')){
    function get_venue_group_options($selectID=NULL){
    
        $venue_group_data = App\Models\adminpanel\Users::where('is_active',1)->where('group_id',config('constants.groups.venue_group_hod'))->orderBy('id', 'asc')->get();
        if($venue_group_data)
         $venue_group_data=$venue_group_data->toArray();
         $options='';
         
        foreach($venue_group_data as $key=>$data){
            $selected='';
            if($selectID==$data['id']) $selected='selected';
            $options .='<option '.$selected.' value="'.$data['id'].'">'.$data['name'].'</option>';
        }
        $options .='<option value="other">Other</option>';
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

if(!function_exists('booking_email_body')){
    function booking_email_body($bookingsMailData){
        $retData='<table width="100%"  style="text-align:center;">
        <tr><td> Name :</td><td>'.$bookingsMailData['customer']['userinfo'][0]['name'].'</td></tr>
        <tr><td> Email :</td><td>'.$bookingsMailData['customer']['userinfo'][0]['email'].'</td></tr>
        <tr><td> Phone :</td><td>'.$bookingsMailData['customer']['userinfo'][0]['phone'].'</td></tr>
        <tr><td> Relationship with Event :</td><td>'.relation_with_event($bookingsMailData['customer']['userinfo'][0]['relation_with_event']).'</td></tr>
        <tr><td colspan=2> <hr></td></tr>
        <tr><td> Groom Name :</td><td>'.$bookingsMailData['groom_name'].'</td></tr>
        <tr><td> Groom Home Phone :</td><td>'.$bookingsMailData['groom_home_phone'].'</td></tr>
        <tr><td> Groom Mobile :</td><td>'.$bookingsMailData['groom_mobile'].'</td></tr>
        <tr><td> Groom Billing Address :</td><td>'.$bookingsMailData['groom_billing_address'].'</td></tr>
        <tr><td> Bride Name :</td><td>'.$bookingsMailData['bride_name'].'</td></tr>
        <tr><td> Bride Home Phone :</td><td>'.$bookingsMailData['bride_home_phone'].'</td></tr>
        <tr><td> Bride Mobile :</td><td>'.$bookingsMailData['bride_mobile'].'</td></tr>
        <tr><td> Bride Billing Address :</td><td>'.$bookingsMailData['bride_billing_address'].'</td></tr>
        <tr><td> Date of Event :</td><td>'.date('d/m/Y',($bookingsMailData['date_of_event'])).'</td></tr>
</table>';
return $retData;
    }
}
?>
  