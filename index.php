<?php include_once('./includes/Core.php'); ?>
<?php
$stream = new Stream();
$setFlash = false;
if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') === false) {
    $setFlash = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="css/bootstrap/bootstrap.min.css" rel="stylesheet" media="screen"/>
    <link href="css/bootstrap/bootstrap-responsive.min.css" rel="stylesheet" media="screen"/>
    <link href="css/style.css" rel="stylesheet" media="screen"/>

    <script type="text/javascript" src="js/jquery/jquery-2.0.3.min.js"></script>
    <script src="js/jquery/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="js/jquery/jquery.hoverIntent.minified.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/stream.js"></script>
    <link rel="Shortcut Icon" href="http://www.rai.it/dl/RaiTV/images/favicon.gif">
    <style>
        #HCB_comment_box .submit {
            background: none; /* Clear twitter bootstrap style. */
            background-color: green;
            border: 1px solid darkgreen;
            color: white;
        }
    </style>
</head>

<body>
<div class="jumbotron subhead">
    <h1 id="titleMenu"> Rai Mobile </h1>
    <!-- ShinyStat -->
    <div class="progress progress-striped active" id="main_loader">
        <div data-percentage="0" class="bar" id="loadbar" style="width: 0%;"></div>
    </div>
</div>

<!--<div class="clearfix"></div>-->
<div class="container">

    <div class="navbar navbar-inverse">
        <div id="video_box">
            <video id="video_tv" class="video-js vjs-default-skin"
                   controls preload="auto" height="200px"
                   poster="img/tvbroken.jpg"
                   data-setup='{"example_option":true}'></video>
        </div>
        <!--        --><?php //if ($setFlash): ?>
        <!--            <script>-->
        <!--                jwplayer("video_tv").setup({-->
        <!--                    image: "img/tvbroken.jpg",-->
        <!--                    file: "http://creativemedia3.rai.it/podcastcdn/Rai/TivuON/1845360_1800.mp4",-->
        <!--                    title: "My Cool Trailer"-->
        <!--                });-->
        <!--            </script>-->
        <!--        --><?php //endif ?>


        <div class="navbar-inner">
            <ul class="nav-pills" id="channel_list">
            </ul>


            <div id="programs" class="">
                <?php $i = 0; ?>
                <?php foreach ($stream->getDayRange() as $day): ?>

                    <?php if ($i % 4 == 0): ?>
                        <div class="row-fluid">
                    <?php endif; ?>

                    <div class="day" id="<?php echo $day ?>">
                        <button class="btn-primary btn active">
                            <label><?php echo $day ?><label>
                        </button>
                        <div class="loader"></div>
                        <div class="program_list">
                        </div>
                    </div>

                    <?php if ($i % 4 == 3): ?>
                        </div>
                        <!--                                <div class='clearfix'></div>-->
                    <?php endif; ?>

                    <?php $i++; ?>
                <?php endforeach; ?>

            </div>
        </div>
    </div>

    <?php if ($_SERVER['HTTP_HOST'] != 'localhost'): ?>
        <footer>
            <div class="shiny">
                <script type="text/javascript"
                        src="http://codice.shinystat.com/cgi-bin/getcod.cgi?USER=postadelmaga"></script>
                <script>
                    (function (i, s, o, g, r, a, m) {
                        i['GoogleAnalyticsObject'] = r;
                        i[r] = i[r] || function () {
                                (i[r].q = i[r].q || []).push(arguments)
                            }, i[r].l = 1 * new Date();
                        a = s.createElement(o),
                            m = s.getElementsByTagName(o)[0];
                        a.async = 1;
                        a.src = g;
                        m.parentNode.insertBefore(a, m)
                    })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

                    ga('create', 'UA-8551093-3', 'byethost7.com');
                    ga('send', 'pageview');

                </script>
                <!-- begin htmlcommentbox.com -->
                <div id="HCB_comment_box"><a href="http://www.htmlcommentbox.com">Comment Form</a> is loading
                    comments...
                </div>
                <link rel="stylesheet" type="text/css"
                      href="//www.htmlcommentbox.com/static/skins/bootstrap/twitter-bootstrap.css?v=0"/>
                <script type="text/javascript" id="hcb"> /*<!--*/
                    if (!window.hcb_user) {
                        hcb_user = {};
                    }
                    (function () {
                        var s = document.createElement("script"), l = (hcb_user.PAGE || "" + window.location), h = "//www.htmlcommentbox.com";
                        s.setAttribute("type", "text/javascript");
                        s.setAttribute("src", h + "/jread?page=" + encodeURIComponent(l).replace("+", "%2B") + "&opts=16862&num=10");
                        if (typeof s != "undefined") document.getElementsByTagName("head")[0].appendChild(s);
                    })();
                    /*-->*/ </script>
                <!-- end htmlcommentbox.com -->
            </div>
            <!-- ShinyStat -->
        </footer>
    <?php endif; ?>
</div>


<script type="text/javascript">
    //    $(document).ready(function () {
    // initialize with default options
    Stream.logCounter = 0;
    var stream = new Stream('jq_video_box',  <?php echo json_encode($stream->getJsonConfig()); ?>);


    <?php if ($setFlash): ?>
    stream.setFlash();
    <?php endif ?>


    $('#titleMenu').bind('contextmenu', function (e) {

        if (confirm('Vuoi ripopolare la lista per ' + stream.currentChannel + '?')) {
            stream._loadChannel(stream.currentChannel, 1);
        }
        return false;
    });


    var myVideo = document.getElementById("video_1");

    function playPause() {
        myVideo.load();
        if (myVideo.paused)
            myVideo.play();
        else
            myVideo.pause();
    }
</script>

</body>
</html>

