@extends('common.layout')

@section('title', $title)

@section('content')
<div class="position-absolute top-50 start-50 translate-middle col-md-5">
    <p class="error text-center">{{$code}} | {{$title}}</p>
</div>
@endsection