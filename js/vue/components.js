Vue.component('ch-title', {
    props: ['channels', 'current'],
});


Vue.component('ch-list', {
    props: ['channel', 'id', 'current'],
});

Vue.component('ch-days', {
    props: ['day', 'current'],
    mounted: function () {
        var self = this;
        $.ajax({
            url: './ajax.php',
            method: 'POST',
            data: {day: self.day, ch: self.current},
            success: function (data) {
                self.items = data;
                console.log('- Mount data:' + self.day + self.current);
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


Vue.component('ch_programs', {
    props: ['program'],
    template: `<a class="w3-bar-item w3-button">{{program.title}}</a>`
});
