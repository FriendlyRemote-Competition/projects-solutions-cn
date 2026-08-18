<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Line extends Model
{
    //

    protected $guarded = [];

    function service_windows()
    {
        return $this->hasMany(ServiceWindow::class,"line_id","id");
    }

    function stationA()
    {


        return $this->belongsTo(Station::class, 'station_a_code', 'station_code');
    }

    function stationB()
    {

        return $this->belongsTo(Station::class, 'station_b_code', 'station_code');
    }
}
