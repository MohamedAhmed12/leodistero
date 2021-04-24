@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
<form method="POST" action="{{ route('users.profile') }}">
    @csrf
    <div class="card">
        
        <div class="card-header">
            <h3>Update Profile</h3>
        </div>

        <div class="card-body">
            
            @if(session()->has('message'))
            <div class="row">
                <div class="col">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session()->get('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            </div>
            @endif
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Name">
                @if($errors->has("name"))
                    <div class="alert alert-danger w-100 m-0" role="alert">
                        {{$errors->first("name")}}
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
