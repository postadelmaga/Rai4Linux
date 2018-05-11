Vue.component('ch-title', {
    props: ['ch_current'],
});

Vue.component('ch-list', {
    props: ['channel', 'id', 'current'],
    data: function () {
        self = this;
        return {
            days: self.$parent.days,
            channels: self.$parent.channels,
            ajaxurl: self.$parent.ajaxurl,
        };
    },
    mounted: function () {
        this.setCurrentChannel(1);
    },
    methods: {
        click: function (event) {
            // now we have access to the native event
            if (event) event.preventDefault();

            this.setCurrentChannel(this.channel.id);
            // this.$emit('SwitchChannel', this.channel.id);
        },

        loadChannels: function () {
            for (var i in this.channels) {
                this.loadChannel(this.channels[c]);
            }
        },

        loadChannel: function (c) {
            for (var i in this.days) {
                this.getChannelDayList(c, this.days[i]);
            }
        },

        getChannelDayList: function (channel, day) {
            var id = channel.id;
            var url = this.ajaxurl;

            if (channel.days.length == 0) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {day: day, ch: id},
                    success: function (data) {
                        var days = channel.days;
                        channel.daysb = JSON.parse(data);
                        days.push({date: day, programs: JSON.parse(data)});
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }
            ;


        },
        setCurrentChannel(ch_id) {
            var c = this.getChById(ch_id);
            this.loadChannel(c);
            this.$parent.ch_current = c;
        },

        getChById: function (ch_id) {
            var i;
            for (i in this.channels) {
                if (this.channels[i].id == ch_id) {
                    return this.channels[i];
                }
            }
        },
    },
    created() {
        var self = this;
        this.$on('SwitchChannel', ch_id => {
            this.selectCh(ch_id);
            console.log(ch_id);
        });
    }
});

Vue.component('video-player', {
    props: ['current_src'],
});


Vue.component('ch-days', {
    props: ['day'],
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

