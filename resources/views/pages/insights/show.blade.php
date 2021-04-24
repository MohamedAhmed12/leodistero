@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Show Order</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Id</th>
                <td>{{ $order->id }}</td>
            </tr>
            <tr>
                <th>Title</th>
                <td>{{ $order->title }}</td>
            </tr>
            <tr>
                <th>Body</th>
                <td class="ql-editor">{!! $order->body !!}</td>
            </tr>
            <tr>
                <th>Excerpt</th>
                <td>{{ $order->excerpt }}</td>
            </tr>

            <tr>
                <th>Image</th>
                <td><img width="150px" onerror="if (this.src != '/images/invest1.png') this.src = '/images/invest1.png';" src="{{ $order->getFirstMediaUrl("image")}}"/> </td>
            </tr>

            <tr>
                <th>Banner</th>
                <td><img width="150px" onerror="if (this.src != '/images/details.jpg') this.src = '/images/details.jpg';" src="{{ $order->getFirstMediaUrl("banner")}}"/> </td>
            </tr>
            <tr>
                <th>Author</th>
                <td>{{ $order->author->name }}</td>
            </tr>
            <tr>
                <th>Cateogry</th>
                <td>{{ $order->category->name }}</td>
            </tr>

            <tr>
                <th>Created At</th>
                <td>{{ $order->created_at }}</td>
            </tr>
            <tr>
                <th>Updated At</th>
                <td>{{ $order->updated_at }}</td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <form action="{{ route('orders.destroy',$order->id) }}" method="post">
            <a type="button" href="{{ route('orders.edit',$order->id) }}" class="btn btn-outline-primary">Edit</a>
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
