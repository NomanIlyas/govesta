@extends('layouts.admin.app')
@section('content')
<form class="ui form" method="POST">
    {{ csrf_field() }}
    <h1>Are you sure to delete Agency?</h1>
    <a class="ui green button" href="/admin/agency/list">No</a>
    <button class="ui red submit button" type="submit">Yes, Delete it</button>
</form>
@endsection