@extends('layouts.admin.app') 
@section('content')
<div class="ui menu">
    <div class="item">
        <h1>Districts</h1>
    </div>
    <div class="item right">
        <a class="ui button green" href="/admin/district/create?id={{ $id }}">Create New</a>
    </div>
</div>
<table class="ui celled table">
    <thead>
        <tr>
            <th></th>
            <th>Country</th>
            <th>State</th>
            <th>City</th>
            <th>Slug</th>
            <th>Name</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($districts as $district)
        <tr>
            <td>
                @if (!empty($district->featured))
                 
                @endif
            </td>
            <td>{{ $district->city->country->name }}</td>
            <td>{{ $district->city->state->name }}</td>
            <td>{{ $district->city->name }}</td>
            <td>{{ $district->slug }}</td>
            <td>{{ $district->name }}</td>
            <td>
                <a class="ui orange button" href="/admin/district/edit?id={{ $district->id }}">Edit</a>
                <a class="ui red button" href="/admin/district/delete?id={{ $district->id }}">Delete</a>
                @if(\App\Enums\Status::Enabled === $district->status) 
                    <a class="ui red button" href="/admin/district/status/{{ $district->id }}/{{ \App\Enums\Status::Disabled }}">Deactive</a>
                @else 
                    <a class="ui yellow button" href="/admin/district/status/{{ $district->id }}/{{ \App\Enums\Status::Enabled }}">Activate</a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="8">
               
            </th>
        </tr>
    </tfoot>
</table>
@endsection