@extends('laraboard::layouts.app')

@section('content')

<div class="container">

    <post-component
        :post='@json($post)'
        :list='@json($list)'
        
        {{-- :board='@json($board)'
        :notices='@json($notices)'
        :posts='@json($posts)'
        :pagination='@json($pagination)'
        :routes='@json($routes)'
        :search-form='@json($searchForm)' --}}
        ></post-component>

</div>

@endsection
