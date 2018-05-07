<?php
include_once('./includes/Core.php');
$stream = new Stream();
?>
<!DOCTYPE html>
<html>
<?php include('app/blocks/head.phtml') ?>

<body>
<div id="app">
    <div class="w3-container w3-amber">
        <h2>{{ current_channel }}</h2>
    </div>
</div>

<div class="w3-container channel">
    <div style="margin: 0% 12%">
        <!--        <img src="img/tv.png" alt="Rai Tv" style="max-width: 100%">-->
        <video id="videoElement" style="max-width: 100%" src=""/>
    </div>
</div>

<div class="w3-bar w3-mobile w3-border w3-round channel_list"></div>

<div id="app-7">
    <!--
      Now we provide each todo-item with the todo object
      it's representing, so that its content can be dynamic.
      We also need to provide each component with a "key",
      which will be explained later.
    -->
    <my-component v-for="ch in channels"></my-component>
</div>


<div class="w3-container channel-wrapper w3-black">
    <p id="description">Descrizione Contenuto</p>
    <p id="extra">Extra Contenuto</p>
</div>


<?php $days = $stream->getDayRange(); ?>
<?php include('app/blocks/week.phtml') ?>

<script type="text/javascript">
    Stream.logCounter = 0;
    var stream = new Stream("videoElement", '<?php echo $stream->getJsonConfig(); ?>');


    var app7 = new Vue({
        el: '#app-7',
        data: {
            channels: [
                {id: 0, text: 'Vegetables'},
                {id: 1, text: 'Cheese'},
                {id: 2, text: 'Whatever else humans are supposed to eat'}
            ]
        }
    });

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