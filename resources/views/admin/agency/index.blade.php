@extends('layouts.admin.app') 
@section('content')
<h1>Agencies</h1>
<div class="ui menu">
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
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Properties</th>
            <th>CPC</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($agencies as $agency)
        <tr>
            <td>{{ $agency->id }}</td>
            <td>{{ $agency->name }}</td>
            <td>{{ $agency->user->email }}</td>
            <td>
                <b>Online:</b> <span>{{ $agency->online_property }}</span> <br />
                <b>Pause:</b> <span>{{ $agency->pause_property }}</span> <br />
            </td>
            <td>{{ $agency->cpc }}</td>
            <td>
                <a class="ui blue button" href="/admin/agency/edit?id={{ $agency->id }}">Edit</a>
                @if(\App\Enums\AgencyStatus::Active == $agency->status) 
                    <a class="ui yellow button" href="/admin/agency/status?id={{ $agency->id }}&status={{ \App\Enums\AgencyStatus::Pause }}">Pause</a>
                @else 
                    <a class="ui green button" href="/admin/agency/status?id={{ $agency->id }}&status={{ \App\Enums\AgencyStatus::Active }}">Activate</a>
                    <a class="ui red button" href="/admin/agency/delete?id={{ $agency->id }}">Delete</a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">
                <div class="ui pagination menu">
                    {{ $agencies->links() }}
                </div>
            </th>
        </tr>
    </tfoot>
</table>
@endsection