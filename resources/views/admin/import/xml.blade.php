@extends('layouts.admin.app') 
@section('content')
<div class="row ">
    <div class="col-xs-10">
        <h2 class="ui header">Import Openimmo XML</h2>
        <form class="ui form" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="field">
                <label>File</label>
                <input type="file" name='file' />
            </div>
            <div class="field">
                <button class="ui teal submit button" type="submit">Import</button>
            </div>
            <div>
                @if(session()->has('error')) @foreach (session('error') as $error)
                <div class="ui red message">
                    {{ $error }}
                </div>
                @endforeach @endif
            </div>
            @if ($success) 
                <div class="ui green message">
                    XML successfully imported!
                </div>
            @endif
        </form>
    </div>
</div>
@endsection