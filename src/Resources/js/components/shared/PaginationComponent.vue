<template lang="pug">

    div(v-if="hasPages")
        nav
            ul.pagination.mb-0

                //- Move to first if current is not the first.
                li.page-item(v-if="!onFirstPage")
                    a.page-link(:href="firstPageUrl") &laquo;

                //- Move to previous if there are more previous items.
                li.page-item(v-if="!onFirstPage")
                    a.page-link(:href="prevPageUrl") &lsaquo;

                //- Page number and links.
                li.page-item(v-for="elem in elements"
                             :class="{ active: elem.active }")
                    a.page-link(:href="elem.link") {{ elem.page }}

                //- Move to next if there are more next items.
                li.page-item(v-if="hasMorePages")
                    a.page-link(:href="nextPageUrl") &rsaquo;

                //- Move to last if current is not the last.
                li.page-item(v-if="!onLastPage")
                    a.page-link(:href="lastPageUrl") &raquo;

</template>

<script>
export default {
    props: {
        currentPage: Number, // Current page number.
        lastPage: Number, // Last page number.
        basePath: String, // Pagination base path.

        // Pagination page query string name. Default is page.
        // ex) lorem.ipsum?page=3   => queryPageName: page
        queryPageName: {
            type: String,
            default: 'page'
        },

        // # of Pagination Elements. Default is 10.
        numOfElements: {
            type: Number,
            default: 10
        },

        // query strings (except queryPageName)
        queryParams: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            // First page number.
            firstPage: 1
        };
    },
    computed: {

        /**
         * Determins if there are pagination items.
         *
         * @return bool
         */
        hasPages() {
            return this.currentPage != 1 || this.hasMorePages;
        },

        /**
         * Determine if there are more items.
         *
         * @return bool
         */
        hasMorePages() {
            return this.currentPage < this.lastPage;
        },

        /**
         * Determine if current is on the first page.
         *
         * @return bool
         */
        onFirstPage() {
            return this.currentPage <= 1;
        },

        /**
         * Determine current is on the last page.
         * 
         * @return bool
         */
        onLastPage() {
            return this.currentPage >= this.lastPage;
        },

        /**
         * Get the first page url.
         * 
         * @return string
         */
        firstPageUrl() {
            return this.paginationUrl(this.firstPage);
        },

        /**
         * Get the last page url.
         * 
         * @return string
         */
        lastPageUrl() {
            return this.paginationUrl(this.lastPage);
        },

        /**
         * Get the previous page url.
         * 
         * @return string
         */
        prevPageUrl() {
            let prevPage = this.prevPage;
            if (prevPage < 0) {
                prevPage = 0;
            }

            return this.paginationUrl(prevPage);
        },

        /**
         * Get the next page url.
         * 
         * @return string
         */
        nextPageUrl() {
            let nextPage = this.nextPage;
            if (nextPage > this.lastPage) {
                nextPage = this.lastPage;
            }

            return this.paginationUrl(nextPage);
        },

        /**
         * Get the preivous page number
         * 
         * @return int
         */
        prevPage() {
            return this.currentPage - 1;
        },

        /**
         * Get the next page number
         * 
         * @return int
         */
        nextPage() {
            return this.currentPage + 1;
        },

        /**
         * Get the pagination page elements.
         * It will return array of pagination info each consist of
         * {page, link, active}.
         * 
         * - page: Page number.
         * - link: Pagination Link.
         * - active: If current page number true else otherwise.
         * 
         * @return array
         */
        elements() {
            const pageOffset = (this.currentPage - 1) / this.numOfElements;
            let startPage = (Math.floor(pageOffset) * this.numOfElements) + 1;
            let endPage = (startPage - 1) + this.numOfElements;

            if (endPage > this.lastPage) {
                endPage = this.lastPage;
            }

            let pages = [];
            for (let page = startPage; page <= endPage; page++) {
                pages.push({
                    'page': page,
                    'link': this.paginationUrl(page),
                    'active': (this.currentPage == page) ? true : false
                });
            }

            return pages;
        }
    },
    methods: {

        /**
         * Get pagination url.
         * 
         * @param int page      A page number.
         * @return string
         */
        paginationUrl(page = 0) {
            let url = this.basePath;
            let param = this.queryParams || [];

            if (page > this.firstPage) {
                param[this.queryPageName] = page;
            }

            const queryString = this.httpBuildQuery(param);

            url = `${url}?${queryString}`;

            return url;
        },

        /**
         * PHP's 'http_build_query' function equivalent in javascript
         *
         * @param {*} jsonObj   JSON Object
         * @see https://gist.github.com/luk-/2722097
         */
        httpBuildQuery(jsonObj) {
            const keys = Object.keys(jsonObj);
            const values = keys.map(key => jsonObj[key]);

            return keys.map((key, index) => {
                    return `${key}=${values[index]}`;
                })
                .join("&");
        }
    }
};
</script>
