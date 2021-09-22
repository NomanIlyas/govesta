@extends('emails.layout') 
@section('content')
<p>Hi <strong>{{ $user->first_name }}</strong>,</p>
<p>Welcome to Govesta! In order to get started, you need to confirm your email address.</p>
<a href="{{ url('api/v1/auth/verify/'.$user->activation_token) }}">Confirm Email</a>
@endsection