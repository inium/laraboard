{{-- 게시글 본문 mark 표시 후 html 형태로 저장 -------------------------------}}
@php
    $postContent = htmlspecialchars_decode($post['content']);
@endphp

{{-- Stylesheets -------------------------------------------------------------}}
@push('stylesheets')
    <style>
        /* Override default Toast UI Viewer font size */
        .tui-editor-contents {
            font-size: initial; 
        }
    </style>
@endpush

{{-- 글수정 페이지 -----------------------------------------------------------}}
<div>

    {{-- 게시글 Header --}}
    <div class="lb-post-modify d-flex flex-row align-items-center pb-2">

        {{-- 게시판 이름 --}}
        <div class="lb-posts-board-name">

            <h4>{{ $post['board']['name_ko'] }} <small>글수정</small></h4>

        </div>

        {{-- Breadcrumb --}}
        <div class="ml-auto">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="/">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ $post['board']['name_ko'] }}
                    </li>
                </ol>
            </nav>

        </div>

    </div>

    {{-- 게시글 글쓰기 --}}
    <div class="lb-post-modify-body py-3">

        <form id="formPostModify"
              method="POST"
              action="{{ route('board.post.modify.put', [
                    'boardName' => $post['board']['name'],
                    'id' => $post['id']
                ]) }}">

            @csrf
            @method('PUT')

            @if ($roles->admin)

                {{-- 공지사항 Checkbox --}}
                <div class="form-group form-check">
                    <input type="checkbox"
                        name="notice"
                        class="form-check-input"
                        id="formCheckNotice">
                    <label class="form-check-label" for="formCheckNotice">
                        공지사항
                    </label>
                </div>

            @endif

            {{-- 게시글 제목 --}}
            <div class="form-group">
                <input type="text"
                       name="subject"
                       class="form-control"
                       id="formInputSubject"
                       placeholder="제목을 입력하세요."
                       value="{{ old('subject', $post['subject']) }}"
                       >
            </div>

            {{-- 게시글 본문 --}}
            <div class="form-group">
                <input id="formInputContent" type="hidden" name="content">
                <div id="editor"></div>
            </div>

            <button class="btn btn-primary" type="submit">글수정</button>

        </form>

    </div>

</div>

{{-- Scripts -----------------------------------------------------------------}}
@push('scripts')
    <script>
        $(document).ready(function () {

            // Toast UI Editor 생성
            const content = `{!! old('content', $postContent) !!}`;
            const editor = new toastui.Editor({
                el: document.querySelector('#editor'),
                height: '500px',
                initialValue: content,
                placeholder: '글을 입력하세요.',
                initialEditType: 'wysiwyg'
            });

            // 게시글 쓰기 Submit. editor의 html을 hidden field에 설정.
            $('#formPostModify').on('submit', function (e) {
                const editorHtml = editor.getHtml();
                $('#formInputContent').val(editorHtml);
            });

        });
    </script>
@endpush
