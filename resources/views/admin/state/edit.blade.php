@extends('layouts.admin.app') 
@section('content')
<div class="row ">
    <div class="col-xs-10">
        <h2 class="ui header">State: {{ $state->name }}</h2>
        <form class="ui form" method="POST" enctype='multipart/form-data'>
            {{ csrf_field() }}
            <div class="field">
                <label>Featured Image</label>
                <input type="file" name="featured_image">
            </div>
            <button class="ui teal submit button" type="submit">Update</button>
        </form>
    </div>
</div>
@endsection