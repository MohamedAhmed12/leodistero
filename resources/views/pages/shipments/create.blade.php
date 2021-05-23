@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
<form method="POST" action="{{ route('shipments.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card">

        <div class="card-header">
            <h3>Create Shipment</h3>
        </div>
        
        <div class="card-body">
            <div class="form-group">
                <label for="name">Shipper Name</label>
                <input type="text" name="shipper_name" value="{{ old('shipper_name') }}" class="form-control @error('shipper_name') is-invalid @enderror" id="shipper_name" placeholder="shipper Name">
                @if($errors->has("shipper_name"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("shipper_name")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Shipper Email</label>
                <input type="text" name="shipper_email" value="{{ old('shipper_email') }}" class="form-control @error('shipper_email') is-invalid @enderror" id="shipper_email" placeholder="Shipper Email">
                @if($errors->has("shipper_email"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("shipper_email")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Shipper Phone Number</label>
                <input type="text" name="shipper_number" value="{{ old('shipper_number') }}" class="form-control @error('shipper_number') is-invalid @enderror" id="shipper_number" placeholder="Shipper Phone Number">
                @if($errors->has("shipper_number"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("shipper_number")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Shipper Country</label>
                <select name="shipper_country_id" id="shipper_country_id" class="form-control @error('shipper_country_id') is-invalid @enderror" placeholder="shipper Country">
                    <option disabled selected>Select Country</option>
                    @foreach ($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
                @if($errors->has("shipper_country_id"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("shipper_country_id")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Shipper City</label>
                <select name="shipper_city_id" id="shipper_city_id" class="form-control @error('shipper_city_id') is-invalid @enderror" placeholder="Shipper City">
                    <option disabled selected>Select City</option>
                    @foreach ($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }} ({{ $city->country_code }})</option>
                    @endforeach
                </select>
                @if($errors->has("shipper_city_id"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("shipper_city_id")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Shipper Address</label>
                <input type="text" name="shipper_adress_line" value="{{ old('shipper_adress_line') }}" class="form-control @error('shipper_adress_line') is-invalid @enderror" id="shipper_adress_line" placeholder="Shipper Address">
                @if($errors->has("shipper_adress_line"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("shipper_adress_line")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Shipper Zip_code</label>
                <input type="text" name="shipper_zip_code" value="{{ old('shipper_zip_code') }}" class="form-control @error('shipper_zip_code') is-invalid @enderror" id="shipper_zip_code" placeholder="Shipper Zip_code">
                @if($errors->has("shipper_zip_code"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("shipper_zip_code")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Recipient Name</label>
                <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" class="form-control @error('recipient_name') is-invalid @enderror" id="recipient_name" placeholder="Recipient Name">
                @if($errors->has("recipient_name"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("recipient_name")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Recipient Email</label>
                <input type="text" name="recipient_email" value="{{ old('recipient_email') }}" class="form-control @error('recipient_email') is-invalid @enderror" id="recipient_email" placeholder="Recipient Email">
                @if($errors->has("recipient_email"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("recipient_email")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Recipient Phone Number</label>
                <input type="text" name="recipient_number" value="{{ old('recipient_number') }}" class="form-control @error('recipient_number') is-invalid @enderror" id="recipient_number" placeholder="Recipient Phone Number">
                @if($errors->has("recipient_number"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("recipient_number")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Recipient Country</label>
                <select name="recipient_country_id" id="recipient_country_id" class="form-control @error('recipient_country_id') is-invalid @enderror" placeholder="Recipient Country">
                    <option disabled selected>Select Country</option>
                    @foreach ($countries as $country)
                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
                @if($errors->has("recipient_country_id"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("recipient_country_id")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Recipient City</label>
                <select name="recipient_city_id" id="recipient_city_id" class="form-control @error('recipient_city_id') is-invalid @enderror" placeholder="Recipient City">
                    <option disabled selected>Select City</option>
                    @foreach ($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }} ({{ $city->country_code }})</option>
                    @endforeach
                </select>
                @if($errors->has("recipient_city_id"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("recipient_city_id")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Recipient Address</label>
                <input type="text" name="recipient_adress_line" value="{{ old('recipient_adress_line') }}" class="form-control @error('recipient_adress_line') is-invalid @enderror" id="recipient_adress_line" placeholder="Recipient Address">
                @if($errors->has("recipient_adress_line"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("recipient_adress_line")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Recipient Zip_code</label>
                <input type="text" name="recipient_zip_code" value="{{ old('recipient_zip_code') }}" class="form-control @error('recipient_zip_code') is-invalid @enderror" id="recipient_zip_code" placeholder="Recipient Zip_code">
                @if($errors->has("recipient_zip_code"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("recipient_zip_code")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Package Weight</label>
                <input type="text" name="package_weight" value="{{ old('package_weight') }}" class="form-control @error('package_weight') is-invalid @enderror" id="package_weight" placeholder="Package Weight">
                @if($errors->has("package_weight"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("package_weight")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Package Length</label>
                <input type="text" name="package_length" value="{{ old('package_length') }}" class="form-control @error('package_length') is-invalid @enderror" id="package_length" placeholder="Package Length">
                @if($errors->has("package_length"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("package_length")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Package Width</label>
                <input type="text" name="package_width" value="{{ old('package_width') }}" class="form-control @error('package_width') is-invalid @enderror" id="package_width" placeholder="Package Width">
                @if($errors->has("package_width"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("package_width")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Package Height</label>
                <input type="text" name="package_height" value="{{ old('package_height') }}" class="form-control @error('package_height') is-invalid @enderror" id="package_height" placeholder="Package Height">
                @if($errors->has("package_height"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("package_height")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Package Value</label>
                <input type="text" name="package_value" value="{{ old('package_value') }}" class="form-control @error('package_value') is-invalid @enderror" id="package_value" placeholder="Package Value">
                @if($errors->has("package_value"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("package_value")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Package Quantity</label>
                <input type="text" name="package_quantity" value="{{ old('package_quantity') }}" class="form-control @error('package_quantity') is-invalid @enderror" id="package_quantity" placeholder="Package Quantity">
                @if($errors->has("package_quantity"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("package_quantity")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Package Pickup Location</label>
                <input type="text" name="package_pickup_location" value="{{ old('package_pickup_location') }}" class="form-control @error('package_pickup_location') is-invalid @enderror" id="package_pickup_location" placeholder="Package Pickup Location">
                @if($errors->has("package_pickup_location"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("package_pickup_location")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Package Description</label>
                <input type="text" name="package_description" value="{{ old('package_description') }}" class="form-control @error('package_description') is-invalid @enderror" id="package_description" placeholder="Package Description">
                @if($errors->has("package_description"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("package_description")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Package Shipping Date</label>
                <input class="form-control" type="datetime-local" name="package_shipping_date_time" value="{{ old('package_shipping_date_time') }}" class="form-control @error('package_shipping_date_time') is-invalid @enderror" id="package_shipping_date_time">
                @if($errors->has("package_shipping_date_time"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("package_shipping_date_time")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Package Due Date</label>
                <input class="form-control" type="datetime-local" name="package_due_date" value="{{ old('package_due_date') }}" class="form-control @error('package_due_date') is-invalid @enderror" id="package_due_date">
                @if($errors->has("package_due_date"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("package_due_date")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Package Shipping Type</label>
                <select name="package_shipment_type" id="package_shipment_type" class="form-control @error('package_shipment_type') is-invalid @enderror">
                    <option disabled selected>Select Shipping Type</option>
                    <option>express</option>
                    <option>standard</option>
                </select>
                @if($errors->has("package_shipment_type"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("package_shipment_type")}}
                </div>
                @endif
            </div>

            <div class="form-group">
                <label for="name">Provider</label>
                <select name="provider" id="provider" class="form-control @error('provider') is-invalid @enderror" placeholder="Recipient Country">
                    <option disabled selected>Select Provider</option>
                    @foreach ($providers as $provider)
                    <option value="{{ $provider }}">{{ $provider }}</option>
                    @endforeach
                </select>
                @if($errors->has("provider"))
                <div class="alert alert-danger w-100 m-0" role="alert">
                    {{$errors->first("provider")}}
                </div>
                @endif
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