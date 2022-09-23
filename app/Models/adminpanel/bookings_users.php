<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bookings_users extends Model
{
    use HasFactory;
    
    public function userinfo()
    {
        return $this->hasMany(users::class, 'id', 'user_id');
    }
    public function bookings()
    {
        return $this->hasMany(Bookings::class, 'id', 'booking_id')->with('customer')->with('photographer')->with('invoices')->with('venue_group')->with('comments')->with('deposite_requests')->with('files')->where('is_active',1);
    }
    public function pencils()
    {
        return $this->hasMany(Bookings::class, 'id', 'booking_id')->with('customer')->with('venue_group')->where('is_active',1)->where('status',config('constants.booking_status.pencil'));
    }
    public function booking()
    {
        return $this->hasOne(Bookings::class, 'id', 'booking_id');
    }
    public function bookings_trashed()
    {
        return $this->hasMany(Bookings::class, 'id', 'booking_id')->with('customer')->with('photographer')->with('invoices')->with('venue_group')->with('comments')->with('deposite_requests')->with('files')->where('is_active',2);
    }
    
}   
