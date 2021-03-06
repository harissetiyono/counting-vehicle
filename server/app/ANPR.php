<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ANPR extends Model
{
    protected $table = 'anpr_record';
    protected $guarded = ['id', 'created_at'];
    
    public function camera()
    {
        return $this->belongsTo(Camera::class, 'id_camera');
    }

    public function ANPRcamera()
    {
        return $this->belongsTo(ANPRcamera::class, 'id_camera');
    }

    public function vehicleType()
    {
        return $this->belongsTo(vehicleType::class, 'vehicleType');
    }

    public function anprViolation()
    {
        return $this->belongsTo(ANPRViolation::class, 'violationStatus');
    }
}
