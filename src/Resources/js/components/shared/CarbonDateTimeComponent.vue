<template lang="pug">
    span
        span(v-if="full") {{ ymdHis }}
        span(v-else)
            span(v-if="diffInTime <= 24") {{ hi }}
            span(v-else) {{ md }}

</template>

<script>
export default {
    props: {
        carbon: String,
        full: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        /**
         * prop으로 입력된 carbon 시간을 Y-m-d H:i:s 시간으로 반환한다.
         * 
         * @return string
         */
        ymdHis() {
            const carbonDate = new Date(this.carbon);

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
         * @return string
         */
        md()
        {
            const carbonDate = new Date(this.carbon);

            let m = carbonDate.getMonth() + 1;  m = m > 9 ? m : '0' + m;
            let d = carbonDate.getDate();       d = d > 9 ? d : '0' + d;

            return `${m}-${d}`;
        },

        /**
         * prop으로 입력된 carbon 시간을 H:i 시간으로 반환한다.
         * 
         * @return string
         */
        hi() {
            const carbonDate = new Date(this.carbon);

            let y = carbonDate.getFullYear();
            let m = carbonDate.getMonth() + 1;  m = m > 9 ? m : '0' + m;

            return `${y}-${m}`;
        },

        /**
         * 현재와 prop으로 입력된 시간 차이를 반환한다.
         * 
         * @return integer
         */
        diffInTime()
        {
            const carbonDate = new Date(this.carbon);
            const now = new Date();

            return now.getTime() - carbonDate.getTime();
        },

        /**
         * 현재와 prop으로 입력된 날짜(day) 차이를 반환한다.
         * 
         * @return integer
         */
        diffInDays()
        {
            const diffInTime = this.diffInTime();
            return diffInTime / (1000 * 3600 * 24); 
        }
    }
};
</script>