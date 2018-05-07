<?php
include_once('./includes/Core.php');
$stream = new Stream();
?>
<!DOCTYPE html>
<html>

<head>
    <title>W3.CSS Template</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/w3.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Karma">
    <style>
        body, h1, h2, h3, h4, h5, h6 {
            font-family: "Karma", sans-serif
        }

        .w3-bar-block .w3-bar-item {
            padding: 20px
        }
    </style>

    <script src="js/jquery/jquery-2.0.3.min.js"></script>
    <script src="js/player_new/build/mediaelement-and-player.js"></script>
    <script src="js/rai_player.js?v=1"></script>

    <!-- Add any other renderers you need; see Use Renderers for more information -->
    <link rel="stylesheet" href="js/player_new/src/css/mediaelementplayer.css"/>

</head>

<body>

<div class="w3-container w3-black">
    <h1>Rai Free</h1>
</div>

<div class="w3-container w3-black">
    <div style="margin: 0% 12%">
        <!--    <img src="img/tv.png" alt="Rai Tv" style="width:100%">-->
        <video style="max-width: 100%"
               src="http://creativemedia2-rai-it.akamaized.net/podcastmhp_world/replaytv_world/raiuno_world/notte_world/8776111_1800.mp4"
               id="videoElement"/>

    </div>
</div>

<div class="w3-container channel-wrapper w3-black">
    <p id="description">Descrizione Contenuto</p>
    <p id="extra">Extra Contenuto</p>
</div>


<div class="w3-bar w3-border w3-round channel_list"></div>

<?php
$days = $stream->getDayRange();

foreach ($days as $day): ?>
    <div class="w3-card w3-cell-row" id="<?php echo $day ?>"">
        <header class="w3-container w3-blue">
            <h1><?php echo $day ?></h1>
            <div class="loader"></div>
        </header>
        <ul class="w3-ul program_list"></ul>
    </div>

    <?php $i++; ?>
    <!--    --><?php //if (count($days) == $i || ($i > 0 && $i % 3 == 0)): ?>
    <!--        </div>-->
    <!--    --><?php //endif; ?>

<?php endforeach; ?>


<script>
    // Script to open and close sidebar
    function w3_open() {
        document.getElementById("mySidebar").style.display = "block";
    }

    function w3_close() {
        document.getElementById("mySidebar").style.display = "none";
    }
</script>
<script type="text/javascript">
    Stream.logCounter = 0;
    var stream = new Stream('videoElement', <?php echo json_encode($stream->getJsonConfig()); ?>);
</script>

</body>

</html>