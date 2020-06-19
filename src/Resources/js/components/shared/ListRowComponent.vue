<template lang="pug">
    .row
        //- 제목
        .col-8
            span.badge.badge-success.mr-2(v-if="isNotice") 공지
            a(:href="postUrl" v-html="highlight(subject, query)")

            small.ml-2(v-if="commentsCount > 0") [{{ commentsCount }}]

        //- 작성자
        .col-2.text-truncate
            //- 썸네일
            img.rounded-circle.thumbnail.align-self-start.mr-2(:src="thumbnail"
                                                               :alt="nickname")

            span(v-html="highlight(nickname, query)")

        //- 조회수
        .col-1
            span {{ numberFormat(viewCount) }}

        //- 작성일
        .col-1
            span {{ createdDatetime(createdAt)  }}

</template>

<script>
import highlight from '../../mixins/highlight';
import numberFormat from '../../mixins/numberFormat';
import carbonDateTime from '../../mixins/carbonDateTime';

export default {
    mixins: [
        highlight,
        numberFormat,
        carbonDateTime
    ],
    props: {
        isNotice: Number,       // 공지여부
        subject: String,        // 제목
        commentsCount: Number,  // 댓글 수
        nickname: String,       // 작성자 닉네임
        thumbnailPath: String,  // 작성자 썸네일
        viewCount: Number,      // 조회 수
        createdAt: String,      // 게시글 작성일
        postUrl: String,        // 게시글 보기 URL

        // highlight할 텍스트. 검색 결과 표시에 사용.
        query: {
            type: String,
            default: null
        }
    },
    computed: {
        /**
         * 사용자 썸네일 경로를 반환한다. 없을 경우 기본 경로를 반환한다.
         * 
         * @return string
         */
        thumbnail() {
            const defaultThumbnail = '/vendor/laraboard/images/user.png';

            return this.thumbnailPath ? this.thumbnailPath : defaultThumbnail;
        }
    },
    methods: {
        /**
         * 출력할 Datetime 정보를 반환한다.
         * 
         * @param carbon Carbon Datetime
         * @return string
         */
        createdDatetime(carbon) {
            if (this.diffInTime(carbon) <= 24) {
                return this.hi(carbon);
            }
            else {
                return this.md(carbon);
            }
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
.thumbnail {
    width: 21px;
    height: 21px;
}
</style>
