<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    use HasFactory;
    protected $table='bookings';
    protected $primaryKey='id';

  
    public function customer()
    {
        return $this->hasOne(bookings_users::class, 'booking_id', 'id')->where('group_id',config('constants.groups.customer'))->with('userinfo');
    }
    public function photographer()
    {
        return $this->hasMany(bookings_users::class, 'booking_id', 'id')->where('group_id',config('constants.groups.photographer'))->with('userinfo');
    }
    public function invoices()
    {
        return $this->hasMany(invoices::class, 'booking_id', 'id');
    }
    public function customer_invoices()
    {
        return $this->hasMany(invoices::class, 'booking_id', 'id')->where('slug','customer');
    }
    public function venue_invoices()
    {
        return $this->hasMany(invoices::class, 'booking_id', 'id')->where('slug','venue_group');
    }
    public function files()
    {
        return $this->hasMany(files::class, 'booking_id', 'id')->where('slug','booking_documents');
    }
    public function gallery()
    {
        return $this->hasMany(files::class, 'booking_id', 'id')->where('slug','booking_photos');
    }
    public function deposite_requests()
    {
        return $this->hasMany(booking_actions::class, 'booking_id', 'id');
    }
    public function comments()
    {
        return $this->hasMany(comments::class, 'booking_id', 'id')->with('user');
    }
    public function venue_group()
    {
        return $this->hasOne(bookings_users::class, 'booking_id', 'id')->where('group_id',config('constants.groups.venue_group_hod'))->with('userinfo');
    }
    
        //return $this->hasOne(User::class, 'foreign_key', 'local_key');
}
