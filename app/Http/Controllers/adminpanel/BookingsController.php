<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\Bookings;
use App\Models\adminpanel\bookings_users;
use App\Models\adminpanel\PhotographicPackages;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use DB;

class BookingsController extends Controller
{
  
    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->bookings= new Bookings;
        $this->packages= new PhotographicPackages;
        $this->bookings_users= new bookings_users;
        //$this->middleware('photographerGaurd', ['only' => ['bookings']]);
        
      }

    public function pencils_form($id=NULL){  // Add pencile Form
        $user=Auth::user(); 
        return view('adminpanel/add_pencils',get_defined_vars());
        
     }
    public function addnew($id=NULL){  // Add pencile Form
        $user=Auth::user(); 
        $id=1;
        $bookingData=$this->bookings
        ->with('customer')
        ->with('photographer')
        ->with('venue_group')
        ->where('id',$id)
        ->orderBy('created_at', 'desc')->get()->toArray();
        $bookingData=$bookingData[0];
        return view('adminpanel/addnew',get_defined_vars());
        
     }
    public function bookings_edit_form($id=NULL){
        if($id==NULL)
        return view('adminpanel/add_bookings',get_defined_vars());

        $user=Auth::user(); 
        $bookingData=$this->bookings
        ->with('customer')
        ->with('photographer')
        ->with('venue_group')
        ->where('id',$id)
        ->orderBy('created_at', 'desc')->get()->toArray();
        $bookingData=$bookingData[0];
        //p($bookingData);
         return view('adminpanel/pencil_to_bookings',get_defined_vars());
     }
     public function save_pencil_data(Request $request){
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            'phone'=>'required',
            'relation_with_event'=>'required',
            'preferred_photographer_id'=>'required',
            'groom_name'=>'required',
            'groom_home_phone'=>'required',
            'groom_mobile'=>'required',
            'groom_email'=>'required|email|distinct|unique:bookings|min:5',
            'groom_billing_address'=>'required',
            'bride_name'=>'required',
            'bride_home_phone'=>'required',
            'bride_mobile'=>'required',
            'bride_email'=>'required|email|distinct|unique:bookings|min:5',
            'bride_billing_address'=>'required',
            'date_of_event'=>'required',
            'venue_group_id'=>'required'
        ]);
        //p($request->all()); die;
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->phone=$request['phone'];
        $this->users->relation_with_event=$request['relation_with_event'];
        $this->users->group_id=config('constants.groups.customer');
        $this->users->save();

        //$this->bookings->preferred_photographer_id=$request['preferred_photographer_id'];
        $this->bookings->groom_name=$request['groom_name'];
        $this->bookings->groom_home_phone=$request['groom_home_phone'];
        $this->bookings->groom_mobile=$request['groom_mobile'];
        $this->bookings->groom_email=$request['groom_email'];
        $this->bookings->groom_billing_address=$request['groom_billing_address'];
        $this->bookings->bride_name=$request['bride_name'];
        $this->bookings->bride_home_phone=$request['bride_home_phone'];
        $this->bookings->bride_mobile=$request['bride_mobile'];
        $this->bookings->bride_email=$request['bride_email'];
        $this->bookings->bride_billing_address=$request['bride_billing_address'];
        $this->bookings->date_of_event=$request['date_of_event'];
        $this->bookings->created_by_user=get_session_value('id');
        $this->bookings->pencile_by=config('constants.pencileBy.office');


        if($request['preferred_photographer_id']!='No'){
            $this->bookings->preferred_photographer_id=$request['preferred_photographer_id'];
        }
        
       
        if(isset($request['other_venue_group']) && !empty($request['other_venue_group']))
            $this->bookings->other_venue_group=$request['other_venue_group'];

        $this->bookings->notes_by_pencil=$request['notes_by_pencil'];
        
        // Add new Pencil
        $this->bookings->save();
        
        // Customer Added to Pencil
        DB::table('bookings_users')->insert([
            ['user_id' => $this->users->id,
             'booking_id' => $this->bookings->id,
             'group_id' => $this->users->group_id,
             'slug' => phpslug('new_customer'),
            ]
        ]);

        // Venue Group Added to Pencil
        if((isset($request['venue_group_id']) && $request['venue_group_id']>0) && !isset($request['other_venue_group']) || empty($request['other_venue_group'])){
        DB::table('bookings_users')->insert([
            ['user_id' => $request['venue_group_id'],
             'booking_id' => $this->bookings->id,
             'group_id' => config('constants.groups.venue_group_hod'),
             'slug' => phpslug('new_venue_group'),
            ]
        ]);

        }
        
        // Preffered Photographer added to Pencil
        // if($request['preferred_photographer_id']!='No'){
        //     DB::table('bookings_users')->insert([
        //         ['user_id' => $request['preferred_photographer_id'],
        //          'booking_id' => $this->bookings->id,
        //          'group_id' => config('constants.groups.photographer'),
        //          'slug' => phpslug('preffered_photographer'),
        //         ]
        //     ]);
        // }
      
        //activity Logged
        $activityComment='Mr.'.get_session_value('name').' Added new Pencil Customer'.$this->users->name;
        $activityData=array(
            'user_id'=>get_session_value('id'),
            'action_taken_on_id'=>$this->bookings->id,
            'action_slug'=>'new_pencil_added',
            'comments'=>$activityComment,
            'others'=>'bookings',
            'created_at'=>date('Y-m-d H:I:s',time()),
        );
        $activityID=log_activity($activityData);


        $request->session()->flash('alert-success', 'New Pencil added Successfully, Please check in Pencils');
        
        return redirect()->back();



     }
     // List All the Pencils 
    public function pencils($type=NULL){
        $user=Auth::user();
        if($type=='office'){
            $pencilData=$this->bookings
            ->with('customer')
            ->with('photographer')
            ->with('venue_group')
            ->where('pencile_by',config('constants.pencileBy.office'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));

        }else if($type=='venue_group'){
            $pencilData=$this->bookings
            ->with('customer')
            ->with('photographer')
            ->with('venue_group')
            ->where('pencile_by',config('constants.pencileBy.venue_group'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }elseif($type=='web'){
            $pencilData=$this->bookings
            ->with('customer')
            ->with('photographer')
            ->with('venue_group')
            ->where('pencile_by',config('constants.pencileBy.website'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }else{
            $pencilData=$this->bookings
            ->with('customer')
            ->with('photographer')
            ->with('venue_group')
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        

      //  if($user->group_id== config('constants.groups.admin'))
            
        return view('adminpanel/pencils',get_defined_vars());
    }

    public function pencils_edit_form($id){
        $user=Auth::user(); 
        $pencilData=$this->bookings
            ->with('customer')
            ->with('photographer')
            ->with('venue_group')
            ->where('id',$id)
            ->orderBy('created_at', 'desc')->get()->toArray();
            $pencilData=$pencilData[0];
            //p($pencilData); die;
        return view('adminpanel/edit_pencils',get_defined_vars());
    }

    // Update Pencil
    public function save_pencil_edit_data($id,Request $request){
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'email'=>'required|email|distinct|unique:users|min:5',
            'phone'=>'required',
            'relation_with_event'=>'required',
            'preferred_photographer_id'=>'required',
            'groom_name'=>'required',
            'groom_home_phone'=>'required',
            'groom_mobile'=>'required',
            'groom_email'=>'required|email|distinct|unique:bookings|min:5',
            'groom_billing_address'=>'required',
            'bride_name'=>'required',
            'bride_home_phone'=>'required',
            'bride_mobile'=>'required',
            'bride_email'=>'required|email|distinct|unique:bookings|min:5',
            'bride_billing_address'=>'required',
            'date_of_event'=>'required',
            'venue_group_id'=>'required'
        ]);
        //p($request->all()); die;
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->email=$request['email'];
        $this->users->phone=$request['phone'];
        $this->users->relation_with_event=$request['relation_with_event'];
        $this->users->group_id=config('constants.groups.customer');
        $this->users->where('id', $request['customer_id'])->limit(1)->update();

        //$this->bookings->preferred_photographer_id=$request['preferred_photographer_id'];
        $this->bookings->groom_name=$request['groom_name'];
        $this->bookings->groom_home_phone=$request['groom_home_phone'];
        $this->bookings->groom_mobile=$request['groom_mobile'];
        $this->bookings->groom_email=$request['groom_email'];
        $this->bookings->groom_billing_address=$request['groom_billing_address'];
        $this->bookings->bride_name=$request['bride_name'];
        $this->bookings->bride_home_phone=$request['bride_home_phone'];
        $this->bookings->bride_mobile=$request['bride_mobile'];
        $this->bookings->bride_email=$request['bride_email'];
        $this->bookings->bride_billing_address=$request['bride_billing_address'];
        $this->bookings->date_of_event=$request['date_of_event'];
        $this->bookings->created_by_user=get_session_value('id');
        $this->bookings->pencile_by=config('constants.pencileBy.office');


        if(isset($request['preferred_photographer_id']) && $request['preferred_photographer_id']>0)
        $this->bookings->preferred_photographer_id=$request['preferred_photographer_id'];
       
        if(isset($request['other_venue_group']) && !empty($request['other_venue_group']))
            $this->bookings->other_venue_group=$request['other_venue_group'];

        $this->bookings->notes_by_pencil=$request['notes_by_pencil'];
        
        // Add new Pencil
        //$this->bookings->save();
        $this->bookings->where('id', $id)->limit(1)->update();
        
       

        // Venue Group Added to Pencil
        //if(isset($request['selected_venue_group_id']) && $request['venue_group_id'])

        if((isset($request['venue_group_id']) && $request['venue_group_id']>0) && !isset($request['other_venue_group']) || empty($request['other_venue_group'])){
        DB::table('bookings_users')->insert([
            ['user_id' => $request['venue_group_id'],
             'booking_id' => $this->bookings->id,
             'group_id' => config('constants.groups.venue_group_hod'),
             'slug' => phpslug('new_venue_group'),
            ]
        ]);

        }
        
        // Preffered Photographer added to Pencil
        if(isset($request['preferred_photographer_id']) && $request['preferred_photographer_id']>0){
            DB::table('bookings_users')->insert([
                ['user_id' => $request['preferred_photographer_id'],
                 'booking_id' => $this->bookings->id,
                 'group_id' => config('constants.groups.photographer'),
                 'slug' => phpslug('preffered_photographer'),
                ]
            ]);
        }
      
        //activity Logged
        $activityComment='Mr.'.get_session_value('name').' Added new Pencil Customer'.$this->users->name;
        $activityData=array(
            'user_id'=>get_session_value('id'),
            'action_taken_on_id'=>$this->bookings->id,
            'action_slug'=>'new_pencil_added',
            'comments'=>$activityComment,
            'others'=>'bookings',
            'created_at'=>date('Y-m-d H:I:s',time()),
        );
        $activityID=log_activity($activityData);


        $request->session()->flash('alert-success', 'New Pencil added Successfully, Please check in Pencils');
        
        return redirect()->back();



     }

     // Booking Section
      // List All the Pencils 
    public function bookings($type=NULL){
        $user=Auth::user();
        if($type=='office'){
            $pencilData=$this->bookings
            ->with('customer')
            ->with('photographer')
            ->with('venue_group')
            ->where('pencile_by',config('constants.pencileBy.office'))
            ->where('status',config('constants.booking_status.awaiting_for_photographer'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));

        }else if($type=='venue_group'){
            $pencilData=$this->bookings
            ->with('customer')
            ->with('photographer')
            ->with('venue_group')
            ->where('pencile_by',config('constants.pencileBy.venue_group'))
            ->where('status',config('constants.booking_status.awaiting_for_photographer'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }elseif($type=='web'){
            $pencilData=$this->bookings
            ->with('customer')
            ->with('photographer')
            ->with('venue_group')
            ->where('pencile_by',config('constants.pencileBy.website'))
            ->where('status',config('constants.booking_status.awaiting_for_photographer'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }else{
            $pencilData=$this->bookings
            ->with('customer')
            ->with('photographer')
            ->with('venue_group')
            ->where('status',config('constants.booking_status.awaiting_for_photographer'))  
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
        

      //  if($user->group_id== config('constants.groups.admin'))
            
        return view('adminpanel/bookings',get_defined_vars());
    }
    public function save_booking_edit_data($id,Request $request){
        $validator=$request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'phone'=>'required',
            'relation_with_event'=>'required',
            'groom_name'=>'required',
            'groom_home_phone'=>'required',
            'groom_mobile'=>'required',
            //'groom_email'=>'required|email|distinct|unique:bookings|min:5',
            'groom_billing_address'=>'required',
            'bride_name'=>'required',
            'bride_home_phone'=>'required',
            'bride_mobile'=>'required',
            //'bride_email'=>'required|email|distinct|unique:bookings|min:5',
            'bride_billing_address'=>'required',
            'date_of_event'=>'required',
            'venue_group_id'=>'required'
        ]);
       
        $dataArray['firstname']=$request['firstname'];
        $dataArray['lastname']=$request['lastname'];
        $dataArray['name']=$request['firstname'].' '.$request['lastname'];
        $dataArray['phone']=$request['phone'];
        $dataArray['relation_with_event']=$request['relation_with_event'];
        
        $this->users->where('id',$request['customer_id'])->update($dataArray);
        
        //$this->bookings->preferred_photographer_id=$request['preferred_photographer_id'];
        $bookingData['groom_name']=$request['groom_name'];
        $bookingData['groom_home_phone']=$request['groom_home_phone'];
        $bookingData['groom_mobile']=$request['groom_mobile'];
        $bookingData['groom_email']=$request['groom_email'];
        $bookingData['groom_billing_address']=$request['groom_billing_address'];
        $bookingData['bride_name']=$request['bride_name'];
        $bookingData['bride_home_phone']=$request['bride_home_phone'];
        $bookingData['bride_mobile']=$request['bride_mobile'];
        $bookingData['bride_email']=$request['bride_email'];
        $bookingData['bride_billing_address']=$request['bride_billing_address'];
        $bookingData['date_of_event']=$request['date_of_event'];
        //$bookingData['created_by_user']=get_session_value('id');
        //$bookingData['pencile_by']=config('constants.pencileBy.office');

       
        if(isset($request['other_venue_group']) && !empty($request['other_venue_group']))
        $bookingData['other_venue_group']=$request['other_venue_group'];

        $bookingData['who_is_paying']=$request['who_is_paying'];
        $bookingData['status']=config('constants.booking_status.awaiting_for_photographer'); // 1 is for waiting for photographer
        if(isset($request['customer_to_pay']) && !empty($request['customer_to_pay']))
        $bookingData['customer_to_pay']=$request['customer_to_pay'];
        if(isset($request['venue_group_to_pay']) && !empty($request['venue_group_to_pay']))
        $bookingData['venue_group_to_pay']=$request['venue_group_to_pay'];

        if(isset($request['title_for_extra_price']) && !empty($request['title_for_extra_price']))
        $bookingData['title_for_extra_price']=$request['title_for_extra_price'];

        if(isset($request['extra_price']) && !empty($request['extra_price']))
        $bookingData['extra_price']=$request['extra_price'];

        if(isset($request['extra_charge_desc']) && !empty($request['extra_charge_desc']))
        $bookingData['extra_charge_desc']=$request['extra_charge_desc'];
        //$this->bookings->notes_by_pencil=$request['notes_by_pencil'];
        
        // Booking data updated
        $this->bookings->where('id',$id)->update($bookingData);
      

        // Venue Group Added to Pencil
        if($request['selected_venue_group_id']!=$request['venue_group_id'])
        if((isset($request['venue_group_id']) && $request['venue_group_id']>0) && !isset($request['other_venue_group']) || empty($request['other_venue_group'])){
            $this->bookings_users->where('booking_id',$id)->where('user_id',$request['venue_group_id'])->limit(1)->delete();
            DB::table('bookings_users')->insert([
            ['user_id' => $request['venue_group_id'],
             'booking_id' => $id,
             'group_id' => config('constants.groups.venue_group_hod'),
             'slug' => phpslug('new_venue_group'),
            ]
        ]);

        }

        foreach($request['photographer_id'] as $key=>$value){
            DB::table('bookings_users')->insert([
                ['user_id' => $value,
                 'booking_id' => $id,
                 'group_id' => config('constants.groups.photographer'),
                 'slug' => phpslug('new_photographer_assigned'),
                 'photographer_expense' => $request['photographer_expense'][$key],
                ]
            ]);
        }
        
     
      
        //activity Logged
        $activityComment='Mr.'.get_session_value('name').' Added new Booking by '.$dataArray['name'];
        $activityData=array(
            'user_id'=>get_session_value('id'),
            'action_taken_on_id'=>$id,
            'action_slug'=>'new_booking_added',
            'comments'=>$activityComment,
            'others'=>'bookings',
            'created_at'=>date('Y-m-d H:I:s',time()),
        );
        $activityID=log_activity($activityData);


        $request->session()->flash('alert-success', 'New Booking added Successfully, Please check in Booking');
        
        return redirect()->back();



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
                    <input placeholder="Photographer Expense" type="text" name="photographer_expense[]"  
                        class=" form-control">
                </div>
            </div>
            <div class="col-1">&nbsp;</div>
        </div>';
            $dataArray['photographer_list']=$photographer_html;
        }
        echo json_encode($dataArray);
        die;
    }
}
