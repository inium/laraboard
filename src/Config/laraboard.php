<?php

return [

    /*
     * 게시판 라우트 설정
     */
    'route' => [

        // 미들웨어에서 사용할 라우트 리스트
        'middleware' => [
            'web'
        ],

        /*
         * 게시판 라우트 prefix. prefix가 'im' 일 경우, /im/board 형태로 생성.
         */
        'prefix' => ''

    ],

    /*
     * 게시판 설정
     */
    'board' => [

        /*
         * 사용자 닉네임 중복 여부. true (중복 방지), false (중복 허용).
         */
        'nickname_unique' => true,

        'file' => [

            // byte 단위 파일 업로드 사이즈.  0 이면 무한
            'max_upload_size' => 0,

            // 파일 업로드 허용 확장자
            'upload_mime_types' => 'mimes:jpg,jpeg,png,gif,bmgp,svg,webp,txt,zip,hwp,xls,xlsx,ppt,pptx,doc,docx'

        ],

        /*
         * 게시판 테이블 이름
         */
        'table_name' => [

            // 게시판 사용자 권한 테이블 이름
            'role' => 'lb_board_user_roles',

            // 게시판 사용자 테이블 이름
            'user' => 'lb_board_users',

            // 게시판 테이블 이름
            'board' => 'lb_boards',

            // 게시글 테이블 이름
            'post' => 'lb_board_posts',

            // 댓글 테이블 이름
            'comment' => 'lb_board_post_comments'

        ]
    ]

];
