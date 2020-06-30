{{--
  -- 게시글 목록 Row
  -- 
  -- 사용방법
  -- ---------------------------------------------------------------------------
  --  @include ('laraboard::shared.listRow', [
  --        'notice'        => $post['notice'],
  --        'boardName'     => $post['board']['name'],
  --        'postId'        => $post['id'],
  --        'subject'       => $post['subject'],
  --        'commentsCount' => $post['comments_count'],
  --        'viewCount'     => $post['view_count'],
  --        'user'          => $post['user'],
  --        'createdAt'     => $post['created_at'],
  --        'page'          => $page,   // Request::get('page'),
  --        'query'         => $query,  // Request::get('query')
  --        'markPostId'    => $markPostId
  --        ])
  ------------------------------------------------------------------------------
  -- @param boolean notice          공지글 여부
  -- @param string boardName        게시판 이름
  -- @param integer postId          게시글 ID
  -- @param string subject          게시글 제목
  -- @param integer commentsCount   댓글 수
  -- @param integer viewCount       조회 수
  -- @param array user              작성자 정보
  -- @param Carbon createdAt        생성일
  -- @param integer page            페이지 번호. 1일 경우 null.
  -- @param string query            검색어. 없을 경우 null.
  -- @param integer markPostId      (optional)강조할 게시글의 ID. 게시글 보기
                                    메뉴 하단의 목록에 현재글 표시에 사용.
  --}}

@php

$marked = false;

if (isset($markPostId)) {
    $marked = ($post['id'] == $markPostId) ? true : false;
}

@endphp

@push('stylesheets')
<style>
.thumbnail {
    width: 21px;
    height: 21px;
}

</style>
@endpush

<li class="list-group-item">

    <div class="row">

        <div class="col-8">

            {{-- 공지 여부 --}}
            @if ($notice)
                <span class="badge badge-success mr-2">공지</span>
            @endif

            {{-- 게시글 보기 --}}
            <a href="{{ route('board.post.view', [
                            'boardName' => $boardName,
                            'id' => $postId,
                            'page' => $page == 1 ? null : $page,
                            'query' => $query
                        ])}}" @if ($marked)class="font-weight-bold"@endif>

                {{ $subject }}

            </a>

            {{-- 댓글 수 --}}
            @if ($commentsCount > 0)
                <small class="ml-2"> [{{ $commentsCount }}] </small>
            @endif

        </div>

        {{-- 작성자 --}}
        <div class="col-2 text-truncate">

            {{-- 게시글 썸네일 --}}
            @include('laraboard::components.shared.thumbnail', [
                'thumbnail' => $user['thumbnail_path'],
                'alt' => 'thumbnail',
                'class' => 'rounded-circle thumbnail align-self-start mr-1'
            ])

            {{ $user['nickname'] }}

        </div>

        {{-- 조회수 --}}
        <div class="col-1">
            {{ number_format($viewCount) }}
        </div>

        {{-- 작성일 --}}
        <div class="col-1">

            {{-- Carbon locale to ko, diff for humans --}}
            @include('laraboard::components.shared.carbonDate', [
                'date' => $createdAt
            ])

        </div>

    </div>
</li>
