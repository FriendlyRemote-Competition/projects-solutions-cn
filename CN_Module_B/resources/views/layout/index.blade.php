<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title',"board")</title>
    @stack('fresh')
    @vite(['resources/css/app.css','resources/js/app.js'])

</head>
<body>
<header class="py-2 border-b border-slate-200 bg-slate-100">
    <div class="layout flex items-center justify-between">
        <h1>
            HuangPuLink
        </h1>
        <nav class="flex gap-4 items-center justify-between">
            <a href="{{route("board.index")}}">Home</a>
            <a href="{{route("stats.index")}}">Stats</a>
        </nav>
    </div>
</header>
<main>
    @yield('main')
</main>
</body>
</html>
