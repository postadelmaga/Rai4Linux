function setBtnActive(jEl) {

    if (jEl.parent()) {
        // remove active class from all buttons
        jEl.parent().find('button').each(function () {
                $(this).removeClass('active')
                $(this).removeClass('btn-primary')
            }
        );
    }
    jEl.addClass("active");
    jEl.addClass("btn-primary");
}

function createBlock(myHTML, cls, id) {

    // class mode
    if (id == '') {
        if ($('.' + cls).length) {
            $("." + cls).each(function () {
                $(this).append(myHTML);
            });
        }
        else {
            return  $("<div/>")
                .attr("class", cls)
                .append(myHTML);
        }
        return $("#" + id);
    }
    // id mode
    else {
        if ($("#" + id).length) {
            $("#" + id).append(myHTML);
            return $("#" + id);
        }
        else {
            return  $("<div/>")
                .attr("id", id)
                .attr("class", cls)
                .append(myHTML);
        }
    }
}


function loadStreams(ch, dayRange) {

    var html = ""
    $('#titleMenu').css('color', 'yellow');
    for (var i in dayRange) {

        date = dayRange[i];
        loadChDay(ch, date);

//        $.each(daysList.reverse(), function (key, val) {
//            html += getDayHtml(key, val);
//        });
    }
    $('#titleMenu').css('color', 'green');
    $('#program-box').show();
    $('#currentGuide').text(ch + ' - ' + date);
    $('#program-list').html(html);
}


function setDllActive(ddl, el, target) {
    var value = el.text();
    target.text(value);

    ddl.each(function () {
        $(this).removeClass('active');
    });
    el.addClass("active");
}


function getDayHtml(day, programList) {

    var html = "";
    html += '<li class="divider"></li>';
    html += '<label>' + day + '</label>';
    html += '<li class="divider"></li>';

    $.each(programList, function (hr, obj) {
//        getDayHtml(key, val);
        html += '<li><a href="' + obj.h264 + '" target="_blank">' + hr + ' - ' + obj.t + '</a></li>';
    });

    return html;
}

function loadChDay(ch, day) {
    $('#' + day + ' .loader').show();
    $('#' + day + ' table tbody').hide();
    $.ajax({
            type: "POST",
            url: 'ajax.php',
            dataType: "json",
            data: { ch: ch, day: day},

            success: function (data) {
                $('#' + day + ' .loader').hide();
                $('#' + day + ' table tbody').show();

                console.log('- StreamList: OK');
                progList = data;//getJsonDays(data);

                getDayChHml(progList, day, ch);
            },
            error: function (data) {
                console.log('- loadDay: ERROR');
                console.log(data);
            }
        }
    )
}

function getDayChHml(progList, day, ch) {

    var target = $('#' + day + ' .hrRow');
    var label = $('#' + day + ' button').text(ch + ' - ' + day);

    target.html('');

    for (var property in progList) {
        var hr;
        hr = progList[property];

        for (var time in hr) {
            var hrhtml = '';
            if (hr.hasOwnProperty(time) && hr[time].h264) {
                var prog = hr[time];
                var title = prog.t;
                var stream = prog.h264;
                var description = prog.t;

                link = '<a onclick="setVideo(this);return false;"href="' + stream + '" >' + title + ' </a>';

                hrhtml += '<tr><td>' + time + '</td><td>' + link + '</td></tr>';
                target.append(hrhtml);
            }
        }
    }
}

function updateStreams() {
    $('#titleMenu').css('color', 'yellow');
    $.ajax({
        type: "POST",
        url: 'ajax.php',
        dataType: "json",
        data: { up: 1},
        success: function (data) {
            console.log('- StreamUpdate: OK');
            console.log(data);
            $('#titleMenu').css('color', 'green');
        },
        error: function (data) {
            console.log('- StreamUpdate: ERROR');
            console.log(data);
            $('#titleMenu').css('color', 'red');

        }
    })
}


function getJsonDays(jsonObj) {
    var items = new Array();

    for (var property in jsonObj) {
        if (jsonObj.hasOwnProperty(property)) {
            items.push(jsonObj[property]);
        }
    }
    return  items[0];
}

function ajaxLoadDay(ch, day, target, stream) {

    $.ajax({
            type: "POST",
            url: 'ajax.php',
            dataType: "json",
            data: { ch: ch, day: day},

            success: function (data) {
                $('#' + day + ' .loader').hide();
                $('#' + day + ' table tbody').show();

                console.log('- StreamList: OK');

                progList = data;//getJsonDays(data);
                test = progList;
                hrhtml = getDayChHml(progList, day, ch);
                target.append(hrhtml);
                stream.weekList[ch][day] = data;
            },
            error: function (data) {
                console.log('- loadDay: ERROR');
                console.log(data);
            }
        }
    )

}