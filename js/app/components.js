Vue.component('player', {
    props: ['src_current'],
    template: '#player',
    data: function () {
        return {src: this.$parent.src_current}
    }
});

// Vue.component('header', {
//     props: ['ch_current'],
// });

Vue.component('channels', {
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

        getChannelById: function (ch_id) {
            var i;
            for (i in this.channels) {
                if (this.channels[i].id == ch_id) {
                    return this.channels[i];
                }
            }
        },

        setCurrentChannel(ch_id) {
            var c = this.getChannelById(ch_id);
            this.loadChannel(c);
            this.$parent.ch_current = c;
        },

        loadChannels: function () {
            for (var i in this.channels) {
                this.loadChannel(this.channels[c]);
            }
        },

        loadChannel: function (c) {

            for (var i in this.days) {

                var day = this.days[i];
                var id = c.id;
                var url = this.ajaxurl;

                if (c.days.length == 0) {
                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: {day: day, ch: id},
                        success: function (data) {
                            try {
                                var result = tryParseJSON(data);
                                if (result.success) {
                                    channel.days.push({date: day, programs: result.programs});
                                }
                                else {
                                    jQuery('body').append(jQuery('<div>').html(data));
                                }
                            } catch (e) {
                                jQuery('body').append(jQuery('<div>').html(data));
                            }

                        },
                        error: function (error) {
                            console.log(error);
                        }
                    });
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


Vue.component('daylist', {
    template: '#daylist',
    props: ['day'],
    data: function () {
        return {days: this.$parent.ch_current.days}
    },
});

Vue.component('program', {
    props: ['ch_current', 'dayList'],
    data: function () {
        return {daylist: this.$parent.ch_current.days}
    }

});


