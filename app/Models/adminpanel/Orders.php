<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    protected $table='orders';
    protected $primaryKey='id';
    protected $fillable = [
     
    ];
     /**
     * Get the user associated with the Orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function customer()
    {
        return $this->hasOne(Users::class, 'id', 'customer_id');
    }
    public function venue_group()
    {
        return $this->hasOne(venue_groups::class, 'id', 'venue_group_id');
    }
    public function package()
    {
        return $this->hasOne(PhotographicPackages::class, 'id', 'package_id');
    }
    public function photographers()
    {
        return $this->hasMany(photographer_orders::class, 'orders_id', 'id');
    }
    public function get_booking_by_id($id){
        $bookingData=$this->where('id',$id)
        ->with('customer')
        ->with('venue_group')
        ->with('photographers')
        ->orderBy('created_at', 'desc')->get()->toArray();

        if(!empty($bookingData))
        return $bookingData[0];
        
        return array();
    }
    
}
