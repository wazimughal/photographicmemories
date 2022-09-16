<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class photographerGaurd
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
        //echo 'group_id '.Auth::user()->group_id;
        if(Auth::check() && (Auth::user()->group_id==config('constants.groups.photographer'))){
            return $next($request);
       }
        else{
            abort(403, sprintf('Photographer are only allowed '));

        }
    }
}
