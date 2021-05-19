@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Show Shipment</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Id</th>
                <td>{{ $shipment->id }}</td>
            </tr>
            <tr>
                <th>Tracking ID</th>
                <td>{{ $shipment->tracking_id }}</td>
            </tr>

            <tr>
                <th>User</th>
                <td>{{ $shipment->user->name }}</td>
            </tr>

            <tr>
                <th>Provider</th>
                <td>{{ $shipment->provider }}</td>
            </tr>

            <tr>
                <th>Status</th>
                <td>{{ $shipment->status }}</td>
            </tr>
            
            <tr>0

            <tr>
                <th>Created At</th>
                <td>{{ $shipment->created_at }}</td>
            </tr>

            <tr>
                <th>Updated At</th>
                <td>{{ $shipment->updated_at }}</td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <form action="{{ route('shipments.destroy',$shipment->id) }}" method="post">
            <a type="button" href="{{ route('shipments.edit',$shipment->id) }}" class="btn btn-outline-primary">Edit</a>
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
