@extends('laraboard::layouts.app')

{{-- Stylesheets -------------------------------------------------------------}}
@push('stylesheets')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.33.0/codemirror.css"/>
    <link rel="stylesheet" href="https://uicdn.toast.com/editor/latest/toastui-editor.css">
@endpush

@section('content')

    <div class="container">

        {{-- Form 오류가 있을 경우 (Form Validation 실패) --}}
        @if ($errors->any())
            @include ('laraboard::components.shared.alertValidationErrors', [
                'errors' => $errors
            ])
        @endif

        {{-- 게시글 쓰기 form --}}
        @include ('laraboard::components.postModify', [
            'post' => $post,
            'roles' => $roles
        ])

    </div>

@endsection

{{-- Scripts -----------------------------------------------------------------}}
@push('scripts')
    <script src="https://uicdn.toast.com/editor/latest/toastui-jquery-editor.min.js"></script>
@endpush
