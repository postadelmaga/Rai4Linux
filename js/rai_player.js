function Stream(videoId, config) {

    this.streamList = new Array();
    this.config = JSON.parse(config);
    this.videoId = videoId;

    this.config.debug = true;

    this.init = function () {

        this.logger('- Create Loader');
        this.loader = new Loader('loadbar');

        this.logger('- Init Player');
        this.player = new MediaElementPlayer('videoElement', {

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
            }
        );

        this.logger('- Init Channels');
        var chList = this.config.channelList;
        for (var i in  chList) {
            var ch = chList[i];
            this.loader.createLoader(ch, this.chClick, this);
            this.loadChannel(ch, false);
        }

        this.chSwitch(chList[0]);

        // this.player;

    };

    this._playProgram = function (el) {
        var data = this.streamList[$(el).data('ch')][$(el).data('day')][el.id];

        // only one resolution
        if (this.player) {
            this.player.setSrc(data.video_urls[0]);
            this.player.load();
            this.player.play();
            this.logger('Set Url: ' + data.video_urls[0]);
        }
    };


    this.chClick = function (ch, nogoto) {

        this.logger('- chClick:' + ch);
        this.chSwitch(ch);
        
        this.loadChannel(ch, 0);

        if (nogoto)
            return;
        this.goToByScroll('program_box');
    };

    this.chSwitch = function (ch) {
        this.currentChannel = ch;
        //el.show('fold', 1000);
        this.loader.resetLoader(ch);

        var ch_button = jQuery('#btn_' + ch);
        // Remove active class from all buttons
        ch_button.parent().find('button').each(function () {
                jQuery(this).removeClass('active')
            }
        );
        ch_button.addClass('active');
    };


    this.loadChannel = function (ch, update) {

        this.streamList[ch] = new Array();
        this.loader.showLoader(ch);

        var days = this.config.dayRange;
        for (var i in days) {
            jQuery('label#load-' + ch).text('0');
            jQuery('label#load').text('0');

            var date = days[i];

            if (ch == this.currentChannel) {
                jQuery('.' + date + ' .loader').show();
                jQuery('.' + date + ' .program_list').hide();
                var today = new Date(date);
                // var cDate = today.getDate() + '-' + (today.getMonth() + 1) + '-' + today.getFullYear();
                // var label = jQuery('#' + date + ' button label').text(ch + ' - ' + today.toDateString());
            }

            jQuery('label#' + ch).css('color', 'yellow');
            this.updateDay(ch, date, 1, update);

        }
    };

    this.updateDay = function (ch, day, first = 0, update = 0,) {

        if (!first) {
            if (this.streamList[ch] && this.streamList[ch][day]) {
                // UPDATE DAY
                if (ch == this.currentChannel) {
                    this.renderDay(day);
                }
                this.loader.up(ch);
            }
        }
        else {
            // var callback = this._updateDay;
            var params = {up: update, ch: ch, day: day};

            jQuery.ajax({
                type: 'POST',
                url: this.config.ajaxUrl,
                dataType: 'json',
                data: params,
                context: this,
                success: function (data) {
                    this.logger('( ' + ch + ', ' + day + ', ' + update + ') - END-OK');
                    // callback.call(this, ch, day, data);

                    if (!this.streamList[ch]) {
                        this.streamList[ch] = new Array();
                    }

                    this.streamList[ch][day] = data;

                    // UPDATE CURRENT DAY HTML
                    if (ch == this.currentChannel) {
                        this.renderDay(day);
                    }

                    this.loader.up(ch);

                },
                error: function (data) {
                    // jQuery('#' + day + ' .loader').hide();
                    this.logger('( ' + ch + ', ' + day + ', ' + update + ') - END-FAIL');
                    this.loader.up(ch);
                }
            });
        }
    };

    this.renderDay = function (day) {
        var stream = this;

        var target = jQuery('#' + day + ' .program_list');
        target.fadeIn().html('');

        var ch = this.currentChannel;
        var dayList = this.streamList[ch][day];

        for (var idP in dayList) {
            var data = dayList[idP];

            this.logger('- Render [Channel:' + ch + ' - Date: ' + day + ']');

            var program_row = jQuery('<li>', {
                id: idP,
                class: 'program w3-card-4'
            });

            // Title
            jQuery('<header>', {
                class: 'w3-container w3-blue'
            })
                .html('<h1>' + data.time + ' -- ' + data.title + '</h1>')
                .appendTo(program_row);

            // Description
            jQuery('<div>', {
                class: 'w3-container'
            })
                .html(data.description)
                .appendTo(program_row);

            // Set data
            $(program_row).data('ch', ch);
            $(program_row).data('day', day);


            if (data.video_urls.length == 0) {
                program_row.addClass('error');
                this.logger('-- No Data Url');
            }
            else {
                program_row.click((function () {
                    stream._playProgram(this);
                }));
            }

            // row.hoverIntent((function () {
            //         jQuery(this).find('.description').fadeIn()
            //     }),
            //     (function () {
            //         jQuery(this).find('.description').fadeOut(1200);
            //     })
            // );

            program_row.appendTo(target);
        }

        jQuery('#' + day + ' .loader').hide();
    };

    this.goToByScroll = function (elId) {
        // var el;
        // if (el = jQuery('#' + elId)) {
        //     // Remove 'link' from the ID
        //     $('html,body').animate({
        //             scrollTop: el.offset().top
        //         },
        //         'slow');
        // }
    };

    this.logger = function (msg) {
        if (this.config.debug) {
            console.log(Stream.logCounter + ': ' + msg);
            Stream.logCounter = Stream.logCounter + 1;
        }
    };

    // INIT
    this.init();
}