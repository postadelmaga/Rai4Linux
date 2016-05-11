<?php include_once('./includes/Core.php');

echo "today:" . date("Y-d-m", mktime(0, 0, 0, date("m"), date("d"), date("Y"))) . "<br>";
$stream = new Stream();
//echo $stream->updateAllStreams();
echo $stream->debug();
