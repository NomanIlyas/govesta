@extends('emails.layout')
@section('content')
<table>
    @foreach ($params as $key=>$value)
    <tr>
        <td>{{ $key }}</td>
        <td>{{ $value }}</td>
    </tr>
    @endforeach
</table>
@endsection