<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CancelledDeparture;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    //

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    function index(Request $request)
    {
        $stations = Station::orderBy('station_code', 'asc')->get();
        return view("board.index", [
            "stations" => $stations
        ]);
    }


    /**
     * @param Request $request
     * @param $code
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    function show(Request $request, $code)
    {
        $data = $request->validate([
            "limit" => 'nullable|integer|min:1|max:20'
        ]);
        $limit = $data["limit"] ?? 8;
        $station = Station::where("station_code", $code)->firstOrFail();
        $targetDate = Carbon::now();
        $linesAll = [$station->lineA, $station->lineB];
        $departures = collect();

        foreach ($linesAll as $line) {
            $windows = $line->service_windows()->orderBy("start_time")->get();

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
        }

        $departures = $departures->limit($limit);

        return view("board.show", [
            "departures" => $departures
        ]);
    }

    function stats(Request $request)
    {
        $validated = $request->validate([
            'date' => 'nullable|date|date_format:Y-m-d',
        ]);
        $targetDate = !empty($validated['date'])
            ? \Carbon\Carbon::parse($validated['date'])
            : \Carbon\Carbon::now();
        $dateStr = $targetDate->format('Y-m-d');
        $allDepartures = collect();
        $linesAll = \App\Models\Line::with(['stationA', 'stationB', 'service_windows'])->get();


//    all line
        foreach ($linesAll as $line) {
            if ($line->status === 'suspended') continue;

            foreach ($line->service_windows as $win) {
                $startCarbon = \Carbon\Carbon::parse($dateStr . ' ' . $win->start_time);
                $endCarbon = \Carbon\Carbon::parse($dateStr . ' ' . $win->end_time);
                $current = $startCarbon->copy();
                while ($current <= $endCarbon) {
                    foreach ([$line->station_a_code, $line->station_b_code] as $depStationCode) {
                        $depTime = $current->copy();
                        $allDepartures->push([
                            'line_code' => $line->line_code,
                            'departure_date' => $dateStr,
                            'departure_time' => $depTime->format('H:i'),
                            'departure_station' => $depStationCode
                        ]);
                    }
                    $current->addMinutes($win->interval_minutes);
                }
            }
        }
        $cancelledRows = \App\Models\CancelledDeparture::where('departure_date', $dateStr)->get();
        $cancelKeySet = $cancelledRows->map(function ($r) {
            return sprintf("%s|%s|%s|%s", $r->line_code, $r->departure_date, $r->departure_time, $r->departure_station);
        })->flip();

        $totalDepartures = $allDepartures->filter(function ($d) use ($cancelKeySet) {
            $key = sprintf("%s|%s|%s|%s", $d['line_code'], $d['departure_date'], $d['departure_time'], $d['departure_station']);
            return !isset($cancelKeySet[$key]);
        })->count();
        $countCancelledDepartures = $cancelledRows->count();


        $bookingsDay = \App\Models\Booking::where('departure_date', $dateStr)->get();

        $totalBookings = $bookingsDay->count();
        $cancelledBookings = $bookingsDay->where('status', 'cancelled')->count();
        $confirmedBookings = $bookingsDay->where('status', 'confirmed');

        $seatsBooked = $confirmedBookings->sum('seats');
        $revenue = $confirmedBookings->sum('total_fare_cny');

        $lineStats = collect();
        foreach ($linesAll as $line) {
            $lineCode = $line->line_code;

            $lineDeps = $allDepartures->where('line_code', $lineCode);
            $lineCancelDeps = $cancelledRows->where('line_code', $lineCode);

            $lineTotalDep = $lineDeps->filter(function ($d) use ($cancelKeySet) {
                $k = sprintf("%s|%s|%s|%s", $d['line_code'], $d['departure_date'], $d['departure_time'], $d['departure_station']);
                return !isset($cancelKeySet[$k]);
            })->count();
            $lineCancelDep = $lineCancelDeps->count();

            $lineBookings = $bookingsDay->where('line_code', $lineCode);
            $lineTotalBook = $lineBookings->count();
            $lineCancelBook = $lineBookings->where('status', 'cancelled')->count();
            $lineConfirmed = $lineBookings->where('status', 'confirmed');
            $lineSeatsBooked = $lineConfirmed->sum('seats');
            $lineRevenue = $lineConfirmed->sum('total_fare_cny');

            $lineStats->push([
                'code' => $line->line_code,
                'name' => $line->line_name,
                'total_departures' => $lineTotalDep,
                'cancelled_departures' => $lineCancelDep,
                'total_bookings' => $lineTotalBook,
                'cancelled_bookings' => $lineCancelBook,
                'seats_booked' => $lineSeatsBooked,
                'revenue' => number_format($lineRevenue, 2)
            ]);
        }


        $global = [
            'total_departures' => $totalDepartures,
            'cancelled_departures' => $countCancelledDepartures,
            'total_bookings' => $totalBookings,
            'cancelled_bookings' => $cancelledBookings,
            'seats_booked' => $seatsBooked,
            'revenue' => number_format($revenue, 2)
        ];

        return view('stats.index', compact('global', 'lineStats'));
    }
}
