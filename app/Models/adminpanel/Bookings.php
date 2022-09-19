<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    use HasFactory;

  
    public function customer()
    {
        return $this->hasOne(bookings_users::class, 'booking_id', 'id')->where('group_id',config('constants.groups.customer'))->with('userinfo');
    }
    public function photographer()
    {
        return $this->hasMany(bookings_users::class, 'booking_id', 'id')->where('group_id',config('constants.groups.photographer'))->with('userinfo');
    }
    public function venue_group()
    {
        return $this->hasOne(bookings_users::class, 'booking_id', 'id')->where('group_id',config('constants.groups.venue_group_hod'))->with('userinfo');
    }
    
        //return $this->hasOne(User::class, 'foreign_key', 'local_key');
}
