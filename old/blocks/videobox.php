<div id="tv_box">
    <div id="tv_title"><span>RAI4Linux</span></div>
    <video id="my-video" class="video-js vjs-default-skin vjs-big-play-centered"
           controls preload="auto" width="930" height="522"
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

<div id="program_box" class="navbar navbar-inverse">
    <?php $i = 0; ?>
    <?php foreach ($stream->getDayRange() as $day): ?>
        <?php if ($i % 4 == 0): ?>
            <div class="row">
        <?php endif; ?>
        <div class="day col-xs-3" id="<?php echo $day ?>">
            <button class="btn-primary btn active">
                <label><?php echo $day ?><label>
            </button>
            <div class="loader"></div>
            <div class="program_list"></div>
        </div>

        <?php if ($i % 4 == 3 || $i == 6): ?>
            </div>
        <?php endif; ?>
        <?php $i++; ?>
    <?php endforeach; ?>
</div>

<script type="text/javascript">
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