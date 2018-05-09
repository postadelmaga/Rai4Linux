<?php
include_once('./includes/Core.php');
$app = new Vue();
?>
<!DOCTYPE html>
<html>
<head>
    <?php include('app/vue/head.phtml') ?>
    <script src="js/vue.js"></script>
    <script src="js/vue/components.js"></script>
</head>
<body>

<script>
    var data = JSON.parse('<?php echo $app->getJsonConfig() ?>');
    var vm = new Vue({
        data: data,
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
        created() {
            var self = this;
            this.$on('SwitchChannel', ch_id => {
                this.selectCh(ch_id);
                console.log(ch_id);
            });
        }
    })
</script>
<div id="app">

    <?php include('app/vue/header.phtml'); ?>

    <?php include('app/vue/video.phtml'); ?>

    <div class="w3-bar w3-mobile w3-border w3-round channel_list">
        <?php include('app/vue/channel.phtml'); ?>
    </div>

    <?php include('app/vue/days.phtml'); ?>

</div>

<script>
    vm.$mount('#app');
</script>

<script>
    var player = new MediaElementPlayer('videoElement', {
        /**
         * YOU MUST SET THE TYPE WHEN NO SRC IS PROVIDED AT INITIALISATION
         * (This one is not very well documented.. If one leaves the type out, the success event will never fire!!)
         **/
        type: ["video/mp4"],
        features: ['playpause', 'progress', 'current', 'duration', 'tracks', 'volume'],
        mediaElementInitialized: false,
        //more options here..

        success: function (mediaElement, domObject) {
            this.mediaElementInitialized = true;
        },
        error: function (e) {
            alert(e);
        }
    });
</script>


</body>
</html>