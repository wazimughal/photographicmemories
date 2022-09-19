<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotographicPackages extends Model
{
    use HasFactory;
    protected $table='packages';
    protected $primaryKey='id';
    public function category()
    {
        return $this->hasOne(packages_categories::class, 'id', 'cat_id');
    }
}
