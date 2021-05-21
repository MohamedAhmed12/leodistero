@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Show Country</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Id</th>
                <td>{{ $country->id }}</td>
            </tr>
           
            <tr>
                <th>Name</th>
                <td>{{ $country->name }}</td>
            </tr>

            <tr>
                <th>Code</th>
                <td>{{ $country->code }}</td>
            </tr>

            <tr>
                <th>Capital</th>
                <td>{{ $country->capital }}</td>
            </tr>

            <tr>
                <th>Created At</th>
                <td>{{ $country->created_at }}</td>
            </tr>

            <tr>
                <th>Updated At</th>
                <td>{{ $country->updated_at }}</td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <form action="{{ route('countries.destroy',$country->id) }}" method="post">
            <a type="button" href="{{ route('countries.edit',$country->id) }}" class="btn btn-outline-primary">Edit</a>
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
