@extends('layouts.admin.app') 
@section('content')
<div class="row ">
    <div class="col-xs-10">
        <h2 class="ui header">Translation: {{ $city->locale }}</h2>
        <form class="ui form" method="POST">
            {{ csrf_field() }}
            <div class="field">
                <label>Name</label>
                <input type="text" name="name" placeholder="Name" value="{{ $city->name }}">
            </div>
            <div class="field">
                <label>Description</label>
                <textarea name="description" placeholder="Description">{{ $city->description }}</textarea>
            </div>
            <div class="field">
                <button class="ui teal submit button" type="submit">Update</button>
            </div>
            <div>
                @if(session()->has('error')) @foreach (session('error') as $error)
                <div class="ui red message">
                    {{ $error }}
                </div>
                @endforeach @endif
            </div>
        </form>
    </div>
</div>
@endsection