<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "code" => $this->line_code,
            "name" => $this->line_name,
            "status" => $this->status,
            "station_a" => [
                "code" => $this->stationA->station_code,
                "name" => $this->stationA->station_name,
            ],

            "station_b" => [
                "code" => $this->stationB->station_code,
                "name" => $this->stationB->station_name,
            ],

            "fare_cny" => number_format($this->fare_cny,2),
            "seat_capacity" => $this->seat_capacity,
            "crossing_minutes" => $this->crossing_minutes,
            "service_windows" => $this->service_windows->map(function ($item) {


                return [
                    "end_time" => Carbon::parse($item->end_time)->format("H:i"),
                    "start_time" => Carbon::parse($item->start_time)->format("H:i"),
                    "interval_minutes" => (int)$item->interval_minutes,
                ];
            })

        ];
    }
}
