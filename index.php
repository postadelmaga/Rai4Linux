<?php include_once('./includes/Core.php'); ?>
<?php $stream = new Stream(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Rai4Linux</title>

    <!-- Bootstrap -->
    <link href="style/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="style/style.css?v=0" rel="stylesheet" media="screen"/>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script type="text/javascript" src="js/jquery/jquery-2.0.3.min.js"></script>
    <!--    <script src="js/jquery/jquery-ui-1.10.3.custom.min.js"></script>-->
    <script src="js/jquery/jquery.hoverIntent.minified.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>

    <script src="js/stream.js?v=1"></script>
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
    <?php include('blocks/chat.php') ?>
</div>

</body>
</html>

