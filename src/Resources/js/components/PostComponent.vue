<template lang="pug">
    .lb-post
        
        //- 게시글 Header
        .lb-post-header.d-flex.flex-row.align-items-center.pb-2

            //- 게시판 이름
            .lb-post-board-name
                span {{post.board.name_ko}}

            //- Breadcrumbs
            .ml-auto
                breadcrumb-component(:breadcrumb="getBreadcrumb")


        //- 게시글 본문
        .lb-post-content

            .post-content-header

                //- 게시글 제목
                .header-title.py-3 
                    span(v-html="highlight(post.subject, query)")

                ul.list-inline.mb-0
                    li.list-inline-item

                        //- 썸네일
                        img.rounded-circle.thumbnail.align-self-start.mr-3(
                                    :src="thumbnail"
                                    :alt="post.user.nickname")

                        span {{ post.user.nickname }}
                    li.list-inline-item 
                        span 조회수: {{ numberFormat(post.view_count) }}

                    li.list-inline-item
                        span 작성일: {{ ymdHis(post.created_at) }}
                        span(v-if="post.updated_at") &nbsp;(수정됨)

                    li.list-inline-item
                        span 댓글 {{ numberFormat(post.comments_count) }}

            //- 게시글 본문
            .post-content-body.py-2
                div(v-html="highlight(decodeHTML(post.content), query)")

            //- 게시글 Footer
            .post-content-footer.pt-1.pb-5
                .d-flex.justify-content-between
                    div
                        //- 글 목록 버튼
                        a.btn.btn-primary(:href="list.routes.list"
                                          v-if="list.routes && list.routes.list") 목록
                    div
                        //- 글 삭제 버튼
                        a.btn.btn-danger(:href="list.routes.delete"
                                         v-if="list.routes && list.routes.delete"
                                         @click="postDelete") 삭제

        //- 댓글
        .lb-post-comments.pb-5
            .lb-post-comment-header.pb-2
                h5 댓글 {{ numberFormat(post.comments_count) }} 개

            //- 댓글 목록
            .lb-post-comment-body
                comment-component(:total-count="post.comments_count"
                                  :query="query"
                                  :comments="comments.comments"
                                  :pagination="comments.pagination")

            .lb-post-comment-footer

        .lb-post-list

            //- 검색결과 출력
            div(v-if="list.search")
                search-component(:board="list.board"
                                 :search="list.search"
                                 :posts="list.posts"
                                 :pagination="list.pagination"
                                 :routes="list.routes"
                                 :search-form="list.searchForm")

            //- 게시글 목록 출력
            div(v-else)
                post-list-component(:board="list.board"
                                    :notices="list.notices"
                                    :posts="list.posts"
                                    :pagination="list.pagination"
                                    :routes="list.routes"
                                    :search-form="list.searchForm")

</template>

<script>
import BreadcrumbComponent from './shared/BreadcrumbComponent';
import CommentComponent from './shared/CommentComponent';
import PostListComponent from './PostListComponent';
import SearchComponent from './SearchComponent';

import decodeHTML from '../mixins/decodeHTML';
import highlight from '../mixins/highlight';
import numberFormat from '../mixins/numberFormat';
import carbonDateTime from '../mixins/carbonDateTime';

export default {
    mixins: [
        decodeHTML,
        highlight,
        numberFormat,
        carbonDateTime
    ],
    components: {
        'breadcrumb-component': BreadcrumbComponent,
        'post-list-component': PostListComponent,
        'search-component': SearchComponent,
        'comment-component': CommentComponent
    },
    props: {
        post: Object,       // 게시글
        list: Object,       // 게시글 목록
        comments: Object    // 댓글
    },
    computed: {
        /**
         * 페이지에 표시할 Breadcrumb을 반환한다.
         * 
         * @return string
         */
        getBreadcrumb() {
            let breadcrumb = [
                { 'name': 'Home', 'link': '/' },
                { 'name': 'Board' }
            ];

            return breadcrumb;
        },

        /**
         * 검색어 정보가 존재할 경우 반환한다.
         * 
         * @return string
         */
        query() {
            return this.list.search ? this.list.search.query : null;
        },

        /**
         * 사용자 썸네일 경로를 반환한다. 없을 경우 기본 경로를 반환한다.
         * 
         * @return string
         */
        thumbnail() {
            const defaultThumbnail = '/vendor/laraboard/images/user.png';

            return this.post.user.thumbnail_path ?
                        this.post.user.thumbnail_path : defaultThumbnail;
        }
    },
    methods: {
        postDelete(e) {
            e.preventDefault();
            alert('삭제?');
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

.lb-post {
    .lb-post-header {
        .lb-post-board-name {
            font-size: 1.25rem;
        }
    }
    .lb-post-content {

        .post-content-header {

            .header-title {
                font-size: 1.2rem;
            }
            .thumbnail {
                width: 48px;
                height: 48px;
            }
        }
        .post-content-body {
            min-height: 20rem;
        }
    }
}
</style>