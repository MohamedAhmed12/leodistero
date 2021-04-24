@extends('adminlte::page')

@section('title', 'Dashboard')


@section('content')
<form method="POST" id="settings" action="{{ route('settings.store') }}">
    @csrf
    <div class="card" id="settings">

        <div class="card-header">
            <h3>Settings</h3>
        </div>

        <div class="card-body">
            @if(session()->get('saved') == true)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong> Settings Saved.</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="form-group row">
                <label for="facebook" class="col-sm-2 col-form-label">Facebook</label>
                <div class="col-sm-10">
                    @php
                    @endphp
                    <input
                        name="settings[facebook_url]"
                        type="text"
                        v-model="facebook_url"
                        class="form-control"
                        id="facebook"
                        placeholder="Facebook URL"
                    >
                </div>
            </div>

            <div class="form-group row">
                <label for="twitter" class="col-sm-2 col-form-label">Twitter</label>
                <div class="col-sm-10">
                    <input
                        name="settings[twitter_url]"
                        type="text"
                        v-model="twitter_url"
                        class="form-control"
                        id="twitter"
                        placeholder="Twitter URL"
                    >
                </div>
            </div>

            <div class="form-group row">
                <label for="instagram" class="col-sm-2 col-form-label">Instagram</label>
                <div class="col-sm-10">
                    @php
                    @endphp
                    <input
                        name="settings[instagram_url]"
                        type="text"
                        v-model="instagram_url"
                        class="form-control"
                        id="instagram"
                        placeholder="Instagram URL"
                    >
                </div>
            </div>

            <div class="form-group row">
                <label for="terms_conditions" class="col-sm-2 col-form-label">Temrs & Conditions</label>
                <div class="col-sm-10">
                    <input type="hidden" id="terms_conditions" name="settings[terms_conditions]">
                    <quill-editor :options="editorOption" v-model="terms_conditions" class="@error('terms_conditions') is-invalid @enderror" />
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
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stop

@section('js')
    <script src="{{ asset('js/app.js') }}"></script>
    @php
        $twitter =  $settings->where('key','twitter_url')->first();
        $facebook =  $settings->where('key','facebook_url')->first();
        $instagram =  $settings->where('key','instagram_url')->first();
        $terms_conditions =  $settings->where('key','terms_conditions')->first();
    @endphp

    <script>

        new Vue({
            el:"#settings",
            data:{
                editorOption: {},
                twitter_url:"{{ $twitter ? $twitter->value : null }}",
                facebook_url:"{{ $facebook ? $facebook->value : null }}",
                instagram_url:"{{ $instagram ? $instagram->value : null }}",
                terms_conditions:`{!! $terms_conditions ? $terms_conditions->value : null !!}`,
            },
            methods:{
                save(){
                    $("#terms_conditions").val(this.terms_conditions);
                    $('#settings').submit();
                }
            }
        })
    </script>
@stop
