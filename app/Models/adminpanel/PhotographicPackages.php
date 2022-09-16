<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotographicPackages extends Model
{
    use HasFactory;
    protected $table='packages';
    protected $primaryKey='id';
}
