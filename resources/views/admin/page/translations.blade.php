@extends('layouts.admin.app') 
@section('content')
<h2 class="ui header">Page: {{ $page->title }}</h2>
<div class="ui menu">
    <div class="item">
        <a class="ui green button" href="/admin/page/create?id={{ $page->id }}">Create</a>
    </div>
</div>
<table class="ui celled table">
    <thead>
        <tr>
            <th>Language</th>
            <th>Slug</th>
            <th>Title</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($translations as $translation)
        <tr>
            <td>{{ $translation->locale }}</td>
            <td>{{ $translation->slug }}</td>
            <td>{{ $translation->title }}</td>
            <td>
                <a class="ui orange button" href="/admin/page/edit?id={{ $translation->page_id }}&locale={{ $translation->locale }}">Edit</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection