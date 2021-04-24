@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Show User</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Id</th>
                <td>{{ $user->id }}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{ $user->name }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td>{{ $user->phone_number }}</td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td>{{ $user->phone_number }}</td>
            </tr>
            <tr>
                <th>Company</th>
                <td>{{ $user->company ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th>address_line_1</th>
                <td>{{ $user->address_line_1 ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th>address_line_2</th>
                <td>{{ $user->address_line_2 ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th>Country</th>
                <td>{{ $user->country->name ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th>city_id</th>
                <td>{{ $user->city->name ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th>state_id</th>
                <td>{{ $user->state->name ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th>zip_code</th>
                <td>{{ $user->zip_code ?? 'N/A' }}</td>
            </tr>

            <tr>
                <th>ID Front</th>
                <td><img width="150px" src="{{ $user->getFirstMediaUrl("id_front")}}" onerror="if (this.src != '/images/user.png') this.src = '/images/user.png';"/> </td>
            </tr>

            <tr>
                <th>ID Back</th>
                <td><img width="150px" src="{{ $user->getFirstMediaUrl("id_back")}}" onerror="if (this.src != '/images/user.png') this.src = '/images/user.png';"/> </td>
            </tr>

            <tr>
                <th>Created At</th>
                <td>{{ $user->created_at }}</td>
            </tr>

            <tr>
                <th>Updated At</th>
                <td>{{ $user->updated_at }}</td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <form action="{{ route('users.destroy',$user->id) }}" method="post">
            <a type="button" href="{{ route('users.edit',$user->id) }}" class="btn btn-outline-primary">Edit</a>
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
