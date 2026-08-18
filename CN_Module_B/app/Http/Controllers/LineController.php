<?php

namespace App\Http\Controllers;

use App\Http\Resources\LineResource;
use App\Models\Booking;
use App\Models\cancelledDeparture;
use App\Models\Line;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LineController extends Controller
{
    //
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    function index()
    {
        $lines = Line::all();
        return LineResource::collection($lines);
    }

    /**
     * @param Line $line
     * @return LineResource
     */
    function show(Line $line)
    {

        return new LineResource($line);
    }

    /**
     * @param Request $request
     * @param Line $line
     * @return \Illuminate\Http\JsonResponse
     */
    function timetable(Request $request, Line $line)
    {

        $validated = $request->validate([
            "date" => "nullable|date|date_format:Y-m-d",
            "station" => ["nullable", "exists:stations,station_code", function ($attribute, $value, $fail) use ($line) {
                $station = Station::where('station_code', $value)->first();
                if (!$station) return $fail("Station not found");
                $lineA = $station->lineA;
                $lineB = $station->lineB;
                if ($lineA->line_code !== $line->line_code && $lineB->line_code !== $lineB->line_code) {
                    return $fail("Station Not belong this line.");
                }
            }],
        ]);
        $targetDate = !empty($validated['date'])
            ? Carbon::parse($validated['date'])
            : Carbon::now();
//        return empty array
        if ($line->status === "suspended") return response()->json(["data" => []]);

        $windows = $line->service_windows()->orderBy("start_time")->get();
        $departures = collect();


        foreach ($windows as $win) {
            $startCarbon = Carbon::parse($targetDate->format('Y-m-d') . ' ' . $win->start_time);
            $endCarbon = Carbon::parse($targetDate->format('Y-m-d') . ' ' . $win->end_time);
            $current = $startCarbon->copy();
            while ($current <= $endCarbon) {
                foreach ([$line->station_a_code, $line->station_b_code] as $depStationCode) {
                    $depTime = $current->copy();
                    $arrTime = $depTime->copy()->addMinutes($line->crossing_minutes);
                    $departureCode = $line->line_code . "-" . Carbon::parse($targetDate)->format('Ymd') . "-" . $depTime->format('Hi') . '-' . $depStationCode;

                    if ($depStationCode === $line->station_a_code) {
                        $origin = $line->stationA;
                        $dest = $line->stationB;
                    } else {
                        $origin = $line->stationB;
                        $dest = $line->stationA;
                    }

                    $cancelRec = CancelledDeparture::where([
                        'line_code' => $line->line_code,
                        'departure_date' => $targetDate,
                        'departure_time' => $depTime,
                        'departure_station' => $depStationCode
                    ])->first();

                    $seatsBooked = Booking::where([
                        'line_code' => $line->line_code,
                        'departure_date' => $targetDate,
                        'departure_time' => $depTime,
                        'departure_station' => $depStationCode,
                        'status' => 'confirmed'
                    ])->sum('seats');


                    $seatsAvailable = $line->seat_capacity - $seatsBooked;
                    $now = Carbon::now();
                    if ($cancelRec) {
                        $status = 'cancelled';
                        $cancellationReason = $cancelRec->reason;
                    } elseif ($depTime <= $now) {
                        $status = 'departed';
                        $cancellationReason = null;
                    } else {
                        $status = 'scheduled';
                        $cancellationReason = null;
                    }
                    $departures->push([
                        'code' => $departureCode,
                        'origin' => [
                            'code' => $origin->station_code,
                            'name' => $origin->station_name
                        ],
                        'destination' => [
                            'code' => $dest->station_code,
                            'name' => $dest->station_name
                        ],
                        'departure_date' => $targetDate->format('Y-m-d'),
                        'departure_time' => $depTime->format('H:i'),
                        'arrival_time' => $arrTime->format('H:i'),
                        'seats_booked' => $seatsBooked,
                        'seats_available' => $seatsAvailable,
                        'fare_cny' => number_format($line->fare_cny, 2),
                        'status' => $status,
                        'cancellation_reason' => $cancellationReason
                    ]);
                }
                $current->addMinutes($win->interval_minutes);
            }
        }

        if (!empty($validated['station'])) {
            $departures = $departures->where('origin.code', $validated['station']);
        }
//        sort by departure_time and code
        $departures = $departures->sortBy([
            ['departure_time', 'asc'],
            ['origin.code', 'asc']
        ])->values();

        return response()->json([
            'data' => $departures
        ]);
    }
}
