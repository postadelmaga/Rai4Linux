function Stream(videoId, config) {

    this.streamList = new Array();
    this.config = JSON.parse(config);
    this.videoId = videoId;

    this.config.debug = true;

    this.init = function () {
        this.currentChannel = this.config.channelList[0];
        this.logger('- Init() Stream obj');


        for (var i in this.config.channelList) {
            this.initChannelBtn(this.config.channelList[i]);
            this._loadChannel(this.config.channelList[i], false);
        }


        this._selectChannelBtn(this.currentChannel);


        this.player = new MediaElementPlayer('videoElement', {
                /**
                 * YOU MUST SET THE TYPE WHEN NO SRC IS PROVIDED AT INITIALISATION
                 * (This one is not very well documented.. If one leaves the type out, the success event will never fire!!)
                 **/
                type: ["video/mp4"],
                features: ['playpause', 'progress', 'current', 'duration', 'tracks', 'volume'],

                //more options here..

                success: function (mediaElement, domObject) {
                    mediaElementInitialized = true;
                },
                error: function (e) {
                    alert(e);
                }
            }
        );
        // this.player;

    };


    this.getPlayer = function () {
        // return videojs(this.videoId);
        return this.player;
    };


    this._setVideo = function (el) {
        var data = this.getDataById(el.id);

        // only one resolution
        if (this.getPlayer()) {
            this.getPlayer().setSrc(data.video_urls[0]);
            this.getPlayer().load();
            this.getPlayer().play();
            this.logger('Set Url: ' + data.video_urls[0]);

        }
    };


    this.initChannelBtn = function (ch) {
        var stream = this;

        var chbtn = jQuery('<a>', {'id': 'btn_' + ch, 'class': "w3-button", 'href': "#"})
            .text(ch)
            .click(function (e) {
                stream.selectChannel(ch);
            }).mousedown(function (event) {
                switch (event.which) {
                    case 3:
                        if (confirm('Vuoi scaricare nuovamente la lista per ' + ch + '?')) {
                            stream._loadChannel(ch, true);
                        }
                        break;
                    default:
                }
            })
            .appendTo(jQuery('.channel_list'));

        var loader = jQuery('<div>', {'class': 'progress progress-striped active'}).append(
            jQuery('<div>', {
                'id': 'loadbar_' + ch,
                'class': 'bar',
                'aria-valuenow': 0
            })).appendTo(chbtn);

        return this;
    };

    this.selectChannel = function (ch, nogoto) {
        this.logger('- selectChannel:' + ch);
        this.currentChannel = ch;
        this._selectChannelBtn(ch);
        this._loadChannel(ch, 0);
        if (nogoto)
            return;
        this.goToByScroll('program_box');
    };

    this._loadChannel = function (ch, update) {

        if (update) {
            this.streamList[ch] = new Array();
            jQuery('#channel_list').find('#btn_' + ch + ' .progress').show();
        }

        for (var i in  this.config.dayRange) {
            jQuery('label#load-' + ch).text('0');
            jQuery('label#load').text('0');
            this._getDayData(ch, this.config.dayRange[i], update);
            var target = jQuery('label#' + ch).css('color', 'yellow');
        }
    };

    this._selectChannelBtn = function (ch) {
        var el = jQuery('#btn_' + ch);
        //el.show('fold', 1000);

        var load = jQuery('#loadbar_' + ch);
        load.attr('aria-valuenow', 0);
        load.css('width', 0);

        if (ch == this.currentChannel) {
            if (el.parent()) {
                // remove active class from all buttons
                el.parent().find('button').each(function () {
                        $(this).removeClass('active')
                    }
                );
            }
            el.find('button').addClass('active');
        }
    };


    this._getDayData = function (ch, day, update) {
        this.logger('- Get(' + ch + ',' + day + ',' + update + ')');
        var target = jQuery('#' + day);

        if (ch == this.currentChannel) {
            target.find('.program_list').html('');
            jQuery('#' + day + ' .loader').show();
            jQuery('#' + day + ' .program_list').hide();
            var today = new Date(day);
//            var cDate = today.getDate() + '-' + (today.getMonth() + 1) + '-' + today.getFullYear();
            var label = jQuery('#' + day + ' button label').text(ch + ' - ' + today.toDateString());
        }

        if (update) {
            this._ajaxDataDay(ch, day, 1);
        }
        else {
            if (this.streamList[ch] && this.streamList[ch][day]) {
                this._processDayData(ch, day, this.streamList[ch][day]);
            }
            else {
                this._ajaxDataDay(ch, day, 0);
            }
        }
    };

    this._processDayData = function (ch, day, data) {
        // add Day Data
        if (!this.streamList[ch])
            this.streamList[ch] = new Array();
        if (day && data)
            this.streamList[ch][day] = data;
        if (ch == this.currentChannel) {
            this._setDayHtml(day, ch);

        }
        this._loaderIncreaseOneDay(ch);
    };

    this._ajaxDataDay = function (ch, day, update) {
        var callback = this._processDayData;
        var data = {ch: ch, day: day};

        if (update)
            data = {up: 1, ch: ch, day: day};

        jQuery.ajax({
                type: 'POST',
                url: this.config.ajaxUrl,
                dataType: 'json',
                data: data,
                context: this,
                success: function (data) {
                    this.logger('( ' + ch + ', ' + day + ', ' + update + ') - END-OK');
                    callback.call(this, ch, day, data);
                },
                error: function (data) {
                    jQuery('#' + day + ' .loader').hide();
                    this.logger('( ' + ch + ', ' + day + ', ' + update + ') - END-FAIL');
                    this._loaderIncreaseOneDay(ch);
                }
            }
        )
    };

    this._setDayHtml = function (day, ch) {
        var target = jQuery('#' + day + ' .program_list');
        target.fadeIn();
        jQuery('#' + day + ' .loader').hide();
        stream = this;

        var dayList = this.streamList[ch][day];

        for (var idP in dayList) {
            var data = dayList[idP];


            var program_row = jQuery('<li>', {
                id: idP,
                class: 'program w3-card-4'
            }).html(data.time + ' -- ' + data.title);

            var program_title = jQuery('<header>', {
                class: 'w3-container w3-blue'
            }).html('<h1>' + data.time + ' -- ' + data.title + '</h1>');

            var program_description = jQuery('<div>', {
                class: 'w3-container'
            }).html(data.description);

            program_title.appendTo(program_row)
            program_description.appendTo(program_row)

            if (data.video_urls.length == 0) {
                program_row.addClass('error');
                this.logger('-- No Data Url');
            }
            else {
                program_row.click((function () {
                    stream._setVideo(this);
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
    };


    this.getDataById = function (prog_id) {
        var ch = this.currentChannel;
        var day = jQuery('#' + prog_id).parents('.day').attr('id');
        return this.streamList[ch][day][prog_id];
    };

    this._loaderIncreaseOneDay = function (ch) {

        // Main Load Bar
        var loadBar = jQuery('#loadbar');
        var perc = parseFloat(loadBar.attr('aria-valuenow'));
        perc = Math.round((perc + (1 / 28 * 100)) * 10) / 10; // round number (*10/10)

        if (perc >= 100) {
            perc = 100;
            loadBar.parent().fadeOut();
        }
        loadBar.attr('aria-valuenow', perc);
        loadBar.css('width', (perc) + '%');
        //loadBar.text(Math.ceil(perc));

        // Ch Bar
        var loadBarCh = jQuery('#loadbar_' + ch);
        perc = parseFloat(loadBarCh.attr('aria-valuenow'));
        perc = Math.round((perc + (1 / 7 * 100)) * 10) / 10; // round number (*10/10)

        if (perc >= 100) {
            perc = 100;
            loadBarCh.css('width', '0px');
            loadBarCh.attr('aria-valuenow', 0);
            loadBarCh.parent().hide();//removeClass('bar');
        }
        loadBarCh.attr('aria-valuenow', perc);
        loadBarCh.css('width', (perc) + '%');
        loadBarCh.text(Math.ceil(perc));
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

    // Init
    this.init();
}