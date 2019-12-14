<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Counting extends Model
{
    //
    public function camera()
    {
        return $this->belongsTo(Camera::class, 'id_camera');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'type');
    }
}
