@extends('emails.layout')
@section('content')
<h3>New Agency Registeration</h3>
<p>
    <strong>Name</strong>
    &nbsp;
    <span>{{ $user->agency->name }}</span>
</p>
<br>
@endsection