@extends('laraboard::layouts.app')

{{-- Stylesheets -------------------------------------------------------------}}
@push('stylesheets')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
@endpush
