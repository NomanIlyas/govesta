@extends('layouts.admin.app') 
@section('content')
<h2 class="ui header">Feature: {{ $feature->name }}</h2>
<div class="ui menu">
    <div class="item">
        <a class="ui green button" href="/admin/property/feature/translation/create?id={{ $feature->id }}">Create</a>
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
                <a class="ui orange button" href="/admin/property/feature/translation/edit?id={{ $feature->id }}&locale={{ $translation->locale }}">Edit</a>
                <a class="ui red button" href="/admin/property/feature/translation/delete?id={{ $feature->id }}&locale={{ $translation->locale }}">Delete</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection