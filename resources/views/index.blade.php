@extends('admin::layout.layout')

@section('style')
    <!-- Styles -->
    <link href="{{ admin_asset('vendor/huztw-admin/css/admin.css') }}" rel="stylesheet">
@endsection

@section('script')
    <!-- Scripts -->
    <script src="{{ admin_asset('vendor/huztw-admin/jQuery/jquery-3.4.1.min.js') }} "></script>
    <script src="{{ admin_asset('vendor/huztw-admin/js/admin.js') }}" defer></script>
@endsection

@section('content')

    @include('admin::partials.header')

    @include('admin::partials.footer')

@endsection