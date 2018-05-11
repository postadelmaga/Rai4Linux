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



var vm = new Vue({
    beforeMount: function () {
        this.loadChannels();
        this.selectCh(1);
    },
    methods: {
        loadChannels: function () {
            for (var c in this.channel) {
                for (var i in this.days) {
                    this.loadDayChannel(this.channel[c].id, this.days[i]);
                }
            }
        },
        loadDayChannel: function (ch_id, day) {
            var channel = this.getChById(ch_id);
            var title = channel.title;
            $.ajax({
                url: './ajax.php',
                method: 'POST',
                data: {day: day, ch: title},
                success: function (data) {
                    var days = channel.days;
                    days.push({title: day, programs: JSON.parse(data)});
                },
                error: function (error) {
                    console.log(error);
                }
            });
        },

        selectCh: function (ch_id) {
            var ch = this.getChById(ch_id);
            this.ch_current = ch;
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
    created() {channels
        var self = this;
        this.$on('SwitchChannel', ch_id => {
            this.selectCh(ch_id);
            console.log(ch_id);
        });
    }
});
