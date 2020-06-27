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

            <div class="media mb-3 @if($comment['parent_comment_id']) child @endif">

                {{-- 게시글 썸네일 --}}
                @include('laraboard::components.shared.thumbnail', [
                    'alt' => 'thumbnail',
                    'class' => 'rounded-circle thumbnail align-self-start mr-3'
                ])

                <div class="media-body">
                    <div class="d-flex jusityf-content-between">
                        <div>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <strong>

                                        {{-- 검색어 mark. 없을 경우 일반 문자열 출력. --}}
                                        @include('laraboard::components.shared.mark', [
                                            'query' => $query,
                                            'content' => $comment['user']['nickname']
                                        ])

                                    </strong>
                                </li>

                                <li class="list-inline-item">

                                    {{-- Carbon locale to ko, diff for humans --}}
                                    @include('laraboard::components.shared.carbonDate', [
                                        'date' => $comment['created_at']
                                    ])

                                </li>
                            </ul>
                        </div>
                    </div>

                    <div>

                        {{-- 검색어 mark. 없을 경우 일반 문자열 출력. --}}
                        @include('laraboard::components.shared.mark', [
                            'query' => $query,
                            'content' => $comment['content']
                        ])

                    </div>
                </div>
            </div>

        @endforeach

    </div>

    {{-- 댓글 Footer --}}
    <div class="lb-comments-footer py-2">

        {{-- 댓글 새로고침 버튼 --}}
        <div class="d-flex justify-content-center mb-5">
            <div>
                <button id="btnCommentReload" class="btn btn-secondary btn-block">
                    댓글 새로고침
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-between">

            {{-- 글 목록 --}}
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

            {{-- 글 쓰기 --}}
            <div>
                @if ($role->post->canWrite)
                    <a href="{{ route('board.post.write.view', [
                                    'boardName' => $board['name']
                                ]) }}" class="btn btn-primary">
                        글쓰기
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
            
        });
    </script>
@endpush
