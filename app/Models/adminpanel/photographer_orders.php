<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class photographer_orders extends Model
{
    use HasFactory;
    protected $table='photographer_orders';
    protected $primaryKey='id';
  
      /**
         * Get the user associated with the photographer_orders
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasOne
         */
        public function bookings()
        {
            return $this->hasMany(orders::class, 'id', 'orders_id');
        }
       
        
   
}
