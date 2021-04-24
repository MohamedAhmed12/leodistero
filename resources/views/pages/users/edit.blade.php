@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
<form method="POST" action="{{ route('users.update',$user->id) }}" enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <div class="card">

        <div class="card-header">
            <h3>Edit User : {{ $user->name }}</h3>
        </div>

        <div class="card-body">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" value="{{ $user->name }}" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Username">
                @if($errors->has("name"))
                    <div class="alert alert-danger w-100 m-0" role="alert">
                        {{$errors->first("name")}}
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" value="{{ $user->email }}" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Useremail">
                        @if($errors->has("email"))
                            <div class="alert alert-danger w-100 m-0" role="alert">
                                {{$errors->first("email")}}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="phone_number">Phone number</label>
                        <input type="text" name="phone_number" value="{{ $user->phone_number }}" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" placeholder="Userphone_number">
                        @if($errors->has("phone_number"))
                            <div class="alert alert-danger w-100 m-0" role="alert">
                                {{$errors->first("phone_number")}}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Password">
                        @if($errors->has("password"))
                            <div class="alert alert-danger w-100 m-0" role="alert">
                                {{$errors->first("password")}}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="password">Password Confirmation</label>
                        <input type="password" name="password_confirmation" class="form-control" id="password" placeholder="Password Confirmation">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="company">Company</label>
                <input type="text" name="company" value="{{ $user->company }}" class="form-control @error('company') is-invalid @enderror" id="company" placeholder="Usercompany">
                @if($errors->has("company"))
                    <div class="alert alert-danger w-100 m-0" role="alert">
                        {{$errors->first("company")}}
                    </div>
                @endif
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="address_line_1">Address Line 1</label>
                        <input type="text" name="address_line_1" value="{{ $user->address_line_1 }}" class="form-control @error('address_line_1') is-invalid @enderror" id="address_line_1" placeholder="Useraddress_line_1">
                        @if($errors->has("address_line_1"))
                            <div class="alert alert-danger w-100 m-0" role="alert">
                                {{$errors->first("address_line_1")}}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="address_line_2">Address Line 2</label>
                        <input type="text" name="address_line_2" value="{{ $user->address_line_2 }}" class="form-control @error('address_line_2') is-invalid @enderror" id="address_line_2" placeholder="Useraddress_line_2">
                        @if($errors->has("address_line_2"))
                            <div class="alert alert-danger w-100 m-0" role="alert">
                                {{$errors->first("address_line_2")}}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="country_id">Country</label>
                        <select class="form-control" name="country_id">
                            <option></option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" {{ $country->id == $user->country_id ? 'selected' : '' }}>{{ $country->name }}}</option>
                            @endforeach
                        </select>
                        @if($errors->has("country_id"))
                            <div class="alert alert-danger w-100 m-0" role="alert">
                                {{$errors->first("country_id")}}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="city_id">City</label>
                        <select class="form-control" name="city_id">
                            <option></option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}" {{ $city->id == $user->city_id ? 'selected' : '' }}>{{ $city->name }}}</option>
                            @endforeach
                        </select>
                        @if($errors->has("city_id"))
                            <div class="alert alert-danger w-100 m-0" role="alert">
                                {{$errors->first("city_id")}}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="state_id">State</label>
                        <select class="form-control" name="state_id">
                            <option></option>
                            @foreach ($states as $state)
                                <option value="{{ $state->id }}" {{ $state->id == $user->state_id ? 'selected' : '' }}>{{ $state->name }}}</option>
                            @endforeach

                        </select>
                        @if($errors->has("state_id"))
                            <div class="alert alert-danger w-100 m-0" role="alert">
                                {{$errors->first("state_id")}}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="zip_code">Zip Code</label>
                        <input type="zip_code" value="{{ $user->zip_code }}" name="zip_code" class="form-control" id="zip_code" placeholder="Zip Code">
                        @if($errors->has("zip_code"))
                            <div class="alert alert-danger w-100 m-0" role="alert">
                                {{$errors->first("zip_code")}}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="id_front">ID front</label>
                        <input class="d-block {{$errors->has("id_front") ? 'is-invalid' : ''}}" type="file" name="id_front"/>
                        @if($errors->has("id_front"))
                            <div class="alert alert-danger w-100 m-0" role="alert">
                                {{$errors->first("id_front")}}
                            </div>
                        @endif
                        @if(isset($edit))
                            <span><img width="160px" src="{{ $edit->getFirstMediaUrl("id_front")}}"/> </span>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="id_back">ID back</label>
                        <input class="d-block {{$errors->has("id_back") ? 'is-invalid' : ''}}" type="file" name="id_back"/>
                        @if($errors->has("id_back"))
                            <div class="alert alert-danger w-100 m-0" role="alert">
                                {{$errors->first("id_back")}}
                            </div>
                        @endif
                        @if(isset($edit))
                            <span><img width="160px" src="{{ $edit->getFirstMediaUrl("id_back")}}"/> </span>
                        @endif
                    </div>
                </div>
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
