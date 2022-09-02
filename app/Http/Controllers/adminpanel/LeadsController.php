<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\Venue_groups;
use App\Models\adminpanel\venue_users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class LeadsController extends Controller
{

    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->venueGroup= new Venue_groups;
        $this->venue_users= new venue_users;
      }

    public function index(){
     
        return view('adminpanel/register');
    }

    public function logout(Request $request){
      
        $request->session()->flush();
        return redirect('/');
    }
    public function noaccess(){

        echo 'this page is not allowed to view';
        die();
    }

    public function login(){
        $data=array();
    
        return view('adminpanel/login', $data);
    }
    public function addLeads(){
       $user=Auth::user(); 
       $leadsTypes=getTypesOfLeads();
       
       $VenueGroupData = $this->venueGroup->orderBy('created_at', 'desc')->with('ownerinfo')->get();
       if($VenueGroupData)
       $VenueGroupData= $VenueGroupData->toArray();
       else
       $VenueGroupData=array();
       

        return view('adminpanel/add_leads',compact('user','VenueGroupData','leadsTypes'));
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
             'password'=>'required',
               
       ]);
       if(Auth::check())
        return redirect('/admin/dashboard');
 
        if (Auth::attempt(['email' => $request['email'], 'password' => $request['password'], 'is_active' => 1])) {

            $userData = $this->users
                                ->with('getGroups')
                                ->with('getVenueGroup')
                                ->where('email', '=', $request['email'])
                                ->where('is_active', '=', 1)
                                ->get()->toArray();

                            $this->user= $userData;
                            $request->session()->put('userData',$userData[0]);
                            $request->session()->put('user_id',$userData[0]['id']);
                            $request->session()->put('group_id',$userData[0]['group_id']);
                            $request->session()->put('role_slug',$userData[0]['get_groups']['slug']);
                            $request->session()->put('org_id',$userData[0]['org_id']);
            // The user is active, not suspended, and exists.
            //$request->session()->flash('alert-success', 'Successfully Logged in');
            //return redirect()->intended('/admin/dashboard');
            return redirect('/admin/dashboard');
        }
        else{
            
            $request->session()->flash('alert-danger', 'Incorrect Email or Password');
            return redirect('/admin/login');
        }
        
    }
    
    public function getlogin(Request $request){
        $data=array();
     
        $request->validate([
           'email'=>'required|email',
            'password'=>'required',
              
      ]);
      $userData = $this->users
                                ->with('getGroups')
                                ->with('getOrganization')
                                ->where('email', '=', $request['email'])
                                //->where('password', '=', md5($request['password']))
                                ->where('is_active', '=', 1)
                                ->get()->toArray();
    //   echo $this->users->where('email', '=', $request['email'])
    //                     ->where('password', '=', md5($request['password']))
    //                     ->toSql();
    //echo $userData[0]['get_groups']['slug'];
     // p($userData); die;
                        if($userData && Hash::check($request['password'], $userData[0]['password'])){
                          echo 'I am in IF Part';
                          $this->user= $userData;
                            $request->session()->put('userData',$userData[0]);
                            $request->session()->put('user_id',$userData[0]['id']);
                            $request->session()->put('group_id',$userData[0]['group_id']);
                            $request->session()->put('role_slug',$userData[0]['get_groups']['slug']);
                            $request->session()->put('org_id',$userData[0]['org_id']);
                            
                            //p($request->session()->all());
                            
                             return redirect('/admin/dashboard/');
                        }
                        else{
                            echo 'I am in else';
                            $request->session()->flash('alert-danger', 'Incorrect Email or Password');
                            return redirect('/admin/login');
                        }

    
    }




    
    public function register(Request $request){
       
         $request->validate([
            'fullname'=>'required',
            'phone'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            'password'=>'required|',
            'password_confirmation'=>'required|same:password',
            'org_id'=>'required',
            'terms'=>'required',

        ]);
        
      
        $groupsData = $this->groups->where('id', '=', config('constants.groups.staff'))->get()->toArray();
       
        
        $this->users->name=$request['fullname'];
        $this->users->email=$request['email'];
        $this->users->cnic=$request['cnic'];
        $this->users->phone=$request['phone'];
        $this->users->password=Hash::make($request['password']);
        $this->users->is_active=0;
        $this->users->created_at=time();
        $this->users->group_id=$groupsData[0]['id'];
        $this->users->org_id=$request['org_id'];
     
        $request->session()->flash('alert-success', 'Successfully Registered! Please login');
        $this->users->save();

        return redirect()->back();
    }
    public function SaveUsersData(Request $request){
       
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            'mobileno'=>'required|distinct|unique:users|min:5',
            'phone'=>'required',
            'venue_group_id'=>'required',
            'lead_type'=>'required',
        ]);
        
        
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->firstname=$request['firstname'];
        $this->users->lead_type=$request['lead_type'];
        $this->users->lastname=$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->mobileno=$request['mobileno'];
        $this->users->phone=$request['phone'];
        $this->users->is_active=1;
        $this->users->password=Hash::make('12345678');

        $this->users->created_at=time();
        $this->users->group_id=config('constants.groups.subscriber');
        
      
  
        $request->session()->flash('alert-success', 'Lead Added! Please Check in Pending Leads Tab');
        $this->users->save();
       
        $this->venue_users->user_id=$this->users->id;
        $this->venue_users->venue_group_id=$request['venue_group_id'];
        $this->venue_users->save();

        $this->users->where('id', $this->users->id)
                    ->update(array('venue_users_id'=>$this->venue_users->id));
        
        return redirect()->back();
        
    }

    // List All the Leads 
    public function leads($type=NULL){
        $user=Auth::user();
        if($type=='pending'){
            $leadsData=$this->users->with('getVenueGroup')
            ->where('group_id', '=', config('constants.groups.subscriber'))
            ->where('status', '=', config('constants.lead_status.pending'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        elseif($type=='approved'){
            $leadsData=$this->users->with('getVenueGroup')
            ->where('group_id', '=', config('constants.groups.subscriber'))
            ->where('status', '=', config('constants.lead_status.approved'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        elseif($type=='cancelled'){
            $leadsData=$this->users->with('getVenueGroup')
            ->where('group_id', '=', config('constants.groups.subscriber'))
            ->where('status', '=', config('constants.lead_status.cancelled'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        else{
            $leadsData=$this->users->with('getVenueGroup')
            ->where('group_id', '=', config('constants.groups.subscriber'))
            ->where('is_active', '=', 1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }

      
 
        // $leadsData=$this->users->with('getVenueGroup')
        // ->where('group_id', '=', config('constants.groups.subscriber'))
        // ->where('is_active', '=', 1)
        // ->orderBy('created_at', 'desc')->get();
    
        // p($leadsData);
        // die;
        //$TestsDatawithPatient=$this->Patient_Tests->where('org_id',$user->org_id)->with('patient')->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));


        //$leadsData=$leadsData->toArray();
        
        return view('adminpanel/leads',compact('leadsData','user'));
    }
    public function UpdateUsersData($id,Request $request)
    {
        $dataArray['error']='No';
        

        $validated =  $request->validate([
            'name' => 'required',
            'group_id' => 'required'
            ]);
            if(!$validated){

                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
                echo json_encode($dataArray);
                die;

            }
     
        $data['name']=$request['name'];
        $data['group_id']=$request['group_id'];

        $groupData=Groups::find($request['group_id']);
       
        $groupData=$groupData->toArray();
        // p($groupData);
        // die;
        $dataArray['name']=$data['name'];
        $dataArray['id']=$id;
        $dataArray['group_title']=$groupData['title'];
        $dataArray['group_role']=$groupData['role'];
        
        $dataArray['msg']='Mr.'.get_session_value('name').', '.$data['name'].' record Successfully Updated !';
        $this->users->where('id', $id)
                    ->update($data);
        echo json_encode($dataArray);
        die;

    }
    public function DeleteLeadssData($id){
        $dataArray['error']='No';
        $dataArray['title']='User';

        $result=$this->users->where('id','=',$id)->update(array('is_active'=>3));             
        if($result)
        $dataArray['msg']='Mr.'.get_session_value('name').', record delted successfully!';
        else{
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please fill all the required fields.';

        }
        echo json_encode($dataArray);
        die;
    }
    public function ajaxcall($id, Request $req){
        $dataArray['error']='No';
        $dataArray['title']='Action Taken';
        
        if(!isset($req['action'])){
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please try again later!.';
            echo json_encode($dataArray);
            die;
        }
        if(isset($req['action']) && $req['action']=='changestatus'){ 
            $dataArray['title']='Lead Status Updated ';
            if(config('constants.lead_status.pending')==$req['status'])
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-danger btn-flat btn-sm"><i class="fas fa-chart-line"></i> Pending</a>';
            else if(config('constants.lead_status.approved')==$req['status'])
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-success btn-flat btn-sm"><i class="fas fa-chart-line"></i> Approved</a>';
            else if(config('constants.lead_status.cancelled')==$req['status'])
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-secondary btn-flat btn-sm"><i class="fas fa-chart-line"></i> Cancelled</a>';

            $result=$this->users->where('id','=',$id)->update(array('status'=>$req['status']));             
            if($result)
            $dataArray['msg']='Mr.'.get_session_value('name').', User Lead '.$req['alertmsg'].' successfully!';
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }
            
        }
        else if(isset($req['action']) && $req['action']=='trash')
        {
            $dataArray['title']='Record Trashed';
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>2));             
            if($result)
            $dataArray['msg']='Mr.'.get_session_value('name').', Record Trashed successfully!';
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }
        }
        else if(isset($req['action']) && $req['action']=='delete')
        {
            $dataArray['title']='Record Deleted';
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>3));             
            if($result)
            $dataArray['msg']='Mr.'.get_session_value('name').', Record Deleted successfully!';
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }
        }
        else if(isset($req['action']) && $req['action'] =='viewLeadData'){
            $dataArray['error']='No';
            $dataArray['msg']='Lead Successfully Updated';
            $dataArray['title']='Leads Panel';
            $leadsData=getLeadWithVenuebyID($req['id']);
            
            //p($leadsData);
            $leadHTML='<div class="container">
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Name</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['name'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Email</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['email'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Mobile No.</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['mobileno'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Phone</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['phone'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5"><strong>Venue Group Name</strong></div>
                <div class="col-5">
                    '.$leadsData['get_venue_group_detail']['name'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Venue Group Address</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['get_venue_group_detail']['address'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Venue Manager Name</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['get_venue_group_detail']['hod_name'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Manager Phone NO.</strong>
                </div>
                <div class="col-5">
                    '.$leadsData['get_venue_group_detail']['hod_phone'].'
                    </div>
                <div class="col-1">&nbsp;</div>
            </div>
        </div>';
            $dataArray['res']=$leadHTML;
        }
        else if(isset($req['action']) && $req['action'] =='SaveEditFormLead'){
            $dataArray['error']='No';
            $dataArray['msg']='Lead Successfully Updated';
            $dataArray['title']='Leads Panel';
            //$dataArray['formdata']=$req->all();

            // $this->venue_users->user_id=$req['id'];
            // $this->venue_users->venue_group_id=$req['venue_group_id'];
            // $this->venue_users->save();
    
            $this->venue_users->where('id', $req['venue_user_id'])->update(array('venue_group_id'=>$req['venue_group_id']));
            $LeadData=array();
            $dataArray['firstname']=$req['firstname'];
            $dataArray['lastname']=$req['lastname'];
            $dataArray['name']=$req['firstname'].' '.$req['lastname'];
            $dataArray['mobileno']=$req['mobileno'];
            $dataArray['phone']=$req['phone'];
            $dataArray['id']=$req['lead_id'];
            $dataArray['lead_type']=$req['lead_type'];
            
            $this->users->where('id', $req['lead_id'])->update(array(
                'firstname'=>$req['firstname'],
                'lastname'=>$req['lastname'],
                'name'=>$req['firstname'].' '.$req['lastname'],
                'mobileno'=>$req['mobileno'],
                'phone'=>$req['phone'],
                'lead_type'=>$req['lead_type'],

            ));
            
            //$this->users->where('id', $req['lead_id'])->update(array($LeadData));

            $leadType=config('constants.lead_types.'.$req['lead_type']);
            $dataArray['lead_type_tile']=$leadType['title'];
            $dataArray['venue_group_name']=$req['venue_group_name'];
            echo json_encode($dataArray);
            die;

        }
        else if(isset($req['action']) && $req['action'] =='updateLeadForm'){
            $dataArray['error']='No';
           
            $data=getLeadWithVenuebyID($req['id']);
           // p($data); die;
            $csrf_token = csrf_token();
            $venueGroupOptions=$leadTypeOptions='';            
            $leadsTypes=getTypesOfLeads();
          
            foreach ($leadsTypes as $key=>$leadtype) 
            {
                $selected='';
                if($data['lead_type']==$key)
                $selected='selected';
                $leadTypeOptions .='<option '.$selected.' value="' . $key . '">' . $leadtype['title'] .'</option>';
             }

             // Get Venue Group detail to list dropdown
             $VenueGroupData = $this->venueGroup->orderBy('created_at', 'desc')->with('ownerinfo')->get();
                if($VenueGroupData)
                $VenueGroupData= $VenueGroupData->toArray();
                else
                $VenueGroupData=array();

             foreach ($VenueGroupData as $venueData){
                $selected='';
                if($data['get_venue_group_detail']['id']==$venueData['id']){
                    $selected='selected';
                    $venueGroupName=$venueData['name'];
                }
                
                $venueGroupOptions .='<option '.$selected.' value="' . $venueData['id'] . '">' . $venueData['name'] . '</option>';
             }
            
        
$formHtml='<form id="EditLeadForm"
                                                                            method="GET"
                                                                            action=""
                                                                            onsubmit="return updateLead('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveEditFormLead" />
                                                                            <input type="hidden" name="venue_user_id" value="'.$data['get_venue_group']['id'].'" />
                                                                            <input type="hidden" name="lead_id" value="'.$data['id'].'" />
                                                                            <input type="hidden" id="venue_group_name" name="venue_group_name" value="'.$venueGroupName.'" />

                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                    <select name="lead_type"class="form-control select2bs4" placeholder="Select Lead Type">
                                                                                   '.$leadTypeOptions.'
                                                                                    </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="firstname"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Name"
                                                                                            value="'. $data['firstname'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="text" name="lastname"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Name"
                                                                                            value="'. $data['lastname'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                        <input disabled readonly type="text"
                                                                                            name="email"
                                                                                            class="form-control"
                                                                                            placeholder="Enter Email"
                                                                                            value="'. $data['email'].'"
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input type="text"
                                                                                        name="mobileno"
                                                                                        class="form-control"
                                                                                        placeholder="Mobile No."
                                                                                        value="'. $data['mobileno'].'"
                                                                                        required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="phone" class="form-control" placeholder="Phone No." value="'. $data['phone'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                    <select id="venue_group_id" name="venue_group_id" onchange="$(\'#venue_group_name\').val($(\'#venue_group_id option:selected\').text())" class="form-control select2bs4" placeholder="Select Venue Group">'.$venueGroupOptions.'</select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-5">&nbsp;</div>
                                                                                <div class="col-2">
                                                                                    <button type="submit"
                                                                                        class="btn btn-outline-success btn-block btn-lg"><i
                                                                                            class="fa fa-save"></i>
                                                                                        Save Changes</button>
                                                                                </div>
                                                                                <div class="col-5">&nbsp;</div>

                                                                            </div>
                                                                        </form>';
            $dataArray['formdata']=$formHtml;
        }
      
        echo json_encode($dataArray);
        die;
    }

   
}
