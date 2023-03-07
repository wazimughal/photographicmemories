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
use Maatwebsite\Excel\Facades\Excel; // To export import excel
// Used for Email Section
use App\Mail\EmailTemplate;
use App\Mail\BookingEmailTemplate;
use Illuminate\Support\Facades\Mail;

// File uploading in Chunks
use Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException;
use Storage;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;


// Import/Export Excelsheet
//use App\Imports\ExportCustomerPayments;
use App\Exports\ExportCustomerPayments;
use App\Exports\ExportPhotographerExpense;

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
        if($user->group_id==config('constants.groups.customer'))
        return view('adminpanel.add_user_pencils',get_defined_vars());
        
        return view('adminpanel.add_pencils',get_defined_vars());
        
     }

     // View Booking Bookings
     public function view_booking($id){
        $user=Auth::user(); 
        $bookingData=$this->bookings
        ->with(['customer','photographer','venue_group','invoices','files','comments','vg_comments','photographer_comments','deposite_requests'])
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
        ->with(['customer','photographer','venue_group',$invoice_of,'files','comments','deposite_requests'])
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
     // View PDF Bookings
     public function booking_pdf_download($id, Request $req){
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
        ->with(['customer','package','photographer','venue_group',$invoice_of])
        ->where('id',$id)
        ->orderBy('created_at', 'desc')->get()->toArray();
        $bookingData=$bookingData[0];
        //p($bookingData); die;
        $invoice_no=time();
       
        //return view('adminpanel/pdf_booking',get_defined_vars());
        // return view('adminpanel/pdf',get_defined_vars());
        // For Creating PDF:
// share data to view
        //return view('adminpanel/pdf_booking',get_defined_vars());
        $fileName=$invoice_of.'-'.date('d/m/Y h:i:s',time());
        view()->share('adminpanel/pdf_booking',get_defined_vars());

        $PDFOptions = ['defaultFont' => 'sans-serif'];
        //$PDFOptions = ['enable_remote' => true];

        $pdf = PDF::loadView('adminpanel/pdf_booking', get_defined_vars())->setOptions($PDFOptions);
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
            'action_slug'=>'downloaded_booking_pdf',
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
            //'email'=>'required|email|distinct|unique:users|min:5',
            'email'=>'required',
            'phone'=>'required',
            //'password'=>'required',
            'relation_with_event'=>'required',
            'preferred_photographer_id'=>'required',
            // 'groom_name'=>'required',
            // 'groom_home_phone'=>'required',
            // 'groom_mobile'=>'required',
            // 'groom_email'=>'required|email|distinct|unique:bookings|min:5',
            // 'groom_billing_address'=>'required',
            // 'bride_name'=>'required',
            // 'bride_home_phone'=>'required',
            // 'bride_mobile'=>'required',
            // 'bride_email'=>'required|email|distinct|unique:bookings|min:5',
            // 'bride_billing_address'=>'required',
            'date_of_event'=>'required',
            'venue_group_id'=>'required'
        ]);
        //p($request->all()); die;
        $userExist=$this->users->where('email',$request['email'])->first('id');
        
        if(!empty($userExist) && ($userExist->id)>0){
            
            $data_to_update_user['firstname']=$request['firstname'];
            $data_to_update_user['lastname']=$request['lastname'];
            $data_to_update_user['name']=$request['firstname'].' '.$request['lastname'];
            $data_to_update_user['email']=$request['email'];
            //$data_to_update_user['password=Hash::make($request['password']);
            $data_to_update_user['phone']=$request['phone'];
            $data_to_update_user['relation_with_event']=$request['relation_with_event'];
            $data_to_update_user['group_id']=config('constants.groups.customer');
            $this->users->where('email',$request['email'])->update($data_to_update_user);

            $this->users->id=$userExist->id;
            $this->users->group_id=config('constants.groups.customer');
        }
        else{
        $this->users->firstname=$request['firstname'];
        $this->users->lastname=$request['lastname'];
        $this->users->name=$request['firstname'].' '.$request['lastname'];
        $this->users->email=$request['email'];
        //$this->users->password=Hash::make($request['password']);
        $this->users->phone=$request['phone'];
        $this->users->relation_with_event=$request['relation_with_event'];
        $this->users->group_id=config('constants.groups.customer');
        $this->users->save();

        }
        

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
        }else if($user->group_id==config('constants.groups.venue_group_hod')){
            $this->bookings->pencile_by=config('constants.pencileBy.venue_group');
            $mailData['subject']='New Pencil - Venue Group: '.get_session_value('vg_name');

        }else if($user->group_id==config('constants.groups.customer')){
            $this->bookings->pencile_by=config('constants.pencileBy.customer');
            $mailData['subject']='New Pencil - Customer: '.get_session_value('name');
        }
        


        if($request['preferred_photographer_id']!='No'){
            $this->bookings->preferred_photographer_id=$request['preferred_photographer_id'];
        }
        
        $venueGroupHTML='';
        if(isset($request['other_venue_group']) && !empty($request['other_venue_group'])){
            $this->bookings->other_venue_group=$request['other_venue_group'];
           $venueGroupHTML= '<tr><td> Venue Group :</td><td>'.$request['other_venue_group'].'</td></tr>';
        }
        else{
            $this->bookings->venue_group_id=$request['venue_group_id'];
        }
            

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
            $venueGroupHTML= '<tr><td> Venue Group :</td><td>'.get_venue_group_name_by_id($request['venue_group_id']).'</td></tr>';

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
                            <tr><td> Bride Billing Address :</td><td>'.$request['bride_billing_address'].'</td></tr>'. $venueGroupHTML.'
                            <tr><td> Date of Event :</td><td>'.date(config('constants.date_formate'),strtotime($request['date_of_event'])).'</td></tr>
        </table>';
        $mailData['button_title']='Go to CRM';
        
        
            $emailAdd=[
                config('constants.admin_email'),
                //$request['email']
                    ];
        $emailAdd=get_users_email_address([],$emailAdd); // by Default get admin Email Address                    
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
     public function save_user_pencil_data(Request $request){
        $user=Auth::user(); 
        $validatorArray=[
            'firstname'=>'required',
            'lastname'=>'required',
            'phone'=>'required',
            'relation_with_event'=>'required',
            'preferred_photographer_id'=>'required',
            
            'date_of_event'=>'required',
            'venue_group_id'=>'required'
        ];

        if(isset($request['email']) && !empty($request['email'])){
            $validatorArray['email']='required|email|distinct|unique:users|min:5';
            $data_to_update_user['email']=$request['email'];
        }
        

        $validator=$request->validate($validatorArray);
        //p($request->all()); die;
        
        $data_to_update_user['firstname']=$request['firstname'];
        $data_to_update_user['lastname']=$request['lastname'];
        $data_to_update_user['name']=$request['firstname'].' '.$request['lastname'];
        $data_to_update_user['phone']=$request['phone'];
        
        
        
        $data_to_update_user['relation_with_event']=$request['relation_with_event'];
        
        $this->users->where('id',get_session_value('id'))->update($data_to_update_user);

        $this->users->group_id=config('constants.groups.customer');
        $this->users->id=get_session_value('id');

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
        else if($user->group_id==config('constants.groups.customer')){
            $this->bookings->pencile_by=config('constants.pencileBy.customer');
            $mailData['subject']='New Pencil - Customer: '.get_session_value('name');
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
                            <tr><td> Date of Event :</td><td>'.date(config('constants.date_formate'),strtotime($request['date_of_event'])).'</td></tr>
        </table>';
        $mailData['button_title']='Go to CRM';
        
        
            $emailAdd=[
                config('constants.admin_email'),
                //$request['email']
                    ];
        $emailAdd=get_users_email_address([],$emailAdd);

        if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
            $request->session()->flash('alert-success', 'Please check your email');
        }

        //$mailData['button_link']='';
        //activity Logged
        $activityComment='Mr.'.get_session_value('name').' Added new Pencil Customer'.$data_to_update_user['name'];
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
    public function pencils($type=NULL, Request $req){
        
        $user=Auth::user();

        $pagination_per_page=config('constants.per_page');
       
        if($user->group_id== config('constants.groups.admin')){
         // Where Clause...   
        $where['is_active']=1;
        $where['status']=config('constants.booking_status.pencil');
        
        if($type=='trashed')
        $where['is_active']=2;

        if($type=='office')
        $where['pencile_by']=config('constants.pencileBy.office');
        elseif($type=='venue_group')
        $where['pencile_by']=config('constants.pencileBy.venue_group');
        elseif($type=='web')
        $where['pencile_by']=config('constants.pencileBy.website');
        elseif($type=='customer')
        $where['pencile_by']=config('constants.pencileBy.customer');
        
       //p($where); die;
        // delete  die;

            $pencil_sql=$this->bookings
            ->with(['customer','photographer','venue_group'])
            ->where($where)
            ->orderBy('created_at', 'desc');

            if(
                isset($req->from_date) &&
                !empty($req->from_date) &&
                isset($req->to_date) &&
                !empty($req->from_date)
            ){
                $from=strtotime($req->from_date);
                $to=strtotime($req->to_date);
                
                $pencil_sql=$pencil_sql->WhereBetween('date_of_event', [$from, $to]);
            }
           // echo $pencil_sql->toSql(); die;
            $pencilData=$pencil_sql->paginate($pagination_per_page);
            
    }
    else{
       
        // For Venue Group Section
        $pencilData=array();

        $to=$from='';
        if(
            isset($req->from_date) &&
            !empty($req->from_date) &&
            isset($req->to_date) &&
            !empty($req->from_date)
        ){
            $from=strtotime($req->from_date);
            $to=strtotime($req->to_date);
           
        }

        
        $pencilData=bookings_users::joinRelationship('bookings', function ($join) use($from,$to) {
            $join->where(['is_active'=>1,'bookings.status'=>config('constants.booking_status.pencil')]);
            if($from!='' && $to=='')
            $join->WhereBetween('date_of_event', [$from, time()]);
            elseif($from!='' && $to!='')
            $join->WhereBetween('date_of_event', [$from, $to]);
        })
        ->with('userinfo')
        ->with('pencils')
        ->where('user_id',get_session_value('id'))
        ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
       // p($pencilData->toArray()); die;
        return view('adminpanel/user_pencils',get_defined_vars());
    }

            
        return view('adminpanel/pencils',get_defined_vars());
    }
// View pencile
public function view_pencil($id){
    $user=Auth::user(); 
    $bookingData=$this->bookings
    ->with(['customer','venue_group','pencil_comments'])
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
        $validatorArray=[
            'firstname'=>'required',
            'lastname'=>'required',
            //'email'=>'required|email|distinct|unique:users|min:5',
            'phone'=>'required',
            'relation_with_event'=>'required',
            //'preferred_photographer_id'=>'required',
            // 'groom_name'=>'required',
            // 'groom_home_phone'=>'required',
            // 'groom_mobile'=>'required',
            // 'groom_email'=>'required|email|distinct|unique:bookings|min:5',
            // 'groom_billing_address'=>'required',
            // 'bride_name'=>'required',
            // 'bride_home_phone'=>'required',
            // 'bride_mobile'=>'required',
            // 'bride_email'=>'required|email|distinct|unique:bookings|min:5',
            // 'bride_billing_address'=>'required',
            'date_of_event'=>'required',
            'venue_group_id'=>'required'
        ];
        
        if(isset($request['email']) && !empty($request['email']))
        $validatorArray['email']='required|email|distinct|unique:users|min:5';
        
        $validator=$request->validate($validatorArray);
       //p($request->all()); die;
        
        $data_to_update['firstname']=$this->users->firstname=$request['firstname'];
        $data_to_update['lastname']=$this->users->lastname=$request['lastname'];
        $data_to_update['name']=$this->users->name=$request['firstname'].' '.$request['lastname'];

        if(isset($request['email']) && !empty($request['email']))
        $data_to_update['email']=$this->users->email=$request['email'];

        $data_to_update['phone']=$this->users->phone=$request['phone'];
        $data_to_update['relation_with_event']=$this->users->relation_with_event=$request['relation_with_event'];
        //$data_to_update['group_id']=$this->users->group_id=config('constants.groups.customer');
       $this->users->where('id', $request['customer_id'])->update($data_to_update);

        //$this->bookings->preferred_photographer_id=$request['preferred_photographer_id'];
        $bookingData_to_update['groom_name']=$this->bookings->groom_name=$request['groom_name'];
        $bookingData_to_update['groom_home_phone']=$this->bookings->groom_home_phone=$request['groom_home_phone'];
        $bookingData_to_update['groom_mobile']=$this->bookings->groom_mobile=$request['groom_mobile'];
        $bookingData_to_update['groom_email']=$this->bookings->groom_email=$request['groom_email'];
        $bookingData_to_update['groom_billing_address']=$this->bookings->groom_billing_address=$request['groom_billing_address'];
        $bookingData_to_update['bride_name']=$this->bookings->bride_name=$request['bride_name'];
        $bookingData_to_update['bride_home_phone']=$this->bookings->bride_home_phone=$request['bride_home_phone'];
        $bookingData_to_update['bride_mobile']=$this->bookings->bride_mobile=$request['bride_mobile'];
        $bookingData_to_update['bride_email']=$this->bookings->bride_email=$request['bride_email'];
        $bookingData_to_update['bride_billing_address']=$this->bookings->bride_billing_address=$request['bride_billing_address'];
        $bookingData_to_update['date_of_event']=$this->bookings->date_of_event=strtotime($request['date_of_event']);
        $bookingData_to_update['created_by_user']=$this->bookings->created_by_user=get_session_value('id');
        //$bookingData_to_update['pencile_by']=$this->bookings->pencile_by=config('constants.pencileBy.office');


        if(isset($request['preferred_photographer_id']) && $request['preferred_photographer_id']>0)
        $bookingData_to_update['preferred_photographer_id']=$this->bookings->preferred_photographer_id=$request['preferred_photographer_id'];
        $bookingData_to_update['other_venue_group']='';
        if(isset($request['other_venue_group']) && !empty($request['other_venue_group'])){
            $bookingData_to_update['other_venue_group']=$this->bookings->other_venue_group=$request['other_venue_group'];
            $this->bookings_users->where(['booking_id'=>$id,'slug'=>'new_venue_group','group_id'=>config('constants.groups.venue_group_hod')])->delete();
        }
        else{
            $bookingData_to_update['venue_group_id']=$this->bookings->venue_group_id=$request['venue_group_id'];
        }
        
        
        

        $bookingData_to_update['notes_by_pencil']=$this->bookings->notes_by_pencil=$request['notes_by_pencil'];
        
        // Add new Pencil
        //$this->bookings->save();
        $this->bookings->where('id', $id)->update($bookingData_to_update);
        
       

        // Venue Group Added to Pencil
        //if(isset($request['selected_venue_group_id']) && $request['venue_group_id'])
        //p($request->all());die;
        if((isset($request['venue_group_id']) && $request['venue_group_id']>0) && !isset($request['other_venue_group']) || empty($request['other_venue_group'])){
        $this->bookings_users->where(['booking_id'=>$id,'slug'=>'new_venue_group','group_id'=>config('constants.groups.venue_group_hod')])->delete();
        //$this->bookings_users->where(['booking_id'=>$id,'slug'=>'new_venue_group','group_id'=>config('constants.groups.venue_group_hod')])->update(['user_id' => $request['venue_group_id']]);
            DB::table('bookings_users')->insert([
            ['user_id' => $request['venue_group_id'],
             'booking_id' => $id,
             'group_id' => config('constants.groups.venue_group_hod'),
             'slug' => phpslug('new_venue_group'),
            ]
        ]);

        }
        
        // Preffered Photographer added to Pencil
        if(isset($request['preferred_photographer_id']) && $request['preferred_photographer_id']>0){
            DB::table('bookings_users')->insert([
                ['user_id' => $request['preferred_photographer_id'],
                 'booking_id' => $id,
                 'group_id' => config('constants.groups.photographer'),
                 'slug' => phpslug('preffered_photographer'),
                ]
            ]);
        }
      
        //activity Logged
        $activityComment='Mr.'.get_session_value('name').' updated the pencil.';
        $activityData=array(
            'user_id'=>get_session_value('id'),
            'action_taken_on_id'=>$id,
            'action_slug'=>'pencil_updated',
            'comments'=>$activityComment,
            'others'=>'bookings',
            'created_at'=>date('Y-m-d H:I:s',time()),
        );
        $activityID=log_activity($activityData);


        $request->session()->flash('alert-success', 'Pencil updated Successfully');
        
        return redirect()->back();



     }
     public function report_customer_payments(Request $req){
        $user=Auth::user();
        if($user->group_id!=config('constants.groups.admin'))
        abort(403, sprintf('Only ADMIN is allowed')); 

        $where_clause=[
            ['group_id', '=', config('constants.groups.customer')],
            ['is_active', '=', 1],
        ];
    // if($user->group_id==config('constants.groups.customer')){
    // $where_clause[]=['customer_id', '=', get_session_value('id')];
    // }
        $usersData=$this->users->where($where_clause)->with(['getGroups'])->paginate(config('constants.per_page'));
        //p($usersData);
        return view('adminpanel.reports.customer_payments',get_defined_vars());
     }
     public function report_vg_payments(Request $req){
        $user=Auth::user();

       

        $where_clause=[
            ['group_id', '=', config('constants.groups.venue_group_hod')],
            ['is_active', '=', 1],
        ];
        if($user->group_id==config('constants.groups.venue_group_hod'))
        $where_clause[]=['id', '=', get_session_value('id')];
   
        $usersData=$this->users->where($where_clause)->with(['getGroups'])->paginate(config('constants.per_page'));
   
        return view('adminpanel.reports.vg_payments',get_defined_vars());
     }
     public function report_photographer_payments(Request $req){
        $user=Auth::user();

       

        $where_clause=[
            ['group_id', '=', config('constants.groups.photographer')],
            ['is_active', '=', 1],
        ];
        if($user->group_id==config('constants.groups.photographer'))
        $where_clause[]=['id', '=', get_session_value('id')];
   
        $usersData=$this->users->where($where_clause)->with(['getGroups'])->paginate(config('constants.per_page'));
   
        return view('adminpanel.reports.photographer_payments',get_defined_vars());
     }
     // Export Customer Expenses
        public function report_export_customer_account($id,Request $req){

            $where_clause=[
                ['user_id', '=',$id],
            ];
            $with=['userinfo','booking'];

            $customerData=$this->bookings_users
            ->with($with)
            ->where($where_clause)
            ->orderBy('created_at', 'desc')->get()->toArray();
             //p($customerData);die;
             $exportData=array();
             $grand_total_cost=$grand_total_payment_recieved=$grand_total_due_payment=0;
             $grand_total_paid_by_customer=$grand_total_paid_by_venuegroup=0;

            if(count($customerData)>0)
            foreach($customerData as $key=>$data){
                
                //p($data); die;

              

                $vg_name=$data['booking']['other_venue_group'];

                $vg_email=$vg_manager_name=$vg_manager_phone=$vg_city='';
                $photographer_name=$photographer_email=$photographer_phone='';

                if(isset($data['venue_group']) && !empty($data['venue_group'])){
                    $vg_name=$data['venue_group']['userinfo'][0]['vg_name'];
                    $vg_email=$data['venue_group']['userinfo'][0]['emial'];
                    $vg_manager_name=data['venue_group']['userinfo'][0]['vg_manager_name'];
                    $vg_manager_phone=data['venue_group']['userinfo'][0]['vg_manager_phone'];
                    $vg_city=data['venue_group']['userinfo'][0]['vg_city'];
                }
                
                if(isset($data['photographer']) && !empty($data['photographer'])){
                    $photographer_name=$data['photographer']['userinfo'][0]['name'];
                    $photographer_email=data['photographer']['userinfo'][0]['email'];
                    $photographer_phone=data['photographer']['userinfo'][0]['phone'];
                    
                }

                $overtime_hours=0;
                if($data['booking']['overtime_hours']>0)
                $overtime_hours=$data['booking']['overtime_hours'];
                
                $overtime_rate_per_hour=0;
                if($data['booking']['overtime_rate_per_hour']>0)
                $overtime_rate_per_hour=$data['booking']['overtime_rate_per_hour'];
                
                // Booking Price
                $extra_price=0;
                if($data['booking']['extra_price']>0)
                $extra_price=$data['booking']['extra_price'];
                
                $over_time_cost=$overtime_rate_per_hour*$overtime_rate_per_hour;
                $package_price=$data['booking']['package']['price'];

                $total_booking_cost=$package_price+$extra_price+$over_time_cost;

                // Payment Received
                $paid_by_customer=$paid_by_venuegroup=0;
                
                if(!empty($data['invoices'])){
                    foreach($data['invoices'] as $invoice){
                        
                        if($invoice['slug']=='customer')
                        $paid_by_customer=$paid_by_customer+$invoice['paid_amount'];
                        
                        if($invoice['slug']=='venue_group')
                        $paid_by_venuegroup=$paid_by_venuegroup+$invoice['paid_amount'];
                        
                    }
                }

                $total_payment_received=$paid_by_customer+$paid_by_venuegroup;

                $due_payment=$total_booking_cost-$total_payment_received;
                // Grand Total Calculations
                $grand_total_cost=$grand_total_cost+$total_booking_cost;
                $grand_total_payment_recieved=$grand_total_payment_recieved+$total_payment_received;
                $grand_total_due_payment=$grand_total_due_payment+$due_payment;

                $grand_total_paid_by_customer=$grand_total_paid_by_customer+$paid_by_customer;
                $grand_total_paid_by_venuegroup=$grand_total_paid_by_venuegroup+$paid_by_venuegroup;

                $exportData[]=[
                    'customer_id'=>$data['user_id'],
                    'customer_firstname'=>$data['userinfo'][0]['firstname'],
                    'customer_lastname'=>$data['userinfo'][0]['lastname'],
                    'customer_email'=>$data['userinfo'][0]['email'],
                    'customer_phone'=>$data['userinfo'][0]['phone'],
                    'Booking ID'=>($data['booking']['id']),
                    'date_of_event'=>date(config('constants.date_formate'),$data['booking']['date_of_event']),
                    'pencile_by'=>pencilBy($data['booking']['pencile_by']),
                    'venue_group_name'=>$vg_name,
                    'venue_group_manager_name'=>$vg_manager_name,
                    'venue_group_phone'=>$vg_manager_phone,
                    'venue_group_city'=>$vg_city,
                    'photographer_name'=>$photographer_name,
                    'photographer_email'=>$photographer_email,
                    'photographer_phone'=>$photographer_phone,

                    'venue_group_to_pay'=>$data['booking']['venue_group_to_pay'],
                    'customer_to_pay'=>$data['booking']['customer_to_pay'],
                    'package_name'=>$data['booking']['package']['name'],
                    'package_price'=>$package_price,
                    'extra_price'=>$extra_price,
                    'over_time_cost'=>$over_time_cost,
                    'total_cost'=>$total_booking_cost,
                    'paid_by_customer'=>$paid_by_customer,
                    'paid_by_venuegroup'=>$paid_by_venuegroup,
                    'total_payment_received'=>$total_payment_received,
                    'due_payment'=>$due_payment,
                    
                ];
            }

            $exportData[]=[
                'customer_id'=>'',
                'customer_firstname'=>'',
                'customer_lastname'=>'',
                'customer_email'=>'',
                'customer_phone'=>'',
                'Booking ID'=>'',
                'date_of_event'=>'',
                'pencile_by'=>'',
                'venue_group_name'=>'',
                'venue_group_manager_name'=>'',
                'venue_group_phone'=>'',
                'venue_group_city'=>'',
                'photographer_name'=>'',
                'photographer_email'=>'',
                'photographer_phone'=>'',

                'venue_group_to_pay'=>'',
                'customer_to_pay'=>'',
                'package_name'=>'',
                'package_price'=>'',
                'extra_price'=>'',
                'over_time_cost'=>'Grand TOTAL',
                'total_cost'=>$grand_total_cost,
                'paid_by_customer'=>$grand_total_paid_by_customer,
                'paid_by_venuegroup'=>$grand_total_paid_by_venuegroup,
                'total_payment_received'=>$grand_total_payment_recieved,
                'due_payment'=>$grand_total_due_payment,
                
            ];
            
            //p($exportData); die;
           //if (ob_get_length() == 0 )
           $response = Excel::download(new ExportCustomerPayments($exportData), 'customer_payements.xls');
            ob_end_clean();
            return $response;
            
        }
     // Export Venue Expenses
        public function report_export_photographer_account($id,Request $req){
       
            $where_clause=[
                ['user_id', '=',$id],
            ];
            $with=['userinfo','booking'];

            $to=$from='';
            if(
                isset($req->from_date) &&
                !empty($req->from_date) &&
                isset($req->to_date) &&
                !empty($req->from_date)
            ){
                $from=strtotime($req->from_date);
                $to=strtotime($req->to_date);
               
            }

            
            $photographerData=bookings_users::joinRelationship('bookings', function ($join) use($from,$to) {
                $join->where(['is_active'=>1]);
                $join->where('bookings.status','>', config('constants.booking_status.pencil'));
                if($from!='' && $to=='')
                $join->WhereBetween('date_of_event', [$from, time()]);
                elseif($from!='' && $to!='')
                $join->WhereBetween('date_of_event', [$from, $to]);
                
                
            })
            ->with($with)
            ->where($where_clause)
            ->orderBy('created_at', 'desc')->get()->toArray();
// p($req->all());
 //p($photographerData); die;
             
             $exportData=array();
             $grand_total_expense=0;
            
             if(count($photographerData)>0)
            foreach($photographerData as $key=>$data){
                
               // p($data); die;

              

                $vg_name=$data['booking']['other_venue_group'];
                if(isset($data['userinfo']) && !empty($data['userinfo'])){
                    $vg_name=$data['booking']['venue_group']['userinfo'][0]['vg_name'];
                }
                $grand_total_expense= $grand_total_expense+$data['photographer_expense'];
                $exportData[]=[
                    'Booking ID'=>($data['booking']['id']),
                    'date_of_event'=>date(config('constants.date_formate'),$data['booking']['date_of_event']),
                    'venue_group_name'=>$vg_name,
                    'id'=>$data['id'],
                    'photographer_id'=>$data['user_id'],
                    'photographer_name'=>$data['userinfo'][0]['name'],
                    'total_expense'=>$data['photographer_expense'],
                ];
            }

            $exportData[]=[
                    'Booking ID'=>'',
                    'date_of_event'=>'',
                    'venue_group_name'=>'',
                    'id'=>'',
                    'photographer_id'=>'',
                    'photographer_name'=>'Grand Total',
                    'total_expense'=>$grand_total_expense,
                
            ];
            
           $response = Excel::download(new ExportPhotographerExpense($exportData), 'photographer-expense.xls');
            ob_end_clean();
            return $response;
            
        }
     // Export Venue Expenses
        public function report_export_venue_account($id,Request $req){
       
            $where_clause=[
                ['user_id', '=',$id],
            ];
            $with=['userinfo','booking'];

            $to=$from='';
            if(
                isset($req->from_date) &&
                !empty($req->from_date) &&
                isset($req->to_date) &&
                !empty($req->from_date)
            ){
                $from=strtotime($req->from_date);
                $to=strtotime($req->to_date);
               
            }

            
            $venueData=bookings_users::joinRelationship('bookings', function ($join) use($from,$to) {
                $join->where(['is_active'=>1]);
                $join->where('bookings.status','>', config('constants.booking_status.pencil'));
                if($from!='' && $to=='')
                $join->WhereBetween('date_of_event', [$from, time()]);
                elseif($from!='' && $to!='')
                $join->WhereBetween('date_of_event', [$from, $to]);
                
                
            })
            ->with($with)
            ->where($where_clause)
            ->orderBy('created_at', 'desc')->get()->toArray();
// p($req->all());
// p($venueData); die;
             
             $exportData=array();
             $grand_total_cost=$grand_total_payment_recieved=$grand_total_due_payment=0;
             $grand_total_paid_by_customer=$grand_total_paid_by_venuegroup=0;

            if(count($venueData)>0)
            foreach($venueData as $key=>$data){
                
               // p($data); die;

              

                $vg_name=$data['booking']['other_venue_group'];

                $vg_email=$vg_manager_name=$vg_manager_phone=$vg_city='';
                $photographer_name=$photographer_email=$photographer_phone='';

                if(isset($data['userinfo']) && !empty($data['userinfo'])){
                    $vg_name=$data['userinfo'][0]['vg_name'];
                    $vg_email=$data['userinfo'][0]['email'];
                    $vg_manager_name=$data['userinfo'][0]['vg_manager_name'];
                    $vg_manager_phone=$data['userinfo'][0]['vg_manager_phone'];
                    $vg_city=$data['userinfo'][0]['city'];
                }
                
                
                
                  
                 
                if(isset($data['booking']['photographer']) && !empty($data['booking']['photographer']) && $data['booking']['photographer']!=''){
                    
                    $photographer=$data['booking']['photographer'][0]['userinfo'][0];
                   // p($photographer);die; 

                    $photographer_name=$photographer['name'];
                    $photographer_email=$photographer['email'];
                    $photographer_phone=$photographer['phone'];
                    
                }
                

                $overtime_hours=0;
                if($data['booking']['overtime_hours']>0)
                $overtime_hours=$data['booking']['overtime_hours'];
                
                $overtime_rate_per_hour=0;
                if($data['booking']['overtime_rate_per_hour']>0)
                $overtime_rate_per_hour=$data['booking']['overtime_rate_per_hour'];
                
                // Booking Price
                $extra_price=0;
                if($data['booking']['extra_price']>0)
                $extra_price=$data['booking']['extra_price'];
                
                $over_time_cost=$overtime_rate_per_hour*$overtime_rate_per_hour;
                $package_price=$data['booking']['package']['price'];

                $total_booking_cost=$package_price+$extra_price+$over_time_cost;

                // Payment Received
                $paid_by_customer=$paid_by_venuegroup=0;
                
                if(!empty($data['invoices'])){
                    foreach($data['invoices'] as $invoice){
                        
                        if($invoice['slug']=='customer')
                        $paid_by_customer=$paid_by_customer+$invoice['paid_amount'];
                        
                        if($invoice['slug']=='venue_group')
                        $paid_by_venuegroup=$paid_by_venuegroup+$invoice['paid_amount'];
                        
                    }
                }

                $total_payment_received=$paid_by_customer+$paid_by_venuegroup;

                $due_payment=$total_booking_cost-$total_payment_received;
                // Grand Total Calculations
                $grand_total_cost=$grand_total_cost+$total_booking_cost;
                $grand_total_payment_recieved=$grand_total_payment_recieved+$total_payment_received;
                $grand_total_due_payment=$grand_total_due_payment+$due_payment;

                $grand_total_paid_by_customer=$grand_total_paid_by_customer+$paid_by_customer;
                $grand_total_paid_by_venuegroup=$grand_total_paid_by_venuegroup+$paid_by_venuegroup;

                $exportData[]=[
                    //'customer_id'=>$data['booking']['customer']['user_id'],
                    'customer_name'=>$data['booking']['customer']['userinfo'][0]['name'],
                    //'customer_lastname'=>$data['booking']['customer']['userinfo'][0]['lastname'],
                    // 'customer_email'=>$data['booking']['customer']['userinfo'][0]['email'],
                    // 'customer_phone'=>$data['booking']['customer']['userinfo'][0]['phone'],
                    //'Booking ID'=>($data['booking']['id']),
                    'date_of_event'=>date(config('constants.date_formate'),$data['booking']['date_of_event']),
                    // 'pencile_by'=>pencilBy($data['booking']['pencile_by']),
                    'venue_group_name'=>$vg_name,
                    // 'venue_group_manager_name'=>$vg_manager_name,
                    // 'venue_group_phone'=>$vg_manager_phone,
                    // 'venue_group_city'=>$vg_city,
                    // 'photographer_name'=>$photographer_name,
                    // 'photographer_email'=>$photographer_email,
                    // 'photographer_phone'=>$photographer_phone,

                    //'venue_group_to_pay'=>$data['booking']['venue_group_to_pay'],
                    //'customer_to_pay'=>$data['booking']['customer_to_pay'],
                    'package_name'=>$data['booking']['package']['name'],
                    'package_price'=>$package_price,
                    //'extra_price'=>$extra_price,
                    //'over_time_cost'=>$over_time_cost,
                    'total_cost'=>$total_booking_cost,
                    //'paid_by_customer'=>$paid_by_customer,
                    //'paid_by_venuegroup'=>$paid_by_venuegroup,
                    'total_payment_received'=>$total_payment_received,
                    'due_payment'=>$due_payment,
                    
                ];
            }

            $exportData[]=[
                //'customer_id'=>'',
                'customer_name'=>'',
                //'customer_lastname'=>'',
                // 'customer_email'=>'',
                // 'customer_phone'=>'',
                //'Booking ID'=>'',
                'date_of_event'=>'',
               //'pencile_by'=>'',
                'venue_group_name'=>'',
                // 'venue_group_manager_name'=>'',
                // 'venue_group_phone'=>'',
                // 'venue_group_city'=>'',
                // 'photographer_name'=>'',
                // 'photographer_email'=>'',
                // 'photographer_phone'=>'',

                //'venue_group_to_pay'=>'',
                //'customer_to_pay'=>'',
                'package_name'=>'',
                'package_price'=>'',
                //'extra_price'=>'',
                //'over_time_cost'=>'Grand TOTAL',
                'total_cost'=>$grand_total_cost,
                //'paid_by_customer'=>$grand_total_paid_by_customer,
               //'paid_by_venuegroup'=>$grand_total_paid_by_venuegroup,
                'total_payment_received'=>$grand_total_payment_recieved,
                'due_payment'=>$grand_total_due_payment,
                
            ];
            
           // p($exportData); die;
           //if (ob_get_length() == 0 )
           $response = Excel::download(new ExportCustomerPayments($exportData), 'venue_payements.xls');
            ob_end_clean();
            return $response;
            
        }
     public function report_bookings(Request $req){
        $user=Auth::user();
        if($user->group_id!=config('constants.groups.admin'))
        abort(403, sprintf('Only ADMIN is allowed')); 
        
        $pagination_per_page=config('constants.per_page');
        // if(isset($req->action) && $req->action=='search_form')
        // $pagination_per_page=200;
        
        $where_in_clause= $customers_id=$bookings_status=$venue_groups_id=$photographers_id=array();

 
        if(isset($req->photographers_id) && !empty($req->photographers_id)){
            $photographers_id=$req->photographers_id;
            $where_in_clause['user_id']=$photographers_id;
        }
        if(isset($req->venue_groups_id) && !empty($req->venue_groups_id)){
            $venue_groups_id=$req->venue_groups_id;
            $where_in_clause['user_id']=$venue_groups_id;
        }

        if(isset($req->customers_id) && !empty($req->customers_id)){
            $customers_id=$req->customers_id;
            }
       
        
//p($bookings_status); die;
        $bookings=bookings_users::joinRelationship('bookings', function ($join) {
            //global $bookings_status;
            $join->where(['is_active'=>1]);
            //$join->whereIn('bookings.status',$bookings_status );
            //$join->whereIn('bookings.customer_id',$customers_id);
        })
        
        ->with('userinfo')
        ->with('pencils')
        ->orderBy('created_at', 'desc');

        if(isset($req->bookings_status) && !empty($req->bookings_status)){
            $bookings_status=$req->bookings_status;
            $bookings->whereIn('bookings.status',$bookings_status );
        }
        foreach($where_in_clause as $column => $values){
                
            $bookings = $bookings->whereIn($column, $values);
        }

        // p($req->all());
        // p($where_in_clause); die;
        if($req->export=='export_xls')
            {
             
                $quotesData=$quotes->get()->toArray();
                $exportData=array();
                foreach($quotesData as $key=>$data){
                    $elevator='Not Available';
                    if($data['elevator']==1)
                    $elevator='Available';
                    $exportData[]=[
                        'id'=>$data['id'],
                        'po_number'=>$data['po_number'],
                        'quote_type'=>$data['quote_type'],
                        'business_type'=>$data['business_type'],
                        'elevator'=>$elevator,
                        'no_of_appartments'=>$data['no_of_appartments'],
                        'list_of_floors'=>(isset($data['list_of_floors']) && $data['list_of_floors']!='null')?implode(',',json_decode($data['list_of_floors'],true)):'',
                        'pickup_street_address'=>$data['pickup_street_address'],
                        'pickup_unit'=>$data['pickup_unit'],
                        'pickup_state'=>$data['pickup_state'],
                        'pickup_city'=>$data['pickup_city'],
                        'pickup_zipcode'=>$data['pickup_zipcode'],
                        'pickup_contact_number'=>$data['pickup_contact_number'],
                        'pickup_date'=>$data['pickup_date'],
                        'drop_off_street_address'=>$data['drop_off_street_address'],
                        'drop_off_unit'=>$data['drop_off_unit'],
                        'drop_off_city'=>$data['drop_off_city'],
                        'drop_off_zipcode'=>$data['drop_off_zipcode'],
                        'drop_off_contact_number'=>$data['drop_off_contact_number'],
                        'drop_off_date'=>$data['drop_off_date'],
                        'drop_off_instructions'=>$data['drop_off_instructions'],
                        'status'=>quote_status_msg($data['status']),
                        'customer_name'=>$data['customer']['name'],
                        'customer_email'=>$data['customer']['email'],
                        'customer_mobileno'=>$data['customer']['mobileno'],
                        'customer_business_name'=>$data['customer']['business_name'],
                        'driver_name'=>(isset($data['driver']['name']) && !empty($data['driver']['name']))?$data['driver']['name']:'',
                        'driver_email'=>(isset($data['driver']['email']) && !empty($data['driver']['email']))?$data['driver']['email']:'',
                        'driver_mobileno'=>(isset($data['driver']['mobileno']) && !empty($data['driver']['mobileno']))?$data['driver']['mobileno']:'',
                        'driver_license_no'=>(isset($data['driver']['license_no']) && !empty($data['driver']['license_no']))?$data['driver']['license_no']:'',
                        
                    ];
                }
                return Excel::download(new ExportQuotes($exportData), 'quotes-deliveries.xlsx');
            }
            else{
                $pencilData=$bookings->paginate($pagination_per_page);
                //$pencilData=$bookings->get()->toArray();
            }
            
            
            
                return view('adminpanel.reports_bookings',get_defined_vars());
       
    }
    
    
    public function booking_galleries(Request $req){
        $user=Auth::user();
        $booking_title='View Bookings';
        $type='all';
        $pencilData=bookings_users::joinRelationship('bookings', function ($join) {
            $join->where(['is_active'=>1]);
            $join->where('bookings.status','>', config('constants.booking_status.pencil'));
        })
        ->with('userinfo')
        ->with('pencils')
        ->where('user_id',get_session_value('id'))
        ->orderBy('created_at', 'desc')->get();
        
            return view('adminpanel.user_bookings_gallery',get_defined_vars());

    }
     // Booking Section
      // List All the Pencils 
    public function bookings($type=NULL, Request $req){
       
        $booking_title='View Bookings';
        $route='bookings.type';
        $user=Auth::user();

        $where_in_clause= $customers_id=$bookings_status=$venue_groups_id=$photographers_id=array();

 
        if(isset($req->photographers_id) && !empty($req->photographers_id)){
            $photographers_id=$req->photographers_id;
           // p($photographers_id);
           $photographer_bookings= $this->bookings_users->whereIn('user_id',$photographers_id)->where('slug','new_photographer_assigned')->get('booking_id')->toArray();
           $booking_ids=[];
           foreach($photographer_bookings as $data){
            $booking_ids[]=$data['booking_id']; 
           }
           //$booking_ids=(array_unique($booking_ids));
           //p($photographer_bookings);
            //die;
            $where_in_clause['id']=$booking_ids;
        }
        if(isset($req->venue_groups_id) && !empty($req->venue_groups_id)){
            $venue_groups_id=$req->venue_groups_id;
            $where_in_clause['venue_group_id']=$venue_groups_id;
        }

        if(isset($req->customers_id) && !empty($req->customers_id)){
            $customers_id=$req->customers_id;
            $where_in_clause['customer_id']=$customers_id;
            }



        if($user->group_id==config('constants.groups.admin')){
             // Where Clause...   
        $where['is_active']=1;
        //$where['status']=config('constants.booking_status.pencil');
        
        if($type=='trashed'){
            $where['is_active']=2;
            $booking_title='View Trash Bookings';
        }elseif($type=='office'){
            $where['pencile_by']=config('constants.pencileBy.office');
            $booking_title='View Office Bookings';
        }elseif($type=='venue_group'){
            $where['pencile_by']=config('constants.pencileBy.venue_group');
            $booking_title='View Hall Bookings';
        }elseif($type=='web'){
            $where['pencile_by']=config('constants.pencileBy.website');
            $booking_title='View Web Bookings';
        }


        $pencil_sql=$this->bookings
                ->with('customer')
                ->with('photographer')
                ->with('venue_group')
                ->where('status','>', config('constants.booking_status.pencil') )
                ->where($where) 
                ->orderBy('created_at', 'desc');

                if(
                    isset($req->from_date) &&
                    !empty($req->from_date) &&
                    isset($req->to_date) &&
                    !empty($req->from_date)
                ){
                    $from=strtotime($req->from_date);
                    $to=strtotime($req->to_date);
                    
                    $pencil_sql=$pencil_sql->WhereBetween('date_of_event', [$from, $to]);
                }

                foreach($where_in_clause as $column => $values){
                
                    $pencil_sql = $pencil_sql->whereIn($column, $values);
                }

            //     p($where_in_clause);
            //  echo config('constants.booking_status.pencil');              
            // p($where);
            //echo $pencilData=$pencil_sql->toSql(); die;
                $pencilData=$pencil_sql->paginate(config('constants.per_page'));
                

        }
        else{

            // From and To Date
            $to=$from='';
            if(
                isset($req->from_date) &&
                !empty($req->from_date) &&
                isset($req->to_date) &&
                !empty($req->from_date)
            ){
                $from=strtotime($req->from_date);
                $to=strtotime($req->to_date);
                
                //$pencil_sql=$pencil_sql->WhereBetween('date_of_event', [$from, $to]);
            }

           
            // $where_user_clause=[
            //     ['group_id','=',config('constants.groups.customer')],
            //     ['group_id','=',config('constants.groups.venue_group_hod')],
            // ];
            
            $pencilData=bookings_users::joinRelationship('bookings', function ($join) use($from,$to,$where_in_clause) {
                $join->where(['is_active'=>1]);
                $join->where('bookings.status','>', config('constants.booking_status.pencil'));
                if($from!='' && $to=='')
                $join->WhereBetween('date_of_event', [$from, time()]);
                elseif($from!='' && $to!='')
                $join->WhereBetween('date_of_event', [$from, $to]);

                if(!empty($where_in_clause))
                foreach($where_in_clause as $column => $values){
                
                    $pencil_sql = $join->whereIn($column, $values);
                }
                
                
            })
            ->with('userinfo')
            ->with('pencils')
            ->where('user_id',get_session_value('id'))
            // ->where(function($query) use($search_val)
            //     {
            //         $query->where('name', 'like', '%' . $search_val . '%')
            //         ->orwhere('vg_name', 'like', '%' . $search_val . '%')
            //         ->orwhere('email', 'like', '%' . $search_val . '%');
            //     })
            ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
            if($user->group_id==config('constants.groups.customer'))
                return view('adminpanel.customer_bookings',get_defined_vars());
            else if($user->group_id==config('constants.groups.venue_group_hod'))
            return view('adminpanel.venue_bookings',get_defined_vars());

                return view('adminpanel.user_bookings',get_defined_vars());
        }
      //  if($user->group_id== config('constants.groups.admin'))
            
        return view('adminpanel.bookings',get_defined_vars());
    }
    public function bookings_awaited(Request $req){
        $user=Auth::user();
        $booking_title='Awaited Bookings';
        $route='bookings.awaited';
        $status_column='Collect Payment';
        

            // From and To Date
            $to=$from='';
            if(
                isset($req->from_date) &&
                !empty($req->from_date) &&
                isset($req->to_date) &&
                !empty($req->from_date)
            ){
                $from=strtotime($req->from_date);
                $to=strtotime($req->to_date);
                
                //$pencil_sql=$pencil_sql->WhereBetween('date_of_event', [$from, $to]);
            }
            $where_clause=[
                ['user_id', '=', get_session_value('id')],
                ['bookings_users.status', '=', 0],
            ];

        $pencilData=bookings_users::joinRelationship('bookings', function ($join) use($from,$to) {
            $join->where(['is_active'=>1]);
            $join->where('bookings.status','>=', config('constants.booking_status.awaiting_for_photographer'));
            //$join->where('bookings.status','<', config('constants.booking_status.confirmed'));
           
            if($from!='' && $to=='')
            $join->WhereBetween('date_of_event', [$from, time()]);
            elseif($from!='' && $to!='')
            $join->WhereBetween('date_of_event', [$from, $to]);

        })
        ->with(['userinfo','pencils'])
        ->where($where_clause)
        ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
        
            return view('adminpanel.awaited_sche_bookings',get_defined_vars());
    }
    public function bookings_scheduled(Request $req){
        $user=Auth::user();
        $booking_title='Scheduled Bookings';
        $route='bookings.scheduled';
        $status_column='Collect Payment';
        // From and To Date
        $to=$from='';
        if(
            isset($req->from_date) &&
            !empty($req->from_date) &&
            isset($req->to_date) &&
            !empty($req->from_date)
        ){
            $from=strtotime($req->from_date);
            $to=strtotime($req->to_date);
            
        }
        $where_clause=[
            ['user_id', '=', get_session_value('id')],
            ['bookings_users.status', '=', 1],
        ];

        $pencilData=bookings_users::joinRelationship('bookings', function ($join) use($from,$to) {
            $join->where(['is_active'=>1]);
            $join->where('bookings.status','>=', config('constants.booking_status.awaiting_for_photographer'));
            //$join->where('bookings.status','>', config('constants.booking_status.confirmed'));
            //$join->where('bookings.photographer_status','=', config('constants.booking_status.confirmed'));

            if($from!='' && $to=='')
            $join->WhereBetween('date_of_event', [$from, time()]);
            elseif($from!='' && $to!='')
            $join->WhereBetween('date_of_event', [$from, $to]);
        })
        ->with(['userinfo','pencils'])
        ->where($where_clause)
        ->orderBy('created_at', 'desc')->paginate(config('constants.per_page'));
       // echo $pencilData->toSql(); die;
            return view('adminpanel.awaited_sche_bookings',get_defined_vars());
    }
    public function calender_schedule(){
        $user=Auth::user();
        if($user->group_id!=config('constants.groups.photographer')){
          
            $where_clause=[
                ['status','>',config('constants.booking_status.pencil')],
                ['is_active','=',1],
            ];

            if($user->group_id==config('constants.groups.customer'))
            $where_clause[]=['customer_id','=',$user->id];
            elseif($user->group_id==config('constants.groups.venue_group_hod'))
            $where_clause[]=['venue_group_id','=',$user->id];

                $bookingData=$this->bookings
                ->with(['customer','photographer','venue_group'])
                ->where($where_clause) 
                ->orderBy('created_at', 'desc')->get()->toArray();
                
    
        }
        else{

            $where_clause=[
                ['status','=',1],
                ['user_id','=',$user->id],
            ];

                $bookingData=$this->bookings_users
                ->with(['userinfo','bookings'])
                ->where($where_clause)  
                ->orderBy('created_at', 'desc')->get()->toArray();
            
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
                    $request->session()->flash('alert-success', 'Thank you, Your Booking has been confirmed');
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
            //p($data);
            if(isset($data['external_link_token']) && Hash::check($req['token'], $data['external_link_token'])){
                
                
                $this->bookings->where('id',$id)
                //->where('customer_approved',0)
                ->update(array('customer_approved'=>1,'external_link_token'=>NULL));
                
                $vg_name='';
                if(!empty($data['venue_group']))
                $vg_name=$data['venue_group']['userinfo'][0]['vg_name'];
                else if(!empty($data['other_venue_group']))
                $vg_name=$data['other_venue_group'];
                
               $mailData['body_message']='Customer '.$data['customer']['userinfo'][0]['name'].' has confirmed and approved a booking for '.date(config('constants.date_formate'),$data['date_of_event']).' in '.$vg_name;
               
               $mailData['subject']='A booking has been confirmed';
               $emailAdd=[
                config('constants.admin_email'),
                    ];
                $emailAdd=get_users_email_address([],$emailAdd);

                if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
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
         // Approval from the customer
         public function customer_reject($id, Request $req){
            $data=$this->bookings->with('customer')->with('venue_group')->where('id',$id)->get()->toArray();
            $data=$data[0];
            //p($data);
            if(isset($data['external_link_token']) && Hash::check($req['token'], $data['external_link_token'])){
                
                
                $this->bookings->where('id',$id)
                //->where('customer_approved',0)
                ->update(array('customer_approved'=>2,'external_link_token'=>NULL));
                
                $vg_name='';
                if(!empty($data['venue_group']))
                $vg_name=$data['venue_group']['userinfo'][0]['vg_name'];
                else if(!empty($data['other_venue_group']))
                $vg_name=$data['other_venue_group'];
                
               $mailData['body_message']='This email is to let you know that Customer '.$data['customer']['userinfo'][0]['name'].' has cancelled/rejected a booking for '.date(config('constants.date_formate'),$data['date_of_event']).' in '.$vg_name;
               
               $mailData['subject']='A booking has been Cancelled';
                $emailAdd=[
                    config('constants.admin_email'),
                        ];
                $emailAdd=get_users_email_address([],$emailAdd);
                if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                    echo 'Your Booking has been Cancelled';
                }
                
                        //activity Logged
                $activityComment='Mr.'.$data['customer']['userinfo'][0]['name'].' Cancelled Booking of date '.date(config('constants.date_formate'),$data['date_of_event']);
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
            $status=config('constants.photographer_assigned.declined');
            $activityComment='Photographer Declined the Invitation';
            $msg= 'we have canceled this booking for you and a new photographer will be assigned';
            $actionMsg='declined';
            if($action=='accept'){
                $status=config('constants.photographer_assigned.accepted');
                $actionMsg='approved';
                $msg="Thank you, you have been confirmed";
                $activityComment='Photographer accepted the Invitation';
            }
            
            $updated=$this->bookings_users->where('booking_id',$booking_id)->where('user_id',$photographer_id)->where('status',0)->update(array('status'=>$status));
            
            $get_booking_status=get_booking_status($booking_id);
            $data_to_update_booking['status']=$get_booking_status;

            // echo '<br>booking_id:'.$booking_id;
            // echo '<br>booking_Status:'. $get_booking_status;
            // echo '<br>ok:'.config('constants.booking_status.pending_customer_agreement');


            if($get_booking_status==config('constants.booking_status.pending_customer_agreement')){
                $data_to_update_booking['photographer_status']=config('constants.photographer_assigned.accepted');
            }else{
                $data_to_update_booking['photographer_status']=config('constants.photographer_assigned.awaiting');
            }
            

            $this->bookings->where('id',$booking_id)->update($data_to_update_booking);
          
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
        
        $booking_before_updation=$this->bookings->where('id',$id)->first();
        
        //  p($booking_before_updation); die;

        $bookingData['date_of_event']=strtotime($request['date_of_event']);
        $bookingData['time_of_event']=$request['time_of_event'];
        $bookingData['package_id']=$request['package_id'];
        
        // Other Venue Group Section
        $bookingData['other_venue_group']='';
        if(isset($request['other_venue_group']) && !empty($request['other_venue_group']))
        $bookingData['other_venue_group']=$request['other_venue_group'];

        $bookingData['who_is_paying']=$request['who_is_paying'];
        $bookingData['paying_via']=$request['paying_via'];
        $bookingData['collected_by_photographer']=$request['collected_by_photographer'];
        $bookingData['photographer_to_collect_amount']=$request['photographer_to_collect_amount'];
        $bookingData['photographer_payee_name']=$request['photographer_payee_name'];
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
        
        
        //$bookingData_to_update['other_venue_group']='';
        if(isset($request['other_venue_group']) && !empty($request['other_venue_group'])){
            $bookingData_to_update['other_venue_group']=$request['other_venue_group'];
            $bookingData['other_venue_group']=$request['other_venue_group'];
            $this->bookings_users->where(['booking_id'=>$id,'slug'=>'new_venue_group','group_id'=>config('constants.groups.venue_group_hod')])->delete();
        }
        else{
            $bookingData['venue_group_id']=$request['venue_group_id'];
        }
        // Booking data updated
       
        $this->bookings->where('id',$id)->update($bookingData);
        //echo 'id :'. $id;
        //p( $bookingData); die;

        // Venue Group Added to Booking
        //echo $request['selected_venue_group_id'].'!='.$request['venue_group_id'];

        if($request['selected_venue_group_id']!=$request['venue_group_id'])
        if((isset($request['venue_group_id']) && $request['venue_group_id']>0 && !isset($request['other_venue_group']))){
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
                    
                    $bookingsMailData=$this->bookings->with(['customer','photographer','package'])->with('venue_group')->with('photographer')->where('id',$id)->get()->toArray();
                    $bookingsMailData=$bookingsMailData[0];

                    $welcomMes='<div style="text=align:justify">You have been assigned to a new booking by kleins photography.<br></div>';
                    $mailData['body_message']=$welcomMes.booking_email_body($bookingsMailData,false);
  
                  
                        $mailData['subject']='you have a new event to confirm';
                        $mailData['button_title']='APPROVE';
                        $mailData['button_link']=route('photograppher.action',['booking_id' => $id,'photographer_id'=>$value,'action'=>base64_encode('accept')]);
                        $mailData['button_title2']='Reject';
                        $mailData['button_link2']=route('photograppher.action',['booking_id' => $id,'photographer_id'=>$value,'action'=>base64_encode('reject')]);
                       
                        $photgrapherEmail=$this->users->where('id',$value)->get('email')->toArray();
                        $toEmail=$photgrapherEmail[0]['email'];
                        
        
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
        
     // Send Email when add to Booking
     // Get All Booking Data
     $bookingsMailData=$this->bookings->with(['customer','photographer','package'])->with('venue_group')->with('photographer')->where('id',$id)->get()->toArray();
     $bookingsMailData=$bookingsMailData[0];

     if($booking_before_updation->status==config('constants.booking_status.pencil')){
     
    //  $booking=$this->bookings
    //  ->where('id',$id)
    //  ->where('status',config('constants.booking_status.awaiting_for_photographer'))
    //  ->orderBy('created_at', 'desc')->get(array('id','status','groom_name','date_of_event'))->toArray();
     
    
     //if(count($booking)>0)
     {
         $welcomMes='<div style="text=align:justify">A booking has been created for you. We have started a booking for your event.
         To confirm the booking please confirm that all the information below is correct and click the Approve button below.<br></div>';
         $mailData['body_message']=$welcomMes.booking_email_body($bookingsMailData);
             $token=sha1(time());
             $booking_data_link['external_link_token']=Hash::make($token);
             $this->bookings->where('id',$id)->update($booking_data_link);
             $mailData['subject']='Welcome to Klein\'s photography';
             $mailData['button_title']='Accept';
             $mailData['button_link']=route('customer.approve',['id' => $id,'token'=>$token]);
             $mailData['button_title2']='Reject';
             $mailData['button_link2']=route('customer.reject',['id' => $id,'token'=>$token]);
                       
             

             $toEmail=[
                $request['customer_email']
            ];

             if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
                 $request->session()->flash('alert-success', 'Please check your email');
             }
     }
    }
     // If Time of the event changed then send Email to all the users
     if($request['time_of_event']!=$booking_before_updation->time_of_event){

      
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
        // if($user->group_id==config('constants.group'))
        // return false;

        $bookingData=$this->bookings
        ->with(['customer','photographer','venue_group','invoices','gallery','comments','photographer_comments','deposite_requests'])
        ->where('id',$id)
        ->orderBy('created_at', 'desc')->get()->toArray();
        $bookingData=$bookingData[0];

        $assigne_photographers=$this->bookings_users->with('userinfo')->where('booking_id',$id)->where('group_id',config('constants.groups.photographer'))->get()->toArray();
        

        return view('adminpanel.upload_photos_booking',get_defined_vars());
}

public function add_photos($id,Request $request){
     // create the file receiver
     $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

     // check if the upload is success, throw exception or return response you need
     if ($receiver->isUploaded() === false) {
       throw new UploadMissingFileException();
     }
  
     // receive the file
     $save = $receiver->receive();
  
     // check if the upload has finished (in chunk mode it will send smaller files)
     if ($save->isFinished()) {
       // save the file and return any response you need, current example uses `move` function. If you are
       // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
       return $this->saveFile($save->getFile(), $request);
     }
  
     // we are in chunk mode, lets send the current progress
     /** @var AbstractHandler $handler */
     $handler = $save->handler();
  
     return response()->json([
       "done" => $handler->getPercentageDone(),
       'status' => true
     ]);
    //return response()->json(['success'=>'i am here']);
    // $user=Auth::user();
        $image = $request->file('file');
        $imageExt=$image->extension();
        $imageName = time().'.'.$imageExt;

        $uploadingPath=base_path().'/public/uploads/bookings'.$id;
        if(base_path()!='/Users/waximarshad/office.thephotographicmemories.com')
        $uploadingPath=base_path().'/public_html/uploads/bookings'.$id;

        
    
 }

 public function saveFile(UploadedFile $file, Request $request) {
    $user_obj = auth()->user();

        
    $fileName = $this->createFilename($file);

    // Get file mime type
    $mime_original = $file->getMimeType();
    $mime = str_replace('/', '-', $mime_original);

    $folderDATE = $request->dataDATE;
    $booking_id = $request->booking_id;

    $folder  = $folderDATE;
    // $filePath=base_path().'/public/uploads/mediagallary/bookings'.$booking_id;
    // if(base_path()!='/Users/waximarshad/office.thephotographicmemories.com')
    // $filePath=base_path().'/public_html/uploads/mediagallary/bookings'.$booking_id;

    $filePath = "public/upload/mediagallary/{$booking_id}/{$folder}/";
    if(base_path()!='/Users/waximarshad/office.thephotographicmemories.com')
    $filePath = "public/upload/mediagallary/{$booking_id}/{$folder}/";

    
    //$finalPath = storage_path("app/".$filePath);
    //$finalPath= public_path('uploads/bookings'.$booking_id);
    $finalPath=base_path().'/public/uploads/bookings'.$booking_id;
    if(base_path()!='/Users/waximarshad/office.thephotographicmemories.com')
    $finalPath=base_path().'/public_html/uploads/bookings'.$booking_id;

    $fileSize = $file->getSize();
    // move the file name
    $file->move($finalPath, $fileName);

    $url_base = $filePath."/".$fileName;
    
    $imageExt = $file->getClientOriginalExtension();
    $orginalImageName= $file->getClientOriginalName();
   
        $this->files->name=$orginalImageName;
        $this->files->file_name=phpslug($fileName);
        $this->files->slug=phpslug('booking_photos');
        $this->files->path=url('uploads/bookings'.$booking_id).'/'.$fileName;
        $this->files->description=$orginalImageName;
        $this->files->otherinfo=$imageExt;
        $this->files->booking_id=$booking_id;
        $this->files->uploaded_by=get_session_value('id');
        $this->files->save();

    // create txt file
    $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
    $txt = json_encode($request)."\n";
    $txt .= 'file path:'.($filePath)."\n";
    $txt .= 'booking_id:'.($booking_id)."\n";
    $txt .= 'finalPath:'.($finalPath)."\n";
    //$txt .= 'file name:'.($fileName)."\n";
    //$txt .= 'file mime_type:'.($mime_type)."\n";
    fwrite($myfile, $txt);
    $txt .= 'file name:'.($fileName)."\n";
    //$txt .= 'file mime_type:'.($mime_type)."\n";
    fwrite($myfile, $txt);
    fclose($myfile);

    return response()->json([
     'path' => $filePath,
     'name' => $fileName,
     'mime_type' => $mime
    ]);
 }

 /**
  * Create unique filename for uploaded file
  * @param UploadedFile $file
  * @return string
  */
  protected function createFilename(UploadedFile $file) {
    $extension = $file->getClientOriginalExtension();
    //$filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension

    //delete timestamp from file name
    // $temp_arr = explode('_', $filename);
    // if ( isset($temp_arr[0]) ) unset($temp_arr[0]);
    // $filename = implode('_', $temp_arr);

    //here you can manipulate with file name e.g. HASHED
    return time().".".$extension;
    return $filename.".".$extension;
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
        $user=Auth::user(); 
        $dataArray['error']='No';
        $dataArray['title']='Action Taken';
        
        if(!isset($req['action']))
        {
            $dataArray['error']='Yes';
           // $dataArray['response']='All Doone';
            $dataArray['msg']='There is some error ! Please try again later!.';
            echo json_encode($dataArray);
            die;
        }
        
        if(isset($req['action']) && $req['action']=='qsearch_pencil'){ 

            $search_val=$req['qsearch'];
            $type=$req['type'];
            // $pencilData=$this->users->map(function ($user) {
            //     return (object) $user->only(['id']);
            // }); 
            
            $where_user_clause=[];
            // $where_user_clause=[
            //     ['group_id','=',config('constants.groups.customer')],
            //     ['group_id','=',config('constants.groups.venue_group_hod')],
            // ];
            
        $pencilData=$this->users
                ->where($where_user_clause)
                ->where(function($query) use($search_val)
                {
                    $query->where('name', 'like', '%' . $search_val . '%')
                    ->orwhere('vg_name', 'like', '%' . $search_val . '%')
                    ->orwhere('email', 'like', '%' . $search_val . '%');
                })
            ->orderBy('created_at', 'desc')->get('id');
            $user_ids=[];
            foreach($pencilData as $data){
                $user_ids[]=$data->id;
            }
            //p($user_ids); die;

             $pencilData=bookings_users::joinRelationship('bookings', function ($join) use($type) {

                $where['is_active']=1;
                $where['bookings.status']=config('constants.booking_status.pencil');

                if($type=='office')
                $where['pencile_by']=config('constants.pencileBy.office');
                elseif($type=='venue_group')
                $where['pencile_by']=config('constants.pencileBy.venue_group');
                elseif($type=='web')
                $where['pencile_by']=config('constants.pencileBy.website');
                elseif($type=='customer')
                $where['pencile_by']=config('constants.pencileBy.customer');
                //p($where);
                //$join->where(['is_active'=>1,'bookings.status'=>config('constants.booking_status.pencil')])->distinct();
                $join->where($where)->distinct();
            })
            ->with('userinfo')
            ->with('pencils')
            //->distinct('booking_id')
            ->whereIn('user_id',$user_ids)
            ->orderBy('created_at', 'desc')->get();
            
            //echo $pencilData->toSql();
            
            //p($pencilData); die;
            $response='<thead>
                            <tr>
                                <th>Event Date</th>
                                <th>Venue Group</th>
                                <th>Customer</th>
                                <th>By</th>
                                <th>Groom Name (Mobile)</th>
                                <th>Groom Billing Address</th>
                                <th>Bride Name (Mobile)</th>
                                <th>Bride Billing Address</th>
                                
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>';
                        $counter = 1;
               
                        foreach ($pencilData as $data){
                            foreach ($data->pencils as $pencil){
                                $response .='<tr id="row_'.$pencil['id'].'">
                                            <td id="date_of_event_'.$pencil['id'].'">'.date(config('constants.date_formate'),$pencil['date_of_event']).'</td>
                                            <td id="venue_group_'.$pencil['id'].'">';
                                               if (isset($pencil['venue_group']))
                                               $response .=$pencil['venue_group']['userinfo'][0]['vg_name'].'</td>';
                                               else
                                               $response .=$pencil['other_venue_group'].'</td>';

                                            $response .= '<td id="customer_name_'.$pencil['id'].'">
                                            '.$pencil['customer']['userinfo'][0]['name'].'</td>
                                            <td id="photographer_name_'.$pencil['id'].'">'.pencilBy($pencil['pencile_by']).'</td>
                                            <td><strong id="groom_name_'.$pencil['id'].'">'.$pencil['groom_name'].' ('.$pencil['groom_mobile'].')</strong></td>
                                            <td id="groom_billing_address_'.$pencil['id'].'">'.$pencil['groom_billing_address'].'
                                            </td>
                                            
                                            <td><strong id="bride_name_'.$pencil['id'].'">'.$pencil['bride_name'].' ('.$pencil['bride_mobile'].')</strong></td>
                                            <td id="bride_billing_address_'.$pencil['id'].'">'.$pencil['bride_billing_address'].'
                                            </td>
                                           ';
                                               
                                               
                                        $response .='<td id="photographer_name_'.$pencil['id'].'"> '.booking_status($pencil['status']).'-'.$pencil['id'].'</td>

                                            <td>';
                                                    $response .='<a href="'.route('pencil.view',$pencil['id']).'" 
                                                    class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i>
                                                    View</a>';

                                                    if($user->group_id==config('constants.groups.admin')){

                                                        $response .=' <a href="'.route('bookings.bookings_form',$pencil['id']).'" class="btn btn-success btn-block btn-sm"><i class="fas fa-plus"></i>
                                                        Booking</a>';
                                                        $response .=' <a href="'.route('pencils.pencils_edit_form',$pencil['id']).'" class="btn btn-info btn-block btn-sm"><i class="fas fa-edit"></i>
                                                    Edit</a>';
                                                        
                                                    
                                                    if ($pencil['is_active']==1){
                                                        $response .=' <button
                                                        onClick="do_action('.$pencil['id'].',\'trash_booking\')"
                                                            type="button" class="btn btn-danger btn-block btn-sm"><i
                                                                class="fas fa-trash"></i>
                                                            Delete</button>';

                                                    }elseif ($pencil['is_active']==2){
                                                        $response .=' <button
                                                        onClick="do_action('.$pencil['id'].',\'restore_booking\')"
                                                    type="button" class="btn btn-warning btn-block btn-sm"><i
                                                        class="fas fa-undo"></i>
                                                    Restore</button>';
                                                    }

                                                }
                                                 
                                                 
                                                
                                $response .=' </td></tr>';

                            }
                            $counter++;
                        }
                                                    
                        $dataArray['response']=$response;

        }
        if(isset($req['action']) && $req['action']=='qsearch_bookings'){ 

            $search_val=$req['qsearch'];
            
            // $pencilData=$this->users->map(function ($user) {
            //     return (object) $user->only(['id']);
            // }); 
            
            
        $pencilData=$this->users
             ->where('name', 'like', '%' . $search_val . '%')
             ->orwhere('vg_name', 'like', '%' . $search_val . '%')
             ->orwhere('email', 'like', '%' . $search_val . '%')
            //->with('userinfo')
            //->distinct('bookings')
            ->orderBy('created_at', 'desc')->get('id');
            $user_ids=[];
            foreach($pencilData as $data){
                $user_ids[]=$data->id;
            }
            
            //p($user_ids); die;

             $pencilData=bookings_users::joinRelationship('bookings', function ($join) {
                $where_clause=[
                    ['bookings.status', '>', config('constants.booking_status.pencil')],
                    //['bookings.status', '<', config('constants.booking_status.complete')],
                    ['is_active', '=', 1],
                ];
                
                //p($where_clause); die;
                //$join->where(['is_active'=>1,'bookings.status'=>config('constants.booking_status.pencil')])->distinct();
                $join->where($where_clause)->distinct();
            })
            ->with('userinfo')
            ->with('pencils')
            //->distinct('booking_id')
            ->whereIn('user_id',$user_ids)
            ->orderBy('created_at', 'desc')->get();
            
            //echo $pencilData->toSql();
            
            //p($pencilData); die;
            $response='<thead>
                            <tr>
                                <th>Event Date</th>
                                <th>Venue Group</th>
                                <th>Customer</th>
                                <th>Groom</th>
                                <th>Bride</th>
                                <th>By</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>';
                        $counter = 1;
               
                        foreach ($pencilData as $data){
                            foreach ($data->pencils as $pencil){
                                $response .='<tr id="row_'.$pencil['id'].'">
                                            <td id="date_of_event_'.$pencil['id'].'">'.date(config('constants.date_formate'),$pencil['date_of_event']).'</td>
                                            <td id="venue_group_'.$pencil['id'].'">';
                                               if (isset($pencil['venue_group']))
                                               $response .=$pencil['venue_group']['userinfo'][0]['vg_name'].'</td>';
                                               else
                                               $response .=$pencil['other_venue_group'].'</td>';

                                            $response .= '<td id="customer_name_'.$pencil['id'].'">
                                            '.$pencil['customer']['userinfo'][0]['name'].'</td>
                                            <td groom_name_'.$pencil['id'].'">
                                            Name:'.$pencil['groom_name'].' <br>
                                            Ph:'.$pencil['groom_mobile'].'<br>
                                            Addr:'.$pencil['groom_billing_address'].'<br>
                                            </td>
                                            <td id="bride_name_'.$pencil['id'].'">
                                            Name:'.$pencil['bride_name'].' <br>
                                            Ph'.$pencil['bride_mobile'].'<br>
                                            Addr'.$pencil['bride_billing_address'].'<br>
                                            </td>
                                            <td id="pencile_by_'.$pencil['id'].'">'.pencilBy($pencil['pencile_by']).'</td>
                                           ';
                                               
                                               
                                        $response .='<td id="customer_status_'.$pencil['id'].'">'.current_booking_status($pencil['customer_approved'],$pencil['photographer_status']).'</td>

                                            <td>';
                                            if ($pencil['is_active'] != 2){
                                                $response .='<a href="'.route('bookings.view', $pencil['id']).'"
                                                class="btn btn-primary btn-block btn-sm"><i class="fas fa-eye"></i>
                                                View</a>';
                                            }
                                                   
                                                
                                            $response .='<div style="margin-top: 5px;">';
                                                    if ($pencil['is_active'] == 2){
                                                        $response .='<button onClick="do_action('.$pencil['id'].',\'restor_booking\')"
                                                            type="button" class="btn btn-warning btn-block btn-sm"><i
                                                                class="fas fa-chart-line"></i>
                                                            Restor</button>';
                                                    }elseif($pencil['is_active'] == 1){
                                                        $response .=' <button onClick="do_action('.$pencil['id'].',\'trash_booking\')"
                                                            type="button" class="btn btn-warning btn-block btn-sm"><i
                                                                class="fas fa-chart-line"></i>
                                                            Trash</button>';
                                                    }

                                                    $response .='  </div>';
                                                    
                                                 
                                                 
                                                
                                $response .=' </td></tr>';

                            }
                            $counter++;
                        }
                                                    
                        $dataArray['response']=$response;

        }
        elseif(isset($req['action']) && $req['action']=='submit_comment'){ 
            
            // p($req->all()); die;

            $this->comments->comment=$req['data']['comment'];
            $this->comments->user_id=get_session_value('id');
            $this->comments->group_id =$req['data']['group_id'];
            $this->comments->slug =$req['data']['slug'];
            $this->comments->for_section =$req['data']['for_section'];
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
        $bookingsMailData=$this->bookings->with(['customer','venue_group','photographer'])->where('id',$id)->get()->toArray();
        $bookingsMailData=$bookingsMailData[0];

        $mailData['body_message']='There was a new note added to the booking of '.$bookingsMailData['customer']['userinfo'][0]['name'].' for event '.date(config('constants.date_formate'),$bookingsMailData['date_of_event']);
        $mailData['subject']='New note added to booking';

         $emailAdd=[
                    config('constants.admin_email'),
                    //$bookingsMailData['customer']['userinfo'][0]['email'],
                    //$bookingsMailData['venue_group']['userinfo'][0]['email']
                ];
                // foreach($bookingsMailData['photographer'] as $photographer){
                //     $emailAdd[]=$photographer['userinfo'][0]['email'];
                // }

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
        elseif(isset($req['action']) && $req['action']=='submit_pencil_comments'){ 
            
            // p($req->all()); die;

            $this->comments->comment=$req['data']['comment'];
            $this->comments->user_id=get_session_value('id');
            $this->comments->group_id =$req['data']['group_id'];
            $this->comments->slug =$req['data']['slug'];
            $this->comments->for_section =$req['data']['for_section'];
            $this->comments->booking_id =$id;
            $this->comments->status =1;
            $this->comments->save();
            $dataArray['error']='No';
            $dataArray['to_replace']='submit_pencil_comments_replace';
            $htmlRes=' <div class="row border">
                            <div class="col-12">
                                <strong>'.get_session_value('name').' ('.$req['data']['slug'].') </strong> '.date('d/m/Y H:i:s',time()).'<br>
                                '.$req['data']['comment'].'
                            </div>
                        </div>';

    // Email Section
      
        // Get All Booking Data
        $bookingsMailData=$this->bookings->with(['customer','venue_group','photographer'])->where('id',$id)->get()->toArray();
        $bookingsMailData=$bookingsMailData[0];

        $mailData['body_message']='There was a new note added to the booking of '.$bookingsMailData['customer']['userinfo'][0]['name'].' for event '.date(config('constants.date_formate'),$bookingsMailData['date_of_event']);
        $mailData['subject']='New note added to booking';

         $emailAdd=[
                    config('constants.admin_email'),
                    //$bookingsMailData['customer']['userinfo'][0]['email'],
                    //$bookingsMailData['venue_group']['userinfo'][0]['email']
                ];
               

        // if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
        //     $dataArray['emailMsg']='Email Sent Successfully';
        // }
    //                        
            $dataArray['response']=$htmlRes;
            $dataArray['msg']='Mr.'.get_session_value('name').', Commented successfully!';
            $activityComment='Mr.'.get_session_value('name').', added comment!';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'pencil_comments_added',
                'comments'=>$activityComment,
                'others'=>'booking_actions',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
        elseif(isset($req['action']) && $req['action']=='submit_vg_comment'){ 
            
            // p($req->all()); die;

            $this->comments->comment=$req['data']['comment'];
            $this->comments->user_id=get_session_value('id');
            $this->comments->group_id =$req['data']['group_id'];
            $this->comments->slug =$req['data']['slug'];
            $this->comments->for_section =$req['data']['for_section'];
            $this->comments->booking_id =$id;
            $this->comments->status =1;
            $this->comments->save();
            $dataArray['error']='No';
            $dataArray['to_replace']='submit_vg_comment_replace';
            $htmlRes=' <div class="row border">
                            <div class="col-12">
                                <strong>'.get_session_value('name').' ('.$req['data']['slug'].') </strong> '.date('d/m/Y H:i:s',time()).'<br>
                                '.$req['data']['comment'].'
                            </div>
                        </div>';

    // Email Section
      
        // Get All Booking Data
        $bookingsMailData=$this->bookings->with(['customer','venue_group','photographer'])->where('id',$id)->get()->toArray();
        $bookingsMailData=$bookingsMailData[0];
        //p($bookingsMailData); die;
        $mailData['body_message']='There was a new note added to the booking of '.$bookingsMailData['customer']['userinfo'][0]['name'].' for event '.date(config('constants.date_formate'),$bookingsMailData['date_of_event']);
        $mailData['subject']='New note added to booking (Venue Group)';

         $emailAdd=[
                    config('constants.admin_email'),
                ];
                if(isset($bookingsMailData['venue_group']['userinfo'][0]['email']) && !empty($bookingsMailData['venue_group']['userinfo'][0]['email']))
                $emailAdd[]=$bookingsMailData['venue_group']['userinfo'][0]['email'];
                

        if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
            $dataArray['emailMsg']='Email Sent Successfully';
        }
    //                        
            $dataArray['response']=$htmlRes;
            $dataArray['msg']='Mr.'.get_session_value('name').', Commented successfully!';
            $activityComment='Mr.'.get_session_value('name').', added comment for Venue Group Section!';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'comment_added_for_vg',
                'comments'=>$activityComment,
                'others'=>'booking_actions',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
        }
        elseif(isset($req['action']) && $req['action']=='submit_photographer_comment'){ 
            
            // p($req->all()); die;

            $this->comments->comment=$req['data']['comment'];
            $this->comments->user_id=get_session_value('id');
            $this->comments->group_id =$req['data']['group_id'];
            $this->comments->slug =$req['data']['slug'];
            $this->comments->for_section =$req['data']['for_section'];
            $this->comments->booking_id =$id;
            $this->comments->status =1;
            $this->comments->save();
            $dataArray['error']='No';
            $dataArray['to_replace']='submit_photographer_comment_replace';
            $htmlRes=' <div class="row border">
                            <div class="col-12">
                                <strong>'.get_session_value('name').' ('.$req['data']['slug'].') </strong> '.date('d/m/Y H:i:s',time()).'<br>
                                '.$req['data']['comment'].'
                            </div>
                        </div>';

    // Email Section
      
        // Get All Booking Data
        $bookingsMailData=$this->bookings->with(['customer','venue_group','photographer'])->where('id',$id)->get()->toArray();
        $bookingsMailData=$bookingsMailData[0];

        $mailData['body_message']='There was a new note added to the booking of '.$bookingsMailData['customer']['userinfo'][0]['name'].' for event '.date(config('constants.date_formate'),$bookingsMailData['date_of_event']);
        $mailData['subject']='New note added to booking (Photographer)';

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
            $activityComment='Mr.'.get_session_value('name').', added comment for Photographer!';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'comment_added_for_photographer',
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
            
            if($req['status']==config('constants.photographer_assigned.removed')){
                $this->bookings_users->where('id',$req['id'])->delete();
                $dataArray['msg']='Mr.'.get_session_value('name').', removed photographer from booking!';
                $activityComment='Mr.'.get_session_value('name').', removed photographer status';
            
            }
            else{
                $data['booking_id']=$req['booking_id'];
                $data['user_id']=$req['user_id'];
                $data['status']=$req['status'];
                $this->bookings_users->where('id',$req['id'])->update(array('status'=>$req['status']));
              
                //$this->bookings->where('id',$req['booking_id'])->update(array('status'=>get_booking_status($req['booking_id'])));
                
                
            }
            if($req['status']==config('constants.photographer_assigned.cancelled')){
                
                // $booking_user_data=$this->bookings_users->where('id',$req['id'])->get()->toArray();
                // $booking_user_data=$booking_user_data[0];

                $bookingData=$this->bookings_users->with('booking')->with('userinfo')
                ->where('id',$req['id'])
                ->get()
                ->toArray();
                $bookingData=$bookingData[0];
            //p($bookingData); die;
                $mailData['body_message']='This email is to let you know that your scheduled booking on dated '.date(config('constants.date_formate'),$bookingData['booking']['date_of_event']).' booking for '.$bookingData['booking']['customer']['userinfo'][0]['name'].' has been cancelled on'.date('d/m/Y');
                $mailData['subject']='Mr.'.$bookingData['userinfo'][0]['name'].' your Scheduled booking has been cancelled ';
                $toEmail=$bookingData['userinfo'][0]['email'];

                if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
                //    echo 'Thank you, Your Booking has been confirmed';
                }
            }

            // This is to update Booking Status with photographer Status
            $get_booking_status=get_booking_status($req['booking_id']);
            $data_to_update_booking['status']=$get_booking_status;

            if($get_booking_status==config('constants.booking_status.pending_customer_agreement')){
                $data_to_update_booking['photographer_status']=config('constants.photographer_assigned.accepted');
            }else{
                $data_to_update_booking['photographer_status']=config('constants.photographer_assigned.awaiting');
            }
            $this->bookings->where('id',$req['booking_id'])->update($data_to_update_booking);


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
        else if(isset($req['action']) && $req['action']=='update_customer_status'){
            
            $data_to_update=array();
            $data_to_update['customer_approved']=$req['status'];
            //$data_to_update['status']=config('constants.booking_status.awaiting_for_photographer');
            $data_to_update['external_link_token']=NULL;
            
            $data=$this->bookings->with('customer')->with('venue_group')->where('id',$req['booking_id'])->get()->toArray();
            $data=$data[0];
            

                $vg_name='';
                if(!empty($data['venue_group']))
                $vg_name=$data['venue_group']['userinfo'][0]['vg_name'];
                else if(!empty($data['other_venue_group']))
                $vg_name=$data['other_venue_group'];

            if($req['status']==0){
                $status_msg='Awaited';
                $dataArray['customer_status_msg']='<span class="badge badge-info">Awaited</span>';
            }
            else if($req['status']==1){
                $status_msg='Accepted';
                $dataArray['customer_status_msg']='<span class="badge badge-success">Accepted</span>';
                
                $mailData['body_message']='Customer '.$data['customer']['userinfo'][0]['name'].' has confirmed and approved a booking for '.date(config('constants.date_formate'),$data['date_of_event']).' in '.$vg_name;
                $mailData['subject']='A booking has been confirmed';
                $toEmail=config('constants.admin_email');
                if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
                    $dataArray['mailmsg']='Thank you, Your Booking has been confirmed';
                }
            }
            else if($req['status']==2){
                $status_msg='Rejected';
                $dataArray['customer_status_msg']='<span class="badge badge-danger">Rejected</span>';

                $mailData['body_message']='This email is to let you know that Customer '.$data['customer']['userinfo'][0]['name'].' has cancelled/rejected a booking for '.date(config('constants.date_formate'),$data['date_of_event']).' in '.$vg_name;
                $mailData['subject']='A booking has been Cancelled';
                $toEmail=config('constants.admin_email');
                if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
                    $dataArray['mailmsg']= 'Your Booking has been cancelled';
                }
            }else{
                $status_msg='Invalide';
                $dataArray['customer_status_msg']='<span class="badge badge-warning">Invalid</span>';
            }
            

            $dataArray['msg']='Mr.'.get_session_value('name').', changed Customer Status and now customer status is '.$status_msg; 
            $activityComment='Mr.'.get_session_value('name').', changed Customer Status and now customer status is '.$status_msg;
         
                $dataArray['booking_id']=$req['booking_id'];
                
              
                $this->bookings->where('id',$req['booking_id'])->update($data_to_update);
                
            

            $dataArray['error']='No';
      

            
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['booking_id'],
                'action_slug'=>'customer_approved_booking',
                'comments'=>$activityComment,
                'others'=>'booking',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
            
        }
        else if(isset($req['action']) && $req['action']=='update_customer_status_admin'){
          
            $data_to_update=array();
            $data_to_update['customer_approved']=$req['customer_approved'];
            
            $dataArray['msg']='Mr.'.get_session_value('name').', changed Customer Status and now,'.customer_status($req['customer_approved']);
            $activityComment='Mr.'.get_session_value('name').', changed Customer Status and now,'.customer_status($req['customer_approved']);
         
                $data['booking_id']=$req['booking_id'];
                
              
                $this->bookings->where('id',$req['booking_id'])->update(array('customer_approved'=>$req['customer_approved']));
                
            

            $dataArray['error']='No';
            //$dataArray['customer_booking_status']=customer_status($req['customer_approved']);
            $dataArray['customer_booking_status']=customer_status($req['customer_approved']);
            
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$req['booking_id'],
                'action_slug'=>'customer_booking_status_changed',
                'comments'=>$activityComment,
                'others'=>'booking',
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
            $update_bookings_users['active']=1;
            $this->bookings->where('id',$id)->update($data_to_update);
            $this->bookings_users->where('booking_id',$id)->update($update_bookings_users);
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
        else if(isset($req['action']) && $req['action']=='change_event_status'){
            $data_to_update=array();
            
            $data_to_update['is_active']= base64_decode($req['data']['active_event']); 
            $this->bookings->where('id',$id)->update($data_to_update);
            $dataArray['error']='No';
            
            $dataArray['msg']='Mr.'.get_session_value('name').', changed Album status';
            $activityComment='Mr.'.get_session_value('name').', changed Album status';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'changed_album_status',
                'comments'=>$activityComment,
                'others'=>'booking_actions',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
         }
        else if(isset($req['action']) && $req['action']=='photo_gallery_status'){
            $data_to_update=array();
            
            $data_to_update['gallery_status']= base64_decode($req['data']['active_event']); 
            $this->bookings->where(['id'=>$id])->update($data_to_update);
            $dataArray['error']='No';
            
            $dataArray['msg']='Mr.'.get_session_value('name').', changed Album status';
            $activityComment='Mr.'.get_session_value('name').', changed Album status';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'changed_gallery_status',
                'comments'=>$activityComment,
                'others'=>'bookings',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
         }
        else if(isset($req['action']) && $req['action']=='trash_booking'){
            $update_bookings_users=$data_to_update=array();
            $data_to_update['is_active']=2;
            $update_bookings_users['active']=2;
            $this->bookings->where('id',$id)->update($data_to_update);
            $this->bookings_users->where('booking_id',$id)->update($update_bookings_users);
            $where=[
                ['booking_id','=',$id],
                ['group_id','!=',config('constants.groups.customer')],
            ];
            $this->bookings_users->where($where)->delete();
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
            $update_bookings_users['active']=3;
            
            $this->bookings->where('id',$id)->update($data_to_update);
            $this->bookings_users->where('booking_id',$id)->update($update_bookings_users);
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
        else if(isset($req['action']) && $req['action']=='restore_booking'){
            $data_to_update=array();
            $data_to_update['is_active']=1; // is_active 1 is for active Booking
            $update_bookings_users['active']=1;
            $this->bookings->where('id',$id)->update($data_to_update);
            $this->bookings_users->where('booking_id',$id)->update($update_bookings_users);
            $dataArray['error']='No';

            $dataArray['id']=$id;
            $dataArray['msg']='Mr.'.get_session_value('name').', restored the booking!';
            $activityComment='Mr.'.get_session_value('name').', restored the booking';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'booking_restored',
                'comments'=>$activityComment,
                'others'=>'bookings',
                'created_at'=>date('Y-m-d H:I:s',time()),
            );
            $activityID=log_activity($activityData);
         }

        else if(isset($req['action']) && $req['action']=='bride_update'){ 
            
            $data_to_update=array();
            $data_to_update['bride_name']=$req['data']['bride_name'];
            $data_to_update['bride_email']=$req['data']['bride_email'];
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
            $data_to_update['groom_email']=$req['data']['groom_email'];
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

            if(isset($req['data']['password']) && !empty($req['data']['password'])){
                $mailData['body_message']='Mr.'.$req['data']['firstname'].', you have beed assigned a new password. You can login to the system using Email:'.$req['data']['email'].' and Password: '.$req['data']['password'];
                $mailData['subject']='New Password Assigned';
                $mailData['button_title']='Login';
        
        
                $emailAdd=[
                $req['data']['email']
                ];
                if(Mail::to($emailAdd)->send(new EmailTemplate($mailData))){
                $retData['email_msg']='Email Sent Successfully';
                }
            }
       
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

            $bookingData=$this->bookings->with('customer')->where(['id'=>$id])->get()->toArray();
            $bookingData=$bookingData[0];
            $toEmail=[
                config('constants.admin_email'),
                $bookingData['customer']['userinfo'][0]['email']
            ];

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
        elseif(isset($req['action']) && $req['action']=='photographer_expense'.$id){ 
            $dataArray['error']='No';
            $where=[
                    ['id','=',$id]
            ];
            $to_update_expense=array();
             $to_update_expense['photographer_expense']=$req['data']['photographer_expense'];
             $this->bookings_users->where($where)->update($to_update_expense);

            
            $dataArray['msg']='Mr.'.get_session_value('name').', added photographer Expenses!';
            $activityComment='Mr.'.get_session_value('name').', added photographer Expenses!';
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

        elseif(isset($req['action']) && $req['action']=='sendcustomeragreement'){ 
                
            $this->booking_actions->title=' Accept/Reject the agreement';
            $this->booking_actions->slug='customeragreementsent';
            $this->booking_actions->user_id=get_session_value('id');
            $this->booking_actions->booking_id=$id;
            $this->booking_actions->save();

          
            $dataArray['msg']='Successfuly submitted';
   
            
            $dataArray['msg']='Successfuly submitted';

            // $bookingData=$this->bookings->with('customer')->where(['id'=>$id])->get()->toArray();
            // $bookingData=$bookingData[0];
            $bookingsMailData=$this->bookings->with(['customer','photographer','package'])->with('venue_group')->with('photographer')->where('id',$id)->get()->toArray();
            $bookingsMailData=$bookingsMailData[0];

            $welcomMes='<div style="text=align:justify">A booking has been created for you. We have started a booking for your event.
         To confirm the booking please confirm that all the information below is correct and click the Approve button below.<br></div>';
         $mailData['body_message']=$welcomMes.booking_email_body($bookingsMailData);
             $token=sha1(time());
             $booking_data_link['external_link_token']=Hash::make($token);
             $this->bookings->where('id',$id)->update($booking_data_link);
             $mailData['subject']='Welcome to Klein\'s photography';
             $mailData['button_title']='Accept';
             $mailData['button_link']=route('customer.approve',['id' => $id,'token'=>$token]);
             $mailData['button_title2']='Reject';
             $mailData['button_link2']=route('customer.reject',['id' => $id,'token'=>$token]);
                       
             

             $toEmail=[
                config('constants.admin_email'),
                $bookingsMailData['customer']['userinfo'][0]['email']
            ];

             if(Mail::to($toEmail)->send(new EmailTemplate($mailData))){
                $dataArray['msg']='A notification sent to customer for agreement';
             }

            $dataArray['error']='No';
            
            $dataArray['msg']='Mr.'.get_session_value('name').', Asked for ageement to Customer';
            $activityComment='Mr.'.get_session_value('name').', Asked for ageement to Customer';
            $activityData=array(
                'user_id'=>get_session_value('id'),
                'action_taken_on_id'=>$id,
                'action_slug'=>'customeragreementsent',
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
