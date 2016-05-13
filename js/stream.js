function Stream(videoId, config) {
    this.streamList = new Array();
    this.config = JSON.parse(config);
    this.videoId = videoId;

    this.init = function () {
        this.currentChannel = this.config.channelList[0];
        this.logger('- Init() Stream obj');
        for (var i in this.config.channelList) {
            this.initChannelBtn(this.config.channelList[i]);
            this._loadChannel(this.config.channelList[i], false);
        }
        this.selectChannel(this.currentChannel);
    };

    this.initChannelBtn = function (ch) {

        var stream = this;

        var chbtn = jQuery('<li>', {'id': 'btn_' + ch})
            .append(jQuery('<a>', {'class': 'btn btn-primary btn-danger btn-large', 'href': '#'}).text(ch))
            .click(function () {
                stream.selectChannel(ch);
            }).appendTo(jQuery('#channel_list'));

        var loader = jQuery('<div>', {'class': 'progress progress-striped active'}).append(
            jQuery('<div>', {
                'id': 'loadbar_' + ch,
                'class': 'bar',
                'data-percentage': 0
            })).appendTo(chbtn);

        return this;
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

    this.selectChannel = function (ch) {
        this.logger('- selectChannel:' + ch);
        this.currentChannel = ch;
        this._selectChannelBtn(ch);
        this._loadChannel(ch, 0);
    };

    this._selectChannelBtn = function (ch) {
        var el = jQuery('#btn_' + ch);
        el.show('fold', 1000);

        var load = jQuery('#loadbar_' + ch);
        load.attr('data-percentage', 0);
        load.css('width', 0);

        if (ch == this.currentChannel) {
            if (el.parent()) {
                // remove active class from all buttons
                el.parent().find('a').each(function () {
                        $(this).removeClass('active')
                    }
                );
            }
            el.find('a').addClass('active');
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
                dayData = this.streamList[ch][day];
                this._processDayData(ch, day, dayData);
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
        this._loaderUp(ch);
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
                    this._loaderUp(ch);
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

        for (var hr in dayList) {
            var curProg = dayList[hr];

            var data = {class: 'program'};

            for (var i in this.config.qualityUrlType) {
                var type = this.config.qualityUrlType[i];

                if (curProg[type] != '') {
                    if (!data.hasOwnProperty('data-url')) {
                        data['data-url'] = curProg[type];
                    }
                    if (data['data-url'] != curProg[type])
                        data['hd-url'] = curProg[type];
                }
            }

            var row = jQuery('<div>', data).html(hr + ' - ' + curProg.t);


            if (!data.hasOwnProperty('data-url')) {
                row.addClass('error');
            }
            else {
                row.click((function () {
                    stream._setVideo(this);
                }));
            }

            var desc = jQuery('<div>', {'class': 'description'}).html(curProg.d)
                .hide().appendTo(row);
            row.hoverIntent((function () {
                    jQuery(this).find('.description').fadeIn()
                }),
                (function () {
                    jQuery(this).find('.description').fadeOut(1200);
                })
            );
            row.appendTo(target);
        }
    };

    this.getPlayer = function () {
        return videojs(this.videoId);
    };

    this.initVideoBox = function (urls) {
        if (urls[1]) {
            this.getPlayer().updateSrc([
                {type: "video/mp4", src: urls[1], label: 'HD'}
            ]);
            this.getPlayer().updateSrc([
                {type: "video/mp4", src: urls[0], label: 'SD'}
            ]);
        }
        else {
            // only one resolution
            this.getPlayer().src([
                {type: "video/mp4", src: urls[0]}
            ]);
        }

        this.getPlayer().load();
        this.getPlayer().play();

        jQuery('#' + this.videoId).height('360');
    };

    this._setVideo = function (el) {
        var streamUrl = jQuery(el).attr('data-url');
        var streamUrlHq = jQuery(el).attr('hd-url');
        if (streamUrl == '') {
            this.logger('-- No Data Url');
        }
        this._ajaxloadVideo(streamUrl, streamUrlHq);
        //this.getPlayer().show('fold', 1000);
        this.goToByScroll(jQuery('#' + this.videoId));
    };

    this._ajaxloadVideo = function (streamUrl, streamUrlHq) {
        this.getPlayer().pause();
        jQuery.ajax({
            type: 'POST',
            url: this.config.ajaxUrl,
            dataType: 'json',
            data: {video: streamUrl, hq: streamUrlHq},
            context: this,
            success: function (data) {
                this.logger('- data-url :' + streamUrl + '  ----->  ' + data);
                this.logger('- hd-url:' + streamUrl + '  ----->  ' + data);
                this.initVideoBox(data);
            },
            error: function (data) {
                this.logger('-ERROR: _ajaxGetRedirect');
            }
        })
    };

    this._loaderUp = function (ch) {

        // Main Load Bar
        var loadBar = jQuery('#loadbar');
        var perc = parseInt(loadBar.attr('data-percentage'));
        var newVal = Math.ceil(perc + (1 / 32 * 100));

        if (newVal > 100) {
            loadBar.parent().fadeOut();
        }
        loadBar.attr('data-percentage', newVal);
        loadBar.css('width', (newVal) + '%');
        loadBar.text(newVal);

        // Ch Bar
        var loadBarCh = jQuery('#loadbar_' + ch);
        var perc = parseInt(loadBarCh.attr('data-percentage'));
        var newVal = Math.ceil(perc + (1 / 7 * 100));

        loadBarCh.attr('data-percentage', newVal);
        loadBarCh.css('width', (newVal) + '%');
        loadBarCh.text(newVal);

        if (newVal > 100) {
            loadBarCh.css('width', '0px');
            loadBarCh.attr('data-percentage', 0);
            loadBarCh.parent().hide();//removeClass('bar');
        }
    };

    this.goToByScroll = function (el) {
        // Remove 'link' from the ID
        $('html,body').animate({
                scrollTop: el.offset().top
            },
            'slow');
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