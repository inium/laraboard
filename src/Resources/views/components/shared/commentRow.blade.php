<div class="media comment-row mb-4 @if($comment['parent_comment_id']) child @endif"
    data-id="{{ $comment['id'] }}"
    data-group-id="{{ $comment['group_id'] }}">

    {{-- 댓글 사용자 썸네일 --}}
    @include('laraboard::components.shared.thumbnail', [
        'thumbnail' => $comment['user']['thumbnail_path'],
        'alt' => 'thumbnail',
        'class' => 'rounded-circle thumbnail align-self-start mr-3'
    ])

    <div class="media-body">

        <div class="d-flex justify-content-between">

            <div>

                <ul class="list-inline">

                    {{-- 댓글 작성자 --}}
                    <li class="list-inline-item">
                        <strong>{{ $comment['user']['nickname'] }}</strong>
                    </li>

                    {{-- 댓글 작성날짜 --}}
                    <li class="list-inline-item">
                        @include('laraboard::components.shared.carbonDate', [
                            'date' => $comment['created_at']
                        ])
                    </li>
                </ul>

            </div>

            {{-- 댓글 사용자인 경우, 수정/삭제 버튼 활성 --}}
            @if ($comment['user']['user']['id'] == Auth::id())

                <div>

                    <ul class="list-inline">

                        {{-- 게시글 수정 버튼. --}}
                        <li class="list-inline-item mr-0">
                            <a href="{{ route('board.comment.update', [
                                                'boardName' => $board['name'],
                                                'postId' => $comment['post']['id'],
                                                'commentId' => $comment['id']
                                            ]) }}"
                                class="btn btn-link text-warning btn-sm px-0 btn-comment-modify">
                                수정
                            </a>
                        </li>

                        {{-- 게시글 삭제 버튼 --}}
                        @if ($comment['children']->count() == 0)

                            <li class="list-inline-item">
                                <form action="{{ route('board.comment.destroy', [
                                                    'boardName' => $board['name'],
                                                    'postId' => $comment['post']['id'],
                                                    'commentId' => $comment['id']
                                                ]) }}"
                                        class="form-comment-delete"
                                        method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger btn-sm pr-0">삭제</button>
                                </form>
                            </li>

                        @endif

                    </ul>

                </div>

            @endif

        </div>

        <div class="comment-content">

            {{-- 댓글 --}}
            <div id="comment_{{$comment['id']}}" class="comment">
                {!! htmlspecialchars_decode($comment['content']) !!}
            </div>

            {{-- 부모 댓글이 없고 댓글 쓰기 권한이 있을 경우, 답글 버튼 활성화 --}}
            @if (is_null($comment['parent']) && $role->comment->canWrite)

                <div class="comment-command py-2">
                    <button class="btn btn-link btn-sm px-0 btn-comment-reply"
                            data-id="{{ $comment['id'] }}">
                        답글
                    </button>
                </div>

            @endif

        </div>
    </div>
</div>
