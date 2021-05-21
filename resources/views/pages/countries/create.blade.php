@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
<form method="POST" action="{{ route('countries.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card">

        <div class="card-header">
            <h3>Create Country</h3>
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
                <label for="name">Code</label>
                <input type="text" name="code" value="{{ old('code') }}" class="form-control @error('code') is-invalid @enderror" id="code" placeholder="Country Code">
                @if($errors->has("code"))
                    <div class="alert alert-danger w-100 m-0" role="alert">
                        {{$errors->first("code")}}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label for="name">Capital</label>
                <input type="text" name="capital" value="{{ old('capital') }}" class="form-control @error('capital') is-invalid @enderror" id="capital" placeholder="Capital">
                @if($errors->has("capital"))
                    <div class="alert alert-danger w-100 m-0" role="alert">
                        {{$errors->first("capital")}}
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
