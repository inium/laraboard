@extends('laraboard::layouts.app')

{{-- 페이지 콘텐츠 -----------------------------------------------------------}}
@section('content')

    <div class="container">

        {{-- 사용자에게 알릴 Alert 메시지가 있을 경우(글 삭제 시 출력) --}}
        @if (Session::has('message'))
            @include('laraboard::components.shared.alert', [
                'class' => Session::get('alert-class', 'alert-info'),
                'message' => Session::get('message')
            ])
        @endif

        @include('laraboard::components.postList', [
            'board'   => $board,
            'notices' => $notices,
            'posts'   => $posts,
            'query'   => null,
            'page'    => $page
        ])

    </div>

@endsection
