@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
<form method="POST" action="{{ route('cities.update',$city->id) }}" enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <div class="card">

        <div class="card-header">
            <h3>Edit City : {{ $city->name }}</h3>
        </div>

        <div class="card-body">
            <div class="form-group">
                <label for="country">Country</label>
                <select class="form-control" name="country" placeholder="Country">
                    <option></option>
                    @foreach ($countries as $country)
                    <option value="{{ $country }}" {{ $country->id == $city->country_id ? 'selected' : '' }}>{{ $country->name }}</option>
                    @endforeach
                </select>
                @if($errors->has("country"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("country")}}
                </div>
                @endif
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" value="{{ $city->name }}" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Name">
                @if($errors->has("name"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("name")}}
                </div>
                @endif
            </div>
            <div class="form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" name="postal_code" value="{{ $city->postal_code }}" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" placeholder="Postal Code">
                @if($errors->has("postal_code"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("postal_code")}}
                </div>
                @endif
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