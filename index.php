<?php include_once('./includes/Core.php'); ?>
<?php $stream = new Stream(); ?>
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
    <script src="js/player/plugin/videojs.hotkeys.js"></script>
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
<?php include('blocks/header.php') ?>

<div class="container">
    <?php include('blocks/videobox.php') ?>
    <?php include('blocks/comment.php') ?>
</div>

</body>
</html>

