<?php

return [
    /**
     * 사용자 정보 수집 여부. true (수집), false (수집하지 않음)
     * @see Inium\Laraboard\Support\Detect;
     *
     * 수집항목:
     * - IP Address (암호화 하여 저장)
     * - User Agent (암호화 하여 저장)
     * - User Agent 분석 Device Type (desktop, tablet, mobile, other 중 1)
     * - OS 이름 / 버전
     * - Browser 이름 / 버전
     */
    "collect_user_info" => false,

    // 게시글 본문에 허용할 HTML 태그 (XSS Protection)
    "allow_post_content_tags" => [
        "p",
        "i",
        "u",
        "br",
        "div",
        "span",
        "hr",
        "a",
        "img",
        "blockquote",
        "ul",
        "ol",
        "li",
        "table",
        "tr",
        "td",
    ],

    // 댓글 본문에 허용할 HTML 태그 (XSS Protection)
    "allow_comment_content_tags" => [
        "p",
        "i",
        "u",
        "br",
        "div",
        "span",
        "hr",
        "a",
        "img",
        "blockquote",
        "ul",
        "ol",
        "li",
        "table",
        "tr",
        "td",
    ],
];
