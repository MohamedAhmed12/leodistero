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

            <tr>
                <th>Provider Status</th>
                <td>{{ $shipment->provider_status}}</td>
            </tr>
            
            <tr>
                <th>Shipper Name</th>
                <td>{{ $shipment->shipper_name}}</td>
            </tr>
            
            <tr>
                <th>Shipper Email</th>
                <td>{{ $shipment->shipper_email}}</td>
            </tr>
            
            <tr>
                <th>Shipper Number</th>
                <td>{{ $shipment->shipper_number}}</td>
            </tr>
            
            <tr>
                <th>Shipper Country</th>
                <td>{{ $shipment->shipperCountry->name}}</td>
            </tr>
            
            <tr>
                <th>Shipper State</th>
                <td>{{ $shipment->shipperCity->name}}</td>
            </tr>
            
            <tr>
                <th>Shipper Adress Line</th>
                <td>{{ $shipment->shipper_adress_line}}</td>
            </tr>
            
            <tr>
                <th>Shipper Zip_code</th>
                <td>{{ $shipment->shipper_zip_code}}</td>
            </tr>
            
            <tr>
                <th>Recipient Name</th>
                <td>{{ $shipment->recipient_name}}</td>
            </tr>
            
            <tr>
                <th>Recipient Email</th>
                <td>{{ $shipment->recipient_email}}</td>
            </tr>
            
            <tr>
                <th>Recipient Number</th>
                <td>{{ $shipment->recipient_number}}</td>
            </tr>
            
            <tr>
                <th>Recipient Country</th>
                <td>{{ $shipment->recipientCountry->name}}</td>
            </tr>
            
            <tr>
                <th>Recipient State</th>
                <td>{{ $shipment->recipientCity->name}}</td>
            </tr>
            
            <tr>
                <th>Recipient Adress Line</th>
                <td>{{ $shipment->recipient_adress_line}}</td>
            </tr>
            
            <tr>
                <th>Recipient Zip_code</th>
                <td>{{ $shipment->recipient_zip_code}}</td>
            </tr>
            
            <tr>
                <th>Package Weight</th>
                <td>{{ $shipment->package_weight}}</td>
            </tr>
            
            <tr>
                <th>Package Length</th>
                <td>{{ $shipment->package_length}}</td>
            </tr>
            
            <tr>
                <th>Package Width</th>
                <td>{{ $shipment->package_width}}</td>
            </tr>
            
            <tr>
                <th>Package Height</th>
                <td>{{ $shipment->package_height}}</td>
            </tr>
            
            <tr>
                <th>Package Quantity</th>
                <td>{{ $shipment->package_quantity}}</td>
            </tr>
            
            <tr>
                <th>Package Pickup Location</th>
                <td>{{ $shipment->package_pickup_location}}</td>
            </tr>
            
            <tr>
                <th>Package Description</th>
                <td>{{ $shipment->package_description}}</td>
            </tr>
            
            <tr>
                <th>Package Shipping Date Time</th>
                <td>{{ $shipment->package_shipping_date_time}}</td>
            </tr>
            
            <tr>
                <th>Package Due Date</th>
                <td>{{ $shipment->package_due_date}}</td>
            </tr>
            
            <tr>
                <th>Package Shipment Type</th>
                <td>{{ $shipment->package_shipment_type}}</td>
            </tr>
            
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