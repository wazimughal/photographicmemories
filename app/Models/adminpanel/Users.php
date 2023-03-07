<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\PowerJoins\PowerJoins;

class Users extends Model
{
    use HasFactory;
    use PowerJoins;
    protected $table='users';
    protected $primaryKey='id';

   
    public function getGroups()
       {
           return $this->hasOne(Groups::class, 'id', 'group_id');
       }
    public function bookings()
       {
           return $this->hasMany(bookings_users::class, 'user_id', 'id')->with('pencils');
       }
       // Relation with Users table and Venue_Users to get Venue Group of all users/customers/leads Detail
    public function getVenueGroup()
       {
           return $this->hasOne(venue_users::class, 'id', 'venue_users_id');
       }
       // Relation with Users table and Venue Group to get Venue Group Detail
    public function VenueGroup()
       {
           return $this->hasOne(venue_groups::class, 'user_id', 'id');
       }
    public function City()
       {
           return $this->hasOne(cities::class, 'id', 'city_id');
       }
    public function ZipCode()
       {
           return $this->hasOne(zipcode::class, 'id', 'zipcode_id');
       }
}
