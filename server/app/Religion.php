<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    protected $table = 'religion';

    public function person()
    {
        return $this->hasMany(Person::class);
    }
}
