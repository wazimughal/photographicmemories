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

// Used for Email Section
use App\Mail\EmailTemplate;
use App\Mail\BookingEmailTemplate;
use Illuminate\Support\Facades\Mail;

use DB;
use PDF;

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
      public function emailtemplate($id=NULL){

        $user=Auth::user(); 
$id=1;
        $bookingData=$this->bookings
        ->with('customer')
        ->with('photographer')
        ->with('venue_group')
        ->with('invoices')
        ->with('files')
        ->with('deposite_requests')
        ->where('id',$id)
        ->orderBy('created_at', 'desc')->get()->toArray();
        $mailData=$bookingData[0];
        

        $assigne_photographers=$this->bookings_users->with('userinfo')->where('booking_id',$id)->where('group_id',config('constants.groups.photographer'))->get()->toArray();
        $mailData['assigned_photographer']=$assigne_photographers;
        $emailAdd=[
            'to.wazim@gmail.com',
            'waximarshad@outlook.com',
        ];
        if(Mail::to($emailAdd)->send(new BookingEmailTemplate($mailData))){
            //$request->session()->flash('alert-success', 'Please check your email');
        }

        return view('emails.booking_email_template',get_defined_vars());
       }

    public function pencils_form($id=NULL){  // Add pencile Form
        $user=Auth::user(); 

        // $request['lastname']='Arshad';
        // $request['firstname']='Wasim';
        // $mailData['body_message']='<table width="100%"  style="text-align:center;">
        //                     <tr><td> Name :</td><td>'.$request['firstname'].' '.$request['lastname'].'</td></tr>
        // </table>';
        // $mailData['button_title']='Go to CRM';
        // $mailData['subject']='subjects of the email';
        // $mailData['button_link']='https://www.google.com';
        // return view('emails.email_template', get_defined_vars());
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
     // View invoice Bookings
     public function invoice_booking($id, Request $req){
        $current_uri = request()->segments();
        if($current_uri[3]=='customer'){
            $invoice_of='customer_invoices';
            $title='Customer';
        }elseif($current_uri[3]=='venue'){
            $invoice_of='venue_invoices';
            $title='Venue';
        }else{
            $invoice_of='invoices';
            $title='complete';
        }
        

      
        
        $user=Auth::user(); 
        $bookingData=$this->bookings
        ->with('customer')
        ->with('photographer')
        ->with('venue_group')
        ->with($invoice_of)
        ->with('files')
        ->with('comments')
        ->with('deposite_requests')
        ->where('id',$id)
        ->orderBy('created_at', 'desc')->get()->toArray();
        $bookingData=$bookingData[0];

        //$assigne_photographers=$this->bookings_users->with('userinfo')->where('booking_id',$id)->where('group_id',config('constants.groups.photographer'))->get()->toArray();
        // if(!isset($req['deb']) && $req['deb']!=1)
        // return view('adminpanel/invoice_booking',get_defined_vars());
        // return view('adminpanel/pdf',get_defined_vars());
        // For Creating PDF:
// share data to view
        $fileName=$invoice_of.'-'.date('d/m/Y h:i:s',time());
        view()->share('adminpanel/pdf',get_defined_vars());

        $PDFOptions = ['defaultFont' => 'sans-serif'];

        $pdf = PDF::loadView('adminpanel/pdf', get_defined_vars())->setOptions($PDFOptions);
        $pdf->getDomPDF()->setHttpContext(
            stream_context_create([
                'ssl' => [
                    'allow_self_signed'=> TRUE,
                    'verify_peer' => FALSE,
                    'verify_peer_name' => FALSE,
                ]
            ])
        );


        //activity Logged
        $activityComment='Mr.'.get_session_value('name').' downloaded '.$title.' invoice';
        $activityData=array(
            'user_id'=>get_session_value('id'),
            'action_taken_on_id'=>$id,
            'action_slug'=>'downloaded_invoice',
            'comments'=>$activityComment,
            'others'=>'bookings',
            'created_at'=>date('Y-m-d H:I:s',time()),
        );
        $activityID=log_activity($activityData);

            // download PDF file with download method
            return $pdf->download($fileName.'.pdf');

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
            'password'=>'required',
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
        $this->users->password=Hash::make($request['password']);
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
        $this->bookings->date_of_event=strtotime($request['date_of_event']);
        $this->bookings->customer_id=$this->users->id;
        $this->bookings->is_active=1;
        $this->bookings->created_by_user=get_session_value('id');

        if($user->group_id==config('constants.groups.admin')){
            $this->bookings->pencile_by=config('constants.pencileBy.office');
            $mailData['subject']='New Pencil - Office';
        }
        
        else if($user->group_id==config('constants.groups.venue_group_hod')){
            $this->bookings->pencile_by=config('constants.pencileBy.venue_group');
            $mailData['subject']='New Pencil - Venue Group: '.get_session_value('vg_name');
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
        
        $mailData['body_message']='<table width="100%"  style="text-align:center;">
                            <tr><td> Name :</td><td>'.$request['firstname'].' '.$request['lastname'].'</td></tr>
                            <tr><td> Email :</td><td>'.$request['email'].'</td></tr>
                            <tr><td> Password :</td><td><strong>'.$request['password'].'</strong></td></tr>
                            <tr><td> Phone :</td><td>'.$request['phone'].'</td></tr>
                            <tr><td> Relationship with Event :</td><td>'.relation_with_event($request['relation_with_event']).'</td></tr>
                            <tr><td colspan=2> <hr></td></tr>
                            <tr><td> Groom Name :</td><td>'.$request['groom_name'].'</td></tr>
                            <tr><td> Groom Home Phone :</td><td>'.$request['groom_home_phone'].'</td></tr>
                            <tr><td> Groom Mobile :</td><td>'.$request['groom_mobile'].'</td></tr>
                            <tr><td> Groom Billing Address :</td><td>'.$request['groom_billing_address'].'</td></tr>
                            <tr><td> Bride Name :</td><td>'.$request['bride_name'].'</td></tr>
                            <tr><td> Bride Home Phone :</td><td>'.$request['bride_home_phone'].'</td></tr>
                            <tr><td> Bride Mobile :</td><td>'.$request['bride_mobile'].'</td></tr>
                            <tr><td> Bride Billing Address :</td><td>'.$request['bride_billing_address'].'</td></tr>
                            <tr><td> Date of Event :</td><td>'.date('d/m/Y',strtotime($request['date_of_event'])).'</td></tr>
        </table>';
        $mailData['button_title']='Go to CRM';
        
        
            $emailAdd=[
                config('constants.admin_email'),
                $request['email']
                    ];
        if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
            $request->session()->flash('alert-success', 'Please check your email');
        }

        //$mailData['button_link']='';
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
        $this->bookings->date_of_event=strtotime($request['date_of_event']);
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
                ->where('status','>', config('constants.booking_status.pencil') )
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
    
            }else if($type=='venue_group'){
                $pencilData=$this->bookings
                ->with('customer')
                ->with('photographer')
                ->with('venue_group')
                ->where('pencile_by',config('constants.pencileBy.venue_group'))
                ->where('status','>', config('constants.booking_status.pencil') )
                ->where('is_active',1)
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }elseif($type=='trashed'){

                $pencilData=$this->bookings
                ->with('customer')
                ->with('photographer')
                ->with('venue_group')
                ->where('is_active',2)
                ->where('status','>', config('constants.booking_status.pencil') )
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));

            }elseif($type=='web'){
                $pencilData=$this->bookings
                ->with('customer')
                ->with('photographer')
                ->with('venue_group')
                ->where('pencile_by',config('constants.pencileBy.website'))
                ->where('is_active',1)
                ->where('status','>', config('constants.booking_status.pencil') )
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            }else{
                $pencilData=$this->bookings
                ->with('customer')
                ->with('photographer')
                ->with('venue_group')
                ->where('status','>', config('constants.booking_status.pencil') )
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
                //->where('status','>', config('constants.booking_status.pencil') )
                //->orderBy('created_at', 'desc')->get()->toArray();
                ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            
                

                return view('adminpanel/user_bookings',get_defined_vars());
        }

        
        

      //  if($user->group_id== config('constants.groups.admin'))
            
        return view('adminpanel/bookings',get_defined_vars());
    }
    public function calender_schedule(){
        $user=Auth::user();
        if($user->group_id==config('constants.groups.admin')){
          
                $bookingData=$this->bookings
                ->with('customer')
                ->with('photographer')
                ->with('venue_group')
//                ->where('date_of_event')
                ->where('status','>',config('constants.booking_status.pencil')) 
                ->where('is_active',1)
                ->orderBy('created_at', 'desc')->get()->toArray();
                //->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
    
        }
        else{

                $bookingData=$this->bookings_users
                ->with('userinfo')
                ->with('bookings')
                ->where('user_id',get_session_value('id'))  
                //->where('status','>',config('constants.booking_status.pencil'))  
                ->where('status',1)
                //->orderBy('created_at', 'desc')->get()->toArray();
                ->orderBy('created_at', 'desc')->get()->toArray();
            //p($bookingData);die;
                return view('adminpanel/user_calender_schedule',get_defined_vars());
          }
         
        
        return view('adminpanel/calender_schedule',get_defined_vars());
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
        $invoices='venue_invoices';
        if($payee[1]=='customer')
        $invoices='customer_invoices';

        $bookingData=$this->bookings
        ->with($invoices)
        ->with('customer')
        ->with('venue_group')
        ->where('id',$id)->get()->toArray();
        $bookingData=$bookingData[0];
        //p($bookingData); die;
        
        $this->invoices->payee_name=$request['payee_name'];
        $this->invoices->payee_uid =$payee[0];
        $this->invoices->slug =$payee[1];
        $this->invoices->description=$request['description'];
        $this->invoices->paid_amount=$request['paid_amount'];
        $this->invoices->booking_id=$id;
        $this->invoices->created_by=get_session_value('id');
        $this->invoices->save();

        // Email Section 
        if(empty($bookingData[$invoices])){
            $mailData['body_message']='This email is to confirm that a booking was confirmed in '.$bookingData['venue_group']['userinfo'][0]['vg_name'].' for '.date(config('constants.date_formate'),$bookingData['date_of_event']).'. we have received a deposit of $'.$request['paid_amount'].' for the event
            booking '.$request['payee_name'].' for '.date('d/m/Y',$request['date_of_event']).' .Please find all booking details below.';
            $mailData['body_message'] .=booking_email_body($bookingData);
            $mailData['body_message'] .='<br>If you see any mistakes in the booking and for any concerns please call us right away at 845-501-1888';
            $mailData['subject']='Photography booking confirmed';
            $mailData['button_title']='Login';
            $mailData['button_link']=route('admin.loginform');
            $bookingData['venue_group']['userinfo'][0]['email'];
            if($payee[1]=='customer')
            $toEmail=$bookingData['customer']['userinfo'][0]['email'];
        }
        elseif($this->invoices->slug=='customer'){
            $mailData['body_message']='This email is to confirm that we have received a deposit of $'.$request['paid_amount'].' for the event
            booking '.$request['payee_name'].' for '.date('d/m/Y',$request['date_of_event']);
            $mailData['body_message'] .='You have been assigned a password to our online portal. Please use your email and password —----------- to sign in.';
            $mailData['body_message'] .='<br>If you see any mistakes in the booking and for any concerns please call us right away at 845-501-1888';
            $mailData['subject']='Deposit Received - Booking Confirmed';
            $mailData['button_title']='Login';
            $mailData['button_link']=route('admin.loginform');
            $toEmail=$bookingData['customer']['userinfo'][0]['email'];

        }
        else{
            $mailData['body_message']='This is to confirm that we have received your payment of $'.$request['paid_amount'].' for the event
            booking '.$request['payee_name'].' for '.date('d/m/Y',$request['date_of_event']);
            $mailData['body_message'] .='<br>If you have any questions please contact us at 845-501-1888';
            $mailData['subject']='Thank you for your payment';
            $mailData['button_title']='Login';
            $mailData['button_link']=route('admin.loginform');
           
            $toEmail=$bookingData['venue_group']['userinfo'][0]['email'];
            if($payee[1]=='customer')
            $toEmail=$bookingData['customer']['userinfo'][0]['email'];
        }
        if(isset($toEmail))
        $emailAdd[]=$toEmail;
        $emailAdd[]=config('constants.admin_email');
                if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                    echo 'Thank you, Your Booking has been confirmed';
                }
        // End                


       
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
        // Approval from the customer
        public function customer_approval($id, Request $req){
            $data=$this->bookings->with('customer')->with('venue_group')->where('id',$id)->get()->toArray();
            $data=$data[0];
            if(isset($data['external_link_token']) && Hash::check($req['token'], $data['external_link_token'])){
                $this->bookings->where('id',$id)
                ->where('customer_approved',0)
                ->update(array('status'=>config('constants.booking_status.awaiting_for_photographer'),'customer_approved'=>1,'external_link_token'=>NULL));
                
                $mailData['body_message']='Customer '.$data['customer']['userinfo'][0]['name'].' has confirmed and approved a booking for '.date(config('constants.date_formate'),$data['date_of_event']).' in '.$data['venue_group']['userinfo'][0]['vg_name'];
                $mailData['subject']='A booking has been confirmed';
                $toEmail=config('constants.admin_email');

                if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
                    echo 'Thank you, Your Booking has been confirmed';
                }
                
                        //activity Logged
                $activityComment='Mr.'.$data['customer']['userinfo'][0]['name'].' approved Booking of date '.date('d/m/Y',$data['date_of_event']);
                $activityData=array(
                    'user_id'=>$data['customer']['userinfo'][0]['id'],
                    'action_taken_on_id'=>$id,
                    'action_slug'=>'customer_approved_booking',
                    'comments'=>$activityComment,
                    'others'=>'bookings',
                    'created_at'=>date('Y-m-d H:I:s',time()),
                );
                $activityID=log_activity($activityData);
               
            }else{
                echo 'Link Expired';
            }
        }
        public function photographer_action($booking_id,$photographer_id,$action){
            
        $bookingData=$this->bookings_users->with('booking')->with('userinfo')
            ->where('booking_id',$booking_id)->where('user_id',$photographer_id)
            ->get()
            ->toArray();
            $bookingData=$bookingData[0];
            // echo $bookingData['booking']['customer']['userinfo'][0]['name'].'<br>';
            // echo $bookingData['userinfo'][0]['name'];
            // p($bookingData);
            // die;
            $action=base64_decode($action);
            $status=2;
            $activityComment='Photographer Declined the Invitation';
            $msg= 'we have canceled this booking for you and a new photographer will be assigned';
            $actionMsg='declined';
            if($action=='accept'){
                $status=1;
                $actionMsg='approved';
                $msg="Thank you, you have been confirmed";
                $activityComment='Photographer accepted the Invitation';
            }
            
            $updated=$this->bookings_users->where('booking_id',$booking_id)->where('user_id',$photographer_id)->where('status',0)->update(array('status'=>$status));
          
            $this->bookings->where('id',$booking_id)->update(array('status'=>get_booking_status($booking_id)));
          
            $activityData=array(
                'user_id'=>$photographer_id,
                'action_taken_on_id'=>$booking_id,
                'action_slug'=>'photographer_status_changed',
                'comments'=>$activityComment,
                'others'=>'bookings_users',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);  
            if($updated){
                
                $mailData['body_message']='This email is to let you know that '.$bookingData['userinfo'][0]['name'].' has '.$actionMsg.' the booking for '.$bookingData['booking']['customer']['userinfo'][0]['name'].' on'.date('d/m/Y');
                $mailData['subject']='New Photographer Response';
                $toEmail=config('constants.admin_email');

                if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
                //    echo 'Thank you, Your Booking has been confirmed';
                }
                echo $msg;
            }
            
            else
            echo 'Link expired';
            exit;

        }
    public function save_booking_edit_data($id,Request $request){
        $validator=$request->validate([
            'date_of_event'=>'required',
            //'venue_group_id'=>'required',
            'package_id'=>'required',
            //'paying_via'=>'required',
            'package_id'=>'required',
        ]);
        
        
        $bookingData['date_of_event']=strtotime($request['date_of_event']);
        $bookingData['time_of_event']=$request['time_of_event'];
        $bookingData['package_id']=$request['package_id'];
        
        if(isset($request['other_venue_group']) && !empty($request['other_venue_group']))
        $bookingData['other_venue_group']=$request['other_venue_group'];

        $bookingData['who_is_paying']=$request['who_is_paying'];
        $bookingData['paying_via']=$request['paying_via'];
        $bookingData['collected_by_photographer']=$request['collected_by_photographer'];
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
        
        // Send Email when add to Booking
        $booking=$this->bookings
        ->where('id',$id)
        ->where('status',config('constants.booking_status.pencil'))
        ->orderBy('created_at', 'desc')->get(array('id','status','groom_name','date_of_event'))->toArray();
        // Get All Booking Data
        $bookingsMailData=$this->bookings->with('customer')->with('venue_group')->with('photographer')->where('id',$id)->get()->toArray();
        $bookingsMailData=$bookingsMailData[0];
       
        if(count($booking)>0){
            $welcomMes='<div style="text=align:justify">A booking has been created for you. We have started a booking for your event.
            To confirm the booking please confirm that all the information below is correct and click the Approve button below.<br></div>';
            $mailData['body_message']=$welcomMes.booking_email_body($bookingsMailData);
                $token=sha1(time());
                $booking_data_link['external_link_token']=Hash::make($token);
                $this->bookings->where('id',$id)->update($booking_data_link);
                $mailData['subject']='Welcome to Klein\'s photography';
                $mailData['button_title']='Acceept';
                $mailData['button_link']=route('customer.approve',['id' => $id,'token'=>$token]);
                

                $toEmail=$request['customer_email'];

                if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
                    $request->session()->flash('alert-success', 'Please check your email');
                }
        }
        // If Time of the event changed then send Email to all the users
        if($request['time_of_event']!=$bookingsMailData['time_of_event']){

         
                $mailData['body_message']=' This is to confirm that the time of the booking for '.$bookingsMailData['customer']['userinfo'][0]['name'].' for '.date('d/m/Y',$bookingsMailData['date_of_event']).' in '.$bookingsMailData['venue_group']['userinfo'][0]['vg_name'].' was updated to '.$request['time_of_event'];
                $mailData['subject']='Time of event updated';
               
                $emailAdd=[
                    config('constants.admin_email'),
                    $bookingsMailData['customer']['userinfo'][0]['email'],
                    $bookingsMailData['venue_group']['userinfo'][0]['email']
                ];
                foreach($bookingsMailData['photographer'] as $photographer){
                    $emailAdd[]=$photographer['userinfo'][0]['email'];
                }
              
                if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                    $request->session()->flash('alert-success', 'Please check your email');
                }
        }
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
                //$date_of_event=time('d/m/Y',strtotime($request['date_of_event']));
                $date_of_event=$request['date_of_event'];
                $retData=assign_photographer_to_booking($date_of_event,$photographerArr);

                if(!$retData['result'])
                $request->session()->flash('alert-warning', $retData['msg']);
                else { // If adding photographer then send Email
                    
                    
                    $welcomMes='<div style="text=align:justify">You have been assigned to a new booking by kleins photography.<br></div>';
                    $mailData['body_message']=$welcomMes.booking_email_body($bookingsMailData);
  
                  
                        $mailData['subject']='you have a new event to confirm';
                        $mailData['button_title']='APPROVE';
                        $mailData['button_link']=route('photograppher.action',['booking_id' => $id,'photographer_id'=>$value,'action'=>base64_encode('accept')]);
                        $mailData['button_title2']='Reject';
                        $mailData['button_link2']=route('photograppher.action',['booking_id' => $id,'photographer_id'=>$value,'action'=>base64_encode('reject')]);
                        $toEmail='photographer@yahoo.com';
        
                        if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
                            $request->session()->flash('alert-success', 'Please check your email');
                        }
                }
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
        $activityComment='Mr.'.get_session_value('name').' Added new Booking of date '.date('d/m/Y',$bookingData['date_of_event']);
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
public function testpage(){
    
    $user=Auth::user();
    return view('adminpanel/test',get_defined_vars());
}
// Upload Booking Photos
public function booking_photos($id){
    
        $user=Auth::user(); 
        if($user->group_id!=config('constants.groups.photographer'))
        return false;

        $bookingData=$this->bookings
        ->with('customer')
        ->with('photographer')
        ->with('venue_group')
        ->with('invoices')
        ->with('gallery')
        ->with('comments')
        ->with('deposite_requests')
        ->where('id',$id)
        ->orderBy('created_at', 'desc')->get()->toArray();
        $bookingData=$bookingData[0];

        $assigne_photographers=$this->bookings_users->with('userinfo')->where('booking_id',$id)->where('group_id',config('constants.groups.photographer'))->get()->toArray();
        

        return view('adminpanel/upload_photos_booking',get_defined_vars());
}

