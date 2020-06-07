@extends('laraboard::layouts.app')

@section('content')

<div class="container">

    {{-- 검색 정보가 존재할 경우: 검색결과 출력 --}}
    @if (isset($search))

        <post-search-component
            :board='@json($board)'
            :search='@json($search)'
            :posts='@json($posts)'
            :pagination='@json($pagination)'
            :routes='@json($routes)'
            :search-form='@json($searchForm)'
            ></post-search-component>

    {{-- 검색 정보가 존재하지 않을 경우: 글 목록 출력 --}}
    @else

        <post-list-component
            :board='@json($board)'
            :notices='@json($notices)'
            :posts='@json($posts)'
            :pagination='@json($pagination)'
            :routes='@json($routes)'
            :search-form='@json($searchForm)'
            ></post-list-component>

    @endif
</div>

@endsection
