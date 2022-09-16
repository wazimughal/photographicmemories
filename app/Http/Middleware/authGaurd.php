<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class authGaurd
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
            if(
            Auth::check() && 
                (
                Auth::user()->group_id==config('constants.groups.admin') || 
                Auth::user()->group_id==config('constants.groups.venue_group_hod') ||
                Auth::user()->group_id==config('constants.groups.customer') ||
                Auth::user()->group_id==config('constants.groups.photographer')
                ) 
            )
            {
                
            return $next($request);
            }
            else{
             
            abort(403, sprintf('You are not authorized to view this Page'));

            }
    }
}
