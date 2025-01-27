<!DOCTYPE html>
<html lang="zh" class="{{ $theme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield("description", Config::get("app.description"))">
    <meta name="author" content="CreamGod45">
    <meta name="copyright" content="@yield('title', Config::get("app.name"))">
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('head', "")
    <title>@yield('title')</title>
</head>
@include('layouts.style')
<body>
@if($menu)
    @include('layouts.menu')
@endif

@yield('content')

@include('layouts.notification')
@if($footer)
    @include('layouts.footer')
@endif
</body>
</html>
