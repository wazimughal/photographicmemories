<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class bookings_users extends Model
{
    use HasFactory;
    use PowerJoins;

    public function venue_group_bookings()
    {
        $pencilData=bookings_users::joinRelationship('bookings', function ($join) {
            $join->where(['is_active'=>1,'bookings.status'=>0]);
        })->where('user_id',2)->get()->toArray();
        return $pencilData;
    }
    
    public function userinfo()
    {
        return $this->hasMany(users::class, 'id', 'user_id');
    }
    public function bookings()
    {
        return $this->hasMany(Bookings::class, 'id', 'booking_id')->with(['customer','photographer','invoices','package','venue_group','comments','deposite_requests','files'])->where('is_active',1);
    }
    public function pencils()
    {
        return $this->hasMany(Bookings::class, 'id', 'booking_id')->with('customer')->with('venue_group');//->where('is_active',1)->where('status',config('constants.booking_status.pencil'));
    }
    public function booking()
    {
        return $this->hasOne(Bookings::class, 'id', 'booking_id')->with(['customer','photographer','invoices','package','venue_group','comments','deposite_requests','files'])->where('is_active',1);
    }
    public function bookings_trashed()
    {
        return $this->hasMany(Bookings::class, 'id', 'booking_id','customer')->with('photographer')->with('invoices')->with('venue_group')->with('comments')->with('deposite_requests')->with('files')->where('is_active',2);
    }
    
}   
