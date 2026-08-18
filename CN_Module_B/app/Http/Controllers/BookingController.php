<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Line;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    //

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function store(Request $request)
    {
        $data = $request->validate([
            "departure_code" => "required",
            "first_name" => "required|max:60",
            "last_name" => "required|max:60",
            "email" => "required|email:strict",
            "phone" => "nullable",
            "seats" => ["required", "numeric", "min:1", "max:16", function ($attribute, $value, $fail) use ($request) {
                $code = $request->input("departure_code");
                $parts = explode("-", $code);
                [$lineCode, $ymd, $hi, $stationCode] = $parts;
                $dt = Carbon::createFromFormat('Ymd', $ymd)->format('Y-m-d');
                $h = (int)substr($hi, 0, 2);
                $m = (int)substr($hi, 2, 2);
                $hours = sprintf("%02d:%02d", $h, $m);


                $line = Line::where('line_code', $lineCode)->first();
                if (!$line) {
                    return $fail("Departure not found");
                }
                if ($line->status === 'suspended') {
                    return $fail("Departure not found");
                }
                $targetCarbon = \Carbon\Carbon::parse("$dt $hours");
                $hasValidWindow = false;
                foreach ($line->service_windows as $win) {
                    $winStart = \Carbon\Carbon::parse("$dt {$win->start_time}");
                    $winEnd = \Carbon\Carbon::parse("$dt {$win->end_time}");
                    if ($targetCarbon->between($winStart, $winEnd)) {
                        $hasValidWindow = true;
                        break;
                    }
                }
                if (!$hasValidWindow) {
                    return $fail("Departure not found");
                }

                if ($stationCode !== $line->station_a_code && $stationCode !== $line->station_b_code) {
                    return $fail("Departure not found");
                }

                $booked = Booking::where([
                    'line_code' => $lineCode,
                    'departure_date' => $dt,
                    'departure_time' => $hours,
                    'departure_station' => $stationCode,
                    'status' => 'confirmed'
                ])->sum('seats');

                $available = $line->seat_capacity - $booked;
                if ($value > $available) {
                    return $fail("Not enough seats available");
                }
            }]
        ]);
        $parts = explode("-", $data['departure_code']);
        [$lineCode, $ymd, $hi, $stationCode] = $parts;
        $line = Line::where('line_code', $lineCode)->first();
        $dt = Carbon::createFromFormat('Ymd', $ymd)->format('Y-m-d');
        $h = (int)substr($hi, 0, 2);
        $m = (int)substr($hi, 2, 2);
        $hours = sprintf("%02d:%02d", $h, $m);
        do {
            $rand = strtoupper(\Illuminate\Support\Str::random(6));
            $bookingCode = "HPF-" . $rand;
        } while (Booking::where('booking_code', $bookingCode)->exists());


        $book = Booking::create([
            'booking_code' => $bookingCode,
            'status' => 'confirmed',
            'line_code' => $lineCode,
            'departure_date' => $dt,
            'departure_time' => $hours,
            'departure_station' => $stationCode,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'seats' => $data['seats'],
            'fare_cny' => $line->fare_cny,
            'total_fare_cny' => $line->fare_cny * $data['seats'],
            'cancelled_at' => null,
        ]);


        return response()->json([
            "data" => [
                'booking_code' => $book->booking_code,
                'status' => $book->status,
                'first_name' => $book->first_name,
                'last_name' => $book->last_name,
                'email' => $book->email,
                'phone' => $book->phone,
                'fare_cny' => number_format($book->fare_cny, 2),
                'total_fare_cny' => number_format($book->total_fare_cny, 2),
                "departure_code" => $data['departure_code'],
                "seats" => $book->seats,
                "created_at" => $book->created_at,
                "updated_at" => $book->updated_at,
                'cancelled_at' => $book->cancelled_at,
            ]
        ], 201);
    }


    function lookup(Request $request)
    {
        $data = $request->validate([
            "booking_code" => "required",
            "first_name" => "required",
            "last_name" => "required",
        ]);
        $inputCode = $data['booking_code'];
        $inputFirst = trim($data['first_name']);
        $inputLast = trim($data['last_name']);
        $book = Booking::where('booking_code', $inputCode)
            ->whereRaw('TRIM(LOWER(first_name)) = ?', [strtolower($inputFirst)])
            ->whereRaw('TRIM(LOWER(last_name)) = ?', [strtolower($inputLast)])
            ->first();

        if (!$book) {
            abort(404);
        }

        $departureCode = sprintf(
            "%s-%s-%s-%s",
            $book->line_code,
            Carbon::parse($book->departure_date)->format('Ymd'),
            Carbon::parse($book->departure_time)->format('Hi'),
            $book->departure_station
        );


        return response()->json([
            "data" => [
                'booking_code' => $book->booking_code,
                'status' => $book->status,
                'first_name' => $book->first_name,
                'last_name' => $book->last_name,
                'email' => $book->email,
                'phone' => $book->phone,
                'fare_cny' => number_format($book->fare_cny, 2),
                'total_fare_cny' => number_format($book->total_fare_cny, 2),
                "departure_code" => $departureCode,
                "seats" => $book->seats,
                "created_at" => $book->created_at,
                "updated_at" => $book->updated_at,
                'cancelled_at' => $book->cancelled_at,
            ]
        ]);
    }

    function update(Request $request, $code)
    {
        $data = $request->validate([
            "seats" => "required|min:1|max:16",
            "first_name" => "required",
            "last_name" => "required",

        ]);
        $inputFirst = trim($data['first_name']);
        $inputLast = trim($data['last_name']);
        $book = Booking::where('booking_code', $code)
            ->whereRaw('TRIM(LOWER(first_name)) = ?', [strtolower($inputFirst)])
            ->whereRaw('TRIM(LOWER(last_name)) = ?', [strtolower($inputLast)])
            ->first();

        if (!$book) {
            abort(404);
        }
        if ($book->status !== "confirmed") abort(422, "Booking is already cancelled");
        $newSeats = $data['seats'];

        $otherBookedSeats = Booking::where([
            'line_code' => $book->line_code,
            'departure_date' => $book->departure_date,
            'departure_time' => $book->departure_time,
            'departure_station' => $book->departure_station,
            'status' => 'confirmed'
        ])
            ->where('id', '!=', $book->id)
            ->sum('seats');
        $line = Line::where("line_code", $book->line_code)->first();
        $capacity = $line->seat_capacity;
        $available = $capacity - $otherBookedSeats;

        if ($newSeats > $available) {
            return response()->json([
                "message" => "Validation failed",
                "errors" => [
                    "seats" => "Not enough seats available"
                ]
            ], 422);
        }

        $book->seats = $newSeats;
        $book->total_fare_cny = $book->fare_cny * $newSeats;
        $book->save();

        $departureCode = sprintf(
            "%s-%s-%s-%s",
            $book->line_code,
            Carbon::parse($book->departure_date)->format('Ymd'),
            Carbon::parse($book->departure_time)->format('Hi'),
            $book->departure_station
        );

        return response()->json([
            'data' => [
                'booking_code' => $book->booking_code,
                'status' => $book->status,
                'first_name' => $book->first_name,
                'last_name' => $book->last_name,
                'email' => $book->email,
                'phone' => $book->phone,
                'fare_cny' => number_format($book->fare_cny, 2),
                'total_fare_cny' => number_format($book->total_fare_cny, 2),
                'departure_code' => $departureCode,
                'seats' => $book->seats,
                'created_at' => $book->created_at,
                'updated_at' => $book->updated_at,
                'cancelled_at' => $book->cancelled_at,
            ]
        ]);

    }


    function cancel(Request $request, $code)
    {
        $data = $request->validate([
            "first_name" => "required",
            "last_name" => "required",

        ]);
        $inputFirst = trim($data['first_name']);
        $inputLast = trim($data['last_name']);
        $book = Booking::where('booking_code', $code)
            ->whereRaw('TRIM(LOWER(first_name)) = ?', [strtolower($inputFirst)])
            ->whereRaw('TRIM(LOWER(last_name)) = ?', [strtolower($inputLast)])
            ->first();

        if (!$book) {
            abort(404);
        }
        if ($book->status !== "confirmed") abort(422, "Booking is already cancelled");
        $departureDatetime = Carbon::parse($book->departure_date . ' ' . $book->departure_time);
        $now = Carbon::now();

        if ($now->diffInMinutes($departureDatetime, false) <= 5) {
            return response()->json([
                'message' => 'Booking closed for this departure'
            ], 422);
        }
        $book->status = 'cancelled';
        $book->cancelled_at = Carbon::now();
        $book->save();
        $departureCode = sprintf(
            "%s-%s-%s-%s",
            $book->line_code,
            Carbon::parse($book->departure_date)->format('Ymd'),
            Carbon::parse($book->departure_time)->format('Hi'),
            $book->departure_station
        );
        return response()->json([
            'data' => [
                'booking_code' => $book->booking_code,
                'status' => $book->status,
                'first_name' => $book->first_name,
                'last_name' => $book->last_name,
                'email' => $book->email,
                'phone' => $book->phone,
                'fare_cny' => number_format($book->fare_cny, 2),
                'total_fare_cny' => number_format($book->total_fare_cny, 2),
                'departure_code' => $departureCode,
                'seats' => $book->seats,
                'created_at' => $book->created_at,
                'updated_at' => $book->updated_at,
                'cancelled_at' => $book->cancelled_at,
            ]
        ]);
    }

}