public function add_photos($id,Request $request){
   
    //return response()->json(['success'=>'i am here']);
    // $user=Auth::user();
        $image = $request->file('file');
        $imageExt=$image->extension();
        $imageName = time().'.'.$imageExt;

        $uploadingPath=base_path().'/public/uploads/bookings'.$id;
        if(base_path()!='/Users/waximarshad/office.thephotographicmemories.com')
        $uploadingPath=base_path().'/public_html/uploads/bookings'.$id;

        //$image->move(public_path('uploads/bookings'.$id),$imageName);
        $image->move($uploadingPath,$imageName);
        $orginalImageName=$image->getClientOriginalName();
    
    //return response()->json(['success'=>$imageName]);

        $this->files->name=$orginalImageName;
        $this->files->file_name=phpslug($imageName);
        $this->files->slug=phpslug('booking_photos');
        $this->files->path=url('uploads/bookings'.$id).'/'.$imageName;
        $this->files->description=$orginalImageName;
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
public function add_documents(Request $request){
    $image = $request->file('file');
    $imageExt=$image->extension();
    $imageName = time().'.'.$imageExt;



    $image->move(public_path('uploads'),$imageName);
    $orginalImageName=$image->getClientOriginalName();
    return response()->json(['success'=>$imageName]);

}
public function upload_documents($id,Request $request){
   
    //return response()->json(['success'=>'i am here']);
    // $user=Auth::user();
        $image = $request->file('file');
        $imageExt=$image->extension();
        $imageName = time().'.'.$imageExt;

        //$uploadingPath=public_path('uploads');
        $uploadingPath=base_path().'/public/uploads';
        if(base_path()!='/Users/waximarshad/office.thephotographicmemories.com')
        $uploadingPath=base_path().'/public_html/uploads';

        $image->move($uploadingPath,$imageName);
        $orginalImageName=$image->getClientOriginalName();
    
    //return response()->json(['success'=>$imageName]);

        $this->files->name=$orginalImageName;
        $this->files->file_name=phpslug($imageName);
        $this->files->slug=phpslug('booking_documents');
        $this->files->path=url('uploads').'/'.$imageName;
        $this->files->description=$orginalImageName;
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

    // Email Section
      
        // Get All Booking Data
        $bookingsMailData=$this->bookings->with('customer')->with('venue_group')->with('photographer')->where('id',$id)->get()->toArray();
        $bookingsMailData=$bookingsMailData[0];

        $mailData['body_message']='There was a new note added to the booking of '.$bookingsMailData['customer']['userinfo'][0]['name'].' for event '.date(config('constants.date_formate'),$bookingsMailData['date_of_event']);
        $mailData['subject']='New note added to booking';

         $emailAdd=[
                    config('constants.admin_email'),
                    //$bookingsMailData['customer']['userinfo'][0]['email'],
                    //$bookingsMailData['venue_group']['userinfo'][0]['email']
                ];
                foreach($bookingsMailData['photographer'] as $photographer){
                    $emailAdd[]=$photographer['userinfo'][0]['email'];
                }

        if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
            $dataArray['emailMsg']='Email Sent Successfully';
        }
    //                        
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
        else if(isset($req['action']) && $req['action']=='change_booking_status'){
            
            $data_to_update=array();
            $data_to_update['status']=$req['status'];
            
            $dataArray['msg']='Mr.'.get_session_value('name').', changed Booking Status and now,'.booking_status_for_msg($req['status']);
            $activityComment='Mr.'.get_session_value('name').', changed Booking Status and now,'.booking_status_for_msg($req['status']);
         
                $data['booking_id']=$req['booking_id'];
                
              
                $this->bookings->where('id',$req['booking_id'])->update(array('status'=>$req['status']));
                
            

            $dataArray['error']='No';
            $dataArray['booking_status_msg']=booking_status_for_msg($req['status']);
            
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['booking_id'],
                'action_slug'=>'booking_status_changed',
                'comments'=>$activityComment,
                'others'=>'booking',
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
            $data_to_update['bride_name']=$req['data']['bride_name'];
            $data_to_update['bride_home_phone']=$req['data']['bride_home_phone'];
            $data_to_update['bride_mobile']=$req['data']['bride_mobile'];
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
            $data_to_update['groom_name']=$req['data']['groom_name'];
            $data_to_update['groom_home_phone']=$req['data']['groom_home_phone'];
            $data_to_update['groom_mobile']=$req['data']['groom_mobile'];
            $data_to_update['groom_billing_address']=$req['data']['groom_billing_address'];
            $this->bookings->where('id',$id)->update($data_to_update);
            $dataArray['error']='No';
            $dataArray[$req['action'].'_replace']=$req['data']['groom_billing_address'];
            $dataArray['msg']='Mr.'.get_session_value('name').', updated Groom !';
            $activityComment='Mr.'.get_session_value('name').', updated Groom !';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'groom_updated',
                'comments'=>$activityComment,
                'others'=>'booking_actions',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
        elseif(isset($req['action']) && $req['action']=='customer_update'){ 
            
            $userData=array();
            $userData['password']=Hash::make($req['data']['password']);
            $userData['firstname']=($req['data']['firstname']);
            $userData['lastname']=($req['data']['lastname']);
            $userData['phone']=($req['data']['phone']);
            $userData['relation_with_event']=($req['data']['relation_with_event']);
            $userData['is_active']=1;
            $this->users->where('id',$req['data']['uid'])->update($userData);
            $dataArray['error']='No';
            $dataArray['msg']='Successfuly updated';
            $dataArray['msg']='Mr.'.get_session_value('name').', updated customer!';
            $activityComment='Mr.'.get_session_value('name').', updated customer !';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['data']['uid'],
                'action_slug'=>'customer_updated',
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

            $dataArray['msg']='Successfuly submitted';

            $mailData['body_message']=
            'your booking is awaiting its final deposit to be added to the calendar. Please submit a deposit of $'.$req['data']['deposit_needed'].'.
            Deposit can be made through debit/credit card by calling us at 845--501-1888 and or send us a zelle to <a href="mailto:jkk10952@gmail.com">jkk10952@gmail.com</a>
            As soon as we receive your deposit your booking will be 100% confirmed and booked.';
            $mailData['subject']='Your booking needs just one final step';
            $toEmail=config('constants.admin_email');

            if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
                $dataArray['msg']='A notification sent to customer for payment';
            }

            $dataArray['error']='No';
            
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

                $uploadedFilePath=base_path().'/public/uploads/';
                if(base_path()!='/Users/waximarshad/office.thephotographicmemories.com')
                $uploadedFilePath=base_path().'/public_html/uploads/';
        
                // $filePath=public_path('uploads').'/'.$fileData['file_name'];
                // if($fileData['slug']=='booking_photos')
                // $filePath=public_path('uploads/bookings'.$req['booking_id']).'/'.$fileData['file_name'];
              
                $filePath=$uploadedFilePath.$fileData['file_name'];
                if($fileData['slug']=='booking_photos')
                $filePath=$uploadedFilePath.'bookings'.$req['booking_id'].'/'.$fileData['file_name'];
              
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
