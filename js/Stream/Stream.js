function Stream(classname, channelList, dayRange, ajaxUrl) {

    this.element = jQuery('.' + classname);
    this.weekBox = '';
    this.chBox = '';
    this.videoBox = '';
    this.videoel = '';

    this.channelList = channelList;
    this.dayRange = dayRange;
    this.ajaxUrl = ajaxUrl;

    this.streamList = new Array();

    this.currentChannel = channelList[0];
    this.currentVideo = "";
    this.showloader = true;

    this.width = 940;
    this.height = 530;
    this.setFlash = false;

    this.init = function () {
        this.logger('- Init() Stream obj');
        for (var i in this.channelList) {
            this.initChannelBtn(this.channelList[i]);
        }
        this.initVideoBox();
    };

    this.preload = function () {

        this.logger('- preload() other channel List');
        for (var i in this.channelList) {
            var chUp = this.channelList[i];
            if (chUp != this.currentChannel) {
                this._loadChannel(chUp, 0);
            }
        }
    }

    this.setFlash = function () {

        this.setFlash = true;
    }

    this.selectChannel = function (ch) {
        this.logger('- selectChannel:' + ch);
        this.currentChannel = ch;

        this._selectChannelBtn(ch);
        this._loadChannel(ch, 0);
    }

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
            el.find('a').addClass("active");
        }
    }

    this._loadChannel = function (ch, update) {

        if (update) {
            this.streamList[ch] = new Array();
            jQuery('#channel_list').find('#btn_' + ch + ' .progress').show();
        }

        for (var i in  this.dayRange) {
            jQuery('label#load-' + ch).text("0");
            jQuery('label#load').text("0");
            this._getDayData(ch, this.dayRange[i], update);
            var target = jQuery('label#' + ch).css('color', 'yellow');
        }
    }

    this._getDayData = function (ch, day, update) {
        this.logger('_getDayData(' + ch + ',' + day + ',' + update + ')');

        // Problemi con prototype
        var target = jQuery("#" + day);

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
    }

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
    }

    this._ajaxDataDay = function (ch, day, update) {

        var callback = this._processDayData;
        var data = { ch: ch, day: day};

        if (update)
            data = { up: 1, ch: ch, day: day };

        jQuery.ajax({
                type: "POST",
                url: this.ajaxUrl,
                dataType: "json",
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
    }

    this._setDayHtml = function (day, ch) {

        var target = jQuery("#" + day + ' .program_list');
        target.fadeIn();
        jQuery('#' + day + ' .loader').hide();
        stream = this;

        var dayList = this.streamList[ch][day];

        for (var hr in dayList) {

            var curProg = dayList[hr];

            var row = jQuery("<div>",
                {"class": "program",
                    "data-url": curProg.h264,
                    "data-url_400": curProg.h264_400,
                    "data-url_600": curProg.h264_600,
                    "data-url_800": curProg.h264_800,
                    "data-url_1200": curProg.h264_1200,
                    "data-url_1500": curProg.h264_1500,
                    "data-url_1800": curProg.h264_1800
                })
                .html(hr + ' - ' + curProg.t)
                .click((function () {
                    stream._setVideo(this);
                }));

            if (curProg.h264 == '') {
                row.addClass('error');
//                row.attr('data-url',curProg.urlrisorsatagli);
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
//            row.append(desc);
            row.appendTo(target);
        }
    }

    this.initChannelBtn = function (ch) {

        var stream = this;

        var chbtn = jQuery("<li>", {"id": 'btn_' + ch})
            .append(jQuery('<a>', {'class': 'btn btn-primary btn-danger btn-large', 'href': '#'}).text(ch))
            .click(function () {
                stream.selectChannel(ch);
            }).appendTo(jQuery('#channel_list'));

        var loader = jQuery("<div>", {'class': 'progress progress-striped active'}).append(
            jQuery("<div>", {
                "id": 'loadbar_' + ch,
                "class": 'bar',
                'data-percentage': 0
            })).appendTo(chbtn);

        return this;
    }

    this.initVideoBox = function (url) {

        //second time show it
        if (this.videoBox.length) {
            this.videoBox = jQuery('#video_tv');
//            this.videoBox = this.videoel.parent();

            if (url) {
                if (this.setFlash == true) {
                    //                jwplayer("video_tv")
                    jwplayer().load([
                        {file: url}
                    ]);
                    jwplayer().play();
                }
                else {
//                this.videoBox.find('video').attr('src', url);
                    this.videoBox.pause();
                    this.videoBox.attr('src', url);
                    this.videoBox.get(0).load();
                    this.videoBox.get(0).play();
//                html5media();
                }
                jQuery('#video_tv').height('500');
            }
        }
        else {
            // first time creates the block
//                this.videoBox = jQuery('#video_tv').hide();
            this.videoBox = jQuery("<div>", {"class": "video_tv"});

//            this.videoel = jQuery("<video>", {
//                "id": video_id,
//                "preload": 'none',
//                "name": "media",
//                "width": this.width,
//                "height": this.height,
//                controls: "",
//                autoplay: ""
//            }).appendTo(this.videoBox);

//            var source = jQuery("<source>", { 'type': 'video/mp4' }).appendTo(this.videoel);
//            this.videoBox.click(function () {
//                jQuery('#video_tv').pause();
//            });
            // prepend it to the base element
//            this.element.prepend(this.videoBox);
        }
        return this;
    }

    this._setVideo = function (el) {
        streamUrl = jQuery(el).attr('data-url');
        this.videoBox.show('fold', 1000);
        this.goToByScroll(this.videoBox);
        this._ajaxloadVideo(streamUrl);
    }

    this._ajaxloadVideo = function (streamUrl) {
        this.videoBox.find('video').stop();
        jQuery.ajax({
            type: "POST",
            url: this.ajaxUrl,
            dataType: "json",
            data: { video: streamUrl},
            context: this,
            success: function (data) {
                this.logger('-_ajaxloadVideo :' + streamUrl + '  ----->  ' + data);
                this.initVideoBox(data);
            },
            error: function (data) {
                this.logger('-ERROR: _ajaxGetRedirect');
            }
        })
    }

    this._loaderUp = function (ch) {

        // Main Load Bar
        var loadBar = jQuery('#loadbar');
        var perc = parseInt(loadBar.attr("data-percentage"));
        var newVal = Math.ceil(perc + (1 / 32 * 100));

        if (newVal > 100) {
            loadBar.parent().fadeOut();
        }
        loadBar.attr("data-percentage", newVal);
        loadBar.css('width', (newVal) + '%');
        loadBar.text(newVal);

        // Ch Bar
        var loadBarCh = jQuery('#loadbar_' + ch);
        var perc = parseInt(loadBarCh.attr("data-percentage"));
        var newVal = Math.ceil(perc + (1 / 7 * 100));

        loadBarCh.attr("data-percentage", newVal);
        loadBarCh.css('width', (newVal) + '%');
        loadBarCh.text(newVal);

        if (newVal > 100) {
            loadBarCh.css('width', '0px');
            loadBarCh.attr("data-percentage", 0);
            loadBarCh.parent().hide();//removeClass('bar');
        }
    }

    this.updateStreams = function (stream) {
        jQuery('#titleMenu').css('color', 'yellow');
        jQuery.ajax({
            type: "POST",
            url: this.ajaxUrl,
            dataType: "json",
            data: { up: 1},
            success: function (data) {
                stream.logger('- StreamUpdate: OK');
                stream.logger(data);
                jQuery('#titleMenu').css('color', 'green');
                stream.init();
            },
            error: function (data) {
                stream.logger('- StreamUpdate: ERROR');
                stream.logger(data);
                jQuery('#titleMenu').css('color', 'red');
            }
        })
    }

    this.goToByScroll = function (el) {
        // Remove "link" from the ID
        $('html,body').animate({
                scrollTop: el.offset().top},
            'slow');
    }

    this.logger = function (msg) {
        console.log(Stream.logCounter + ': ' + msg);
        Stream.logCounter = Stream.logCounter + 1;
    }

    // Init
    this.init();
}