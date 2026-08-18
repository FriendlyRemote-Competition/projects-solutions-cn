<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Line;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function bookings(Request $request)
    {
        $data = $request->validate([
            "date" => "nullable|date|date_format:Y-m-d",
            "line_code" => "nullable|string|exists:lines,line_code",
            "status" => "nullable|in:confirmed,cancelled",
            "search" => 'nullable|string',
            'page' => 'nullable|integer|min:1',
        ]);
        $q = Booking::query();
        if (!empty($validated['date'])) {
            $filterDate = Carbon::parse($validated['date'])->format('Y-m-d');
            $q->where('departure_date', $filterDate);
        }

        if (!empty($validated['line_code'])) {
            $q->where('line_code', $validated['line_code']);
        }

        if (!empty($validated['status'])) {
            $q->where('status', $validated['status']);
        }

        if (!empty($validated['search'])) {
            $s = trim($validated['search']);
            $q->where(function ($sub) use ($s) {
                $sub->where('booking_code', 'like', "%{$s}%")
                    ->orWhereRaw('LOWER(first_name) LIKE ?', ["%" . strtolower($s) . "%"])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', ["%" . strtolower($s) . "%"])
                    ->orWhere('email', 'like', "%{$s}%");
            });
        }
        $q->orderBy('departure_date')
            ->orderBy('departure_time')
            ->orderBy('booking_code');
        $paginate = $q->paginate(15);

        $items = $paginate->collect()->map(function (Booking $b) {
            $departureCode = sprintf(
                "%s-%s-%s-%s",
                $b->line_code,
                Carbon::parse($b->departure_date)->format('Ymd'),
                Carbon::parse($b->departure_time)->format('Hi'),
                $b->departure_station
            );
            return [
                'booking_code' => $b->booking_code,
                'status' => $b->status,
                'first_name' => $b->first_name,
                'last_name' => $b->last_name,
                'email' => $b->email,
                'phone' => $b->phone,
                'fare_cny' => number_format($b->fare_cny, 2),
                'total_fare_cny' => number_format($b->total_fare_cny, 2),
                'departure_code' => $departureCode,
                'seats' => $b->seats,
                'created_at' => $b->created_at,
                'updated_at' => $b->updated_at,
                'cancelled_at' => $b->cancelled_at,
            ];
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $paginate->currentPage(),
                'last_page' => $paginate->lastPage(),
                'per_page' => $paginate->perPage(),
                'total' => $paginate->total()
            ]
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function lines(Request $request)
    {
        $data = $request->validate([
            "code" => "required|string|uppercase|min:2|max:4|unique:lines,line_code",
            "name" => "required|string",
            'station_a_code' => 'required|exists:stations,station_code',
            'station_b_code' => 'required|exists:stations,station_code|different:station_a_code',
            "seat_capacity" => "required|integer|min:1|max:500",
            "crossing_minutes" => "required|integer|min:1|max:120",
            'fare_cny' => 'required|numeric|min:0|max:999.99',
            'status' => 'nullable|in:active,suspended',
        ]);

        $line = Line::create([
            'line_code' => $data['code'],
            'line_name' => $data['name'],
            'status' => $data['status'] ?? 'active',
            'station_a_code' => $data['station_a_code'],
            'station_b_code' => $data['station_b_code'],
            'seat_capacity' => $data['seat_capacity'],
            'crossing_minutes' => $data['crossing_minutes'],
            'fare_cny' => $data['fare_cny'],
        ]);

        return response()->json([
            'data' => [
                'code' => $line->line_code,
                'name' => $line->line_name,
                'status' => $line->status,
                'station_a' => [
                    'code' => $line->stationA->station_code,
                    'name' => $line->stationA->station_name
                ],
                'station_b' => [
                    'code' => $line->stationB->station_code,
                    'name' => $line->stationB->station_name
                ],
                'fare_cny' => number_format($line->fare_cny, 2),
                'seat_capacity' => $line->seat_capacity,
                'crossing_minutes' => $line->crossing_minutes,
                'service_windows' => $line->service_windows->map(function ($w) {
                    return [
                        'start_time' => $w->start_time,
                        'end_time' => $w->end_time,
                        'interval_minutes' => $w->interval_minutes
                    ];
                })
            ]
        ], 201);
    }


    function updateLine(Request $request, Line $line)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'station_a_code' => 'required|exists:stations,station_code',
            'station_b_code' => 'required|exists:stations,station_code|different:station_a_code',
            'seat_capacity' => 'required|integer|min:1|max:500',
            'crossing_minutes' => 'required|integer|min:1|max:120',
            'fare_cny' => 'required|numeric|min:0|max:999.99',
            'status' => 'nullable|in:active,suspended',
        ]);

        $newCapacity = $data['seat_capacity'];

        $maxBookedFuture = Booking::where('line_code',$line->code)
            ->where('departure_date','>=',Carbon::now()->format('Y-m-d'))
            ->where('status','confirmed')
            ->sum('seats');
        if ($newCapacity < $maxBookedFuture) {
            return response()->json([
                'message' => 'Capacity is lower than seats already booked'
            ],422);
        }

        $line->update([
            'line_name' => $data['name'],
            'status' => $data['status'] ?? 'active',
            'station_a_code' => $data['station_a_code'],
            'station_b_code' => $data['station_b_code'],
            'seat_capacity' => $data['seat_capacity'],
            'crossing_minutes' => $data['crossing_minutes'],
            'fare_cny' => $data['fare_cny'],
        ]);

        return response()->json([
            'data' => [
                'code' => $line->line_code,
                'name' => $line->line_name,
                'status' => $line->status,
                'station_a' => [
                    'code' => $line->stationA->station_code,
                    'name' => $line->stationA->station_name
                ],
                'station_b' => [
                    'code' => $line->stationB->station_code,
                    'name' => $line->stationB->station_name
                ],
                'fare_cny' => number_format($line->fare_cny, 2),
                'seat_capacity' => $line->seat_capacity,
                'crossing_minutes' => $line->crossing_minutes,
                'service_windows' => $line->service_windows->map(function ($w) {
                    return [
                        'start_time' => $w->start_time,
                        'end_time' => $w->end_time,
                        'interval_minutes' => $w->interval_minutes
                    ];
                })
            ]
        ]);

    }
}
