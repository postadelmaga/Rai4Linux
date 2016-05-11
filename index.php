<?php include_once('./includes/Core.php'); ?>
<?php
$stream = new Stream();
//$setFlash = false;
//if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') === false) {
//    $setFlash = true;
//}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="css/bootstrap/bootstrap.min.css" rel="stylesheet" media="screen"/>
    <link href="css/bootstrap/bootstrap-responsive.min.css" rel="stylesheet" media="screen"/>
    <link href="css/style.css" rel="stylesheet" media="screen"/>

    <script type="text/javascript" src="js/jquery/jquery-2.0.3.min.js"></script>
<!--    <script src="js/jquery/jquery-ui-1.10.3.custom.min.js"></script>-->
    <script src="js/jquery/jquery.hoverIntent.minified.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>

    <script src="js/stream.js"></script>
    <link href="js/player/video-js.css" rel="stylesheet">
    <link href="js/player/plugin/videojs-resolution-switcher.css" rel="stylesheet">
    <script src="js/player/video.js"></script>
    <script src="js/player/plugin/videojs-resolution-switcher.js"></script>
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

    <div id="video_box">
        <video id="my-video" class="video-js vjs-default-skin vjs-big-play-centered"
               controls preload="auto" width="640" height="264"
               poster="img/tvbroken.jpg">
            <p class="vjs-no-js">
                To view this video please enable JavaScript, and consider upgrading to a web browser that
                <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>
        </video>
    </div>

    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav-pills" id="channel_list"></ul>
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
    <?php include('blocks/comment.php') ?>
</div>


<script>
    videojs.options.flash.swf = "js/player/plugin/video-js.swf"
</script>
<script type="text/javascript">
    videojs('my-video').videoJsResolutionSwitcher();

    Stream.logCounter = 0;
    var stream = new Stream('my-video', <?php echo json_encode($stream->getJsonConfig()); ?>);


    $('#titleMenu').bind('contextmenu', function (e) {
        if (confirm('Vuoi ripopolare la lista per ' + stream.currentChannel + '?')) {
            stream._loadChannel(stream.currentChannel, 1);
        }
        return false;
    });
</script>
</body>
</html>

