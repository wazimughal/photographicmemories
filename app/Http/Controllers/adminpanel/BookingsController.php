<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\Bookings;
use App\Models\adminpanel\bookings_users;
use App\Models\adminpanel\booking_actions;
use App\Models\adminpanel\files;
use App\Models\adminpanel\comments;
use App\Models\adminpanel\invoices;
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
        $this->invoices= new invoices;
        $this->comments= new comments;
        $this->files= new files;
        $this->booking_actions= new booking_actions;


        //$this->middleware('photographerGaurd', ['only' => ['bookings']]);
        
      }

    public function pencils_form($id=NULL){  // Add pencile Form
        $user=Auth::user(); 
        return view('adminpanel/add_pencils',get_defined_vars());
        
     }

     // View Booking Bookings
     public function view_booking($id){
        $user=Auth::user(); 
        $bookingData=$this->bookings
        ->with('customer')
        ->with('photographer')
        ->with('venue_group')
        ->with('invoices')
        ->with('files')
        ->with('comments')
        ->with('deposite_requests')
        ->where('id',$id)
        ->orderBy('created_at', 'desc')->get()->toArray();
        $bookingData=$bookingData[0];

        $assigne_photographers=$this->bookings_users->with('userinfo')->where('booking_id',$id)->where('group_id',config('constants.groups.photographer'))->get()->toArray();
        

        return view('adminpanel/view_booking',get_defined_vars());
     }
    public function bookings_edit_form($id=NULL){
        if($id==NULL)
        return view('adminpanel/add_bookings',get_defined_vars());

        // $photographerArr=
        // ['user_id' => 11,
        //  'booking_id' => 2,
        //  'group_id' => config('constants.groups.photographer'),
        //  'slug' => phpslug('new_photographer_assigned'),
        //  //'photographer_expense' => $request['photographer_expense'][$key],
        // ];
        // assign_photographer_to_booking('09/22/2022',$photographerArr);
        // die;

        $user=Auth::user(); 
        $bookingData=$this->bookings
        ->with('customer')
        ->with('photographer')
        ->with('venue_group')
        ->with('invoices')
        ->with('files')
        ->with('deposite_requests')
        ->where('id',$id)
        ->orderBy('created_at', 'desc')->get()->toArray();
        $bookingData=$bookingData[0];

        $assigne_photographers=$this->bookings_users->with('userinfo')->where('booking_id',$id)->where('group_id',config('constants.groups.photographer'))->get()->toArray();
        //p($assigne_photographers); die;
         return view('adminpanel/add_to_booking',get_defined_vars());
         //return view('adminpanel/pencil_to_bookings',get_defined_vars());
     }
     public function save_pencil_data(Request $request){
        $user=Auth::user(); 
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
        $this->bookings->customer_id=$this->users->id;
        $this->bookings->is_active=1;
        $this->bookings->created_by_user=get_session_value('id');

        if($user->group_id==config('constants.groups.admin')){
            $this->bookings->pencile_by=config('constants.pencileBy.office');
            $emailData['subject']='New Pencil - Office';
        }
        
        else if($user->group_id==config('constants.groups.venue_group_hod')){
            $this->bookings->pencile_by=config('constants.pencileBy.venue_group');
            $emailData['subject']='New Pencil - Venue Group: '.get_session_value('vg_name');
        }
        


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
        
        $emailData['body_message']='<table width="100%"  style="text-align:center;">
                            <tr><td> Name :</td><td>'.$request['firstname'].' '.$request['lastname'].'</td></tr>
                            <tr><td> Email :</td><td>'.$request['email'].'</td></tr>
                            <tr><td> Phone :</td><td>'.$request['phone'].'</td></tr>
                            <tr><td> Relationship with Event :</td><td>'.$request['relation_with_event'].'</td></tr>
                            <tr><td colspan=2> <hr></td></tr>
                            <tr><td> Groom Name :</td><td>'.$request['groom_name'].'</td></tr>
                            <tr><td> Groom Home Phone :</td><td>'.$request['groom_home_phone'].'</td></tr>
                            <tr><td> Groom Mobile :</td><td>'.$request['groom_mobile'].'</td></tr>
                            <tr><td> Groom Billing Address :</td><td>'.$request['groom_billing_address'].'</td></tr>
                            <tr><td> Bride Name :</td><td>'.$request['bride_name'].'</td></tr>
                            <tr><td> Bride Home Phone :</td><td>'.$request['bride_home_phone'].'</td></tr>
                            <tr><td> Bride Mobile :</td><td>'.$request['bride_mobile'].'</td></tr>
                            <tr><td> Bride Billing Address :</td><td>'.$request['bride_billing_address'].'</td></tr>
                            <tr><td> Date of Event :</td><td>'.$request['date_of_event'].'</td></tr>
        </table>';
        $emailData['button_title']='Go to CRM';
        send_email($emailData);
        //$emailData['button_link']='';
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
        if($user->group_id== config('constants.groups.admin')){
        
        if($type=='office'){
            $pencilData=$this->bookings
            ->with('customer')
            ->with('photographer')
            ->with('venue_group')
            ->where('pencile_by',config('constants.pencileBy.office'))
            ->where('is_active',1)
            ->where('status',config('constants.booking_status.pencil'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));

        }else if($type=='venue_group'){
            $pencilData=$this->bookings
            ->with('customer')
            ->with('photographer')
            ->with('venue_group')
            ->where('pencile_by',config('constants.pencileBy.venue_group'))
            ->where('is_active',1)
            ->where('status',config('constants.booking_status.pencil'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }elseif($type=='web'){
            $pencilData=$this->bookings
            ->with('customer')
            ->with('photographer')
            ->with('venue_group')
            ->where('pencile_by',config('constants.pencileBy.website'))
            ->where('is_active',1)
            ->where('status',config('constants.booking_status.pencil'))
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }else{
            $pencilData=$this->bookings
            ->with('customer')
            ->with('photographer')
            ->with('venue_group')
            ->where('status',config('constants.booking_status.pencil'))
            ->where('is_active',1)
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
            
    }
    else{
       
        // For Venue Group Section
        $pencilData=array();
        if($type=='venue_group'){
            $pencilData=$this->bookings_users
            ->with('userinfo')
            ->with('pencils')
            ->where('user_id',get_session_value('id'))  
            //->orderBy('created_at', 'desc')->get()->toArray();
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }else{
            $pencilData=$this->bookings_users
            ->with('userinfo')
            ->with('pencils')
            ->where('user_id',get_session_value('id'))  
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        }
    
        return view('adminpanel/user_pencils',get_defined_vars());
    }

            
        return view('adminpanel/pencils',get_defined_vars());
    }
// View pencile
public function view_pencil($id){
    $user=Auth::user(); 
    $bookingData=$this->bookings
    ->with('customer')
    ->with('venue_group')
    ->where('id',$id)
    ->orderBy('created_at', 'desc')->get()->toArray();
    $bookingData=$bookingData[0];

    return view('adminpanel/view_pencil',get_defined_vars());
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
        if($user->group_id==config('constants.groups.admin')){
            if($type=='office'){
                $pencilData=$this->bookings
                ->with('customer')
                ->with('photographer')
                ->with('venue_group')
                ->where('pencile_by',config('constants.pencileBy.office'))
                ->where('is_active',1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
    
            }else if($type=='venue_group'){
                $pencilData=$this->bookings
                ->with('customer')
                ->with('photographer')
                ->with('venue_group')
                ->where('pencile_by',config('constants.pencileBy.venue_group'))
                ->where('is_active',1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }elseif($type=='trashed'){

                $pencilData=$this->bookings
                ->with('customer')
                ->with('photographer')
                ->with('venue_group')
                ->where('is_active',2)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));

            }elseif($type=='web'){
                $pencilData=$this->bookings
                ->with('customer')
                ->with('photographer')
                ->with('venue_group')
                ->where('pencile_by',config('constants.pencileBy.website'))
                ->where('is_active',1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }else{
                $pencilData=$this->bookings
                ->with('customer')
                ->with('photographer')
                ->with('venue_group')
                //->where('status',config('constants.booking_status.awaiting_for_photographer')) 
                ->where('is_active',1) 
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }

        }
        else{

            
                $pencilData=$this->bookings_users
                ->with('userinfo')
                ->with('bookings')
                ->where('user_id',get_session_value('id'))  
                //->orderBy('created_at', 'desc')->get()->toArray();
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            
                

                return view('adminpanel/user_bookings',get_defined_vars());
        }

        
        

      //  if($user->group_id== config('constants.groups.admin'))
            
        return view('adminpanel/bookings',get_defined_vars());
    }
    
    public function save_booking_invoice_data($id,Request $request){
        $validator=$request->validate([
            'payee_name'=>'required',
            //'payee_uid '=>'required',
            'paid_amount'=>'required',
        ]);
        $payee=explode('-',$request['payee_uid']);
        // p($request->all());
        // p($payee); die;
        $this->invoices->payee_name=$request['payee_name'];
        $this->invoices->payee_uid =$payee[0];
        $this->invoices->slug =$payee[1];
        $this->invoices->description=$request['description'];
        $this->invoices->paid_amount=$request['paid_amount'];
        $this->invoices->booking_id=$id;
        $this->invoices->created_by=get_session_value('id');
        $this->invoices->save();
       
        //activity Logged
        $activityComment='Mr.'.get_session_value('name').' Received Payment ';
        $activityData=array(
            'user_id'=>get_session_value('id'),
            'action_taken_on_id'=>$id,
            'action_slug'=>'new_payment',
            'comments'=>$activityComment,
            'others'=>'bookings',
            'created_at'=>date('Y-m-d H:I:s',time()),
        );
        $activityID=log_activity($activityData);


        $request->session()->flash('alert-success', 'Payment Added in System');

        return redirect()->back();
        //redirect route('bookings.save_booking_edit_data', $id);
    }

    public function save_booking_edit_data($id,Request $request){
        $validator=$request->validate([
            'date_of_event'=>'required',
            //'venue_group_id'=>'required',
            'package_id'=>'required',
            //'paying_via'=>'required',
            'package_id'=>'required',
        ]);
       
        $bookingData['date_of_event']=$request['date_of_event'];
        $bookingData['time_of_event']=$request['time_of_event'];
        $bookingData['package_id']=$request['package_id'];
        
        if(isset($request['other_venue_group']) && !empty($request['other_venue_group']))
        $bookingData['other_venue_group']=$request['other_venue_group'];

        $bookingData['who_is_paying']=$request['who_is_paying'];
        $bookingData['paying_via']=$request['paying_via'];
        $bookingData['is_active']=1;
        $bookingData['status']=config('constants.booking_status.awaiting_for_photographer'); // 1 is for waiting for photographer
        
        if(isset($request['customer_to_pay']) && !empty($request['customer_to_pay']))
        $bookingData['customer_to_pay']=$request['customer_to_pay'];
        
        if(isset($request['venue_group_to_pay']) && !empty($request['venue_group_to_pay']))
        $bookingData['venue_group_to_pay']=$request['venue_group_to_pay'];

        
        if(isset($request['extra_price']) && !empty($request['extra_price']))
        $bookingData['extra_price']=$request['extra_price'];

        if(isset($request['extra_charge_desc']) && !empty($request['extra_charge_desc']))
        $bookingData['extra_charge_desc']=$request['extra_charge_desc'];
        
        if(isset($request['overtime_hours']) && !empty($request['overtime_hours']))
        $bookingData['overtime_hours']=$request['overtime_hours'];
        
        if(isset($request['overtime_rate_per_hour']) && !empty($request['overtime_rate_per_hour']))
        $bookingData['overtime_rate_per_hour']=$request['overtime_rate_per_hour'];
     
        $bookingData['deposit_needed']=1;
        if($request['deposit_needed']=='NO')
        $bookingData['deposit_needed']=0;
        //$this->bookings->notes_by_pencil=$request['notes_by_pencil'];
        
        // Booking data updated
        $this->bookings->where('id',$id)->update($bookingData);
      

        // Venue Group Added to Booking
        echo $request['selected_venue_group_id'].'!='.$request['venue_group_id'];

        if($request['selected_venue_group_id']!=$request['venue_group_id'])
        if((isset($request['venue_group_id']) && $request['venue_group_id']>0)){
            //$this->bookings_users->where('booking_id',$id)->where('user_id',$request['venue_group_id'])->delete();
            $this->bookings_users->where('booking_id',$id)->where('group_id',config('constants.groups.venue_group_hod'))->delete();
            //echo '<br> I am in'; die;
            DB::table('bookings_users')->insert([
            ['user_id' => $request['venue_group_id'],
             'booking_id' => $id,
             'group_id' => config('constants.groups.venue_group_hod'),
             'slug' => phpslug('new_venue_group'),
            ]
        ]);

        }

        
        
    
        //  if(!empty($request['photographer_id'][0]))
        //  $this->bookings_users->where('booking_id',$id)->where('status',0)->where('group_id',config('constants.groups.photographer'))->delete();

        foreach($request['photographer_id'] as $key=>$value){
            if(empty($value))
            continue;
            $photographerArr=
                ['user_id' => $value,
                 'booking_id' => $id,
                 'group_id' => config('constants.groups.photographer'),
                 'slug' => phpslug('new_photographer_assigned'),
                 'photographer_expense' => $request['photographer_expense'][$key],
                ];
                $retData=assign_photographer_to_booking($request['date_of_event'],$photographerArr);

                if(!$retData['result'])
                $request->session()->flash('alert-warning', $retData['msg']);
            continue;
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
        $activityComment='Mr.'.get_session_value('name').' Added new Booking of date '.$bookingData['date_of_event'];
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
// Upload new documents
public function upload_documents($id,Request $request){
    $user=Auth::user();
        $image = $request->file('file');
        $imageExt=$image->extension();
        $imageName = time().'.'.$imageExt;

 

        $image->move(public_path('uploads'),$imageName);
        $orginalImageName=$image->getClientOriginalName();
    
    //return response()->json(['success'=>$imageName]);

        $this->files->name=$orginalImageName;
        $this->files->slug=phpslug($imageName);
        $this->files->path=url('uploads').'/'.$imageName;
        $this->files->description=$orginalImageName.' file uploaded';
        $this->files->otherinfo=$imageExt;
        $this->files->booking_id=$id;
        $this->files->uploaded_by=get_session_value('id');
        $this->files->save();
    //             ->update($data);
    // $this->files->where('id', $id)
    //             ->update($data);

                // Activity Log
                $activityComment='Mr.'.get_session_value('name').' uploaded documents for booking';
                $activityData=array(
                    'user_id'=>get_session_value('id'),
                    'action_taken_on_id'=>$id,
                    'action_slug'=>'booking_documents_added',
                    'comments'=>$activityComment,
                    'others'=>'files',
                    'created_at'=>date('Y-m-d H:I:s',time()),
                );
                $activityID=log_activity($activityData);

    return response()->json(['success'=>$imageName]);

    
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
        
        if(isset($req['action']) && $req['action']=='submit_comment'){ 
            
            // p($req->all()); die;

            $this->comments->comment=$req['data']['comment'];
            $this->comments->user_id=get_session_value('id');
            $this->comments->group_id =$req['data']['group_id'];
            $this->comments->slug =$req['data']['slug'];
            $this->comments->booking_id =$id;
            $this->comments->status =1;
            $this->comments->save();
            $dataArray['error']='No';
            $dataArray['to_replace']='submit_comment_replace';
            $htmlRes=' <div class="row border">
                            <div class="col-12">
                                <strong>'.get_session_value('name').' ('.$req['data']['slug'].') </strong> '.date('d/m/Y H:i:s',time()).'<br>
                                '.$req['data']['comment'].'
                            </div>
                        </div>';
            $dataArray['response']=$htmlRes;
            $dataArray['msg']='Mr.'.get_session_value('name').', Commented successfully!';
            $activityComment='Mr.'.get_session_value('name').', added comment!';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'comment_added',
                'comments'=>$activityComment,
                'others'=>'booking_actions',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
        else if(isset($req['action']) && $req['action']=='change_photographer_status'){
            
            $data_to_update=array();
            $data_to_update['status']=$req['status'];
            
            $dataArray['msg']='Mr.'.get_session_value('name').', changed photographer status!';
            $activityComment='Mr.'.get_session_value('name').', changed photographer status';
            
            if($req['status']==4){
                $this->bookings_users->where('id',$req['id'])->delete();
                $dataArray['msg']='Mr.'.get_session_value('name').', removed photographer from booking!';
                $activityComment='Mr.'.get_session_value('name').', removed photographer status';
            
            }
            else{
                $data['booking_id']=$req['booking_id'];
                $data['user_id']=$req['user_id'];
                $data['status']=$req['status'];
                $this->bookings_users->where('id',$req['id'])->update(array('status'=>$req['status']));
              
                $this->bookings->where('id',$req['booking_id'])->update(array('status'=>get_booking_status($req['booking_id'])));
                
                
            }


            $dataArray['error']='No';
            $dataArray['status']=$req['status'];
            $dataArray['id']=$req['id'];
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['booking_id'],
                'action_slug'=>'photographer_status_changed',
                'comments'=>$activityComment,
                'others'=>'bookings_users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
            
        }
        else if(isset($req['action']) && $req['action']=='restor_booking'){
            $data_to_update=array();
            $data_to_update['is_active']=1;
            $this->bookings->where('id',$id)->update($data_to_update);
            $dataArray['error']='No';
            
            $dataArray['msg']='Mr.'.get_session_value('name').', restored Booking!';
            $activityComment='Mr.'.get_session_value('name').', restored Booking';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'restored_booking',
                'comments'=>$activityComment,
                'others'=>'booking_actions',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
        else if(isset($req['action']) && $req['action']=='trash_booking'){
            $data_to_update=array();
            $data_to_update['is_active']=2;
            $this->bookings->where('id',$id)->update($data_to_update);
            $dataArray['error']='No';
            
            $dataArray['msg']='Mr.'.get_session_value('name').', moved booking to Trash!';
            $activityComment='Mr.'.get_session_value('name').', moved booking to Trash';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'moved_booking_to_trash',
                'comments'=>$activityComment,
                'others'=>'booking_actions',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
         }
        else if(isset($req['action']) && $req['action']=='delete_pencil'){
            $data_to_update=array();
            $data_to_update['is_active']=3; // is_active 3 is for deleted bookings
            $this->bookings->where('id',$id)->update($data_to_update);
            $dataArray['error']='No';

            $dataArray['id']=$id;
            $dataArray['msg']='Mr.'.get_session_value('name').', delted the pencil!';
            $activityComment='Mr.'.get_session_value('name').', delted the pencile';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'pencil_delete',
                'comments'=>$activityComment,
                'others'=>'bookings',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
         }
        else if(isset($req['action']) && $req['action']=='delete_booking'){
            $data_to_update=array();
            $data_to_update['is_active']=3; // is_active 3 is for deleted bookings
            $this->bookings->where('id',$id)->update($data_to_update);
            $dataArray['error']='No';

            $dataArray['id']=$id;
            $dataArray['msg']='Mr.'.get_session_value('name').', delted the booking!';
            $activityComment='Mr.'.get_session_value('name').', delted the booking';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'booking_delete',
                'comments'=>$activityComment,
                'others'=>'bookings',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
         }

        else if(isset($req['action']) && $req['action']=='bride_update'){ 
            
            $data_to_update=array();
            $data_to_update['bride_billing_address']=$req['data']['bride_billing_address'];
            $this->bookings->where('id',$id)->update($data_to_update);
            $dataArray['error']='No';
            $dataArray[$req['action'].'_replace']=$req['data']['bride_billing_address'];
            $dataArray['msg']='Mr.'.get_session_value('name').', updated bride Billing Address!';
            $activityComment='Mr.'.get_session_value('name').', updated bride Billing Address!';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'bride_billing_address_updated',
                'comments'=>$activityComment,
                'others'=>'booking_actions',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
        elseif(isset($req['action']) && $req['action']=='groom_update'){ 
            
            $data_to_update=array();
            $data_to_update['groom_billing_address']=$req['data']['groom_billing_address'];
            $this->bookings->where('id',$id)->update($data_to_update);
            $dataArray['error']='No';
            $dataArray[$req['action'].'_replace']=$req['data']['groom_billing_address'];
            $dataArray['msg']='Mr.'.get_session_value('name').', updated Groom Billing Address!';
            $activityComment='Mr.'.get_session_value('name').', updated Groom Billing Address!';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'groom_billing_address_updated',
                'comments'=>$activityComment,
                'others'=>'booking_actions',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
        elseif(isset($req['action']) && $req['action']=='customer_update'){ 
            
            $userData=array();
            $userData['password']=Hash::make($req['data']['password']);
            $userData['is_active']=1;
            $this->users->where('id',$req['data']['uid'])->update($userData);
            $dataArray['error']='No';
            $dataArray['msg']='Successfuly updated';
            $dataArray['msg']='Mr.'.get_session_value('name').', updated customer password!';
            $activityComment='Mr.'.get_session_value('name').', updated customer password!';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['data']['uid'],
                'action_slug'=>'customer_password_updated',
                'comments'=>$activityComment,
                'others'=>'booking_actions',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
        elseif(isset($req['action']) && $req['action']=='askfordeposit'){ 
            
            $this->booking_actions->title='Please submit Payment';
            $this->booking_actions->amount=$req['data']['deposit_needed'];
            $this->booking_actions->slug='deposit_needed';
            $this->booking_actions->user_id=get_session_value('id');
            $this->booking_actions->booking_id=$id;
            $this->booking_actions->save();

            $dataArray['error']='No';
            $dataArray['msg']='Successfuly submitted';
            $dataArray['msg']='Mr.'.get_session_value('name').', Asked for Payment to Customer/Venue Group!';
            $activityComment='Mr.'.get_session_value('name').', Asked for Payment to Customer/Venue Group!';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'asked_payment',
                'comments'=>$activityComment,
                'others'=>'booking_actions',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
         else if(isset($req['action']) && $req['action']=='delteFile'){ 
            $dataArray['title']='ٖFile deleted';
            $fileData=$this->files->where('id','=',$id)->get()->toArray();
            if($fileData){
                $fileData=$fileData[0];
              $filePath=public_path('uploads').'/'.$fileData['slug'];
              
                unlink($filePath);
                
           
                $file=$this->files->where('id', $id)->delete();
                $dataArray['msg']='Mr.'.get_session_value('name').', deleted  '.$fileData['name'].' successfully!';
                $activityComment=$fileData['name'].' File delted ';
                $activityData=array(
                    'user_id'=>get_session_value('id'),
                    'action_taken_on_id'=>$id,
                    'action_slug'=>'file_deleted',
                    'comments'=>$activityComment,
                    'others'=>'files',
                    'created_at'=>date('Y-m-d H:I:s',time()),
                );
                $activityID=log_activity($activityData);
                $dataArray['error']='No';
            }
            
            else{
                $dataArray['error']='Yes';
                $dataArray['msg']='There is some error ! Please fill all the required fields.';
            }
            
        }
        else if(isset($req['action']) && $req['action'] =='show_photographer'){
            $dataArray['error']='No';
            
           
    $photographer_html= '<div class="row form-group">
            
            <div class="col-6">
                <div class="input-group mb-3">
                    <select placeholder="Select Photographer" type="text" name="photographer_id[]" required class=" select2bs4 form-control">
                        '. get_photographer_options().'
                    </select>
                </div>
            </div>
            <div class="col-6">
                <div class="input-group mb-3">
                    <input placeholder="Photographer Expense" type="text" name="photographer_expense[]"  
                        class=" form-control">
                </div>
            </div>
            
        </div>';
            $dataArray['photographer_list']=$photographer_html;
        }
        echo json_encode($dataArray);
        die;
    }
}
