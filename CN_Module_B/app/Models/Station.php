<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    //
    protected $guarded = [];

    function lineA()
    {
        return $this->hasOne(Line::class, 'station_a_code', 'station_code');
    }

    function lineB()
    {
        return $this->hasOne(Line::class, 'station_b_code', 'station_code');
    }
}
