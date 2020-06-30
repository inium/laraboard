{{-- Stylesheets -------------------------------------------------------------}}
@push('stylesheets')
    <style>
        /* Override default Quill Editor font size */
        .ql-editor {
            min-height: 500px;
            font-size: initial;
        }
    </style>
@endpush

{{-- 글쓰기 페이지 -----------------------------------------------------------}}
<div>

    {{-- 게시글 Header --}}
    <div class="lb-post-write d-flex flex-row align-items-center pb-2">

        {{-- 게시판 이름 --}}
        <div class="lb-posts-board-name">

            <h4>{{ $board['name_ko'] }} <small>글쓰기</small></h4>

        </div>

        {{-- Breadcrumb --}}
        <div class="ml-auto">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="/">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ $board['name_ko'] }}
                    </li>
                </ol>
            </nav>

        </div>

    </div>

    {{-- 게시글 글쓰기 --}}
    <div class="lb-post-write-body py-3">

        <form id="formPostWrite"
              method="POST"
              action="{{ route('board.post.write.post', [
                    'boardName' => $board['name']
                ]) }}">

            @csrf

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
                       value="{{ old('subject') }}"
                       required>
            </div>

            {{-- 게시글 본문 --}}
            <div class="form-group">
                <input id="formInputContent" type="hidden" name="content">
                <div id="editor"></div>
            </div>


            <div class="d-flex justify-content-between">

                {{-- 목록 버튼 --}}
                <div>
                    <a href="{{ route('board.postlistsearch.view', [
                                    'boardname' => $board['name'],
                                ]) }}" class="btn btn-primary">
                        목록
                    </a>
                </div>

                {{-- 글 쓰기 버튼 --}}
                <div>
                    <button class="btn btn-primary" type="submit">글쓰기</button>
                </div>

            </div>

        </form>

    </div>

</div>

{{-- Scripts -----------------------------------------------------------------}}
@push('scripts')
    <script>
        $(document).ready(function () {

            // Quill Editor Toolbar 정보
            const toolbarOptions = [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                ['link', 'image', 'video'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['clean']
            ];

            // Quill Editor 생성
            const quill = new Quill('#editor', {
                modules: {
                    toolbar: toolbarOptions
                },
                // readOnly: true,
                placeholder: '글을 입력하세요.',
                theme: 'snow'
            });

            // 초기값 설정. form validation 실패할 경우 복구 실행.
            quill.root.innerHTML
                = `{!! htmlspecialchars_decode(old('content')) !!}`;

            // 게시글 쓰기 Submit. editor의 html을 hidden field에 설정.
            $('#formPostWrite').on('submit', function (e) {
                const editorHtml = quill.root.innerHTML;
                $('#formInputContent').val(editorHtml);
            });

        });
    </script>
@endpush
