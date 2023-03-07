<?php

namespace App\Http\Controllers\adminpanel;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\adminpanel\Users;
use App\Models\adminpanel\Groups;
use App\Models\adminpanel\Venue_groups;
use App\Models\adminpanel\venue_users;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    function __construct() {
        
        $this->users= new Users;
        $this->groups= new Groups;
        $this->venueGroup= new Venue_groups;
        $this->venue_users= new venue_users;
      }

    public function index($id=NULL){
        $user=Auth::user();
        
        if(isset($_GET['resetpassword']) && $_GET['resetpassword']==1) 
        return redirect()->route('admin.logout');

        if($user->group_id== config('constants.groups.admin')){
            $record_count=get_record_count();
            // p($record_count);
            // die;
         
            return view('adminpanel/home',get_defined_vars());
        }
        else{
            return view('adminpanel/user_dashboard',get_defined_vars());
        }

        
    }
}
