<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    // use SoftDeletes;
    protected $guarded = [];
    protected $table = 'persons';
    protected $primaryKey = 'nik';

    public function religion()
    {
        return $this->belongsTo(Religion::class, 'religion');
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'profession');
    }
}
