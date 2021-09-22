@extends('emails.layout')
@section('content')
<p>Hi <strong>{{ $user->first_name }}</strong>,</p>
<p>We’ve received a request to reset your password. If you didn’t make the request, just ignore this message. Otherwise, you can reset your password using this link:</p>
<a href="{{ env('APP_DASHBOARD_DOMAIN') }}/reset-password/{{ $token }}">Click here to reset your password</a>
@endsection