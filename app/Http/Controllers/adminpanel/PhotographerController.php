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

// Used for Email Section
use App\Mail\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class PhotographerController extends Controller
{
    

    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->venueGroup= new Venue_groups;
        $this->venue_users= new venue_users;
      }
      public function addphotographers(){
        $user=Auth::user(); 
        
         return view('adminpanel/add_photographers',get_defined_vars());
     }
     public function SavephotographersData(Request $request){
       
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            'password'=>'required',
            'phone'=>'required',
            //'city'=>'required',
            //'address'=>'required',
        ]);
        
        
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->phone=$request['phone'];
        $this->users->is_active=1;
        $this->users->password=Hash::make($request['password']);

        $this->users->created_at=time();
        $this->users->group_id=config('constants.groups.photographer');
       
        $this->users->city=$request['city'];

        $request->session()->flash('alert-success', 'photographer Added! Please Check in photographers list Tab');
        $this->users->save();
       //
       $mailData['body_message']='you have been assigned a new password to the Online portal. Please use your email and password <strong>'.$request['password'].'</strong> to sign in to your dashboard.';
       $mailData['subject']='Welcome to kleins photography';
       $toEmail=$request['email'];

       if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
        $request->session()->flash('alert-success', 'photographer Added');
       }
        // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' Added new photographer '.$this->users->name;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$this->users->id,
                        'action_slug'=>'photographer_added',
                        'comments'=>$activityComment,
                        'others'=>'users',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);

        return redirect()->back();
        
    }
    // List All the photographers 
    public function photographers($type=NULL){
        
        $user=Auth::user();
        if($user->group_id==config('constants.groups.admin')){
            $photographersData=$this->users->with('City')
            ->where('group_id', '=', config('constants.groups.photographer'))
            ->where('is_active', '=', 1)
            ->where('zipcode_id', '!=', NULL)
            ->where('city_id', '!=', NULL)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }else{
            $photographersData=$this->users->with('City')
            ->where('group_id', '=', config('constants.groups.photographer'))
            ->where('is_active', '=', 1)
            ->where('id', '=', get_session_value('id'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
            
       
        return view('adminpanel/photographers',compact('photographersData','user'));
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
            $activityComment='Mr.'.get_session_value('name').' moved photographer to approved/pending/cancelled';

            if(config('constants.lead_status.pending')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-danger btn-flat btn-sm"><i class="fas fa-chart-line"></i> Pending</a>';
            $activityComment='Mr.'.get_session_value('name').' moved photographer to pending';
            }
            else if(config('constants.lead_status.approved')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-success btn-flat btn-sm"><i class="fas fa-chart-line"></i> Approved</a>';
            $activityComment='Mr.'.get_session_value('name').' moved photographer to approved';
            }
            else if(config('constants.lead_status.cancelled')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-secondary btn-flat btn-sm"><i class="fas fa-chart-line"></i> Cancelled</a>';
            $activityComment='Mr.'.get_session_value('name').' moved photographer to cancelled';
            }
            $result=$this->users->where('id','=',$id)->update(array('status'=>$req['status']));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', photographer '.$req['alertmsg'].' successfully!';
                
                $activityData=array(
                    'user_id'=>get_session_value('id'),
                    'action_taken_on_id'=>$id,
                    'action_slug'=>'photographer_status_changed',
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
            
        }
        else if(isset($req['action']) && $req['action']=='trash')
        {
            $dataArray['title']='Record Trashed';
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>2));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Trashed successfully!';
                  // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'photographer_trashed',
                'comments'=>'Mr.'.get_session_value('name').' moved photographer to trash',
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
            $result=$this->users->where('id','=',$id)->update(array('is_active'=>3));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', Record Deleted successfully!';
                // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'photographer_deleted',
                'comments'=>'Mr.'.get_session_value('name').' deleted photographer',
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }

        }
        else if(isset($req['action']) && $req['action'] =='view_photographer'){
            $dataArray['error']='No';
            $dataArray['msg']='Lead Successfully Updated';
            $dataArray['title']='Photographer';
           
            $data=$this->users->with('City')
            ->where('group_id', '=', config('constants.groups.photographer'))
            ->where('id', '=', $req['id'])
            ->orderBy('created_at', 'desc')->get()->toArray();
            $photographerData=$data[0];
            
            $leadHTML='<div class="container">
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Name</strong>
                </div>
                <div class="col-5">
                    '.$photographerData['name'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Email</strong>
                </div>
                <div class="col-5">
                    '.$photographerData['email'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Phone</strong>
                </div>
                <div class="col-5">
                    '.$photographerData['phone'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5"><strong>Unit Number</strong></div>
                <div class="col-5">
                    '.$photographerData['unitnumber'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Address</strong>
                </div>
                <div class="col-5">
                    '.$photographerData['address'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>City</strong>
                </div>
                <div class="col-5">
                    '.$photographerData['city']['name'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
        </div>';
            $dataArray['res']=$leadHTML;
        }
        else if(isset($req['action']) && $req['action'] =='SaveAddtophotographerForm'){
            $dataArray['error']='No';
            $dataArray['msg']='photographer Successfully Updated';
            $dataArray['title']='Photographer';
            $dataArray['actionType']='move_to_photographer';
            //$dataArray['formdata']=$req->all();

            $dataArray['firstname']=$req['firstname'];
            $dataArray['lastname']=$req['lastname'];
            $dataArray['name']=$req['firstname'].' '.$req['lastname'];
            $dataArray['phone']=$req['phone'];
            $dataArray['address']=$req['address'];
            $dataArray['unitnumber']=$req['unitnumber'];
            if(isset($req['password']) && !empty($req['password']))
            $req['password']=Hash::make($req['password']);
            $dataArray['id']=$req['photographer_id'];
            
            $this->users->where('id', $req['photographer_id'])->update(
                array(
                    'firstname'=>$req['firstname'],
                    'lastname'=>$req['lastname'],
                    'name'=>$req['firstname'].' '.$req['lastname'],
                    'phone'=>$req['phone'],
                    'unitnumber'=>$req['unitnumber'],
                    'address'=>$req['address'],
                    )
            );
            // Activity Logged
            $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['photographer_id'],
                'action_slug'=>'photographer_updated',
                'comments'=>'Mr.'.get_session_value('name').' updated a photographer Mr.'.$req['firstname'].' '.$req['lastname'],
                'others'=>'photographer',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            
            
            echo json_encode($dataArray);
            die;

        }
        
        else if(isset($req['action']) && $req['action'] =='editphotographerForm'){
            $dataArray['error']='No';
           
            
            $data=$this->users->with('City')
            ->where('group_id', '=', config('constants.groups.photographer'))
            ->where('id', '=', $req['id'])
            ->orderBy('created_at', 'desc')->get()->toArray();
            $data=$data[0];
            $csrf_token = csrf_token();
            
        
$formHtml='<form id="EditphotographerForm"
                                                                            method="GET" action="" onsubmit="return updatephotographer('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveAddtophotographerForm" />
                                                                            <input type="hidden" name="photographer_id" value="'.$data['id'].'" />
                                                                            
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                <label style="float:left">First Name</label>
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
                                                                                <label style="float:left">Last Name</label>
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
                                                                                <label style="float:left">Email</label>
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
                                                                                <label style="float:left">Password</label>
                                                                                    <div class="input-group mb-3">
                                                                                        <input type="password"
                                                                                            name="password"
                                                                                            class="form-control"
                                                                                            placeholder="Leave blank if you don\'t want to change"
                                                                                            >
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                            <label style="float:left">Phone</label>
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="phone" class="form-control" placeholder="Phone No." value="'. $data['phone'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                            <label style="float:left">Address</label>
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="address" class="form-control" placeholder="Home Address" value="'. $data['address'].'" >
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
        }elseif(isset($req['action']) && $req['action']=='qsearch_photographer'){ 

            $search_val=$req['qsearch'];
            
        $usersData=$this->users
            ->where(function($query) use($search_val){
                $query->where('name', 'like', '%' . $search_val . '%')
                        ->orwhere('name', 'like', '%' . $search_val . '%')
                        ->orwhere('email', 'like', '%' . $search_val . '%');
            })
            ->where('group_id',config('constants.groups.photographer'))
            //->with('userinfo')
            //->with('bookings')
            ->orderBy('created_at', 'desc')->get()->toArray();
            $user_ids=[];
     
            $response='<thead>
                            <tr>
                            <th> Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Booking From</th>
                            <th>Booking To</th>
                            <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>';
                        $counter = 1;
               
                        $photographer_id_array=array();
                            foreach($usersData as $data){
                                $photographer_id_array[]=$data['id'];
                                $response .=' <form
                                 id="download_venuegroup_balance_'.$data['id'].'" 
                                 method="GET" 
                                 action="'.route('reports.vg.payments.export',$data['id']).'"
                                 >
                                <input type="hidden" id="token_'.$data['id'].'" name="_token" value="'.csrf_token().'">
                                <input type="hidden" name="action" value="download_venuegroup_balance">
                                <input type="hidden" name="venue_group_id" value="'.$data['id'].'"> ';

                                $response .='<tr id="row_'.$data['id'].'">
                                <td id="date_of_event_'.$data['id'].'">'.$data['name'].'</td>
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
                                             <button onclick="sumit_form('.$data['id'].')" type="button" class="btn btn-block btn-primary"><i class="fa fa-download"></i> Photographer Balance Excel</button>
                                            </td>
                            </tr>';
                            //<button onclick="$(\'#download_venuegroup_balance_'.$data['id'].'\').submit()" type="button" class="btn btn-block btn-primary"><i class="fa fa-download"></i> Venue Balance Excel</button>
                            $response .='</form>';

                          
                            $counter++;
                        }
                        $response .='  </tbody>
                        <tfoot>
                            <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Booking From</th>
                            <th>Booking To</th>
                            <th>Action</th>
                            </tr>
                            </tfoot>';
                        
                        $dataArray['photographer_ids']= implode(',',$photographer_id_array)  ;
                        $dataArray['response']=$response;

        }
      
        echo json_encode($dataArray);
        die;
    }

 
}
