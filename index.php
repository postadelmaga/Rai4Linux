<?php include_once('./includes/Core.php'); ?>
<?php $stream = new Stream(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen"/>
    <link href="css/bootstrap-responsive.min.css" rel="stylesheet" media="screen"/>
    <link href="css/style.css" rel="stylesheet" media="screen"/>

    <!--    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>-->
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script src="js/jquery-ui-1.10.3.custom.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/functions.js"></script>
    <script src="js/Stream.js"></script>
    <!--    <script src="http://vjs.zencdn.net/4.0/video.js"></script>-->
    <link rel="Shortcut Icon" href="http://www.rai.tv/dl/RaiTV/images/favicon.gif">
</head>

<body>
<div class="jumbotron subhead">
    <h1 id="titleMenu"> Rai Mobile </h1>
    <!-- ShinyStat -->
    <div class="progress progress-striped active">
        <div data-percentage="0" class="bar" id="loadbar" style="width: 0%;"></div>
    </div>
</div>

<!--<div class="clearfix"></div>-->
<div class="container">
    <div class="navbar navbar-inverse">
        <div class="jq_video_box"></div>
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

        var currentCh = stream.currentChannel;
        if (confirm('sicuro ?')) {
            ch = stream.currentChannel;
            stream.streemList[ch] = new Array();
            for (var i in stream.dayRange) {
                day = stream.dayRange[i];
                stream._loadChannel(ch, 1);
            }
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

