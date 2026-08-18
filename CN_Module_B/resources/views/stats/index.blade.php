@extends('layout.index')

@section('main')
    <div class="layout !mt-10">
        {{-- stats--}}
        <h2>Stats</h2>

        {{--grid container--}}
        <div class="grid gap-4  my-5 grid-cols-4">
            <article
                class="border flex flex-col gap-2 items-start hover:shadow-lg duration-200 border-slate-200 rounded-2xl p-4">
                <h3>Total Departures</h3>
                <span>{{$global['total_departures']}}</span>
            </article>

            <article
                class="border flex flex-col gap-2 items-start hover:shadow-lg duration-200 border-slate-200 rounded-2xl p-4">
                <h3>Cancelled Departures</h3>
                <span>{{$global['cancelled_departures']}}</span>
            </article>

            <article
                class="border flex flex-col gap-2 items-start hover:shadow-lg duration-200 border-slate-200 rounded-2xl p-4">
                <h3>Total Booking</h3>
                <span>{{$global['total_bookings']}}</span>
            </article>

            <article
                class="border flex flex-col gap-2 items-start hover:shadow-lg duration-200 border-slate-200 rounded-2xl p-4">
                <h3>Cancel Booking</h3>
                <span>{{$global['cancelled_bookings']}}</span>
            </article>

            <article
                class="border flex flex-col gap-2 items-start hover:shadow-lg duration-200 border-slate-200 rounded-2xl p-4">
                <h3>Seats Booked</h3>
                <span>{{$global['seats_booked']}}</span>
            </article>

            <article
                class="border flex flex-col gap-2 items-start hover:shadow-lg duration-200 border-slate-200 rounded-2xl p-4">
                <h3>Revenue</h3>
                <span>{{$global['revenue']}}</span>
            </article>
        </div>
        <div class="table my-5">
            <table>
                <thead>
                <tr>
                    <th>Line Code</th>
                    <th>Line Name</th>
                    <th>Total Departures</th>
                    <th>Cancel Departures</th>
                    <th>Total Booking</th>
                    <th>Cancel Booking</th>
                    <th>Seat Booked</th>
                    <th>Revenue</th>
                </tr>
                </thead>
                <tbody>
                @foreach($lineStats as $line)
                    <tr>
                        <td>{{$line['code']}}</td>
                        <td>{{$line['name']}}</td>
                        <td>{{$line['total_departures']}}</td>
                        <td>{{$line['cancelled_departures']}}</td>
                        <td>{{$line['total_bookings']}}</td>
                        <td>{{$line['cancelled_bookings']}}</td>
                        <td>{{$line['seats_booked']}}</td>
                        <td>{{$line['revenue']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
