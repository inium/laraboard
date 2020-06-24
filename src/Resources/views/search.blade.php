@extends('laraboard::layouts.app')

@section('content')

<div class="container">

    @include('laraboard::components.search', [
        'board'  => $board,
        'search' => $search,
        'query'  => $query,
        'page'   => $page
    ])

</div>

@endsection
