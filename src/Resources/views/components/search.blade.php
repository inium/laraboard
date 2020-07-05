@push('stylesheets')
<style>
.thumbnail {
    width: 21px;
    height: 21px;
}
</style>
@endpush

{{-- 게시글 목록 -------------------------------------------------------------}}
<div class="lb-posts">

    {{-- 게시글 Header --}}
    <div class="lb-posts-header d-flex flex-row align-items-center pb-2">

        {{-- 게시판 이름 --}}
        <div class="lb-posts-board-name">
            <h4>{{ $board['name_ko'] }} <small>검색결과</small></h4>
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

    {{-- 게시글 목록 --}}
    <div class="lb-posts-body py-3">

        @if (count ($search) > 0)

            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-8 text-truncate">제목</div>
                        <div class="col-2">작성자</div>
                        <div class="col-1 text-truncate">조회수</div>
                        <div class="col-1 text-truncate">작성일</div>
                    </div>
                </li>

                {{-- 검색결과 목록 --}}
                @foreach($search as $post)

                    @include('laraboard::components.shared.listRow', [
                        'notice'        => $post['notice'],
                        'boardName'     => $post['board']['name'],
                        'postId'        => $post['id'],
                        'subject'       => $post['subject'],
                        'commentsCount' => $post['comments_count'],
                        'viewCount'     => $post['view_count'],
                        'user'          => $post['user'],
                        'createdAt'     => $post['created_at'],
                        'page'          => $page,   // Request::get('page'),
                        'query'         => $query,  // Request::get('query')
                        'markPostId'    => isset($markPostId) ? $markPostId : null
                    ])

                @endforeach

            </ul>

        @else
            <div class="text-center">
                <h5>No contents</h5>
            </div>
        @endif

    </div>

    {{-- 게시글 Footer --}}
    <div class="lb-posts-footer">

        <div class="d-flex justify-content-between">

            {{-- 글 목록 --}}
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

            {{-- 페이지네이션 --}}
            <div>
                {{ $search->appends(['query' => $query])->links() }}
            </div>

            {{-- 글 쓰기 --}}
            <div>
                @if ($role->post->canWrite)
                    <a href="{{ route('board.post.create', [
                                    'boardName' => $board['name']
                                ]) }}" class="btn btn-primary">
                        글쓰기
                    </a>
                @endif
            </div>

        </div>

        {{-- 검색 form --}}
        <div class="d-flex justify-content-center pt-3">

            @include ('laraboard::components.shared.searchForm', [
                'action' => route('board.post.index', [
                                        'boardName' => $board['name']
                                    ]),
                'query' => $query
            ])

        </div>

    </div>

</div>

{{-- Script --}}
@push('scripts')
<script>
    $(document).ready(function () {

        // $('.highlight')

        // let check = new RegExp(query, "ig");
        // return words.toString().replace(check, function (match, a, b) {
        //     return `<span class="highlight">${match}</span>`;
        // });
    })
</script>
@endpush
