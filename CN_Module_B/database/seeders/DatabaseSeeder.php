<?php

namespace Database\Seeders;

use App\Models\cancelledDeparture;
use App\Models\Line;
use App\Models\Station;
use App\Models\User;
use Dotenv\Parser\Lines;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminsFile = fopen(database_path("csv_file/admins.csv"), "r");
        $adminHeaders = fgetcsv($adminsFile);
        while (($row = fgetcsv($adminsFile)) !== false) {

            $data = array_combine($adminHeaders, $row);
            User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => $data['role'],
                'is_active' => $data['is_active'],
            ]);
        }


        $stationsFile = fopen(database_path("csv_file/stations.csv"), "r");

        $stationHeaders = fgetcsv($stationsFile);

        while (($row = fgetcsv($stationsFile)) !== false) {
            $data = array_combine($stationHeaders, $row);
            Station::create([
                'station_code' => $data['station_code'],
                'station_name' => $data['station_name'],
                'bank' => $data['bank'],
                'district' => $data['district'],
                'address' => $data['address'],
            ]);
        }


        $linesFile = fopen(database_path("csv_file/lines.csv"), "r");

        $linesHeaders = fgetcsv($linesFile);
        while (($row = fgetcsv($linesFile)) !== false) {
            $data = array_combine($linesHeaders, $row);

            $line = Line::where('line_code', $data['line_code'])->first();
            if (!$line) {
                $line = Line::create([
                    "line_code" => $data['line_code'],
                    "line_name" => $data['line_name'],
                    "status" => $data['line_status'],
                    "station_a_code" => $data['station_a_code'],
                    "station_b_code" => $data['station_b_code'],
                    "seat_capacity" => $data['seat_capacity'],
                    "crossing_minutes" => $data['crossing_minutes'],
                    "fare_cny" => $data['fare_cny'],
                ]);
            }
            $line->service_windows()->create([
                "start_time" => $data['service_start'],
                "end_time" => $data['service_end'],
                "interval_minutes" => $data['interval_minutes'],
            ]);
        }


        $cancelled_departure_file = fopen(database_path("csv_file/cancelled_departures.csv"), 'r');

        $departureHeaders = fgetcsv($cancelled_departure_file);

        while (($row = fgetcsv($cancelled_departure_file)) !== false) {
            $data = array_combine($departureHeaders, $row);

            CancelledDeparture::create([
                "line_code" => $data['line_code'],
                "cancelled_at" => $data['cancelled_at'],
                "reason" => $data['reason'],
                'departure_date' => $data['departure_date'],
                "departure_time" => $data['departure_time'],
                "departure_station" => $data['departure_station'],
            ]);
        }

    }
}
