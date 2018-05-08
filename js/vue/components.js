Vue.component('ch-title', {
    props: ['chcurrent'],
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

Vue.component('ch-days', {
    props: ['day', 'chcurrent'],
});

Vue.component('day', {
    props: ['day', 'chcurrent'],
    mounted: function () {
        var self = this;

        $.ajax({
            url: './ajax.php',
            method: 'POST',
            data: {day: self.day, ch: self.chcurrent.title},
            success: function (data) {
                var ch_data = self.chcurrent;
                ch_data.programs[self.day] = data;
            },
            error: function (error) {
                console.log(error);
            }
        });
    }
});

Vue.component('video-player', {
    props: ['current_src'],
});

Vue.component('ch-programs', {
    props: ['program'],
    template: `<a class="w3-bar-item w3-button">{{program.title}}</a>`
});
