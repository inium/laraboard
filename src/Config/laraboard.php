<?php

return [

    /**
     * 게시판 라우트 설정
     */
    'route' => [

        /**
         * 미들웨어에서 사용할 라우트 리스트
         */
        'middleware' => [
            'web'
        ],

        /**
         * 게시판 라우트 prefix. prefix가 'im' 일 경우, /im/board 형태로 생성.
         */
        'prefix' => ''

    ],

    /**
     * 게시판 설정
     */
    'board' => [

        /**
         * 사용자 정보 수집 여부. true (수집), false (수집하지 않음)
         * 
         * 수집항목:
         * - IP Address(암호화 하여 저장)
         * - User Agent(암호화 하여 저장)
         * - User Agent 분석 Device Type (desktop, tablet, mobile, other 중 1)
         * - OS name, version
         * - Browser name, version
         */
        'collect_user_info' => false,

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
