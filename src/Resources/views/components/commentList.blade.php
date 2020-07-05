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

        .form-comment-modify .ql-container .ql-editor {
            min-height: 100px;
            font-size: initial;
            padding: 12px 15px !important;
        }
        .form-comment-modify .ql-toolbar {
            display: block !important;
        }
        .form-comment-modify .ql-container.ql-snow {
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
<div id="commentList" class="lb-comments">

    {{-- 댓글 목록 Header --}}
    <div class="lb-comments-header py-2">
        <h5>댓글 {{ number_format($post->comments->count()) }} 개</h5>
    </div>

    {{-- 댓글 목록. AJAX로 로딩 --}}
    <div id="commentContents" class="lb-comments-contents py-2">

    </div>

    {{-- 댓글 Footer --}}
    <div class="lb-comments-footer py-2">

        {{-- 댓글 Loading Spinner --}}
        <div id="commentLoadingSpinner" class="text-center pb-5">
            <div class="spinner-border text-success text-center" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        {{-- 댓글 쓰기 Form --}}
        <div id="commentFormOriginArea" class="pb-5">

            {{-- 댓글 더 보기 버튼 --}}
            <a href="{{route('board.comment.index', [
                            'boardName' => $board['name'],
                            'postId' => $post['id']
                        ])}}"
                    id="btnCommentReload"
                    class="btn btn-secondary btn-block rounded-0 mb-2" 
                    data-last-group-id="">
                댓글 더 보기
            </a>

            {{-- 댓글 작성한 사용자들만 작성 Form 활성화 --}}
            @if ($role->comment->canWrite)
                <form id="formCommentWrite"
                    class="form-comment-write"
                    action="{{ route('board.comment.store', [
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
                    <a href="{{ route('board.post.index', [
                                    'boardName' => $board['name'],
                                    'query' => $query,
                                ]) }}" class="btn btn-primary">
                        목록
                    </a>
                @endif
            </div>

        </div>

    </div>

    {{-- 댓글 수정 Form --}}
    <form id="formCommentModify"
        class="form-comment-modify"
        action=""
        method="POST"
        style="display:none">

        @csrf
        @method('PUT')

        {{-- 댓글 본문 --}}
        <div class="form-group">
            <input id="formInputContent" type="hidden" name="content">
            <div id="modifyEditor"></div>
        </div>

        {{-- 댓글 취소, 글쓰기 버튼 --}}
        <div class="d-flex justify-content-between">
            <div>
                <button id="btnFormCommentModifyCancel" class="btn btn-outline-danger">취소</button>
            </div>
            <div>
                <button class="btn btn-primary" type="submit">글수정</button>
            </div>
        </div>
    </form>

</div>

