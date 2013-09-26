<?php include_once('./includes/Core.php'); ?>
<?php $stream = new Stream(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen"/>
    <link href="css/bootstrap-responsive.min.css" rel="stylesheet" media="screen"/>
    <link href="css/style.css" rel="stylesheet" media="screen"/>
    <link href="player_skin/jplayer.blue.monday.css" rel="stylesheet" media="screen"/>

    <!--    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>-->
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
    <script type="text/javascript" src="js/jquery.hoverIntent.minified.js"></script>
    <script type="text/javascript" src="js/jquery.jplayer.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/Stream.js"></script>
    <!--    <script src="http://vjs.zencdn.net/4.0/video.js"></script>-->
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
        <div class="jq_video_box">
            <div id="jp_container_1" class="jp-video ">
                <div class="jp-type-single">
                    <div id="jquery_jplayer_1" class="jp-jplayer"></div>
                    <div class="jp-gui">
                        <div class="jp-video-play">
                            <a href="javascript:;" class="jp-video-play-icon" tabindex="1">play</a>
                        </div>
                        <div class="jp-interface">
                            <div class="jp-progress">
                                <div class="jp-seek-bar">
                                    <div class="jp-play-bar"></div>
                                </div>
                            </div>
                            <div class="jp-current-time"></div>
                            <div class="jp-duration"></div>
                            <div class="jp-controls-holder">
                                <ul class="jp-controls">
                                    <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
                                    <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
                                    <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
                                    <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
                                    <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a>
                                    </li>
                                    <li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max
                                            volume</a></li>
                                </ul>
                                <div class="jp-volume-bar">
                                    <div class="jp-volume-bar-value"></div>
                                </div>
                                <ul class="jp-toggles">
                                    <li><a href="javascript:;" class="jp-full-screen" tabindex="1" title="full screen">full
                                            screen</a></li>
                                    <li><a href="javascript:;" class="jp-restore-screen" tabindex="1"
                                           title="restore screen">restore screen</a></li>
                                    <li><a href="javascript:;" class="jp-repeat" tabindex="1" title="repeat">repeat</a>
                                    </li>
                                    <li><a href="javascript:;" class="jp-repeat-off" tabindex="1" title="repeat off">repeat
                                            off</a></li>
                                </ul>
                            </div>
                            <div class="jp-title">
                                <ul>
                                    <li>Big Buck Bunny Trailer</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="jp-no-solution">
                        <span>Update Required</span>
                        To play the media you will need to either update your browser to a recent version or update your
                        <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
                    </div>
                </div>
            </div>
        </div>
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

<footer>
    <div class="shiny">
        <script type="text/javascript"
                src="http://codice.shinystat.com/cgi-bin/getcod.cgi?USER=postadelmaga"></script>
    </div>
    <!-- ShinyStat -->
</footer>

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

