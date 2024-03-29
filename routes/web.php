<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\adminpanel\DashboardController;
use App\Http\Controllers\adminpanel\AdminController;
use App\Http\Controllers\adminpanel\AdminLabTestsController;
use App\Http\Controllers\adminpanel\OrganizationsController;
use App\Http\Controllers\adminpanel\LoginController;
use App\Http\Controllers\adminpanel\PatientReportsController;

use App\Mail\EmailTemplate;
use Illuminate\Support\Facades\Mail;


Route::get('sendemail', function(){
    
    $mailData = [
        "name" => "Test NAME",
        "dob" => "12/12/1993",
        
    ];
    $id=1;
    
    // $bookingsMailData=App\Models\adminpanel\bookings::with(['customer','photographer','package'])->with('venue_group')->with('photographer')->where('id',$id)->get()->toArray();
    // $bookingsMailData=$bookingsMailData[0];
    //p($bookingsMailData); die;
    $welcomMes='<div style="text=align:justify">A booking has been created for you. We have started a booking for your event.
    To confirm the booking please confirm that all the information below is correct and click the Approve button below.<br></div>';
    $body_message=$mailData['body_message']=$welcomMes;
        $token=sha1(time());
        $booking_data_link['external_link_token']=Hash::make($token);
        //$this->bookings->where('id',$id)->update($booking_data_link);
        $mailData['subject']='testing email';
        $mailData['button_title']='Acceept';
        $mailData['button_link']=route('customer.approve',['id' => $id,'token'=>$token]);
    
    //return view('emails.email_template', get_defined_vars());
    $toEmail=[
        "to.wazim@gmail.com",
    ];
    //$toEmail=get_users_email_address();
    if(Mail::to($toEmail)->send(new EmailTemplate($mailData)))
    dd("Mail Sent Successfully!");
    else
    dd('Sending Failed');
});
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Auth::routes();
Route::group(['prefix' => 'admin'], function () {

    Auth::routes();

});
// Route::get('/clearroute', function () {

//     $exitCode = Artisan::call('route:cache');

//     return "route Cache Cleared!";

// });
Route::get('clearcache', function () {
    $exitCode = Artisan::call('config:cache');
    $exitCode1 = Artisan::call('config:clear');
    $exitCode2 = Artisan::call('cache:clear');
    $exitCode3 = Artisan::call('route:cache');
    $exitCode3 = Artisan::call('route:clear');

    return "Cache is cleared 4";
});
Route::get('route-list', function () {
    $exitCode = Artisan::call('route:list');
 return $exitCode;
    return "Route list";
});
Route::get('migrate-fresh', function () {
    $exitCode = Artisan::call('migrate:fresh');
    return "migareted Fresh!";
});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/admin/login/', [AdminController::class,'login'])->name('admin.loginform');
Route::post('/admin/login/', [AdminController::class,'authenticate'])->name('admin.login');
Route::get('/admin/register/', [AdminController::class,'index'])->name('admin.registerform');
Route::post('/admin/register/', [AdminController::class,'register'])->name('admin.register');
Route::get('/admin/logout/', [AdminController::class,'logout'])->name('admin.logout');


// Route::middleware(['roleGaurd'])->group(function () {
// Route::get('/admin/dashboard/{id?}', [DashboardController::class,'index'])->name('admin/dashboard/{id?}');
// });

Route::get('/admin/dashboard/{id?}', [DashboardController::class,'index'])->name('admin.dashboard');

    
    

