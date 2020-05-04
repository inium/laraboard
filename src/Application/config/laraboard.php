<?php

return [

    /*
     | 라라벨 Auth 설정
     */
    'auth' => [

        // 'php artisan make:auth' 명령어로 만들어지는 사용자 정보 저장 테이블
        'user_table_name' => 'users',

        // Auth User 모델 클래스명.
        'model_name' => 'App\User'
    ],

    /*
     | 게시판 라우트 설정
     */
    'route' => [

        // 미들웨어에서 사용할 라우트 리스트
        'middleware' => [
            'web'
        ],

        /*
         | 게시판 라우트 prefix.
         | ex) prefix가 'im' 일 경우, 라우트는 /im/board/~~ 로 생성
         */
        'prefix' => ''

    ],

    /*
     | 게시판 설정
     */
    'board' => [

        /*
         | 사용자 닉네임 중복 방지 여부
         | true일 경우 중복 방지, false일 경우 중복 허용.
         */
        'nickname_unique' => true,

        /*
         | 테이블 이름
         */
        'table_name' => [

            // 게시판 사용자 권한 테이블 이름
            'privilege' => 'lb_board_user_privileges',

            // 게시판 사용자 테이블 이름
            'user' => 'lb_board_user',

            // 게시판 테이블 이름
            'board' => 'lb_boards',

            // 게시글 테이블 이름
            'post' => 'lb_board_posts',

            // 댓글 테이블 이름
            'comment' => 'lb_board_post_comments'

        ]
    ]

];
