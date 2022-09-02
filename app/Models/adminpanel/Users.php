<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;
    protected $table='users';
    protected $primaryKey='id';


    function __construct() {
     
        
    }
    public function getGroups()
       {
           return $this->hasOne(Groups::class, 'id', 'group_id');
       }
    public function getVenueGroup()
       {
           return $this->hasOne(venue_users::class, 'id', 'venue_users_id');
       }
}
