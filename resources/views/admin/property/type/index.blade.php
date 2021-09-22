@extends('layouts.admin.app') 
@section('content')
<h1>Property Types</h1>
<div class="ui menu">
    <div class="item right">
        <a class="ui button green" href="/admin/property/type/translation/create">Create New</a>
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
        @foreach ($types as $type)
        <tr>
            <td>{{ $type->slug }}</td>
            <td>{{ $type->name }}</td>
            <td>
                <a class="ui blue button" href="/admin/property/type/translation/list?id={{ $type->id }}">Translations</a>
                <a class="ui green button" href="/admin/property/stype/list?tid={{ $type->id }}">Sub Types</a>
                <a class="ui red button" href="/admin/property/type/delete?id={{ $type->id }}">Delete</a>
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">
                <div class="ui pagination menu">
                    {{ $types->links() }}
                </div>
            </th>
        </tr>
    </tfoot>
</table>
@endsection