const mixins = {

    methods: {

        /**
         * 매개변수로 입력된 carbon 시간을 Y-m-d H:i:s 시간으로 반환한다.
         * 
         * @param carbon    Carbon datetime
         * @return string
         */
        ymdHis(carbon) {
            const carbonDate = new Date(carbon);

            let y = carbonDate.getFullYear();
            let m = carbonDate.getMonth() + 1;  m = m > 9 ? m : '0' + m;
            let d = carbonDate.getDate();       d = d > 9 ? d : '0' + d;
            let h = carbonDate.getHours();      h = h > 9 ? h : '0' + h;
            let i = carbonDate.getMinutes();    i = i > 9 ? i : '0' + i;
            let s = carbonDate.getSeconds();    s = s > 9 ? s : '0' + s;

            return `${y}-${m}-${d} ${h}:${i}:${s}`;
        },

        /**
         * prop으로 입력된 carbon 시간을 m-d 시간으로 반환한다.
         * 
         * @param carbon    Carbon datetime
         * @return string
         */
        md(carbon)
        {
            const carbonDate = new Date(carbon);

            let m = carbonDate.getMonth() + 1;  m = m > 9 ? m : '0' + m;
            let d = carbonDate.getDate();       d = d > 9 ? d : '0' + d;

            return `${m}-${d}`;
        },

        /**
         * prop으로 입력된 carbon 시간을 H:i 시간으로 반환한다.
         * 
         * @param carbon    Carbon datetime
         * @return string
         */
        hi(carbon) {
            const carbonDate = new Date(carbon);

            let y = carbonDate.getFullYear();
            let m = carbonDate.getMonth() + 1;  m = m > 9 ? m : '0' + m;

            return `${y}-${m}`;
        },

        /**
         * 현재와 prop으로 입력된 시간 차이를 반환한다.
         * 
         * @param carbon    Carbon datetime
         * @return integer
         */
        diffInTime(carbon)
        {
            const carbonDate = new Date(carbon);
            const now = new Date();

            return now.getTime() - carbonDate.getTime();
        },

        /**
         * 현재와 prop으로 입력된 날짜(day) 차이를 반환한다.
         * 
         * @param carbon    Carbon datetime
         * @return integer
         */
        diffInDays(carbon)
        {
            const diffInTime = this.diffInTime(carbon);
            return diffInTime / (1000 * 3600 * 24); 
        }
    }
};

export default mixins;
