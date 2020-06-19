<template lang="pug">
    .lb-list 
        //- 게시글 Header
        .lb-list-header.d-flex.flex-row.align-items-center.pb-2

            //- 게시판 이름
            .lb-list-board-name
                span {{board.name_ko}}
                small &nbsp; 검색결과

            //- Breadcrumbs
            .ml-auto
                breadcrumb-component(:breadcrumb="getBreadcrumb")

        //- 게시글 목록
        .lb-list-body.py-3

            //- 콘텐츠가 존재하는 경우 표시
            div(v-if="hasContents")

                ul.list-group.list-group-flush
                    li.list-group-item
                        .row
                            .col-8.text-truncate 제목
                            .col-2 작성자
                            .col-1.text-truncate 조회수
                            .col-1.text-truncate 작성일

                    //- 게시글 목록
                    li.list-group-item(v-for="post in posts")
                        list-row-component(:is-notice="post.notice"
                                           :subject="post.subject"
                                           :comments-count="post.comments_count"
                                           :nickname="post.user.nickname"
                                           :thumbnail-path="post.user.thumbnail_path"
                                           :view-count="post.view_count"
                                           :created-at="post.created_at",
                                           :post-url="post.post_url",
                                           :query="searchBrief.query")

            //- 콘텐츠가 존재하지 않는 경우
            div(v-else)
                .lb-list-no-contents
                    span No posts

        //- 게시글 목록 Footer
        .lb-list-footer
            div(v-if="hasContents")
                .d-flex.justify-content-between
                    div
                        //- 글 목록 버튼
                        a.btn.btn-primary(:href="routes.list"
                                          v-if="routes && routes.list") 목록

                    div
                        //- 페이지네이션
                        pagination-component(
                            :current-page="pagination.current_page"
                            :last-page="pagination.last_page"
                            :base-path="pagination.base_path"
                            :query-params="pagination.query_params"
                        )

                    div
                        //- 글쓰기 버튼
                        a.btn.btn-primary(:href="routes.write"
                                          v-if="routes && routes.write") 글쓰기

                .d-flex.justify-content-center.pt-3(v-if="form && form.search")
                    //- 검색 Form
                    search-form-component(:action="form.search.action"
                                          :user-query="searchBrief.query")
</template>

<script>
import BreadcrumbComponent from './shared/BreadcrumbComponent';
import ListRowComponent from './shared/ListRowComponent';
import PaginationComponent from './shared/PaginationComponent';
import SearchFormComponent from './shared/SearchFormComponent';

export default {
    components: {
        'breadcrumb-component': BreadcrumbComponent,
        'list-row-component': ListRowComponent,
        'pagination-component': PaginationComponent,
        'search-form-component': SearchFormComponent
    },
    props: {
        board: Object,          // 게시판
        searchBrief: Object,    // 검색 정보
        posts: Array,           // 게시글
        pagination: Object,     // 페이지네이션
        routes: Object,         // Route 정보
        form: {
            type: Object,      // 검색 Form 정보
            default: null
        }
    },
    data() {
        return {

        };
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
         * 공지사항 혹은 게시글이 존재하는지 여부를 검사하여 반환한다.
         * 둘 중 하나라도 존재하면 true, 없을 경우 false를 반환한다.
         * 
         * @return boolean
         */
        hasContents() {
            return (this.posts.length) ? true : false;
        }
    },
    methods: {

    }
};
</script>

<style lang="scss" scoped>
.lb-list {
    .lb-list-header {
        .lb-list-board-name {
            font-size: 1.25rem;
        }
    }
    .lb-list-body {
        .lb-list-no-contents {
            font-size: 1.5rem;
            text-align: center;
        }
    }
}
</style>