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
    public function booking()
    {
        return $this->hasMany(Bookings::class, 'id', 'booking_id');
    }
    
}
