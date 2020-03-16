<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ Admin::title() }} @if($header) | {{ $header }}@endif</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Scripts -->
    <script src="{{ Admin::jQuery() }}"></script>
    {!! Admin::headerJs() !!}

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    {!! Admin::css() !!}

</head>

<body class="hold-transition {{ config('admin.skin') }} {{ join(' ', config('admin.layout')) }}">

@if($alert = config('admin.top_alert'))
    <div style="text-align: center;padding: 5px;font-size: 12px;background-color: #ffffd5;color: #ff0000;">
        {!! $alert !!}
    </div>
@endif

<div class="wrapper">

    @include('admin::partials.header')

    <div class="content-wrapper" id="pjax-container">
        {!! Admin::style() !!}
        <div id="app">
        @yield('content')
        </div>
        {!! Admin::script() !!}
        {!! Admin::html() !!}
    </div>

    @include('admin::partials.footer')

</div>

<button id="totop" title="Go to top" style="display: none;"><i class="fa fa-chevron-up"></i></button>

<script>
    function LA() {}
    LA.token = "{{ csrf_token() }}";
    LA.user = @json($_user_);
</script>

<!-- REQUIRED JS SCRIPTS -->
{!! Admin::js() !!}

</body>
</html>
@if($error = session()->get('error'))
    失敗
@elseif ($errors = session()->get('errors'))
    @if ($errors->hasBag('error'))
    多失敗
    @endif
@endif

@if($success = session()->get('success'))
    成功
@endif
