<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@if($_title_){{ $_title_ }}@endif</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        @if($_style_)
            @foreach($_style_ as $style)
                {!! $style !!}
            @endforeach
        @endif

        @if($_style_)
            @foreach($_script_ as $script)
                {!! $script !!}
            @endforeach
        @endif
    </head>
    <body>
        @if($_content_)
            {!! $_content_ !!}
        @endif
    </body>
</html>
