Vue.component('player', {
    props: ['src_current'],
    template: '#player',
});

// Vue.component('header', {
//     props: ['ch_current'],
// });

Vue.component('channels', {
    props: ['id','title'],
    before: function () {
    },
    methods: {
        click: function (event) {
            // now we have access to the native event
            if (event) event.preventDefault();

            // this.setCurrentChannel(this.channel.id);
            this.$parent.ch_current = this.id;
            this.$parent.$emit('switch-channel', this.id);
        },
    },
});


Vue.component('daylist', {
    template: '#daylist',
    data: function () {
        return {};
    },
    created: function () {
        this.$parent.$on('switch-channel', ch_id => {
            this.setCurrentChannel(ch_id);
            console.log(ch_id);
        });
    },
    methods: {
        loadDayChannel: function (day, ch_id) {
            var url = this.ajaxurl;
            var channel = this.$parent.getChannelById(ch_id);

            if (channel.days.length == 0) {
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
    template: '#program',
    prop: ['day', 'title'],
});


// Define a new component called button-counter
Vue.component('button-counter', {
    template: '<button v-on:click="count++">You clicked me {{ count }} times.</button>'
});


