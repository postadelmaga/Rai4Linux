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

        var chList = this.config.channelList;
        for (var i in  chList) {
            var self = this;
            var ch = chList[i];

            this.logger('- Init Channel: ' + ch);
            this.loader.createLoader(chList[i]);

            var chButton = jQuery('<a>', {'id': 'btn_' + ch, 'class': "w3-bar-item w3-button", 'href': "#"}).html(ch);
            chButton.data('ch', ch);
            chButton.click(
                function (event) {
                    self.chClick(event);
                }
            ).mousedown(function (event) {
                switch (event.which) {
                    case 3:
                        if (confirm('Vuoi scaricare nuovamente la lista per ' + ch + '?')) {
                            self.loadChannel(ch, 1);
                        }
                        break;
                    default:
                }
            });

            chButton.appendTo(jQuery('.channel_list'));

            this.loadChannel(ch);
        }
        this.selectChannel(chList[0]);

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

    this.chClick = function (event, nogoto = 0) {
        var ch = jQuery(event.currentTarget).data('ch');
        this.selectChannel(ch);
        this.loadChannel(ch);

        if (!nogoto) {
            this.goToByScroll('program_box');
        }
    };

    this.selectChannel = function (ch) {
        this.logger('- Select: ' + ch);

        jQuery(page_title).html(ch);
        this.currentChannel = ch;
        //el.show('fold', 1000);
        this.loader.resetLoader(ch);

        var ch_button = jQuery('#btn_' + ch);
        // Remove active class from all buttons
        ch_button.parent().find('a').each(function (el) {
                jQuery(this).removeClass('w3-green')
            }
        );
        ch_button.addClass('w3-green');
    };


    this.loadChannel = function (ch, update = 0) {
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

    this.updateDay = function (ch, day, first = 0, update = 0) {

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
                    this.logger('- Data_OK [' + ch + ' - ' + day + ', ' + update + ']');
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
                    this.logger('- Data_NO [' + ch + ' - ' + day + ', ' + update + ']');
                    this.loader.up(ch);
                }
            });
        }
    };

    this.renderDay = function (day) {

        var stream = this;
        var ch = this.currentChannel;

        this.logger('- Render [' + day + ']');
        var target = jQuery('#' + day + ' .program_list');
        target.fadeIn().html('');

        var program_list = this.streamList[ch][day];

        var i = 0;
        for (var prog_id in program_list) {
            if (i % 3 == 0) {
                var current_row = jQuery('<div>', {
                    class: 'program w3-card-4 w3-cell-row'
                });
            }
            var program_cell = jQuery('<div>', {
                id: prog_id,
                class: 'program w3-card-4 w3-cell'
            });
            // Set data
            $(program_cell).data('ch', ch);
            $(program_cell).data('day', day);

            var data = program_list[prog_id];

            var title = data.title;
            var desc = data.description;
            var time = data.time;

            // Title
            jQuery('<header>', {
                class: 'w3-container w3-rex'
            })
                .html('<h6>' + time + ' -- ' + title + '</h6>')
                .appendTo(program_cell);

            // Description
            jQuery('<div>', {
                class: 'w3-container'
            }).html('<p>' + data.description + '</p>')
                .appendTo(program_cell);

            if (data.video_urls.length == 0) {
                program_cell.addClass('error');
                this.logger('-- No Video - Program: ' + prog_id);
            }
            else {
                program_cell.click((function () {
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

            program_cell.appendTo(current_row);
            current_row.appendTo(target);
            i++;
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