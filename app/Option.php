<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{

    public function variant()
    {
        return $this->belongsTo('App\Variant');
    }
}
