@extends('layouts.admin.app') 
@section('content')
<h1>Property Sub Types</h1>
<div class="ui menu">
    <div class="item right">
        <a class="ui button green" href="/admin/property/stype/translation/create?tid={{ $tid }}">Create New</a>
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
        @foreach ($stypes as $stype)
        <tr>
            <td>{{ $stype->slug }}</td>
            <td>{{ $stype->name }}</td>
            <td>
                <a class="ui blue button" href="/admin/property/stype/translation/list?id={{ $stype->id }}">Translations</a>
                <a class="ui red button" href="/admin/property/stype/delete?id={{ $stype->id }}">Delete</a>
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">
                <div class="ui pagination menu">
                    {{ $stypes->links() }}
                </div>
            </th>
        </tr>
    </tfoot>
</table>
@endsection