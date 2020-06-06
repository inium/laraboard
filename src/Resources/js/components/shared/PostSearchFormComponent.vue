<template lang="pug">
    div
        form.form-inline(method="GET" :action="action" @submit="validateForm")
            .d-flex.align-items-start
                .form-group
                    select.custom-select(name="type")
                        option(:value="key"
                               v-for="(value, key) in searchTypes"
                               :selected="key == userSearchType") {{ value }}

                .form-group.mx-2
                    .form-input
                        input.form-control(type="text"
                                           name="query"
                                           placeholder="검색어 입력"
                                           v-model="query"
                                           :class="{'is-invalid' : isInvalid}")

                        .invalid-feedback(v-if="isInvalid") Required

                .form-group
                    button.btn.btn-primary(type="submit") 검색
</template>

<script>
export default {
    props: {
        searchTypes: Object,        // 검색 유형
        action: String,             // Form Action URL

        // 사용자가 검색한 type.
        userSearchType: {
            type: String,
            default: null
        },

        // 사용자 검색어
        userQuery: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            query: this.userQuery,  // 검색어
            invalidForm: false      // Input 값이 유효한지 여부를 저장
        };
    },
    computed: {
        /**
         * Input 값이 유효하지 여부를 검사 후 반환한다.
         * 
         * @return boolean  유효할 경우 true. 그 외 false.
         */
        isInvalid() {
            if (this.query.length > 0) {
                this.invalidForm = false;
            }

            return this.invalidForm;
        }
    },
    methods: {
        /**
         * Form Validation
         * 
         * @param event e   event
         * @return boolean  true: 검증완료, false: 검증 실패. query 값 없음.
         */
        validateForm: function (e) {
            if (this.query) {
                this.invalidForm = false;
                return false;
            }
            else {
                this.invalidForm = true;
                e.preventDefault();
            }
        }
    }
};
</script>

<style lang="scss" scoped>

</style>