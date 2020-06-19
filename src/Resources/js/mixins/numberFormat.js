const mixins = {
    methods: {

        /**
         * 숫자에 콤마(,)를 삽입한다.
         * 
         * @return String
         */
        numberFormat(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }
};

export default mixins;
