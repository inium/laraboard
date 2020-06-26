@extends('laraboard::layouts.app')

@section('content')

<div class="container">

    {{-- 사용자에게 알릴 Alert 메시지가 있을 경우 --}}
    @if (Session::has('message'))
        @include('laraboard::components.shared.alert', [
            'class' => Session::get('alert-class', 'alert-info'),
            'message' => Session::get('message')
        ])
    @endif

    <div>
        {{-- 게시글 --}}
        @include('laraboard::components.post', [
            'post'  => $post,
            'role'  => $role,
            'query' => $query,
        ])
    </div>

    <div>
        {{-- 댓글 목록 --}}
        @include('laraboard::components.commentList', [
            'board'    => $post['board'],
            'comments' => $comments,
            'role'     => $role,
            'query'    => $query
        ])
    </div>

    <div class="my-5">

        {{-- 게시글 목록 --}}
        {{-- 검색어가 존재하는 경우 검색결과 목록 출력 --}}
        @if ($query)

            @include('laraboard::components.search', [
                'board'      => $list['board'],
                'search'     => $list['search'],
                'query'      => $list['query'],
                'page'       => $list['page'],
                'markPostId' => $post['id']
            ])

        {{-- 게시글 목록 출력 --}}
        @else

            @include('laraboard::components.postList', [
                'board'      => $list['board'],
                'notices'    => $list['notices'],
                'posts'      => $list['posts'],
                'query'      => null,
                'page'       => $list['page'],
                'markPostId' => $post['id']
            ])

        @endif

    </div>

</div>

@endsection
