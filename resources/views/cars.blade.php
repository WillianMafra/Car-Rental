@extends('layouts.app')

@section('content')
    <car-component :user="{{auth()->user()}}"></car-component>
@endsection
