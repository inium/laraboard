@foreach ($comments as $comment)

    {{-- 댓글 Row --}}
    @include ('laraboard::components.shared.commentRow', [
        'comment' => $comment,
        'board'   => $comment['board'],
        'role'    => $role,
    ])

@endforeach
