<?php

namespace Inium\Laraboard\Support\Faker;

class RandomWysiwygFragment
{
    /**
     * 게시판 WYSIWYG 에디터의 기본 텍스트 Layout를 생성한다.
     * - <p> {fake text} </p>를 생성
     *
     * @param \Faker\Generator $fakerKo     한글 Faker
     * @param integer $sentences            문단 수.
     * @return string
     */
    public static function generate(\Faker\Generator $fakerKo,
                                    int $sentences = 5): string
    {
        $ret = [];

        for($i = 0; $i < $sentences; $i++) {
            $text = $fakerKo->realText();

            // Quill Editor 문단 삽입
            $appendText = '<p><br></p>';

            // 마지막 문단엔 $appendText 추가하지 않음
            if ($i < ($sentences - 1)) {
                $appendText = null;
            }

            array_push($ret, "<p>{$text}</p>{$appendText}");
        }

        return implode($ret);
    }
}
