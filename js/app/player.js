Vue.component('player', {
    props: ['src_current'],
    template: '#player',
});

// Vue.component('header', {
//     props: ['current_id'],
// });

Vue.component('channels', {
    props: ['id', 'title'],
    before: function () {
    },
    data: function () {
        return {current_id: 1};
    },
    methods: {
        click: function (event) {
            // now we have access to the native event
            if (event) event.preventDefault();

            // this.setCurrentChannel(this.channel.id);
            // this.$parent.$emit('switch-channel', this.id);
            // this.$dispatch('switch-channel', this.id);
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