Route::middleware(['authGaurd'])->group(function () {


// Customers Management 

// Route::get('/admin/customers',[App\Http\Controllers\adminpanel\CustomersController::class,'customers'])->name('/admin/customers');
// Route::get('/admin/customers/add',[App\Http\Controllers\adminpanel\CustomersController::class,'addcustomers'])->name('/admin/customers/add');
// Route::post('admin/customers/add',[App\Http\Controllers\adminpanel\CustomersController::class,'SaveCustomersData'])->name('admin/customers/add');
// Route::any('admin/customers/ajaxcall/{id}',[App\Http\Controllers\adminpanel\CustomersController::class,'ajaxcall'])->name('admin/customers/changestatus/{id}');

// Pencils Management 

Route::get('/admin/pencils/',[App\Http\Controllers\adminpanel\BookingsController::class,'pencils'])->name('admin.pencils');
Route::get('/admin/pencil/{type?}',[App\Http\Controllers\adminpanel\BookingsController::class,'pencils'])->name('admin.pencil.types');
Route::get('/admin/pencils/add/',[App\Http\Controllers\adminpanel\BookingsController::class,'pencils_form'])->name('pencils.pencils_form');
Route::post('admin/pencils/add',[App\Http\Controllers\adminpanel\BookingsController::class,'save_pencil_data'])->name('pencils.save_pencils_data');
Route::post('admin/user-pencils/add',[App\Http\Controllers\adminpanel\BookingsController::class,'save_user_pencil_data'])->name('pencils.save_user_pencils_data');
Route::get('/admin/pencils/view/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'view_pencil'])->name('pencil.view');
Route::get('/admin/pencils/edit/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'pencils_edit_form'])->name('pencils.pencils_edit_form');
Route::post('admin/pencils/edit/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'save_pencil_edit_data'])->name('pencils.save_pencil_edit_data');
Route::any('admin/pencils/ajaxcall/{id?}',[App\Http\Controllers\adminpanel\BookingsController::class,'ajaxcall'])->name('pencils.ajaxcall');

// Booking Management 
//Route::get('/admin/bookings',[App\Http\Controllers\adminpanel\BookingsController::class,'bookings'])->name('admin.bookings');
Route::get('/admin/bookings/awaited',[App\Http\Controllers\adminpanel\BookingsController::class,'bookings_awaited'])->name('bookings.awaited');
Route::get('/admin/bookings/scheduled',[App\Http\Controllers\adminpanel\BookingsController::class,'bookings_scheduled'])->name('bookings.scheduled');
Route::get('/admin/bookings/view/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'view_booking'])->name('bookings.view');
Route::get('/admin/bookings/pdf-download/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'booking_pdf_download'])->name('booking_pdf_download');
Route::get('/admin/bookings/invoice/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'invoice_booking'])->name('bookings.invoice');
Route::get('/admin/bookings/invoice/customer/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'invoice_booking'])->name('download.customer.invoice');
Route::get('/admin/bookings/invoice/venue/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'invoice_booking'])->name('download.venue.invoice');
Route::get('/admin/bookings/invoice/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'invoice_booking'])->name('download.invoice');
Route::get('/admin/booking/{type?}',[App\Http\Controllers\adminpanel\BookingsController::class,'bookings'])->name('bookings.type');
Route::get('/admin/bookings/add/{id?}',[App\Http\Controllers\adminpanel\BookingsController::class,'bookings_edit_form'])->name('bookings.bookings_form');
Route::any('admin/bookings/add',[App\Http\Controllers\adminpanel\BookingsController::class,'save_booking_data'])->name('bookings.save_bookings_data');
Route::get('/admin/bookings/edit/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'bookings_edit_form'])->name('bookings.bookings_edit_form');
Route::post('admin/bookings/edit/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'save_booking_edit_data'])->name('bookings.save_booking_edit_data');

Route::post('admin/bookings/add_invoice/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'save_booking_invoice_data'])->name('bookings.add_invoice');
Route::get('/bookings/calender',[App\Http\Controllers\adminpanel\BookingsController::class,'calender_schedule'])->name('user.calender');
Route::get('/bookings/galleries',[App\Http\Controllers\adminpanel\BookingsController::class,'booking_galleries'])->name('booking_galleries');
Route::any('admin/bookings/ajaxcall/{id?}',[App\Http\Controllers\adminpanel\BookingsController::class,'ajaxcall'])->name('bookings.ajaxcall');

// Reports
Route::get('/admin/reports/bookings',[App\Http\Controllers\adminpanel\BookingsController::class,'report_bookings'])->name('reports.bookings');
//Route::get('/reports/export-bookings',[App\Http\Controllers\adminpanel\BookingsController::class,'report_export_quote_delivery'])->name('reports.bookings.export');
Route::get('/admin/reports/customer',[App\Http\Controllers\adminpanel\BookingsController::class,'report_customer_payments'])->name('reports.customer.payments');
Route::get('/reports/export-customer-payments/{id?}',[App\Http\Controllers\adminpanel\BookingsController::class,'report_export_customer_account'])->name('reports.customer.payments.export');
Route::get('/admin/reports/venue-group',[App\Http\Controllers\adminpanel\BookingsController::class,'report_vg_payments'])->name('reports.vg.payments');
Route::any('/reports/export-vg-payments/{id?}',[App\Http\Controllers\adminpanel\BookingsController::class,'report_export_venue_account'])->name('reports.vg.payments.export');
Route::get('/admin/reports/photographer',[App\Http\Controllers\adminpanel\BookingsController::class,'report_photographer_payments'])->name('reports.photographer.payments');
Route::any('/reports/export-photographer-payments/{id?}',[App\Http\Controllers\adminpanel\BookingsController::class,'report_export_photographer_account'])->name('reports.photographer.payments.export');

Route::any('/photographer/bookings/upload-photos/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'booking_photos'])->name('booking.photos');// Booking Photos by Photographer
Route::any('/photographer/bookings/add-photos/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'add_photos'])->name('booking.add_photos');// Add Photos by photographer


//Media library routes
Route::get('/medialibrary', [App\Http\Controllers\MediaLibraryController::class, 'mediaLibrary'])->name('media-library');

//FILE UPLOADS CONTROLER
Route::post('medialibrary/upload', [App\Http\Controllers\UploaderController::class, 'upload'])->name('file-upload');
Route::post('medialibrary/delete', [App\Http\Controllers\UploaderController::class, 'delete'])->name('file-delete');

Route::any('/admin/bookings/testpage',[App\Http\Controllers\adminpanel\BookingsController::class,'testpage'])->name('bookings.testpage');// test version
Route::any('/admin/bookings/add-documents',[App\Http\Controllers\adminpanel\BookingsController::class,'add_documents'])->name('bookings.adddocuments');// test version
Route::any('/admin/bookings/upload-documents/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'upload_documents'])->name('bookings.uploaddocuments');

// Color Management 
Route::get('/admin/colors',[App\Http\Controllers\adminpanel\ColorsController::class,'colors'])->name('colors');
Route::post('/admin/colors',[App\Http\Controllers\adminpanel\ColorsController::class,'SavecolorsData'])->name('colors.add');
Route::any('admin/colors/ajaxcall/{id}',[App\Http\Controllers\adminpanel\ColorsController::class,'ajaxcall'])->name('colors.ajaxcall');


// PhotoGrapher Management 
Route::get('/admin/photographers',[App\Http\Controllers\adminpanel\PhotographerController::class,'photographers'])->name('admin.photographers');
Route::get('/admin/photographers/add',[App\Http\Controllers\adminpanel\PhotographerController::class,'addphotographers'])->name('photographers.addform');
Route::post('admin/photographers/add',[App\Http\Controllers\adminpanel\PhotographerController::class,'SavephotographersData'])->name('photographers.addsave');
Route::any('admin/photographers/ajaxcall/{id?}',[App\Http\Controllers\adminpanel\PhotographerController::class,'ajaxcall'])->name('photographers.ajaxcall');

// Venue Group Management 
Route::get('/admin/venuegroups',[App\Http\Controllers\adminpanel\VenuegroupsController::class,'venuegroups'])->name('admin.venuegroups');
Route::get('/admin/vgroup/{type?}',[App\Http\Controllers\adminpanel\VenuegroupsController::class,'venuegroups'])->name('admin.vg_type');
Route::get('/admin/venuegroups/add',[App\Http\Controllers\adminpanel\VenuegroupsController::class,'addvenuegroups'])->name('venuegroups.addform');
Route::post('admin/venuegroups/add',[App\Http\Controllers\adminpanel\VenuegroupsController::class,'SavevenuegroupsData'])->name('venuegroups.addsave');
Route::any('admin/venuegroups/ajaxcall/{id?}',[App\Http\Controllers\adminpanel\VenuegroupsController::class,'ajaxcall'])->name('venuegroups.ajaxcall');

// Package Management
Route::get('/admin/packages/categories',[App\Http\Controllers\adminpanel\PackagesController::class,'categoreis'])->name('admin.categories'); 
Route::get('/admin/packages',[App\Http\Controllers\adminpanel\PackagesController::class,'packages'])->name('admin.packages');
Route::get('/admin/packages/add',[App\Http\Controllers\adminpanel\PackagesController::class,'addpackages'])->name('packages.openform');
Route::post('admin/packages/add',[App\Http\Controllers\adminpanel\PackagesController::class,'SavepackagesData'])->name('packages.add');
Route::any('admin/packages/ajaxcall/{id}',[App\Http\Controllers\adminpanel\PackagesController::class,'ajaxcall'])->name('packages.ajaxcall');
Route::any('admin/packages/categoryajaxcall/{id?}',[App\Http\Controllers\adminpanel\PackagesController::class,'categoryajaxcall'])->name('pro_category.ajaxcall');

//echo 'echo'. config('constants.groups.staff');
//echo '<br>echasdo'. config('constants.groups.subscriber'); die;
// Users Management
Route::get('/admin/users',[App\Http\Controllers\adminpanel\AdminController::class,'users'])->name('admin.users');
Route::get('/admin/users/add',[App\Http\Controllers\adminpanel\AdminController::class,'addUser'])->name('users.addform');
Route::post('admin/users/add',[App\Http\Controllers\adminpanel\AdminController::class,'SaveUsersData'])->name('users.addsave');
Route::any('admin/users/update/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'UpdateUsersData'])->name('users.updatesave');
Route::any('admin/users/delete/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'DeleteUsersData'])->name('users.delete');
Route::any('admin/users/changestatus/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'changeStatus'])->name('users.changestatus');
Route::get('/admin/activity-log',[App\Http\Controllers\adminpanel\AdminController::class,'activitylog'])->name('admin.activitylog');

});

// Photographer Section Only
Route::middleware(['photographerGaurd'])->group(function () {
//Route::get('/photographer/bookings',[App\Http\Controllers\adminpanel\OrdersController::class,'photographer_bookings'])->name('photographer.bookings');
}
);

Route::get('/admin/no-access/', function(){
    echo 'you are not allowed to access the page ! ONLY the admins are allowed';
    //return redirect('/admin/login');
    die;
});
Route::get('/hod/no-access/', function(){
    echo 'you are not allowed to access the page ! ONLY the Hod are allowed';
    //return redirect('/admin/login');
    die;
});

Route::get('/', function () {
    return redirect()->route('admin.loginform');
    //return view('welcome');
});

Route::get('email/template/booking/{id?}',[App\Http\Controllers\adminpanel\BookingsController::class,'emailtemplate'])->name('email.template');
// to approve Customer Email
Route::get('/customer/booking/approve/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'customer_approval'])->name('customer.approve');
Route::get('/customer/booking/reject/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'customer_reject'])->name('customer.reject');
Route::get('/photographer/booking/action/{booking_id}/{photographer_id}/{action}',[App\Http\Controllers\adminpanel\BookingsController::class,'photographer_action'])->name('photograppher.action');

Route::get('/test', function () {
    
   echo  public_path('uploads');
   echo  '<br>';
   $uploadingPath=public_path('uploads');
   if(base_path()!='/Users/waximarshad/office.thephotographicmemories.com')
   $uploadingPath=base_path().'/public_html/uploads';
   
   echo $uploadingPath;
   
    
});