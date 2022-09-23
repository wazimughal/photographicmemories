<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\adminpanel\DashboardController;
use App\Http\Controllers\adminpanel\AdminController;
use App\Http\Controllers\adminpanel\AdminLabTestsController;
use App\Http\Controllers\adminpanel\OrganizationsController;
use App\Http\Controllers\adminpanel\LoginController;
use App\Http\Controllers\adminpanel\PatientReportsController;



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
Auth::routes();
// Route::group(['prefix' => 'admin'], function () {

//     Auth::routes();

// });
// Route::get('/clearroute', function () {

//     $exitCode = Artisan::call('route:cache');

//     return "route Cache Cleared!";

// });
Route::get('clearcache', function () {
    $exitCode = Artisan::call('config:cache');
    $exitCode1 = Artisan::call('config:clear');
    $exitCode2 = Artisan::call('cache:clear');
    $exitCode3 = Artisan::call('route:cache');

    return "View Cache Cleared!";
});

Route::resource('/admin/patient-reports', PatientReportsController::class)->except([
    'store'
])->middleware('adminHodGaurd');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/admin/login/', [AdminController::class,'login'])->name('admin.loginform');
Route::post('/admin/login/', [AdminController::class,'authenticate'])->name('admin.login');
Route::get('/admin/register/', [AdminController::class,'index'])->name('admin.registerform');
Route::post('/admin/register/', [AdminController::class,'register'])->name('admin.register');
Route::get('/admin/logout/', [AdminController::class,'logout'])->name('admin.logout');


// Route::middleware(['roleGaurd'])->group(function () {
// Route::get('/admin/dashboard/{id?}', [DashboardController::class,'index'])->name('admin/dashboard/{id?}');
// });

Route::get('/admin/dashboard/{id?}', [DashboardController::class,'index'])->name('admin.dashboard}');

    
    

