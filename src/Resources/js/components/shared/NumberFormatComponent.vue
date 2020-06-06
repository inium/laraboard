<template lang="pug">
    span {{ numberFormat() }}
</template>

<script>
export default {
    props: {
        number: Number
    },
    methods: {

        /**
         * 숫자에 콤마(,)를 삽입한다.
         * 
         * @return String
         */
        numberFormat() {
            return this.number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

    }
}
</script>