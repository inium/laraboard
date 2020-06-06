@extends('laraboard::layouts.app')

@section('content')

<div class="container">

    <search-component
        :board='@json($board)'
        :search='@json($search)'
        :posts='@json($posts)'
        :pagination='@json($pagination)'
        :routes='@json($routes)'
        :search-form='@json($searchForm)'
        ></search-component>

</div>

@endsection