{{-- 댓글 목록 ---------------------------------------------------------------}}
@push('scripts')
    <script>
        // 글쓰기 Editor
        let quillCommentWrite = null;

        // 글수정 Editor
        let quillCommentModify = null;

        // Quill Editor Toolbar 정보
        const toolbarOptions = [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                // ['link', 'image', 'video'],
                ['link'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['clean']
            ];

        /**
         * 다음 댓글을 가져온다.
         * 
         * @param function callback     댓글 가져온 이후 호출되는 callback
         */
        const getNextCommentByGroupId = function (callback) {
            const CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            let url = $('#btnCommentReload').attr('href');
            let lastGroupId = $('#btnCommentReload').attr('data-last-group-id');

            if (lastGroupId) {
                url = `${url}?group_id=${lastGroupId}`;
            }

            $.get(url, { _token: CSRF_TOKEN }, function (data) {

                if (data.trim().length > 0) {
                    let $data = $(data);

                    // Quill editor 적용
                    $('#commentContents').append($data);

                    // 댓글 Quill Editor Post viewer 생성
                    $data.find('.comment').each(function (v) {
                        let q = new Quill('#' + $(this).attr('id'), {
                            modules: {
                                toolbar: toolbarOptions,
                            },
                            readOnly: true,
                            theme: 'snow'
                        });

                        lastGroupId = $(this).closest('.comment-row')
                                                .attr('data-group-id');

                    }).promise().done(function () {
                        
                        // 댓글 불러오기 버튼에 마지막으로 가져온 Group Id 추가
                        $('#btnCommentReload').attr('data-last-group-id',
                                                    lastGroupId);
                        callback($data);
                    });
                }
                else {
                    callback(null);
                }
            });
        };

        $(document).ready(function () {

            // 댓글 쓰기 form이 존재할 경우
            if ($('#formCommentWrite').length) {

                // 글쓰기 Quill Editor 생성
                quillCommentWrite = new Quill('#editor', {
                    modules: {
                        toolbar: toolbarOptions
                    },
                    placeholder: '글을 입력하세요.',
                    theme: 'snow'
                });

                // 초기값 설정. form validation 실패할 경우 복구 실행.
                quillCommentWrite.root.innerHTML
                    = `{!! htmlspecialchars_decode(old('content')) !!}`;

            }

            // 초기 댓글 정보를 가져온다.
            $('#commentFormOriginArea').hide();     // 댓글 입력 form 숨김
            $('#commentLoadingSpinner').show();     // Loading Spinner 보임

            getNextCommentByGroupId(function() {
                $('#commentFormOriginArea').show(); // 댓글 입력 form 보임
                $('#commentLoadingSpinner').hide(); // Loading Spinner 숨김
            });


            // 댓글 더 보기 버튼 클릭
            $('#btnCommentReload').click(function (e) {
                e.preventDefault();

                // $('#commentFormOriginArea').hide();
                $('#commentLoadingSpinner').show();

                getNextCommentByGroupId(function() {
                    // $('#commentFormOriginArea').show();
                    $('#commentLoadingSpinner').hide();
                });
            });

            // 댓글 쓰기 Submit. editor의 html을 hidden field에 설정.
            $('#formCommentWrite').on('submit', function (e) {
                const editorHtml = quillCommentWrite.root.innerHTML;
                $('#formInputContent').val(editorHtml);
            });
        });


        $(function() {
            // 현재 선택된 댓글 Area
            let selectedComment = null;

            // 사용자가 클릭한 이전 수정 버튼
            let clickedModifyButton = null;

            /**
             * 댓글 쓰기를 취소한다.
             */
            const cancelFormCommentWrite = function () {
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
            };

            /**
             * 댓글 수정을 취소한다.
             */
            const cancelFormCommentModify = function () {

                // Form 초기화
                // Quill Editor 초기화
                quillCommentModify.setContents([]);

                // 댓글 목록 하단에 수정 form 추가 (본래자리로 복귀)
                $('#commentList').append($('#formCommentModify'));
                $('#formCommentModify').attr('action', ''); // Action field 제제
                $('#formCommentModify').hide(); // 댓글 수정 form 숨김

                // 기존 댓글 콘텐츠 활성화
                clickedModifyButton.closest('.comment-row')
                                    .find('.comment-content')
                                    .show();

                // 이전 클릭 수정 버튼 정보 초기화
                clickedModifyButton = null;
            };

            // 답글 버튼 클릭
            $(document).on('click', '.btn-comment-reply', function (e) {
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

                // 댓글 수정 form 취소
                cancelFormCommentModify();

                // 댓글 작성 form 보임
                $('#formCommentWrite').show();
            });

            // 댓글 입력 Form의 취소 버튼을 클릭했을 때, Editor 콘텐츠 비움.
            $(document).on('click', '#btnFormCommentWriteCancel', function (e) {
                e.preventDefault();

                // 댓글 입력 form 취소
                cancelFormCommentWrite();
            });

            // 댓글 수정 버튼
            $(document).on('click', '.btn-comment-modify', function (e) {
                e.preventDefault();

                // 이전 클릭한 수정 버튼이 존재하는 경우
                // 감추어둔 이전 댓글 다시 출력
                if (clickedModifyButton) {
                    clickedModifyButton.closest('.comment-row')
                                       .find('.comment-content')
                                       .show();
                }

                clickedModifyButton = $(this);

                // 글수정 Editor가 없는 경우 생성
                if (quillCommentModify == null) {
                    quillCommentModify = new Quill('#modifyEditor', {
                        modules: {
                            toolbar: toolbarOptions
                        },
                        placeholder: '글을 입력하세요.',
                        theme: 'snow'
                    });
                }

                // 사용자가 클릭한 수정 버튼이 포함된 댓글 Row 
                let $commentRow = $(this).closest('.comment-row');

                // 기존 댓글 정보 숨김
                $commentRow.find('.comment-content').hide();

                // 수정 Form
                // Action field 수정
                $('#formCommentModify').attr('action', $(this).attr('href'));

                // 수정 Form을 기존 댓글 영역에 추가
                $commentRow.find('.media-body').append($('#formCommentModify'));

                // Quill Editor에 현재 댓글 Content 추가
                quillCommentModify.root.innerHTML 
                    = $commentRow.find('.comment-content')
                                 .find('.ql-editor')
                                 .html();

                // 댓글 수정 Form 출력
                $('#formCommentModify').show();

                // 댓글 쓰기 form 감춤
                cancelFormCommentWrite();

                // 댓글 작성 form 감춤
                $('#formCommentWrite').hide();
            });

            // 댓글 수정 취소 버튼 클릭
            $(document).on('click', '#btnFormCommentModifyCancel', function (e){
                e.preventDefault();

                cancelFormCommentModify();

                // 댓글 작성 form 보임
                $('#formCommentWrite').show();
            });

            // 댓글 수정 Submit. editor의 html을 hidden field에 설정.
            // $('#formCommentModify').on('submit', function (e) {
            $(document).on('submit', '#formCommentModify', function (e) {
                const editorHtml = quillCommentModify.root.innerHTML;
                $('#formInputContent').val(editorHtml);
            });

            // 댓글 삭제 버튼 클릭
            $(document).on('submit', '.form-comment-delete', function (e) {
                e.preventDefault();

                if (!confirm('댓글을 삭제하시겠습니까?')) {
                    return;
                }

                $(this)[0].submit();
            });
        });
    </script>
@endpush
