<?php
include_once "./includes/Core.php";

$ch = isset($_POST['ch']) ? $_POST['ch'] : null;
$day = isset($_POST['day']) ? $_POST['day'] : null;
$update = isset($_POST['up']) ? $_POST['up'] : null;
$videoUrl = isset($_POST['video']) ? $_POST['video'] : null;
$videoUrl = isset($_POST['video']) ? $_POST['video'] : null;
$videoUrlHq = isset($_POST['hq']) ? $_POST['hq'] : null;

$stream = new Stream();

if ($videoUrl) {
    $hd = null;
    $url = getRedirectUrl($videoUrl);
    if ($videoUrlHq && $videoUrlHq != $videoUrl) {
        $hd = getRedirectUrl($videoUrlHq);
    }

    echo json_encode(array($url, $hd));
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


function getRedirectUrl($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    $header = "Location: ";
    $pos = strpos($response, $header);
    $pos += strlen($header);
    $redirect_url = substr($response, $pos, strpos($response, "\r\n", $pos) - $pos);

    return $redirect_url;
}

return;