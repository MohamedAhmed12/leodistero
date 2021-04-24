@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Show User</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Id</th>
                <td>{{ $state->id }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $state->name }}</td>
            </tr>

            <tr>
                <th>City</th>
                <td>{{ $state->city->name }}</td>
            </tr>

            <tr>
                <th>Created At</th>
                <td>{{ $state->created_at }}</td>
            </tr>

            <tr>
                <th>Updated At</th>
                <td>{{ $state->updated_at }}</td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <form action="{{ route('states.destroy',$state->id) }}" method="post">
            <a type="button" href="{{ route('states.edit',$state->id) }}" class="btn btn-outline-primary">Edit</a>
            @method('DELETE')
            @csrf
            <button type="submit" class="btn btn-outline-danger">Delete</button>
        </form>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/app.js') }}"></script>
@stop
