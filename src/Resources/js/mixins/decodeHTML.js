const mixins = {

    methods: {

        /**
         * Encoding된 HTML을 decoding 한다.
         * 
         * @param string text   Encoded HTML text
         * @return string       Decoded HTML text
         * @see https://gist.github.com/daraul/7057c25495dc0284d1c4e77997d25938
         */
        decodeHTML(text) {
            const map = {
                '&amp;': '&',
                '&#038;': "&",
                '&lt;': '<',
                '&gt;': '>',
                '&quot;': '"',
                '&#039;': "'",
                '&#8217;': "’",
                '&#8216;': "‘",
                '&#8211;': "–",
                '&#8212;': "—",
                '&#8230;': "…",
                '&#8221;': '”'
            };

            return text.replace(/\&[\w\d\#]{2,5}\;/g, function(m) {
                return map[m];
            });
        }
    }

};

export default mixins;
