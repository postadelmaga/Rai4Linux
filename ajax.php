<?php
include_once "./includes/Core.php";

class streamAjax extends Varien_Object
{
    public function getParam()
    {

    }
}

$ch = isset($_POST['ch']) ? $_POST['ch'] : null;
$day = isset($_POST['day']) ? $_POST['day'] : null;
$update = isset($_POST['up']) ? $_POST['up'] : null;
$videoUrl = isset($_POST['sd']) ? $_POST['sd'] : null;
$videoUrlHq = isset($_POST['hq']) ? $_POST['hq'] : null;


$stream = new Stream();

if ($videoUrl) {
    $hd = null;
    $url = $stream->getVideoUrl($videoUrl);
    if ($videoUrlHq && $videoUrlHq != $videoUrl) {
        $hd = $stream->getVideoUrl($videoUrlHq);
    }

    echo json_encode(array('sd' => $url, 'hd' => $hd));
}

if ($update) {
    if ($ch && $day) {
        $json = $stream->updateDay($ch, $day, true);
        echo $json;
        return;
    } else {
        $stream->updateAllStreams();
        echo json_encode(array('Update All End'));
    }
    return;
}

// Single Day Request
if ($ch && $day) {

    $json = $stream->updateDay($ch, $day);
//    echo json_encode(array('OK'));
    echo $json;
    return;

}

if ($ch) {
    $json = json_encode($stream->getChannel($ch));
    echo $json;
}

return;