@extends('layouts.admin.app')
@section('content')
<div class="ui menu">
    <div class="item">
        <a class="ui green button" href="/admin/state/create">Create</a>
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
            <th>Slug</th>
            <th>Name</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($states as $state)
        <tr>
            <td>{{ $state->country->name }}</td>
            <td>{{ $state->slug }}</td>
            <td>{{ $state->name }}</td>
            <td>
                <a class="ui orange button" href="/admin/state/edit?id={{ $state->id }}">Edit</a>
                <a class="ui blue button" href="/admin/state/translation/list?id={{ $state->id }}">Translations</a>
                <a class="ui green button" href="/admin/city/list?state={{ $state->id }}">Cities</a>
                <a class="ui red button" href="/admin/state/delete?id={{ $state->id }}">Delete</a>
                @if(\App\Enums\Status::Enabled === $state->status) 
                    <a class="ui red button" href="/admin/state/status/{{ $state->id }}/{{ \App\Enums\Status::Disabled }}">Deactive</a>
                @else 
                    <a class="ui yellow button" href="/admin/state/status/{{ $state->id }}/{{ \App\Enums\Status::Enabled }}">Activate</a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">
                <div class="ui pagination menu">
                    {{ $states->appends(request()->query())->links() }}
                </div>
            </th>
        </tr>
    </tfoot>
</table>
@endsection