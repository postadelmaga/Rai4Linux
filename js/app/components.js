Vue.component('player', {
    props: ['src_current'],
    template: '#player',
});

// Vue.component('header', {
//     props: ['ch_current'],
// });

Vue.component('channels', {
    props: ['channel', 'id', 'current'],
    data: function () {
        self = this;
        return {
            channels: self.$parent.channels,
        };
    },

    before: function () {
    },
    methods: {
        click: function (event) {
            // now we have access to the native event
            if (event) event.preventDefault();

            // this.setCurrentChannel(this.channel.id);
            this.$parent.ch_current = this.channel.id;
            this.$parent.$emit('switch-channel', this.channel.id);
        },
    },
});


Vue.component('daylist', {
    template: '#daylist',
    methods: {
        loadDayChannel: function (day, ch_id) {
            var url = this.ajaxurl;
            if (c.days.length == 0) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {day: day, ch: ch_id},
                    success: function (data) {
                        try {
                            var result = tryParseJSON(data);
                            if (result.success) {
                                return {date: day, programs: result.programs};
                            }
                            else {
                                jQuery('body').append(jQuery('<div>').html(data));
                                return null;
                            }
                        } catch (e) {
                            jQuery('body').append(jQuery('<div>').html(data));
                            return null;
                        }

                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            }
        }
    }
});

Vue.component('program', {
    data: function () {
        return {
            time: this.$parent.day,
        }
    },
    template: '<div>{{ time }} times.</div>'
});

// Define a new component called button-counter
Vue.component('button-counter', {
    data: function () {
        return {
            count: 0
        }
    },
    template: '<button v-on:click="count++">You clicked me {{ count }} times.</button>'
});


