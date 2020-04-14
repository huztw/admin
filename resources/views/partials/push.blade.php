@push($key)
@if (is_array($value))
@foreach($value as $v)
        {!! $v !!}        
@endforeach
@else
        {!! $value !!}  
@endif
@endpush