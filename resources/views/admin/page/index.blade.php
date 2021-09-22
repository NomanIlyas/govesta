@extends('layouts.admin.app') 
@section('content')
<div class="ui menu">
    <div class="item">
        <a class="ui green button" href="/admin/page/create">Create</a>
    </div>
</div>
<table class="ui celled table">
    <thead>
        <tr>
            <th>Slug</th>
            <th>Title</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pages as $page)
        <tr>
            <td>{{ $page->slug }}</td>
            <td>{{ $page->title }}</td>
            <td>
                <a class="ui blue button" href="/admin/page/translations?id={{ $page->id }}">Translations</a>
            </td>
        </tr>
        @endforeach
    </tbody>
    @if ($pages->hasPages())
    <tfoot>
        <tr>
            <th colspan="2">
                <div class="ui pagination menu">
                    {{ $pages->links() }}
                </div>
            </th>
        </tr>
    </tfoot>
    @endif
</table>
@endsection