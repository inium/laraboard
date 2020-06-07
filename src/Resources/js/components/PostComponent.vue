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

            .post-content-header.mb-1

                //- 게시글 제목
                .header-title.py-3 
                    span(v-html="highlight(post.subject, query)")

                ul.list-inline.mb-0
                    li.list-inline-item
                        span 작성자: {{ post.user.nickname }}
                    li.list-inline-item 
                        span 조회수: 
                        number-format-component(:number="post.view_count")

                    li.list-inline-item
                        span 작성일: 
                        carbon-datetime-component(:carbon="post.created_at"
                                                  full=true)

                        span(v-if="post.updated_at") &nbsp;(수정됨)

                    li.list-inline-item
                        span 댓글 
                        number-format-component(:number="post.comments_count")

            .post-content-body.py-4
                div(v-html="highlight(post.content, query)")

        .lb-post-comments

        .lb-post-footer

            //- 검색결과 출력
            div(v-if="list.search")
                post-search-component(:board="list.board"
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
import CarbonDateTimeComponent from './shared/CarbonDateTimeComponent';
import NumberFormatComponent from './shared/NumberFormatComponent';
import PostListComponent from './PostListComponent';
import PostSearchComponent from './PostSearchComponent';

export default {
    components: {
        'breadcrumb-component': BreadcrumbComponent,
        'carbon-datetime-component': CarbonDateTimeComponent,
        'number-format-component': NumberFormatComponent,
        'post-list-component': PostListComponent,
        'post-search-component': PostSearchComponent
    },
    props: {
        post: Object,
        list: Object
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

        query() {
            let q = null;

            if (this.list.search) {
                q = this.list.search.query;
            }

            return q;

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
                return `<span class="highlight">${match}</span>`;
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

.lb-post {
    .lb-post-header {
        .lb-post-board-name {
            font-size: 1.25rem;
        }
    }
    .lb-post-content {
        // padding-top: 1rem;
        // padding-bottom: 1rem;
        // min-height: 20rem;

        .post-content-header {

            .header-title {
                font-size: 1.2rem;
            }
        }
        .post-content-body {
            min-height: 20rem;
        }
    }
}
</style>