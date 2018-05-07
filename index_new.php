<?php
define('ROOT', getcwd());

include_once('./includes/Core.php');
$stream = new Stream();

?>
<!DOCTYPE html>
<html>
<?php include('app/blocks/head.phtml') ?>

<body>
<div class="w3-container w3-black">
    <h1 id="page_title"></h1>
</div>

<div class="w3-container w3-black">
    <div style="margin: 0% 12%">
        <!--    <img src="img/tv.png" alt="Rai Tv" style="width:100%">-->
        <video style="max-width: 100%"
               src=""
               id="videoElement"/>
    </div>
</div>

<div class="w3-bar w3-mobile w3-border w3-round channel_list"></div>


<div class="w3-container channel-wrapper w3-black">
    <p id="description">Descrizione Contenuto</p>
    <p id="extra">Extra Contenuto</p>
</div>

<div class="w3-container w3-black">
    <?php $days = $stream->getDayRange(); ?>
    <?php include('app/blocks/week.phtml') ?>
</div>

<script type="text/javascript">
    Stream.logCounter = 0;
    var stream = new Stream('videoElement', <?php echo json_encode($stream->getJsonConfig()); ?>);
</script>


<!--<script>-->
<!--    // Script to open and close sidebar-->
<!--    function w3_open() {-->
<!--        document.getElementById("mySidebar").style.display = "block";-->
<!--    }-->
<!---->
<!--    function w3_close() {-->
<!--        document.getElementById("mySidebar").style.display = "none";-->
<!--    }-->
<!--</script>-->


</body>
</html>