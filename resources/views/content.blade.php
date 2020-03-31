@extends('admin::index', ['header' => strip_tags('')])

@section('content')
    <h1>
        {!! $header ?? trans('admin.title') !!}
        <small>{!! $description ?? trans('admin.description') !!}</small>
    </h1>
@endsection
