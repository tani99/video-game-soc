@extends('layouts.app')

@section('content')

{{ Form::open(array('url' => 'foo/bar')) }}

{{ Form::close() }}
editGame {{$id}}

@endsection