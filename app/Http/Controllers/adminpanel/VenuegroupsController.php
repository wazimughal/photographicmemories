<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Used for Email Section
use App\Mail\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class VenuegroupsController extends Controller
{
   
    

    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        
      }
      public function addvenuegroups(){
        $user=Auth::user(); 
        
 
         return view('adminpanel/add_venuegroups',get_defined_vars());
     }
     public function SavevenuegroupsData(Request $request){
       
        $validator=$request->validate([
            //'firstname'=>'required',
            //'lastname'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            //'phone'=>'required',
            'vg_name'=>'required',
            'vg_manager_name'=>'required',
            'vg_manager_phone'=>'required',
            //'vg_description'=>'required',
            'city'=>'required',
            //'address'=>'required',
            'password'=>'required',
        ]);
        
        
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->phone=$request['phone'];
        $this->users->vg_name=$request['vg_name'];
        $this->users->vg_manager_name=$request['vg_manager_name'];
        $this->users->vg_manager_phone=$request['vg_manager_phone'];
        $this->users->vg_description=$request['vg_description'];
        $this->users->address=$request['address'];
        $this->users->is_active=1;
        $this->users->zipcode_id=1;
        $this->users->password=Hash::make($request['password']);
        $this->users->created_at=date('Y-m-d H:I:s',time());
        $this->users->group_id=config('constants.groups.venue_group_hod');
        $this->users->city=$request['city'];

//
// Send Email
$mailData['body_message']='Welcome To Klein’s photography. You have been assigned a new password to your online portal. Your password is: '.$request['password'].'. please use your email as your username.';
$mailData['subject']='Kleins Photography invitation';
$mailData['button_title']='LOGIN';
$toEmail=$request['email'];


if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
    $request->session()->flash('alert-success', 'Please check your email');
}

        $request->session()->flash('alert-success', 'venuegroup Added! Please Check in venuegroups list Tab');
        $this->users->save();
        // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' Added a new venuegroup '.$this->users->vg_name;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$this->users->id,
                        'action_slug'=>'venuegroup_added',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
    }
    // List All the venuegroups 
    public function venuegroups($type=NULL){
        $user=Auth::user();
        $where_clause=[
            ['group_id', '=', config('constants.groups.venue_group_hod')],
            ['is_active', '=', 1],
        ];
        if($user->group_id!=config('constants.groups.admin'))
        $where_clause[]=['id', '=', $user->id];
        if($type=='trash')
        $where_clause[1]=['is_active', '=', 2];

            $venuegroupsData=$this->users
            ->where($where_clause)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
       
        return view('adminpanel.venuegroups',get_defined_vars());
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

                    $activityComment='Mr.'.get_session_value('name').' updated User '.$data['name'].' Record';
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$id,
                        'action_slug'=>'user_record_updated',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);
        echo json_encode($dataArray);
        die;

    }


    public function DeleteLeadssData($id){
        $dataArray['error']='No';
        $dataArray['title']='User';

        $result=$this->users->where('id','=',$id)->update(array('is_active'=>3));             
        if($result){
            $dataArray['msg']='Mr.'.get_session_value('name').', record delted successfully!';

            $activityComment='Mr.'.get_session_value('name').' moved lead to approved/pending/cancelled';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'lead_status_changed',
                'comments'=>$activityComment,
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
        
        else{
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please fill all the required fields.';

        }
        echo json_encode($dataArray);
        die;
    }
    public function ajaxcall($id=NULL, Request $req){
        $dataArray['error']='No';
        $dataArray['title']='Action Taken';
        
        if(!isset($req['action'])){
            $dataArray['error']='Yes';
            $dataArray['msg']='There is some error ! Please try again later!.';
            echo json_encode($dataArray);
            die;
        }
        else if(isset($req['action']) && $req['action']=='trash')
        {
            $dataArray['title']='Record Trashed';
            $dataArray['id']=$id;
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>2));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Trashed successfully!';
                  // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'venuegroup_trashed',
                'comments'=>'Mr.'.get_session_value('name').' moved venuegroup to trash',
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }
        }
        else if(isset($req['action']) && $req['action']=='restore')
        {
            $dataArray['title']='Record Restored';
            $dataArray['id']=$id;
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>1));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Restored successfully!';
                  // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'venuegroup_restored',
                'comments'=>'Mr.'.get_session_value('name').' removed from Trash to Venue Group',
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }
        }
        else if(isset($req['action']) && $req['action']=='delete')
        {
            $dataArray['title']='Record Deleted';
            $dataArray['id']=$id;
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>3));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Deleted successfully!';
                // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'venuegroup_deleted',
                'comments'=>'Mr.'.get_session_value('name').' deleted venuegroup',
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }

        }
        else if(isset($req['action']) && $req['action'] =='viewVenueGroupData'){
            $dataArray['error']='No';
            $dataArray['msg']='Lead Successfully Updated';
            $dataArray['title']='Leads Panel';
           
            $data=$this->users->with('City')
            ->where('group_id', '=', config('constants.groups.venue_group_hod'))
            ->where('id', '=', $req['id'])
            ->orderBy('created_at', 'desc')->get()->toArray();
            $venuegroupData=$data[0];
            
            //p($venuegroupData); dd('bss');
            $leadHTML='<div class="container">
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Venue Group Name</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['vg_name'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Venue Group Manager Name</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['vg_manager_name'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Email</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['email'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Manager Phone</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['vg_manager_phone'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Description</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['vg_description'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>City</strong>
                </div>
                <div class="col-5">
                    '.$venuegroupData['city']['name'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5"><strong>Venue Group Address</strong></div>
                <div class="col-5">
                    '.$venuegroupData['address'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            
        </div>';
            $dataArray['formdata']=$leadHTML;
        }
        else if(isset($req['action']) && $req['action'] =='SaveAddtovenuegroupForm'){
            $dataArray['error']='No';
            $dataArray['msg']='venuegroup Successfully Updated';
            $dataArray['title']='Leads Panel';
            $dataArray['actionType']='move_to_venuegroup';
            //$dataArray['formdata']=$req->all();

            $dataArray['firstname']=$req['firstname'];
            $dataArray['lastname']=$req['lastname'];
            $dataArray['name']=$req['firstname'].' '.$req['lastname'];
            $dataArray['vg_name']=$req['vg_name'];
            $dataArray['vg_manager_phone']=$req['vg_manager_phone'];
            $dataArray['vg_manager_name']=$req['vg_manager_name'];
            $dataArray['address']=$req['address'];
            $dataArray['vg_description']=$req['vg_description'];
            $dataArray['id']=$req['uid'];
            $dataArray['venuegroup_id']=$req['venuegroup_id'];
            $dataArray['city']=$req['cityname'];
            $cityId=$req['city'];

            $this->users->where('id', $req['uid'])->update(
                array(
                    'firstname'=>$req['firstname'],
                    'lastname'=>$req['lastname'],
                    'name'=>$req['firstname'].' '.$req['lastname'],
                    'vg_manager_name'=>$req['vg_manager_name'],
                    'vg_manager_phone'=>$req['vg_manager_phone'],
                    'vg_name'=>$req['vg_name'],
                    'address'=>$req['address'],
                    'vg_description'=>$req['vg_description'],
                    'city'=>$req['city']
                    )
            );
           
            
            // Activity Logged
            $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['uid'],
                'action_slug'=>'venuegroup_updated',
                'comments'=>'Mr.'.get_session_value('name').' updated a Venue Group Mr.'.$req['firstname'].' '.$req['lastname'],
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            
            
            echo json_encode($dataArray);
            die;

        }
        else if(isset($req['action']) && $req['action'] =='editvenuegroupForm'){
            $dataArray['error']='No';
           
            //$data=getLeadWithVenuebyID($req['id']);
            $data=$this->users
            ->where('group_id', '=', config('constants.groups.venue_group_hod'))
            ->where('id', '=', $req['id'])
            ->orderBy('created_at', 'desc')->get()->toArray();
            $data=$data[0];
            //p($data); die;
            $csrf_token = csrf_token();
            
        
$formHtml='<form id="EditvenuegroupForm"
                                                                            method="GET" action="" onsubmit="return updatevenuegroup('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveAddtovenuegroupForm" />
                                                                            <input type="hidden" name="uid" value="'.$data['id'].'" />
                                                                            <input type="hidden" id="cityname" name="cityname" value="'.$data['city'].'" />
                                                                            
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="vg_name" class="form-control" placeholder="Group Venue Name" value="'. $data['vg_name'].'" required>
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
                                                                                    <input  type="text" name="vg_manager_name" class="form-control" placeholder="Group Venue Manager Name" value="'. $data['vg_manager_name'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="vg_manager_phone" class="form-control" placeholder="Manager Phone" value="'. $data['vg_manager_phone'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                    <select id="city"  name="city" class="form-control select2bs4" placeholder="Select City">'.getCitiesOptions($data['city']).'</select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div id="othercity"></div>
                                                                           
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="address" id="vg_address" class="form-control" placeholder="Venue Group Address" value="'. $data['address'].'" >
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-4">&nbsp;</div>
                                                                                <div class="col-4">
                                                                                    <button type="submit"
                                                                                        class="btn btn-outline-success btn-block btn-lg"><i
                                                                                            class="fa fa-save"></i> Save Changes</button>
                                                                                </div>
                                                                                <div class="col-4">&nbsp;</div>

                                                                            </div>
                                                                        </form>';
            $dataArray['formdata']=$formHtml;

        }elseif(isset($req['action']) && $req['action']=='qsearch_venue'){ 

            $search_val=$req['qsearch'];
            
        $usersData=$this->users
            ->where(function($query) use($search_val){
                $query->where('name', 'like', '%' . $search_val . '%')
                        ->orwhere('vg_name', 'like', '%' . $search_val . '%')
                        ->orwhere('email', 'like', '%' . $search_val . '%');
            })
            ->where('group_id',config('constants.groups.venue_group_hod'))
            //->with('userinfo')
            //->with('bookings')
            ->orderBy('created_at', 'desc')->get()->toArray();
            $user_ids=[];
     
            $response='<thead>
                            <tr>
                            <th>Venue Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Booking From</th>
                            <th>Booking To</th>
                            <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>';
                        $counter = 1;
               
                        $venuegroup_id_array=array();
                            foreach($usersData as $data){
                                $venuegroup_id_array[]=$data['id'];
                                $response .=' <form
                                 id="download_venuegroup_balance_'.$data['id'].'" 
                                 method="GET" 
                                 action="'.route('reports.vg.payments.export',$data['id']).'"
                                 >
                                <input type="hidden" id="token_'.$data['id'].'" name="_token" value="'.csrf_token().'">
                                <input type="hidden" name="action" value="download_venuegroup_balance">
                                <input type="hidden" name="venue_group_id" value="'.$data['id'].'"> ';

                                $response .='<tr id="row_'.$data['id'].'">
                                <td id="date_of_event_'.$data['id'].'">'.$data['vg_name'].'</td>
                                <td id="venue_group_'.$data['id'].'">'.$data['email'].'</td>
                                <td id="address_'.$data['id'].'">'.$data['address'].'</td>
                                <td id="row_from_date_'.$data['id'].'">
                                                <div class="input-group date" id="from_date_'.$data['id'].'" data-target-input="nearest">
                                                    <input id="input_from_date_'.$data['id'].'"  type="text"  name="from_date" placeholder="From date" class="form-control datetimepicker-input" data-target="#from_date_'.$data['id'].'"/>
                                                    <div class="input-group-append" data-target="#from_date_'.$data['id'].'" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div>  
                                            </td>
                                            <td id="row_to_date_'.$data['id'].'">
                                                <div class="input-group date" id="to_date_'.$data['id'].'" data-target-input="nearest">
                                                    <input id="input_to_date_'.$data['id'].'" type="text"  name="to_date" placeholder="To Date" class="form-control datetimepicker-input" data-target="#to_date_'.$data['id'].'"/>
                                                    <div class="input-group-append" data-target="#to_date_'.$data['id'].'" data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                    </div>
                                                </div> 
                                            </td>
                                             <td>
                                             <button onclick="sumit_form('.$data['id'].')" type="button" class="btn btn-block btn-primary"><i class="fa fa-download"></i> Venue Balance Excel</button>
                                            </td>
                            </tr>';
                            //<button onclick="$(\'#download_venuegroup_balance_'.$data['id'].'\').submit()" type="button" class="btn btn-block btn-primary"><i class="fa fa-download"></i> Venue Balance Excel</button>
                            $response .='</form>';

                          
                            $counter++;
                        }
                        $response .='  </tbody>
                        <tfoot>
                            <tr>
                            <th>Venue Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Booking From</th>
                            <th>Booking To</th>
                            <th>Action</th>
                            </tr>
                            </tfoot>';
                        
                        $dataArray['venuegroup_ids']= implode(',',$venuegroup_id_array)  ;
                        $dataArray['response']=$response;

        }
      
        echo json_encode($dataArray);
        die;
    }

}
