<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\PhotographicPackages;
use App\Models\adminpanel\photographer_orders;
use App\Models\adminpanel\Orders;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use DB;

class OrdersController extends Controller
{
    

    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->orders= new Orders;
        $this->packages= new PhotographicPackages;
        $this->photographer_orders= new photographer_orders;

        //$this->middleware('photographerGaurd', ['only' => ['bookings']]);
        
      }
      public function bookings_form($id=NULL){
        $user=Auth::user(); 
        
         return view('adminpanel/add_booking',compact('user','id'));
     }
     public function photographer_bookings($id=NULL){
        $user=Auth::user(); 
        $bookingData=$this->photographer_orders
            ->with('bookings')
            ->where('photographer_id',get_session_value('id'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));

         return view('adminpanel/photographer_bookings',get_defined_vars());
     }
    public function bookings_edit_form($id){
        $user=Auth::user(); 
        $bookingData=$this->orders
            ->with('photographers')
            ->with('package')
            ->where('id',$id)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
             
         return view('adminpanel/edit_booking',get_defined_vars());
     }
     public function save_booking_data(Request $request){
       
        $validator=$request->validate([
            'groom_first_name'=>'required',
            'groom_last_name'=>'required',
            'groom_email'=>'required',
            'groom_contact_number'=>'required',
            'groom_billing_address'=>'required',
            'bride_first_name'=>'required',
            'bride_last_name'=>'required',
            'bride_email'=>'required',
            'bride_contact_number'=>'required',
            'bride_billing_address'=>'required',
            'event_date_time'=>'required',
            'venue_group_id'=>'required',
            'package_id'=>'required',
            'who_is_paying'=>'required',
            'payment_source'=>'required',
            'photographer_expense'=>'required',
        ]);
        
        //p($request->all()); die;
        $this->orders->groom_first_name=$request['groom_first_name'];
        $this->orders->groom_last_name=$request['groom_last_name'];
        $this->orders->groom_email=$request['groom_email'];
        $this->orders->groom_contact_number=$request['groom_contact_number'];
        $this->orders->groom_billing_address=$request['groom_billing_address'];
        $this->orders->bride_first_name=$request['bride_first_name'];
        $this->orders->bride_last_name=$request['bride_last_name'];
        $this->orders->bride_email=$request['bride_email'];
        $this->orders->bride_contact_number=$request['bride_contact_number'];
        $this->orders->bride_billing_address=$request['bride_billing_address'];
        $this->orders->event_date_time=$request['event_date_time'];
        $this->orders->payment_source=$request['payment_source'];
        $this->orders->venue_group_id=$request['venue_group_id'];
        $this->orders->who_is_paying=$request['who_is_paying'];
        $this->orders->booking_notes=$request['booking_notes'];
        if(isset($request['customer_to_pay']) && !empty($request['customer_to_pay']))
        $this->orders->customer_to_pay=$request['customer_to_pay'];
        if(isset($request['venue_group_to_pay']) && !empty($request['venue_group_to_pay']))
        $this->orders->venue_group_to_pay=$request['venue_group_to_pay'];

        $this->orders->customer_id=$request['customer_id'];
        
        $this->orders->status=0;
        $this->orders->created_uid =get_session_value('id');
        $this->orders->created_at=time();
        
        if($request['package_id']>0 && $request['package_id']!='manual_package')
        $this->orders->package_id=$request['package_id'];
        else
        {
            $this->packages->name=$request['package_name'];
            $this->packages->price=$request['package_price'];
            $this->packages->description=$request['description'];
            $this->packages->manually_added=1;
            $this->packages->save();

            $this->orders->package_id=$this->packages->id;
        }
     
     
  
        $request->session()->flash('alert-success', 'order Added! Please Check in orders list Tab');
        $this->orders->save();
        p($request['photographer_id']);
        $k=1;
        foreach($request['photographer_id'] as $key=>$value){
            DB::table('photographer_orders')->insert([
                ['photographer_id' => $value,
                 'orders_id' => $this->orders->id,
                 'photographer_cost' => $request['photographer_expense'][$key],
                ]
            ]);
        }
        
                    // Activity Log
                    $activityComment='Mr.'.get_session_value('name').' Added new order '.$this->users->name;
                    $activityData=array(
                        'user_id'=>get_session_value('id'),
                        'action_taken_on_id'=>$this->orders->id,
                        'action_slug'=>'order_added',
                        'comments'=>$activityComment,
                        'others'=>'orders',
                        'created_at'=>date('Y-m-d H:I:s',time()),
                    );
                    $activityID=log_activity($activityData);
       return redirect()->back();
        
    }
    // List All the orders 
    public function bookings($type=NULL){
        $user=Auth::user();
        if($user->group_id== config('constants.groups.admin')){
            $bookingData=$this->orders
            ->with('customer')
            ->with('venue_group')
            ->with('photographers')
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        else if($user->group_id== config('constants.groups.customer')){
            $bookingData=$this->orders
            ->with('customer')
            ->with('venue_group')
            ->with('photographers')
            ->where('customer_id',get_session_value('id'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        else{
            $bookingData=$this->orders
            ->with('customer')
            ->with('venue_group')
            ->with('photographers')
            ->where('venue_group_id',get_session_value('id'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
     
        return view('adminpanel/bookings',get_defined_vars());
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
    public function DeletebookingssData($id){
        $dataArray['error']='No';
        $dataArray['title']='User';

        $result=$this->users->where('id','=',$id)->update(array('is_active'=>3));             
        if($result){
            $dataArray['msg']='Mr.'.get_session_value('name').', record delted successfully!';

            $activityComment='Mr.'.get_session_value('name').' moved booking to approved/pending/cancelled';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'booking_status_changed',
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
        if(isset($req['action']) && $req['action']=='changestatus'){ 
            $dataArray['title']='booking Status Updated ';
            $activityComment='Mr.'.get_session_value('name').' moved order to approved/pending/cancelled';

            if(config('constants.booking_status.pending')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-danger btn-flat btn-sm"><i class="fas fa-chart-line"></i> Pending</a>';
            $activityComment='Mr.'.get_session_value('name').' moved order to pending';
            }
            else if(config('constants.booking_status.approved')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-success btn-flat btn-sm"><i class="fas fa-chart-line"></i> Approved</a>';
            $activityComment='Mr.'.get_session_value('name').' moved order to approved';
            }
            else if(config('constants.booking_status.cancelled')==$req['status']){
            $dataArray['status_btn']='<a disabled="" class="btn bg-gradient-secondary btn-flat btn-sm"><i class="fas fa-chart-line"></i> Cancelled</a>';
            $activityComment='Mr.'.get_session_value('name').' moved order to cancelled';
            }
            $result=$this->users->where('id','=',$id)->update(array('status'=>$req['status']));             
            if($result){
                $dataArray['msg']='Mr.'.get_session_value('name').', order '.$req['alertmsg'].' successfully!';
                
                $activityData=array(
                    'user_id'=>get_session_value('id'),
                    'action_taken_on_id'=>$id,
                    'action_slug'=>'order_status_changed',
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
                'action_slug'=>'order_trashed',
                'comments'=>'Mr.'.get_session_value('name').' moved order to trash',
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
                'action_slug'=>'order_deleted',
                'comments'=>'Mr.'.get_session_value('name').' deleted order',
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }

        }
        else if(isset($req['action']) && $req['action'] =='viewBookingData'){
            $dataArray['error']='No';
            $dataArray['msg']='booking Successfully Updated';
            $dataArray['title']='bookings Panel';
            $bookingsData=$this->orders->where('id',$req['id'])->with('customer')->with('photographers')->with('venue_group')->get()->toArray();
            $bookingsData=$bookingsData[0];
            //p($bookingsData);
            $customer_to_pay=$venue_group_to_pay='';
            if(isset($bookingsData['customer_to_pay']) && !empty($bookingsData['customer_to_pay'])){
                $customer_to_pay='<div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Paid by Customer :</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['customer_to_pay'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>';
            }
            if(isset($bookingsData['venue_group_to_pay']) && !empty($bookingsData['venue_group_to_pay'])){
                $venue_group_to_pay='<div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Paid by Venue group :</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['venue_group_to_pay'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>';
            }
            
            $bookingHTML='<div style="text-align:justify" class="container">
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Groom Name :</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['groom_first_name'].' '.$bookingsData['groom_last_name'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Groom Email</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['groom_email'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Groom Contact No.</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['groom_contact_number'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Groom Billing Address</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['groom_billing_address'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>bride Name :</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['bride_first_name'].' '.$bookingsData['bride_last_name'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>bride Email</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['bride_email'].'</div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>bride Contact No.</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['bride_contact_number'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>bride Billing Address</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['bride_billing_address'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5"><strong>Event Date and Time :</strong></div>
                <div class="col-5">
                    '.$bookingsData['event_date_time'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Payment Source</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['payment_source'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Who is paying</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['who_is_paying'].'
                </div>
                <div class="col-1">&nbsp;</div>
            </div>'.$customer_to_pay.$venue_group_to_pay.'
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Customer Name.</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['customer']['name'].'
                    </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Customer Email</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['customer']['email'].'
                    </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Customer Phone</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['customer']['phone'].'
                    </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Venue Group</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['venue_group']['name'].'
                    </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Venue Group</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['venue_group']['hod_phone'].'
                    </div>
                <div class="col-1">&nbsp;</div>
            </div>
            <div class="row">
                <div class="col-1">&nbsp;</div>
                <div class="col-5">
                    <strong>Venue Group address</strong>
                </div>
                <div class="col-5">
                    '.$bookingsData['venue_group']['address'].'
                    </div>
                <div class="col-1">&nbsp;</div>
            </div>
        </div>';
            $dataArray['res']=$bookingHTML;
        }
        else if(isset($req['action']) && $req['action'] =='SaveAddtoorderForm'){
            $dataArray['error']='No';
            $dataArray['msg']='order Successfully Updated';
            $dataArray['title']='bookings Panel';
            $dataArray['actionType']='move_to_order';
            
            $bookingData=array();
            $dataArray['firstname']=$req['firstname'];
            $dataArray['lastname']=$req['lastname'];
            $dataArray['name']=$req['firstname'].' '.$req['lastname'];
            $dataArray['mobileno']=$req['mobileno'];
            $dataArray['phone']=$req['phone'];
            $dataArray['business_name']=$req['business_name'];
            $dataArray['business_address']=$req['business_address'];
            $dataArray['business_mobile']=$req['business_mobile'];
            $dataArray['business_phone']=$req['business_phone'];
            $dataArray['id']=$req['booking_id'];
            if(isset($req['othercity']) && !empty($req['othercity']))
                $cityId = getOtherCity($req['othercity']);
            else
                $cityId=$req['city'];

            $this->users->where('id', $req['booking_id'])->update(
                array(
                    'firstname'=>$req['firstname'],
                    'lastname'=>$req['lastname'],
                    'name'=>$req['firstname'].' '.$req['lastname'],
                    'mobileno'=>$req['mobileno'],
                    'phone'=>$req['phone'],
                    'business_name'=>$req['business_name'],
                    'business_address'=>$req['business_address'],
                    'business_mobile'=>$req['business_mobile'],
                    'business_mobile'=>$req['business_mobile'],
                    'group_id'=>config('constants.groups.order'),
                    'city_id'=>$cityId)
            );
            // Activity Logged
            $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['booking_id'],
                'action_slug'=>'order_updated',
                'comments'=>'Mr.'.get_session_value('name').' updated a order Mr.'.$req['firstname'].' '.$req['lastname'],
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));
            
            echo json_encode($dataArray);
            die;

        }
        else if(isset($req['action']) && $req['action'] =='SaveEditFormbooking'){
            $dataArray['error']='No';
            $dataArray['msg']='booking Successfully Updated';
            $dataArray['title']='bookings Panel';
            //$dataArray['formdata']=$req->all();

            // $this->venue_users->user_id=$req['id'];
            // $this->venue_users->venue_group_id=$req['venue_group_id'];
            // $this->venue_users->save();
    
            $this->venue_users->where('id', $req['venue_user_id'])->update(array('venue_group_id'=>$req['venue_group_id']));
            $bookingData=array();
            $dataArray['firstname']=$req['firstname'];
            $dataArray['lastname']=$req['lastname'];
            $dataArray['name']=$req['firstname'].' '.$req['lastname'];
            $dataArray['mobileno']=$req['mobileno'];
            $dataArray['phone']=$req['phone'];
            $dataArray['id']=$req['booking_id'];
            $dataArray['booking_type']=$req['booking_type'];
            
            $this->users->where('id', $req['booking_id'])->update(array(
                'firstname'=>$req['firstname'],
                'lastname'=>$req['lastname'],
                'name'=>$req['firstname'].' '.$req['lastname'],
                'mobileno'=>$req['mobileno'],
                'phone'=>$req['phone'],
                'booking_type'=>$req['booking_type'],

            ));
             // Activity Logged
             $activityID=log_activity(array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['booking_id'],
                'action_slug'=>'booking_updated',
                'comments'=>'Mr.'.get_session_value('name').' updated booking having name Mr.'.$req['firstname'].' '.$req['lastname'],
                'others'=>'users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            ));

            $bookingType=config('constants.booking_types.'.$req['booking_type']);
            $dataArray['booking_type_tile']=$bookingType['title'];
            $dataArray['venue_group_name']=$req['venue_group_name'];
            echo json_encode($dataArray);
            die;

        }
        else if(isset($req['action']) && $req['action'] =='show_photographer'){
            $dataArray['error']='No';
           
           
    $photographer_html= '<div class="row form-group">
            <div class="col-1">&nbsp;</div>
            <div class="col-5">
                <div class="input-group mb-3">
                    <select placeholder="Select Photographer" type="text" name="photographer_id[]" required class=" select2bs4 form-control">
                        '. get_photographer_options().'
                    </select>
                </div>
            </div>
            <div class="col-5">
                <div class="input-group mb-3">
                    <input placeholder="Photographer Expense" type="text" name="photographer_expense[]" required 
                        class=" form-control">
                </div>
            </div>
            <div class="col-1">&nbsp;</div>
        </div>';
            $dataArray['photographer_list']=$photographer_html;
        }
        else if(isset($req['action']) && $req['action'] =='editorderForm'){
            $dataArray['error']='No';
            
            $data=$this->users->where('id',$req['id'])->get()->toArray();
            $data=$data[0];
            $csrf_token = csrf_token();
            
$formHtml='<form id="EditorderForm"
                                                                            method="GET"
                                                                            action=""
                                                                            onsubmit="return updateorder('. $data['id'].','. $req['counter'].')">
                                                                            <input type="hidden" name="_token" value="'.$csrf_token.'" />
                                                                            <input type="hidden" name="action" value="SaveAddtoorderForm" />
                                                                            <input type="hidden" name="booking_id" value="'.$data['id'].'" />
                                                                                                                        
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
                                                                                    <input  type="text" name="business_name" class="form-control" placeholder="Business Name " value="'. $data['business_name'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="business_address" class="form-control" placeholder="Business Address" value="'. $data['business_address'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="business_mobile" class="form-control" placeholder="Business Mobile No." value="'. $data['business_mobile'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                            <div class="col-3">&nbsp;</div>
                                                                            <div class="col-6">
                                                                                <div class="input-group mb-3">
                                                                                    <input  type="text" name="business_phone" class="form-control" placeholder="Business Phone No." value="'. $data['business_phone'].'" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div class="row form-group">
                                                                                <div class="col-3">&nbsp;</div>
                                                                                <div class="col-6">
                                                                                    <div class="input-group mb-3">
                                                                                    <select id="city" onChange="changeCity()" name="city" class="form-control select2bs4" placeholder="Select Venue Group">'.getCitiesOptions($data['city_id']).'</select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-3">&nbsp;</div>
                                                                            </div>
                                                                            <div id="othercity"></div>
                                                                            
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
        }
      
        echo json_encode($dataArray);
        die;
    }

}
