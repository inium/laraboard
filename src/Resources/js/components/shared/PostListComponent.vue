<template lang="pug">
    .row
        //- 제목
        .col-8
            span.badge.badge-success.mr-2(v-if="post.notice") 공지
            a(:href="post.postUrl") {{ post.subject }}
            small.ml-2 [{{ post.comments_count }}]

        //- 작성자
        .col-2.text-truncate
            span {{ post.user.nickname }}

        //- 조회수
        .col-1
            number-comma-component(:number="post.view_count")

        //- 작성일
        .col-1
            carbon-datetime-component(:carbon="post.created_at")

</template>

<script>
import NumberCommaComponent from './NumberCommaComponent';
import CarbonDateTimeComponent from './CarbonDateTimeComponent';

export default {
    components: {
        'number-comma-component': NumberCommaComponent,
        'carbon-datetime-component': CarbonDateTimeComponent
    },
    props: {
        post: Object,   // 게시글
    }
};
</script>
