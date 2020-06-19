@extends('laraboard::layouts.app')

@section('content')

<div class="container">

    <post-component
        :post='@json($post)'
        :list='@json($list)'
        :comments='@json($comments)'
        ></post-component>

</div>

@endsection
