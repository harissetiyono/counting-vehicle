<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Filtering extends Model
{
    use SoftDeletes;
    protected $table = 'filtering';
    protected $guarded = [];
}
