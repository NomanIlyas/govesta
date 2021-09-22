@extends('layouts.admin.app')
@section('content')
<div class="row ">
    <div class="col-xs-10">
        <h2 class="ui header">Create State</h2>
        <form class="ui form" method="POST" enctype='multipart/form-data'>
            {{ csrf_field() }}
            <div class="field">
                <label>Country</label>
                <select name="country_id">
                    <?php foreach ($countries as $country) : ?>
                        <option <?php echo $country->id === 82 ? 'selected=""' : "" ?> value="<?php echo $country->id ?>"><?php echo $country->name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label>Name</label>
                <input type="text" name="name" placeholder="Name">
            </div>
            <div class="field">
                <label>Description</label>
                <textarea name="description" placeholder="Description"></textarea>
            </div>
            <div class="field">
                <label>Featured Image</label>
                <input type="file" name="featured_image">
            </div>
            <div class="field">
                <button class="ui teal submit button" type="submit">Create</button>
            </div>
            <div>
                @if(session()->has('error'))
                @foreach (session('error') as $error)
                <div class="ui red message">
                    {{ $error }}
                </div>
                @endforeach @endif
            </div>
        </form>
    </div>
</div>
@endsection