@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
<form method="POST" action="{{ route('cities.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card">

        <div class="card-header">
            <h3>Create City</h3>
        </div>

        <div class="card-body">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Username">
                @if($errors->has("name"))
                    <div class="alert alert-danger w-100 m-0" role="alert">
                        {{$errors->first("name")}}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="country_id">Country</label>
                <select class="form-control" name="country_id">
                    <option></option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}}</option>
                    @endforeach
                </select>
                @if($errors->has("country_id"))
                    <div class="alert alert-danger w-100 m-0" role="alert">
                        {{$errors->first("country_id")}}
                    </div>
                @endif
            </div>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Create</button>
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
