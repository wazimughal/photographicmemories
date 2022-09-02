<?php

namespace App\Http\Controllers\adminpanel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         $this->projects = Auth::user()->projects;
 
    //         return $next($request);
    //     });
    // }
    //
    public function index($id=NULL){
        $user=Auth::user();
        return view('adminpanel/home'.$id,compact('user'));
        
    }
}
