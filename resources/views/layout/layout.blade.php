<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@isset($_title_){{ $_title_ }}@endisset</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        @isset($_style_)
            @foreach($_style_ as $style)
                {!! $style !!}
            @endforeach
        @endisset
        @yield('style')

        @isset($_script_)
            @foreach($_script_ as $script)
                {!! $script !!}
            @endforeach
        @endisset
        @yield('script')
    </head>
    <body>
        @isset($_content_)
            {!! $_content_ !!}
        @endisset
        @yield('content')
    </body>
</html>
