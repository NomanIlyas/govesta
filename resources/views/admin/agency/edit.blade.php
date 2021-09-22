@extends('layouts.admin.app') 
@section('content')
<div class="row ">
    <div class="col-xs-10">
        <h2 class="ui header">Agency: {{ $agency->name }}</h2>
        <form class="ui form" method="POST">
            {{ csrf_field() }}
            <div class="field">
                <label>CPC</label>
                <input type="text" name="cpc" placeholder="CPC" value="{{ $agency->cpc }}">
            </div>
            <div class="field">
                <label>Analytics Links</label>
                <textarea name="analytics_links">{{ $agency->analytics_links }}</textarea>
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