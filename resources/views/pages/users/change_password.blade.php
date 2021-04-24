@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
<form method="POST" action="{{ route('users.change_password') }}">
    @csrf
    <div class="card">
        
        <div class="card-header">
            <h3>Update Password</h3>
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
                <label for="current_password">Current Password</label>
                <input type="password" name="current_password" value="{{ old('current_password') }}" class="form-control @error('current_password') is-invalid @enderror" id="current_password" placeholder="Current Password">
                @if($errors->has("current_password"))
                    <div class="alert alert-danger w-100 m-0" role="alert">
                        {{$errors->first("current_password")}}
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Password">
                @if($errors->has("password"))
                    <div class="alert alert-danger w-100 m-0" role="alert">
                        {{$errors->first("password")}}
                    </div>
                @endif
            </div>
            
            <div class="form-group">
                <label for="password">New Password Confirmation</label>
                <input type="password" name="password_confirmation" class="form-control" id="password" placeholder="Password Confirmation">
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
