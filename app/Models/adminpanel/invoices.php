<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoices extends Model
{
    use HasFactory;
    protected $table='invoices';
    protected $primaryKey='id';

    public function invoices()
    {
        return $this->hasMany(bookings::class, 'id', 'booking_id');
    }
    public function venue_group_invoices()
    {
        return $this->hasMany(bookings::class, 'id', 'booking_id')->where('slug','venue_group');
    }
    public function customer_invoices()
    {
        return $this->hasMany(bookings::class, 'id', 'booking_id')->where('slug','customer');
    }
     //return $this->hasOne(User::class, 'foreign_key', 'local_key');
}
