<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ANPRViolation extends Model
{
    protected $table = 'anpr_violation';

    public function anpr()
    {
        return $this->hasMany(ANPR::class);
    }
}
