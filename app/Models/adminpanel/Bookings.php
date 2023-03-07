<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Bookings extends Model
{
    use HasFactory;
    use PowerJoins;
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
    public function package()
    {
        return $this->hasOne(packages::class, 'id', 'package_id');
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
        return $this->hasMany(files::class, 'booking_id', 'id')->where(['slug'=>'booking_photos','status'=>1]);
    }
    public function deposite_requests()
    {
        return $this->hasMany(booking_actions::class, 'booking_id', 'id');
    }
    public function comments()
    {
        return $this->hasMany(comments::class, 'booking_id', 'id')->with('user')->where('for_section','admin_only_section');
    }
    public function vg_comments()
    {
        return $this->hasMany(comments::class, 'booking_id', 'id')->with('user')->where('for_section','venue_group_section');;
    }
    public function photographer_comments()
    {
        return $this->hasMany(comments::class, 'booking_id', 'id')->with('user')->where('for_section','photographer_section');;
    }
    public function pencil_comments()
    {
        return $this->hasMany(comments::class, 'booking_id', 'id')->with('user')->where('for_section','pencil_comments');;
    }
    public function venue_group()
    {
        return $this->hasOne(bookings_users::class, 'booking_id', 'id')->where('group_id',config('constants.groups.venue_group_hod'))->with('userinfo');
    }
    
        //return $this->hasOne(User::class, 'foreign_key', 'local_key');
}
