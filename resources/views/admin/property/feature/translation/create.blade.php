@extends('layouts.admin.app') 
@section('content')
<div class="row ">
    <div class="col-xs-10">
        <h2 class="ui header">Create New</h2>
        <form class="ui form" method="POST">
            {{ csrf_field() }}
            <div class="field">
                <label>Locale</label>
                <select name="locale" class="ui dropdown">
                    @foreach ($languages as $language)
                        <option value="{{ $language->code }}">{{ $language->name }} </option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Name</label>
                <input type="text" name="name" placeholder="Name">
            </div>
            <div class="field">
                <button class="ui teal submit button" type="submit">Save</button>
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