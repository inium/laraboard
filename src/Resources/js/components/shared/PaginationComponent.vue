<template lang="pug">

    div(v-if="hasPages")
        nav
            ul.pagination.mb-0

                //- Create the move to first button if current is not the first.
                li.page-item(v-if="!onFirstPage")
                    a.page-link(:href="firstPageUrl") &laquo;

                //- Create the move to previous button if current is not the first.
                li.page-item(v-if="!onFirstPage")
                    a.page-link(:href="prevPageUrl") &lsaquo;

                //- Create page number and links.
                li.page-item(v-for="elem in elements"
                             :class="{ active: elem.active }")
                    a.page-link(:href="elem.link") {{ elem.page }}

                //- Create the move to next button if current is not the last.
                li.page-item(v-if="hasMorePages")
                    a.page-link(:href="nextPageUrl") &rsaquo;

                //- Creat the move to last if current is not the last.
                li.page-item(v-if="!onLastPage")
                    a.page-link(:href="lastPageUrl") &raquo;

</template>

<script>
export default {
    props: {
        total: Number,
        perPage: Number,
        currentPage: Number,
        lastPage: Number,
        path: String,

        queryPageName: String,

        numOfPageLink: {
            type: Number,
            default: 10
        }
    },
    computed: {
        /**
         * Determine if there are enough items to split into multiple pages.
         *
         * @return bool
         */
        hasPages() {
            return this.currentPage != 1 || this.hasMorePages;
        },

        /**
         * Determine if there are more items in the data source.
         *
         * @return bool
         */
        hasMorePages() {
            return this.currentPage < this.lastPage;
        },

        /**
         * Determine if the paginator is on the first page.
         *
         * @return bool
         */
        onFirstPage() {
            return this.currentPage <= 1;
        },

        /**
         * Determine if the paginator is on the last page.
         *
         * @return bool
         */
        onLastPage() {
            return this.currentPage >= this.lastPage;
        },

        firstPageUrl() {
            return this.paginationUrl(this.path);
        },
        lastPageUrl() {
            return this.paginationUrl(this.path, this.lastPage);
        },
        prevPageUrl() {
            let prevPage = this.currentPage - 1;
            if (prevPage < 0) {
                prevPage = 0;
            }

            return this.paginationUrl(this.path, prevPage);
        },
        nextPageUrl() {
            let nextPage = this.currentPage + 1;
            if (nextPage > this.lastPage) {
                nextPage = this.lastPage;
            }

            return this.paginationUrl(this.path, nextPage);
        },

        elements() {
            let pageOffset 
                    = Math.floor((this.currentPage - 1) / this.numOfPageLink);

            let startPage = (pageOffset * this.numOfPageLink) + 1;
            let endPage = (startPage - 1) + this.numOfPageLink;

            if (endPage > this.lastPage) {
                endPage = this.lastPage;
            }

            let pages = [];
            for (let page = startPage; page <= endPage; page++) {
                pages.push({
                    'page': page,
                    'link': this.paginationUrl(this.path, page),
                    'active': (this.currentPage == page) ? true : false
                });
            }

            return pages;
        }
    },
    methods: {
        paginationUrl(baseUrl, page = 0) {
            let url = baseUrl;
            if (page > 0) {
                url = `${url}?${this.queryPageName}=${page}`
            }

            return url;
        }
        // changed()
    }
};
</script>
