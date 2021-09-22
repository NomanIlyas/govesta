@extends('layouts.admin.app') 
@section('content')
<h2 class="ui header">State: {{ $state->name }}</h2>
<div class="ui menu">
    <div class="item">
        <a class="ui green button" href="/admin/state/translation/create?id={{ $state->id }}">Create</a>
    </div>
</div>
<table class="ui celled table">
    <thead>
        <tr>
            <th>Language</th>
            <th>Slug</th>
            <th>Name</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($translations as $translation)
        <tr>
            <td>{{ $translation->locale }}</td>
            <td>{{ $translation->slug }}</td>
            <td>{{ $translation->name }}</td>
            <td>
                <a class="ui orange button" href="/admin/state/translation/edit?id={{ $state->id }}&locale={{ $translation->locale }}">Edit</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection