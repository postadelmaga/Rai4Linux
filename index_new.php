<?php
include_once('./includes/Core.php');
$stream = new Stream();
?>
<!DOCTYPE html>
<html>
<head>
    <?php include('app/blocks/head.phtml') ?>
    <script src="js/rai_player.js?v=1"></script>
    <script src="js/loader.js?v=1"></script>
</head>

<body>
<div class="w3-container w3-amber">
    <h2></h2>
</div>

<div class="w3-container channel">
    <div style="margin: 0% 12%">
        <!--        <img src="img/tv.png" alt="Rai Tv" style="max-width: 100%">-->
        <video id="videoElement" style="max-width: 100%" src=""/>
    </div>
</div>

<div class="w3-bar w3-mobile w3-border w3-round channel_list"></div>

<div class="w3-container channel-wrapper w3-black">
    <p id="description">Descrizione Contenuto</p>
    <p id="extra">Extra Contenuto</p>
</div>


<?php $days = $stream->getDayRange(); ?>
<?php include('app/blocks/week.phtml') ?>

<script type="text/javascript">
    Stream.logCounter = 0;
    var stream = new Stream("videoElement", '<?php echo $stream->getJsonConfig(); ?>');

</script>

</body>
</html>