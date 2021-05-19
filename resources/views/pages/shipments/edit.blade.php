@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
<form method="POST" action="{{ route('shipments.update',$shipment->id) }}" enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <div class="card">

        <div class="card-header">
            <h3>Edit Shipment : {{ $shipment->tracking_id }}</h3>
        </div>

        <div class="card-body">
            <div class="form-group">
                <label for="tracking_id">Tracking ID</label>
                <input type="text" name="tracking_id" value="{{ $shipment->tracking_id }}" class="form-control @error('tracking_id') is-invalid @enderror" id="tracking_id" placeholder="Tracking ID" disabled>
                <!-- @if($errors->has("tracking_id"))
                    <div class="alert alert-danger w-100 m-0" role="alert">
                        {{$errors->first("tracking_id")}}
                    </div>
                @endif -->
            </div>
            <div class="form-group">
                <label for="user">User</label>
                <input type="text" name="user" value="{{ $shipment->user->name }}" class="form-control " disabled>
            </div>
            <div class="form-group">
                <label for="provider">Provider</label>
                <input type="text" name="provider" value="{{ $shipment->provider }}" class="form-control " disabled>
            </div>
            <div class="form-group">
                <label for="status">status</label>
                <select class="form-control" name="status" id="status">
                    <option></option>
                    <option value="1" {{ $shipment->status == 'pending admin review' ? 'selected' : '' }}>pending admin review</option>
                    <option value="2" {{ $shipment->status == 'pending need updates' ? 'selected' : '' }}>pending need updates</option>
                    <option value="3" {{ $shipment->status == 'pending user payment' ? 'selected' : '' }}>pending user payment</option>
                    <option value="4" {{ $shipment->status == 'paid' ? 'selected' : '' }}>paid</option>
                    <option value="5" {{ $shipment->status == 'shipped' ? 'selected' : '' }}>shipped</option>
                </select>
                @if($errors->has("status"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("status")}}
                </div>
                @endif
            </div>
            <div class="form-group">
                <label for="provider_status">Provider Status</label>
                <input type="text" name="provider_status" value="{{ $shipment->provider_status }}" class="form-control " disabled>
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <button type="reset" class="btn btn-success">Reset</button>
        </div>
    </div>
</form>

@stop

@section('css')
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stop

@section('js')
<script src="{{ asset('js/app.js') }}"></script>
@stop