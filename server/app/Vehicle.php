<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    public function counting()
    {
        return $this->hasMany(Counting::class);
    }
}
