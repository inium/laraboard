@extends('laraboard::layouts.app')

{{-- 페이지 콘텐츠 -----------------------------------------------------------}}
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
