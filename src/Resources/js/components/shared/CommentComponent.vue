<template lang="pug">
    div
        .comment-head

        .comment-body
            .media.mb-3(v-for="comment in comments"
                        :class="{'child': hasParent(comment) }")
                //- 썸네일
                img.rounded-circle.thumbnail.align-self-start.mr-3(
                                    :src="thumbnail(comment)"
                                    :alt="comment.user.nickname")
                .media-body
                    ul.list-inline
                        li.list-inline-item 
                            b {{ comment.user.nickname }}

                        li.list-inline-item
                            span {{ ymdHis(comment.created_at) }}

                    div(v-html="highlight(decodeHTML(comment.content), query)")
</template>

<script>
import decodeHTML from '../../mixins/decodeHTML';
import highlight from '../../mixins/highlight';
import carbonDateTime from '../../mixins/carbonDateTime';

export default {
    mixins: [
        decodeHTML,
        highlight,
        carbonDateTime
    ],
    props: {
        query: String,
        comments: Array,
        pagination: Object
    },
    computed: {

    },
    methods: {
        /**
         * 부모 댓글이 존재하는지 여부를 반환한다.
         * 
         * @return bool
         */
        hasParent(comment) {
            return comment.parent_comment_id ? true : false;
        },

        /**
         * 사용자 썸네일 경로를 반환한다. 없을 경우 기본 경로를 반환한다.
         * 
         * @return string
         */
        thumbnail(comment) {
            const defaultThumbnail = '/vendor/laraboard/images/user.png';

            return comment.user.thumbnail_path ? 
                    comment.user.thumbnail_path : defaultThumbnail;
        }
    }
};
</script>

<style lang="scss" scoped>
.comment-body {
    .media {
        &.child {
            padding-left: 4rem !important;

            .thumbnail {
                width: 32px;
                height: 32px;
            }
        }
        .thumbnail {
            width: 48px;
            height: 48px;
        }
    }
}
</style>