@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>List Countries</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        @if(session()->has('can_not_be_deleted') == true)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong> {{  session()->get('can_not_be_deleted') }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{$dataTable->table()}}
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    {{$dataTable->scripts()}}
@stop
