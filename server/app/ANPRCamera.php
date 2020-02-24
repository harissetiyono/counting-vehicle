<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ANPRCamera extends Model
{
    use SoftDeletes;
    protected $table = 'anpr_cameras';
    protected $guarded = ['id'];

    public function anpr()
    {
        return $this->hasMany(ANPR::class);
    }
}
