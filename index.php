<?php include_once('./includes/Core.php'); ?>
<?php $stream = new Stream(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="css/bootstrap/bootstrap.min.css" rel="stylesheet" media="screen"/>
    <link href="css/bootstrap/bootstrap-responsive.min.css" rel="stylesheet" media="screen"/>
    <link href="css/style.css" rel="stylesheet" media="screen"/>

    <!--    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>-->
    <script type="text/javascript" src="js/jquery/jquery-2.0.3.min.js"></script>
    <script src="js/jquery/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="js/jquery/jquery.hoverIntent.minified.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/Stream/Stream.js"></script>
    <!--    <script src="js/html5media.js"></script>-->
    <!--    <link href="http://vjs.zencdn.net/4.1/video-js.css" rel="stylesheet">-->
    <!--    <script src="http://vjs.zencdn.net/4.1/video.js"></script>-->
    <!--    <script src="http://api.html5media.info/1.1.5/html5media.min.js"></script>-->
    <!--        <script src="js/JW/jwplayer.js"></script>-->
    <script src="js/JW/jwplayer.js"></script>
    <!--        <script src="http://api.html5media.info/1.1.5/html5media.min.js"></script>-->

    <link rel="Shortcut Icon" href="http://www.rai.tv/dl/RaiTV/images/favicon.gif">
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
                   controls preload="auto" width="640" height="480"
                   poster="img/tvbroken.jpg"
                   data-setup='{"example_option":true}'></video>
        </div>
        <script>
            jwplayer("video_tv").setup({
                image: "img/tvbroken.jpg",
                file: "http://creativemedia3.rai.it/podcastcdn/Rai/TivuON/1845360_1800.mp4",
                title: "My Cool Trailer"
            });
        </script>

        <div class="navbar-inner">
            <ul class="nav-pills" id="channel_list">
            </ul>


            <div id="programs" class="">
                <?php $i = 0; ?>
                <?php foreach ($stream->getDaysRange() as $day): ?>

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

</div>
</div>

<?php if ($_SERVER['HTTP_HOST'] != 'localhost'): ?>
    <footer>
        <div class="shiny">
            <script type="text/javascript"
                    src="http://codice.shinystat.com/cgi-bin/getcod.cgi?USER=postadelmaga"></script>
        </div>
        <!-- ShinyStat -->
    </footer>
<?php endif; ?>
<script type="text/javascript">
    //    $(document).ready(function () {
    // initialize with default options
    var daysRange = <?php echo json_encode($stream->getDaysRange()); ?>;
    var channelList = <?php echo json_encode($stream->getChannelList()); ?>;
    console.log(daysRange);
    console.log(channelList);

    Stream.logCounter = 0;
    var stream = new Stream('jq_video_box', channelList, daysRange, "ajax.php");

    stream.selectChannel(channelList[0]);
    stream.preload();

    $('#titleMenu').bind('contextmenu', function (e) {

        if (confirm('Vuoi ripopolare la lista per ' + stream.currentChannel + '?')) {
            stream._loadChannel(stream.currentChannel, 1);
        }
        return false;
    });

    //    });

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

