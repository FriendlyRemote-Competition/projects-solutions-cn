@extends('layout.index')

@section('main')
    <div class="layout !mt-10">
        <h2>Station List</h2>
        <div class="grid gap-4  my-5 grid-cols-4">
            @foreach($stations as $station)
                <article
                    class="border flex flex-col gap-2 items-start hover:shadow-lg duration-200 border-slate-200 rounded-2xl p-4">
                    <h3>{{$station->station_name}}</h3>
                    <a href="{{route("board.show",['stationCode'=>$station->station_code])}}"
                       class="primary btn">Detail</a>
                </article>
            @endforeach
        </div>
    </div>

@endsection
