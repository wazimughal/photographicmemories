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
   // $exitCode3 = Artisan::call('route:cache');

    return "View Cache Cleared!";
});

Route::resource('/admin/patient-reports', PatientReportsController::class)->except([
    'store'
])->middleware('adminHodGaurd');

Route::post('/admin/patient-reports/save', [PatientReportsController::class,'saveReport'])->name('save')->middleware('adminHodGaurd');
Route::get('/admin/patient-reports/delete/{id}', [PatientReportsController::class,'destroy'])->name('destroy')->middleware('adminHodGaurd');
Route::get('/admin/patient-reports/show/{id}', [PatientReportsController::class,'showReportJson'])->name('showReport')->middleware('adminHodGaurd');
Route::get('/admin/patient-reports/view/{id}', [PatientReportsController::class,'viewReport'])->name('view')->middleware('adminHodGaurd');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/admin/login/', [AdminController::class,'login'])->name('admin/login/');
//Route::post('/admin/login/', [AdminController::class,'getlogin'])->name('admin/login/');
Route::post('/admin/login/', [AdminController::class,'authenticate'])->name('admin/login/');
Route::get('/admin/register/', [AdminController::class,'index'])->name('admin/register/');
Route::post('/admin/register/', [AdminController::class,'register'])->name('admin/register/');
Route::get('/admin/logout/', [AdminController::class,'logout'])->name('admin/logout/');


// Route::middleware(['roleGaurd'])->group(function () {
// Route::get('/admin/dashboard/{id?}', [DashboardController::class,'index'])->name('admin/dashboard/{id?}');
// });

Route::get('/admin/dashboard/{id?}', [DashboardController::class,'index'])->name('admin/dashboard/{id?}');

    
    

Route::middleware(['adminHodGaurd'])->group(function () {

//CRUD for Organization Data
Route::get('/admin/organizations',[OrganizationsController::class,'show'])->name('/admin/organizations');
Route::get('/admin/organizations/add',[OrganizationsController::class,'add'])->name('/admin/organizations/add');
Route::post('admin/organizations/add',[OrganizationsController::class,'SaveOrgData'])->name('admin/organizations/add');
Route::get('admin/organizations/update/{id}',[OrganizationsController::class,'UpdateOrgData'])->name('admin/organizations/update/{id}');

// Lead Management 
// Route::get('/admin/dashboard/{id?}', [DashboardController::class,'index'])->name('admin/dashboard/{id?}');
Route::get('/admin/leads',[App\Http\Controllers\adminpanel\LeadsController::class,'leads'])->name('/admin/users');
Route::get('/admin/lead/{type?}',[App\Http\Controllers\adminpanel\LeadsController::class,'leads'])->name('/admin/users');
Route::get('/admin/leads/add',[App\Http\Controllers\adminpanel\LeadsController::class,'addLeads'])->name('/admin/users/add');
Route::post('admin/leads/add',[App\Http\Controllers\adminpanel\LeadsController::class,'SaveUsersData'])->name('admin/users/add');
Route::any('admin/leads/ajaxcall/{id}',[App\Http\Controllers\adminpanel\LeadsController::class,'ajaxcall'])->name('admin/users/changestatus/{id}');

//echo 'echo'. config('constants.groups.staff');
//echo '<br>echasdo'. config('constants.groups.subscriber'); die;
// Users Management
Route::get('/admin/users',[AdminController::class,'users'])->name('/admin/users');
Route::get('/admin/users/add',[AdminController::class,'addUser'])->name('/admin/users/add');
Route::post('admin/users/add',[AdminController::class,'SaveUsersData'])->name('admin/users/add');
Route::any('admin/users/update/{id}',[AdminController::class,'UpdateUsersData'])->name('admin/users/update/{id}');
Route::any('admin/users/delete/{id}',[AdminController::class,'DeleteUsersData'])->name('admin/users/delete/{id}');
Route::any('admin/users/changestatus/{id}',[AdminController::class,'changeStatus'])->name('admin/users/changestatus/{id}');



// CRUD For Lab Tests 
Route::get('/admin/lab-tests/', [AdminLabTestsController::class,'index'])->name('admin/lab-tests');
Route::get('/admin/lab-tests/add',[AdminLabTestsController::class,'add'])->name('admin/lab-tests/add');
Route::post('/admin/lab-tests/add',[AdminLabTestsController::class,'saveFormData'])->name('admin/lab-tests/add');
Route::get('/admin/lab-tests/edit/{id}',[AdminLabTestsController::class,'editTestData'])->name('admin/lab-tests/edit/{id}');
Route::post('/admin/lab-tests/edit/{id}',[AdminLabTestsController::class,'UpdateTestData'])->name('admin/lab-tests/edit/{id}');
Route::get('/admin/lab-tests-params/delete/{id}',[AdminLabTestsController::class,'deleteTestParam'])->name('admin/lab-tests-params/delete/{id}');

});

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
    return view('welcome');
});


Route::get('/test', function () {
    $_POST['capital']='100.000';
    echo $_POST['capital']=str_replace('.','',$_POST['capital']);
});