Route::middleware(['authGaurd'])->group(function () {

// Lead Management 

// Route::get('/admin/leads',[App\Http\Controllers\adminpanel\LeadsController::class,'leads'])->name('admin.leads');
// Route::get('/admin/lead/{type?}',[App\Http\Controllers\adminpanel\LeadsController::class,'leads'])->name('admin.lead');
// Route::get('/admin/leads/add',[App\Http\Controllers\adminpanel\LeadsController::class,'addLeads'])->name('leads.addform');
// Route::post('admin/leads/add',[App\Http\Controllers\adminpanel\LeadsController::class,'SaveUsersData'])->name('leads.add');
// Route::any('admin/leads/ajaxcall/{id}',[App\Http\Controllers\adminpanel\LeadsController::class,'ajaxcall'])->name('leads.changestatus/{id}');

// Customers Management 

// Route::get('/admin/customers',[App\Http\Controllers\adminpanel\CustomersController::class,'customers'])->name('/admin/customers');
// Route::get('/admin/customers/add',[App\Http\Controllers\adminpanel\CustomersController::class,'addcustomers'])->name('/admin/customers/add');
// Route::post('admin/customers/add',[App\Http\Controllers\adminpanel\CustomersController::class,'SaveCustomersData'])->name('admin/customers/add');
// Route::any('admin/customers/ajaxcall/{id}',[App\Http\Controllers\adminpanel\CustomersController::class,'ajaxcall'])->name('admin/customers/changestatus/{id}');

// Pencils Management 

Route::get('/admin/pencils',[App\Http\Controllers\adminpanel\BookingsController::class,'pencils'])->name('admin.pencils');
Route::get('/admin/pencil/{type?}',[App\Http\Controllers\adminpanel\BookingsController::class,'pencils'])->name('admin.order.types');
Route::get('/admin/pencils/add/',[App\Http\Controllers\adminpanel\BookingsController::class,'pencils_form'])->name('pencils.pencils_form');
Route::post('admin/pencils/add',[App\Http\Controllers\adminpanel\BookingsController::class,'save_pencil_data'])->name('pencils.save_pencils_data');
Route::get('/admin/pencils/view/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'view_pencil'])->name('pencil.view');
Route::get('/admin/pencils/edit/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'pencils_edit_form'])->name('pencils.pencils_edit_form');
Route::post('admin/pencils/edit/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'save_pencil_edit_data'])->name('pencils.save_pencil_edit_data');
Route::any('admin/pencils/ajaxcall/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'ajaxcall'])->name('pencils.ajaxcall');

// Booking Management 
Route::get('/admin/bookings',[App\Http\Controllers\adminpanel\BookingsController::class,'bookings'])->name('admin.bookings');
Route::get('/admin/bookings/view/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'view_booking'])->name('bookings.view');
Route::get('/admin/booking/{type?}',[App\Http\Controllers\adminpanel\BookingsController::class,'bookings'])->name('bookings.trash');
Route::get('/admin/bookings/add/{id?}',[App\Http\Controllers\adminpanel\BookingsController::class,'bookings_edit_form'])->name('bookings.bookings_form');
Route::any('admin/bookings/add',[App\Http\Controllers\adminpanel\BookingsController::class,'save_booking_data'])->name('bookings.save_bookings_data');
Route::get('/admin/bookings/edit/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'bookings_edit_form'])->name('bookings.bookings_edit_form');
Route::post('admin/bookings/edit/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'save_booking_edit_data'])->name('bookings.save_booking_edit_data');
Route::any('admin/bookings/ajaxcall/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'ajaxcall'])->name('bookings.ajaxcall');
Route::post('admin/bookings/add_invoice/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'save_booking_invoice_data'])->name('bookings.add_invoice');

//Route::get('/admin/bookings/add-documents/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'add_documents'])->name('bookings.add-documents');
Route::any('/admin/bookings/upload-documents/{id}',[App\Http\Controllers\adminpanel\BookingsController::class,'upload_documents'])->name('bookings.uploaddocuments');

// Color Management 
Route::get('/admin/colors',[App\Http\Controllers\adminpanel\ColorsController::class,'colors'])->name('colors');
Route::post('/admin/colors',[App\Http\Controllers\adminpanel\ColorsController::class,'SavecolorsData'])->name('colors.add');
Route::any('admin/colors/ajaxcall/{id}',[App\Http\Controllers\adminpanel\ColorsController::class,'ajaxcall'])->name('colors.ajaxcall');


// PhotoGrapher Management 
Route::get('/admin/photographers',[App\Http\Controllers\adminpanel\PhotographerController::class,'photographers'])->name('/admin/photographers');
Route::get('/admin/photographers/add',[App\Http\Controllers\adminpanel\PhotographerController::class,'addphotographers'])->name('/admin/photographers/add');
Route::post('admin/photographers/add',[App\Http\Controllers\adminpanel\PhotographerController::class,'SavephotographersData'])->name('admin/photographers/add');
Route::any('admin/photographers/ajaxcall/{id}',[App\Http\Controllers\adminpanel\PhotographerController::class,'ajaxcall'])->name('admin/photographers/changestatus/{id}');

// Venue Group Management 
Route::get('/admin/venuegroups',[App\Http\Controllers\adminpanel\VenuegroupsController::class,'venuegroups'])->name('/admin/venuegroups');
Route::get('/admin/venuegroups/add',[App\Http\Controllers\adminpanel\VenuegroupsController::class,'addvenuegroups'])->name('/admin/venuegroups/add');
Route::post('admin/venuegroups/add',[App\Http\Controllers\adminpanel\VenuegroupsController::class,'SavevenuegroupsData'])->name('admin/venuegroups/add');
Route::any('admin/venuegroups/ajaxcall/{id}',[App\Http\Controllers\adminpanel\VenuegroupsController::class,'ajaxcall'])->name('admin/venuegroups/changestatus/{id}');

// Package Management
Route::get('/admin/packages/categories',[App\Http\Controllers\adminpanel\PackagesController::class,'categoreis'])->name('admin.categories'); 
Route::get('/admin/packages',[App\Http\Controllers\adminpanel\PackagesController::class,'packages'])->name('/admin/packages');
Route::get('/admin/packages/add',[App\Http\Controllers\adminpanel\PackagesController::class,'addpackages'])->name('packages.openform');
Route::post('admin/packages/add',[App\Http\Controllers\adminpanel\PackagesController::class,'SavepackagesData'])->name('packages.add');
Route::any('admin/packages/ajaxcall/{id}',[App\Http\Controllers\adminpanel\PackagesController::class,'ajaxcall'])->name('packages.ajaxcall');
Route::any('admin/packages/categoryajaxcall/{id?}',[App\Http\Controllers\adminpanel\PackagesController::class,'categoryajaxcall'])->name('pro_category.ajaxcall');

//echo 'echo'. config('constants.groups.staff');
//echo '<br>echasdo'. config('constants.groups.subscriber'); die;
// Users Management
Route::get('/admin/users',[App\Http\Controllers\adminpanel\AdminController::class,'users'])->name('/admin/users');
Route::get('/admin/users/add',[App\Http\Controllers\adminpanel\AdminController::class,'addUser'])->name('/admin/users/add');
Route::post('admin/users/add',[App\Http\Controllers\adminpanel\AdminController::class,'SaveUsersData'])->name('admin/users/add');
Route::any('admin/users/update/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'UpdateUsersData'])->name('admin/users/update/{id}');
Route::any('admin/users/delete/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'DeleteUsersData'])->name('admin/users/delete/{id}');
Route::any('admin/users/changestatus/{id}',[App\Http\Controllers\adminpanel\AdminController::class,'changeStatus'])->name('admin/users/changestatus/{id}');
Route::get('/admin/activity-log',[App\Http\Controllers\adminpanel\AdminController::class,'activitylog'])->name('/admin/activitylog');
Route::get('/admin/calender',[App\Http\Controllers\adminpanel\AdminController::class,'calenderSchedule'])->name('user.calender');


});

// Photographer Section Only
Route::middleware(['photographerGaurd'])->group(function () {
Route::get('/photographer/bookings',[App\Http\Controllers\adminpanel\OrdersController::class,'photographer_bookings'])->name('photographer.bookings');
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


Route::get('/test', function () {
    $_POST['capital']='100.000';
    echo $_POST['capital']=str_replace('.','',$_POST['capital']);
});