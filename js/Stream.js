function Stream(classname, channelList, dayRange, ajaxUrl) {

    this.element = jQuery('.' + classname);
    this.weekBox = '';
    this.chlBox = '';
    this.videoBox = '';

    this.channelList = channelList;
    this.dayRange = dayRange;
    this.ajaxUrl = ajaxUrl;

    this.streemList = new Array();

    this.currentChannel = channelList[0];
    this.currentVideo = "";
    this.showloader = true;

    this.width = 940;
    this.height = 530;

    this.init = function () {
        this.logger('init');
//        this.logger(this.element);
        for (var i in this.channelList) {
            this.createChannelBtn(this.channelList[i]);
        }
        this.initVideoBox();
    };

    this.preload = function () {

        this.logger('preload');
        for (var i in this.channelList) {
            var chUp = this.channelList[i];
            if (chUp != this.currentChannel) {
                this._loadChannel(chUp, 0);
            }
        }
    }

    this.selectChannel = function (ch) {
        this.logger('selectChannel:' + ch);
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

        // load.parent().show('fold', 500);

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
            this._ajaxDataDay(ch, day, this._processDayData, 1);
        }
        else {
            if (this.streemList[ch] && this.streemList[ch][day]) {
                dayData = this.streemList[ch][day];
                this._processDayData(ch, day, dayData);
            }
            else {
                this._ajaxDataDay(ch, day, this._processDayData, 0);
            }
        }
    }

    this._processDayData = function (ch, day, data) {

        // add Day Data
        if (!this.streemList[ch])
            this.streemList[ch] = new Array();
        if (day && data)
            this.streemList[ch][day] = data;

        if (ch == this.currentChannel) {
            this._setDayHtml(day, ch);

        }
        this._loaderUp(ch);
    }

    this._ajaxDataDay = function (ch, day, callback, update) {

        var data = { ch: ch, day: day};
        if (update)
            data = { up: 1, ch: ch, day: day};

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
        target.show();
        jQuery('#' + day + ' .loader').hide();
        stream = this;

        var dayList = this.streemList[ch][day];

        for (var property in dayList) {
            var hr;
            hr = dayList[property];

            for (var time in hr) {

                if (hr.hasOwnProperty(time) && hr[time].h264) {
                    var prog = hr[time];
                    var title = prog.t;
                    var streamUrl = prog.h264;
                    var description = prog.t;

                    var row = jQuery("<div>", {"class": "program"})
                        .html(time + ' - ' + title)
                        .click((function () {
                            stream._setVideo(streamUrl);
                        }));

                    var desc = jQuery('<div>', {'class': 'description'}).html(description);
                    desc.hide();

                    row.append(desc);
                    row.appendTo(target);
//                    row.hover(alert('ss'));
                }
            }
        }
    }

    this._setVideo = function (streamUrl) {
        this.videoBox.show();
        this.logger('_setVideo');
        this.videoBox.find('video').stop();
        this._ajaxloadVideo(streamUrl);

    }

    this.createChannelBtn = function (ch) {

        var stream = this;

        var chbtn = jQuery("<li>", {"id": 'btn_' + ch})
            .append(jQuery('<a>', {'class': 'btn btn-primary btn-danger btn-large', 'href': '#'}).text(ch));

        chbtn.click(function () {
            stream.selectChannel(ch);
        })

        var load = jQuery("<div>", {'class': 'progress progress-striped active'}).append(
            jQuery("<div>", {
                "id": 'loadbar_' + ch,
                "class": 'bar',
                'data-percentage': 0
            }));

        chbtn.append(load);
//            chbtn.hide();
        jQuery('#channel_list').append(chbtn)
//        jQuery('#programs').prepend(chbtn);

        return this;
    }

    this.initVideoBox = function (url) {

        var video_id = "jq_videoplayer";

        //second time show
        if (jQuery('.jq_current_video').length) {
            this.videoBox = jQuery('.jq_current_video');
            if (url) {
                this.logger('set video:' + url);
                this.videoBox.find('source').attr('src', url);
                this.videoBox.find('video').load();
            }
            this.videoBox.show('fold', 1000);
        }
        else {
            this.videoBox = jQuery("<div>", {"class": "jq_current_video"});

            var video = jQuery("<video>", {
                "id": video_id,
                "preload": 'none',
                "name": "media",
                "width": this.width,
                "height": this.height,
                controls: "",
                autoplay: ""
            });
            var source = jQuery("<source>", { 'type': 'video/mp4' });

            video.append(source);
            this.videoBox.append(video);
            this.videoBox.hide();
            this.element.prepend(this.videoBox);
        }
        return this;
    }

    this._ajaxloadVideo = function (streamUrl) {
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

    this.logger = function (msg) {
        console.log(Stream.logCounter + ': ' + msg);
        Stream.logCounter = Stream.logCounter + 1;
    }
    this.init();


    this.updateStreams = function (stream) {
        jQuery('#titleMenu').css('color', 'yellow');
        jQuery.ajax({
            type: "POST",
            url: this.ajaxUrl,
            dataType: "json",
            data: { up: 1},
            success: function (data) {
                stream.log('- StreamUpdate: OK');
                stream.log(data);
                jQuery('#titleMenu').css('color', 'green');
                stream.init();
            },
            error: function (data) {
                stream.log('- StreamUpdate: ERROR');
                stream.log(data);
                jQuery('#titleMenu').css('color', 'red');

            }
        })
    }
}