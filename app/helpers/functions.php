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
                ->where('group_id', '=', config('constants.groups.subscriber'))
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
// Get Test Report by Test ID
if(!function_exists('getReportByTestId')){
    function getReportByTestId($id){
        //return array($id);
        $testReport = App\Models\adminpanel\PatientReports::where('patient_test_id',$id)->orderBy('id', 'desc')->get();
        if($testReport)
        return $testReport->toArray();
        
        return array();
    }
}
if(!function_exists('getReportByIDs')){
    function getReportByIDs($advisedTestID, $patientTestID){
        //return array($id);
        $testReport = App\Models\adminpanel\PatientReports::with('LabTest')->where('lab_test_id',$advisedTestID)->where('patient_test_id',$patientTestID)->orderBy('id', 'desc')->get();
        if($testReport)
        return $testReport->toArray();
        
        return array();
    }
}

?>