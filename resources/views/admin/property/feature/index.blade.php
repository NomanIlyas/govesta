@extends('layouts.admin.app') 
@section('content')
<h1>Property Features</h1>
<div class="ui menu">
    <div class="item right">
        <a class="ui button green" href="/admin/property/feature/translation/create">Create New</a>
    </div>
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
        @foreach ($features as $feature)
        <tr>
            <td>{{ $feature->slug }}</td>
            <td>{{ $feature->name }}</td>
            <td>
                <a class="ui blue button" href="/admin/property/feature/translation/list?id={{ $feature->id }}">Translations</a>
                <a class="ui red button" href="/admin/property/feature/delete?id={{ $feature->id }}">Delete</a>
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">
                <div class="ui pagination menu">
                    {{ $features->links() }}
                </div>
            </th>
        </tr>
    </tfoot>
</table>
@endsection