@extends('layouts.app')

@section('content')
    <my-leases-component :user="{{ auth()->user() }}"></my-leases-component>
@endsection
