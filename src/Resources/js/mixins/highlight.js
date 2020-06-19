const mixins = {

    methods: {

        /**
         * words에 포함된 query를 highlight 처리한다.
         * 
         * @param string words  단어, 문장
         * @param string query  highlight할 단어
         * @see https://stackoverflow.com/questions/37839608/vue-js-text-highlight-filter/46378407
         */
        highlight(words, query) {
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

export default mixins;
