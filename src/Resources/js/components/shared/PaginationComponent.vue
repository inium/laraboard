<template lang="pug">
    div(v-if="hasPages")
        nav
            ul.pagination



</template>

<script>
export default {
    props: {
        total: Number,
        perPage: Number,
        currentPage: Number,
        lastPage: Number,
        firstPageUrl: String,
        lastPageUrl: String,
        nextPageUrl: String,
        prevPageUrl: String,
        path: String,
        from: Number,
        to: Number
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
        }
    },
    methods: {

    }
};
</script>