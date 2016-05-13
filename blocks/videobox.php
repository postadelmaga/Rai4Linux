<div id="video_box">
    <video id="my-video" class="video-js vjs-default-skin vjs-big-play-centered"
           controls preload="auto" width="930" height="524"
           poster="img/tvbroken.jpg">
        <p class="vjs-no-js">
            To view this video please enable JavaScript, and consider upgrading to a web browser that
            <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
        </p>
    </video>
    <div class="channel-wrapper">
        <ul class="nav-pills" id="channel_list"></ul>
    </div>
</div>

<div class="navbar navbar-inverse">
    <div class="navbar-inner">
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
                <?php endif; ?>

                <?php $i++; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<script type="text/javascript">
    videojs.options.flash.swf = "js/player/plugin/video-js.swf"
    videojs('my-video').ready(function () {
        this.hotkeys({
            volumeStep: 0.1,
            seekStep: 5
        });
    });
    videojs('my-video').videoJsResolutionSwitcher();
    Stream.logCounter = 0;
    var stream = new Stream('my-video', <?php echo json_encode($stream->getJsonConfig()); ?>);
</script>