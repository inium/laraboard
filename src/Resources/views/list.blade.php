@extends('laraboard::layouts.app')

@section('content')

<div class="container">

    <list-component
        :board='@json($board)'
        :notices='@json($notices)'
        :posts='@json($posts)'
        :pagination='@json($pagination)'
        :routes='@json($routes)'
        :search-form='@json($searchForm)'
        ></list-component>

</div>

@endsection
