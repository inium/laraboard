<template lang="pug">
    .row
        //- 제목
        .col-8
            span.badge.badge-success.mr-2(v-if="isNotice") 공지
            a(:href="postUrl" v-html="highlight(subject, query)")
                //- span()

            small.ml-2 [{{ commentsCount }}]

        //- 작성자
        .col-2.text-truncate
            span {{ nickname }}

        //- 조회수
        .col-1
            number-format-component(:number="viewCount")

        //- 작성일
        .col-1
            carbon-datetime-component(:carbon="createdAt")

</template>

<script>
import NumberFormatComponent from './NumberFormatComponent';
import CarbonDateTimeComponent from './CarbonDateTimeComponent';

export default {
    components: {
        'number-format-component': NumberFormatComponent,
        'carbon-datetime-component': CarbonDateTimeComponent
    },
    props: {
        isNotice: Number,       // 공지여부
        subject: String,        // 제목
        commentsCount: Number,  // 댓글 수
        nickname: String,       // 작성자 닉네임
        viewCount: Number,      // 조회 수
        createdAt: String,      // 게시글 작성일
        postUrl: String,        // 게시글 보기 URL

        // highlight할 텍스트. 검색 결과 표시에 사용.
        query: {
            type: String,
            default: null
        }
    },
    methods: {
        /**
         * words에 포함된 query를 highlight 처리한다.
         * 
         * @param string words  단어, 문장
         * @param string query  highlight할 단어
         * @see https://stackoverflow.com/questions/37839608/vue-js-text-highlight-filter/46378407
         */
        highlight: function (words, query) {
            if (!words) {
                return '';
            }

            if (!query) {
                return words;
            }

            let check = new RegExp(query, "ig");
            return words.toString().replace(check, function (match, a, b) {
                return `<span class="highlight">'${match}'</span>`;
            });
        }
    }
};
</script>

<style lang="scss" scoped>
// deep selector
// @see https://stackoverflow.com/questions/48032006/how-do-i-use-deep-or-in-vue-js
::v-deep .highlight {
    background-color: #ffc107;
}
</style>
