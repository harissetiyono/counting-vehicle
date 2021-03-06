<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $table = 'anpr_vehicle_type';

    public function anpr()
    {
        return $this->hasMany(ANPR::class);
    }
}
