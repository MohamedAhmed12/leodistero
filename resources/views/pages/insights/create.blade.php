@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
<form method="POST" id="orders" action="{{ route('orders.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card" id="orders">

        <div class="card-header">
            <h3>Order</h3>
        </div>

        <div class="card-body">
            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Title</label>
                <div class="col-sm-10">
                    <input name="title" type="text" v-model="title" class="form-control @error('title') is-invalid @enderror" id="title" placeholder="Title"/>
                    @if($errors->has("title"))
                        <div class="alert alert-danger w-100 m-0" role="alert">
                            {{$errors->first("title")}}
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label for="body" class="col-sm-2 col-form-label">Body</label>
                <div class="col-sm-10">
                    <input type="hidden" id="body" name="body">
                    <div>
                        <quill-editor :options="editorOption" v-model="body" class="@error('body') is-invalid @enderror" />
                    </div>
                    @if($errors->has("body"))
                        <div class="alert alert-danger w-100 m-0" role="alert">
                            {{$errors->first("body")}}
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label for="excerpt" class="col-sm-2 col-form-label">Excerpt</label>
                <div class="col-sm-10">
                    <textarea name="excerpt" type="text" v-model="excerpt" class="form-control @error('excerpt') is-invalid @enderror" id="excerpt" placeholder="Excerpt"></textarea>
                    @if($errors->has("excerpt"))
                        <div class="alert alert-danger w-100 m-0" role="alert">
                            {{$errors->first("excerpt")}}
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-group row">
                <label for="title" class="col-sm-2 col-form-label">Category</label>
                <div class="col-sm-10">
                    <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" v-model="category_id">
                        <option value=""></option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"> {{ $category->name }} </option>
                        @endforeach
                    </select>
                    @if($errors->has("category_id"))
                        <div class="alert alert-danger w-100 m-0" role="alert">
                            {{$errors->first("category_id")}}
                        </div>
                    @endif

                </div>
            </div>

            <div class="form-group row">
                <label for="author_id" class="col-sm-2 col-form-label">Author</label>
                <div class="col-sm-10">
                    <select class="form-control @error('author_id') is-invalid @enderror" name="author_id" v-model="author_id">
                        <option value=""></option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"> {{ $user->name }} </option>
                        @endforeach
                    </select>
                    @if($errors->has("author_id"))
                        <div class="alert alert-danger w-100 m-0" role="alert">
                            {{$errors->first("author_id")}}
                        </div>
                    @endif

                </div>
            </div>

            <div class="form-group row">
                <label for="image" class="col-sm-2 col-form-label">Image</label>
                <div class="col-sm-10">
                    <input class="{{$errors->has("image") ? 'is-invalid' : ''}}" type="file" name="image"/>
                    @if($errors->has("image"))
                        <div class="alert alert-danger w-100 m-0" role="alert">
                            {{$errors->first("image")}}
                        </div>
                    @endif
                    @if(isset($edit))
                        <span><img width="160px" src="{{ $edit->getFirstMediaUrl("image")}}"/> </span>
                    @endif
                </div>
            </div>


            <div class="form-group row">
                <label for="banner" class="col-sm-2 col-form-label">Banner</label>
                <div class="col-sm-10">
                    <input class="{{$errors->has("banner") ? 'is-invalid' : ''}}" type="file" name="banner"/>
                    @if($errors->has("banner"))
                        <div class="alert alert-danger w-100 m-0" role="alert">
                            {{$errors->first("banner")}}
                        </div>
                    @endif
                    @if(isset($edit))
                        <span><img width="160px" src="{{ $edit->getFirstMediaUrl("banner")}}"/> </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" @click.prevent="save" class="btn btn-primary">Save</button>
        </div>
    </div>
  </form>

@stop

@section('css')
    <style>
        .editr.is-invalid{
            border: 1px solid red;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/app.js') }}"></script>

    <script>

        new Vue({
            el:"#orders",
            data:{
                editorOption: {
                },
                title:"{{ isset($edit) ? $edit->title : old('title') }}",
                body:`{!! isset($edit) ? $edit->body : old('body') !!}`,
                excerpt:"{{ isset($edit) ? $edit->excerpt : old('excerpt') }}",
                category_id:"{{ isset($edit) ? $edit->category_id : old('category_id') }}",
                author_id:"{{ isset($edit) ? $edit->author_id : old('author_id') }}",
            },
            methods:{
                save(){
                    $("#body").val(this.body);
                    $('#orders').submit();
                }
            }
        })
    </script>
@stop
