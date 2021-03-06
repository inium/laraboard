{{-- Stylesheets -------------------------------------------------------------}}
@push('stylesheets')
    <style>
        .post-content-body {
            min-height: 500px;
        }
        .lb-post .thumbnail {
            width: 48px;
            height: 48px;
        }

        /* Quill Editor read-only */
        .lb-post .ql-container.ql-snow {
            border: 0;
        }
        .lb-post .ql-container .ql-editor{
            font-size: initial;
            padding: 0;
        }
        .lb-post .ql-toolbar {
            display: none;
        }
    </style>
@endpush


{{-- 게시글 정보 -------------------------------------------------------------}}
<div class="lb-post">

    {{-- 게시글 Header --}}
    <div class="lb-post-header d-flex flex-row align-items-center">

        {{-- 게시판 이름 --}}
        <div class="lb-post-board-name">

            <h4>{{ $post['board']['name_ko']}}</h4>

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

    {{-- 게시글 본문 --}}
    <div class="lb-post-content pt-3">

        <div class="post-content-header">

            {{-- 게시글 제목 --}}
            <div class="header-title py-2">

                <h5>{{ $post['subject'] }}</h5>

            </div>

            <div class="d-flex justify-content-between py-2">

                <div>

                    {{-- 게시글 정보 --}}
                    <ul class="list-inline mb-0">

                        {{-- 작성자 닉네임 --}}
                        <li class="list-inline-item">

                            {{-- 게시글 썸네일 --}}
                            @include('laraboard::components.shared.thumbnail', [
                                'thumbnail' => $post['user']['thumbnail_path'],
                                'alt' => 'thumbnail',
                                'class' => 'rounded-circle thumbnail align-self-start mr-1'
                            ])

                            <strong> {{ $post['user']['nickname'] }}</strong>

                        </li>

                        <li class="list-inline-item">
                            조회수: {{ number_format ($post['view_count'] )}}
                        </li>

                        <li class="list-inline-item">
                            작성일:

                            <span tabindex="0" data-toggle="tooltip" title="{{ $post['created_at'] }}">
                                {{-- Carbon locale to ko, diff for humans --}}
                                @include('laraboard::components.shared.carbonDate', [
                                    'date' => $post['created_at']
                                ])
                            </span>

                            {{-- 댓글 수정날짜 --}}
                            @if ($post['updated_at'])
                                <span tabindex="0" data-toggle="tooltip" title="{{ $post['updated_at'] }}">
                                    (수정됨)
                                </span>
                            @endif
                        </li>

                        <li class="list-inline-item">
                            댓글: {{ number_format($post['comments_count']) }}
                        </li>

                    </ul>

                </div>

                {{-- 본인이 작성한 글에 대해서만 수정, 삭제 가능 --}}
                @if ($post['user']->user->id == Auth::id())

                    <div>

                        <ul class="list-inline mb-0">

                            {{-- 게시글 수정 --}}
                            <li class="list-inline-item mr-0">
                                <a href="{{ route('board.post.edit', [
                                                'boardName' => $post['board']['name'],
                                                'postId' => $post['id']
                                            ]) }}" class="btn btn-primary">
                                    수정
                                </a>
                            </li>

                            {{-- 댓글이 없을 경우에만 게시글 삭제 --}}
                            @if ($post['comments_count'] == 0)

                                <li class="list-inline-item">

                                    {{-- 게시글 삭제는 DELETE에서 처리 --}}
                                    <form id="formDeletePost"
                                          method="POST"
                                          action="{{ route('board.post.destroy', [
                                                        'boardName' => $post['board']['name'],
                                                        'postId' => $post['id']
                                                    ]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">삭제</button>
                                    </form>

                                </li>

                            @endif

                        </ul>

                    </div>

                @endif

            </div>

        </div>

        {{-- 게시글 본문 --}}
        <div class="post-content-body py-3">

            {{-- 게시글을 Toast UI Viewer로 출력 --}}
            <div id="viewer">
                {!! htmlspecialchars_decode($post['content']) !!}
            </div>

        </div>

    </div>

    {{-- 게시글 Footer --}}
    <div class="lb-post-footer">

    </div>

</div>


{{-- Scripts -----------------------------------------------------------------}}
@push('scripts')
    <script>
        $(document).ready(function () {

            // Quill Editor Post viewer 생성
            const quillPost = new Quill('#viewer', {
                readOnly: true,
                theme: 'snow'
            });

            // 게시글 삭제 버튼 클릭
            $('#formDeletePost').on('submit', function (e) {
                e.preventDefault();

                if (!confirm('게시글을 삭제하시겠습니까?')) {
                    return;
                }

                $(this)[0].submit();
            });

        });
    </script>
@endpush
