@extends('layouts.admin.app')
@section('content')
<div class="ui menu">
    
</div>
<table class="ui celled table">
    <thead>
        <tr>
            <th>Slug</th>
            <th>Name</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($countries as $country)
        <tr>
            <td>{{ $country->slug }}</td>
            <td>{{ $country->name }}</td>
            <td>
                <a class="ui green button" href="/admin/state/list?country={{ $country->id }}">States</a>
                @if(\App\Enums\Status::Enabled === $country->status) 
                    <a class="ui red button" href="/admin/country/status/{{ $country->id }}/{{ \App\Enums\Status::Disabled }}">Deactive</a>
                @else 
                    <a class="ui yellow button" href="/admin/country/status/{{ $country->id }}/{{ \App\Enums\Status::Enabled }}">Activate</a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection