Vue.component('ch-title', {
    props: ['ch_current'],
});

Vue.component('ch-list', {
    props: ['channel', 'id', 'current'],
    methods: {
        click: function (event) {
            // now we have access to the native event
            if (event) event.preventDefault();
            this.$parent.$emit('SwitchChannel', this.channel.id);
        },
    },
});

Vue.component('video-player', {
    props: ['current_src'],
});


Vue.component('ch-days', {
    props :['day'],
    data: function () {
        return {days: this.$parent.ch_current.days}
    },
});

Vue.component('ch_day_program', {
    props: ['ch_current', 'dayList'],

    data: function () {
        return {daylist: this.$parent.ch_current['days']}
    }

});
Vue.component('ch_program', {
    data: function () {
        return {day_data: this.$parent}
    }
});
