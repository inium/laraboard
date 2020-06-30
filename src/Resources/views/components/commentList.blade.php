{{-- Stylesheets -------------------------------------------------------------}}
@push('stylesheets')

    <style>
        .lb-comments .media.child {
            padding-left: 4rem !important;
        }
        .lb-comments .media .thumbnail {
            width: 48px;
            height: 48px;
        }
        .lb-comments .media.child .thumbnail {
            width: 32px;
            height: 32px;
        }

        /* Override default Quill Editor font size */
        .form-comment-write .ql-container .ql-editor {
            min-height: 100px;
            font-size: initial;
            padding: 12px 15px !important;
        }
        .form-comment-write .ql-toolbar {
            display: block !important;
        }
        .form-comment-write .ql-container.ql-snow {
            border: 1px solid #ccc !important;
            border-top: 0 !important;
        }

        /* Quill Editor read-only */
        .lb-comments .lb-comments-contents .comment-content .ql-container.ql-snow {
            border: 0;
        }
        .lb-comments .lb-comments-contents .comment-content .ql-container .ql-editor {
            font-size: initial;
            padding: 0;
        }
        .lb-comments .lb-comments-contents .comment-content .ql-toolbar {
            display: none;
        }
    </style>

@endpush


{{-- 댓글 목록 ---------------------------------------------------------------}}
<div class="lb-comments">

    {{-- 댓글 목록 Header --}}
    <div class="lb-comments-header py-2">
        <h5>댓글 {{ number_format($comments->total()) }} 개</h5>
    </div>

    {{-- 댓글 목록 --}}
    <div class="lb-comments-contents py-2">

        @foreach ($comments as $comment)

            {{-- 댓글 Row --}}
            @include ('laraboard::components.shared.commentRow', [
                'comment' => $comment,
                'board'   => $board,
                'role'    => $role,
            ])

        @endforeach

    </div>

    {{-- 댓글 Footer --}}
    <div class="lb-comments-footer py-2">

        {{-- 댓글 쓰기 Form --}}
        <div id="commentFormOriginArea" class="pb-5">

            {{-- 댓글 새로고침 버튼 --}}
            <button id="btnCommentReload" class="btn btn-secondary btn-block rounded-0">
                댓글 새로고침
            </button>

            {{-- 댓글 작성한 사용자들만 작성 Form 활성화 --}}
            @if ($role->comment->canWrite)
                <form id="formCommentWrite"
                    class="form-comment-write"
                    action="{{ route('board.comment.post', [
                                    'boardName' => $board['name'],
                                    'postId' => $postId
                                ]) }}"
                    method="POST">

                    @csrf

                    {{-- 부모 댓글 ID --}}
                    <input id="formInputParentCommentId" type="hidden" name="parent_comment_id">

                    {{-- 댓글 본문 --}}
                    <div class="form-group">
                        <input id="formInputContent" type="hidden" name="content">
                        <div id="editor"></div>
                    </div>

                    {{-- 댓글 취소, 글쓰기 버튼 --}}
                    <div class="d-flex justify-content-between">
                        <div>
                            <button id="btnFormCommentWriteCancel" class="btn btn-outline-danger">취소</button>
                        </div>
                        <div>
                            <button class="btn btn-primary" type="submit">글쓰기</button>
                        </div>
                    </div>
                </form>
            @endif
        </div>

        {{-- 글 목록 외 --}}
        <div class="d-flex justify-content-between">

            <div>
                @if ($role->post->canRead)
                    <a href="{{ route('board.postListSearch.view', [
                                    'boardName' => $board['name'],
                                    'query' => $query,
                                ]) }}" class="btn btn-primary">
                        목록
                    </a>
                @endif
            </div>

        </div>

    </div>

</div>

{{-- 댓글 목록 ---------------------------------------------------------------}}
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
            const quillCommentWrite = new Quill('#editor', {
                modules: {
                    toolbar: toolbarOptions
                },
                placeholder: '글을 입력하세요.',
                theme: 'snow'
            });

            // 초기값 설정. form validation 실패할 경우 복구 실행.
            quillCommentWrite.root.innerHTML
                = `{!! htmlspecialchars_decode(old('content')) !!}`;

            // 댓글 Quill Editor Post viewer 생성
            $('.comment').each(function() {
                let q = new Quill('#' + $(this).attr('id'), {
                    modules: {
                        toolbar: toolbarOptions,
                    },
                    readOnly: true,
                    theme: 'snow'
                });
            });

            // 댓글 쓰기 Submit. editor의 html을 hidden field에 설정.
            $('#formCommentWrite').on('submit', function (e) {
                const editorHtml = quillCommentWrite.root.innerHTML;
                $('#formInputContent').val(editorHtml);
            });

            // 댓글 삭제 버튼 클릭
            $('.form-comment-delete').on('submit', function (e) {
                e.preventDefault();

                if (!confirm('댓글을 삭제하시겠습니까?')) {
                    return;
                }

                $(this)[0].submit();
            });



            // 댓글, 답글 추가
            // let currentReplyButton = null;

            // 현재 선택된 댓글 Area
            let selectedComment = null;

            // 답글 버튼 클릭
            $('.btn-comment-reply').on('click', function (e) {
                e.preventDefault();

                selectedComment = $(this).closest('.comment-row');

                // 댓글 입력 Form을 답글 버튼 아래로 이동 후 답글 버튼 감춤
                $(this).hide();
                $(this).closest('.comment-content')
                       .append($('#formCommentWrite'));

                // 자식 댓글의 경우 form에 parent id 추가
                if (!selectedComment.hasClass('child')) {
                    const parentCommentId = selectedComment.attr('data-id');
                    $('#formInputParentCommentId').val(parentCommentId);
                }
            });

            // 댓글 입력 Form의 취소 버튼을 클릭했을 때, Editor 콘텐츠 비움.
            $('#btnFormCommentWriteCancel').click(function (e){
                e.preventDefault();

                // Quill Editor 초기화
                quillCommentWrite.setContents([]);

                // 답글 버튼을 이용해 자식 댓글을 입력할 경우, 답글버튼 활성화
                if (selectedComment) {
                    selectedComment.find('.btn-comment-reply').show();
                    selectedComment = null;
                }

                // 댓글 Form의 부모 댓글 ID 초기화
                $('#formInputParentCommentId').val(null);

                // 댓글을 본래의 위치로 이동
                $('#commentFormOriginArea').append($('#formCommentWrite'));
            });


            // let currentCommentMediaBody = null;
            // let currentModifyButton = null;

            // 댓글 수정 버튼 클릭
            $('.btn-comment-modify').on('click', function (e) {
                e.preventDefault();
                alert('modify');

                // currentModifyButton = $(this);
                // currentCommentMediaBody = $(this).closest('.comment-media-body');

                // let comment = currentCommentMediaBody.find('.ql-editor');

                // currentModifyButton.hide();
                // currentCommentMediaBody.find('.comment-elem').hide();
                // currentCommentMediaBody.append($('#formCommentWrite'));

                // quillCommentWrite.root.innerHTML = comment.html();
            });

        });
    </script>
@endpush
