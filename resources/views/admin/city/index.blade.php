@extends('layouts.admin.app')
@section('content')
<div class="ui menu">
    <div class="item">
        <a class="ui green button" href="/admin/city/create?state=<?php echo $state; ?>">Create</a>
    </div>
    <div class="right item">
        <form class="ui action input">
            <input type="text" placeholder="Navigate to..." name="term">
            <button class="ui button" type="submit">Go</button>
        </form>
    </div>
</div>
<table class="ui celled table">
    <thead>
        <tr>
            <th>Country</th>
            <th>State</th>
            <th>Slug</th>
            <th>Name</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($cities as $city)
        <tr>
            <td>{{ $city->country->name }}</td>
            <td>{{ $city->state->name }}</td>
            <td>{{ $city->slug }}</td>
            <td>{{ $city->name }}</td>
            <td>
                <a class="ui orange button" href="/admin/city/edit?id={{ $city->id }}">Edit</a>
                <a class="ui blue button" href="/admin/city/translation/list?id={{ $city->id }}">Translations</a>
                <a class="ui green button" href="/admin/district/list?id={{ $city->id }}">Districts</a>
                <a class="ui red button" href="/admin/city/delete?id={{ $city->id }}">Delete</a>
                @if(\App\Enums\Status::Enabled === $city->status) 
                    <a class="ui red button" href="/admin/city/status/{{ $city->id }}/{{ \App\Enums\Status::Disabled }}">Deactive</a>
                @else 
                    <a class="ui yellow button" href="/admin/city/status/{{ $city->id }}/{{ \App\Enums\Status::Enabled }}">Activate</a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">
                <div class="ui pagination menu">
                    {{ $cities->appends(request()->query())->links() }}
                </div>
            </th>
        </tr>
    </tfoot>
</table>
@endsection