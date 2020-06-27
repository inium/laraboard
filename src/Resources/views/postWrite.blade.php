@extends('laraboard::layouts.app')

{{-- 페이지 본문 --}}
@section('content')

    <div class="container">

        {{-- Form 오류가 있을 경우 (Form Validation 실패) --}}
        @if ($errors->any())
            @include ('laraboard::components.shared.alertValidationErrors', [
                'errors' => $errors
            ])
        @endif

        {{-- 게시글 쓰기 form --}}
        @include ('laraboard::components.postWrite', [
            'board' => $board,
            'roles' => $roles
        ])

    </div>

@endsection
