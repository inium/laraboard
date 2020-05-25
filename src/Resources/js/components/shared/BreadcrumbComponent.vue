<template lang="pug">
    nav(aria-label="breadcrumb")
        ol.breadcrumb.mb-0
            li.breadcrumb-item(
                v-for="(item, index) in breadcrumb"
                :class="{ active : lastLoop(index, item.length) }"
            )
                span(v-if="item.link")
                    a(:href="item.link") {{ item.name }}
                span(v-else) {{ item.name }}
</template>

<script>
export default {
    props: {
        breadcrumb: Array
    },
    methods: {
        lastLoop: function (index, length) {
            return index == length - 1 ? true : false;
        }
    }
};
</script>
