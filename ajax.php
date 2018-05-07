<?php
$ch = isset($_POST['ch']) ? $_POST['ch'] : null;
$day = isset($_POST['day']) ? $_POST['day'] : null;

$update = isset($_POST['up']) ? $_POST['up'] : null;
$videoUrl = isset($_POST['sd']) ? $_POST['sd'] : null;
$videoUrlHq = isset($_POST['hq']) ? $_POST['hq'] : null;

include_once "./includes/Core.php";

class Request extends Varien_Object
{
    public function __construct(array $data = array())
    {
        self::__construct($data);
    }
}

$request = new Request($_POST);
$stream = new Stream();


if ($videoUrl = $request->getData('sd')) {
    $url = $stream->getVideoUrl($videoUrl);
    if ($videoUrlHq && $videoUrlHq != $videoUrl) {
        $hd = $stream->getVideoUrl($videoUrlHq);
    }
    echo json_encode(array('sd' => $url, 'hd' => $hd));
}


$ch = $request->getData('ch');
$day = $request->getData('day');

if ($request->getData('up')) {
    if ($ch && $day) {
        $json = $stream->updateDay($ch, $day, true);

    } else {
        $stream->updateAllStreams();
        $json = json_encode(array('Update All End'));
    }
} elseif ($ch) {
    $json = json_encode($stream->getChannel($ch));
} else {
    // Single Day Request
    if ($ch && $day) {
        $json = $stream->updateDay($ch, $day);
//    echo json_encode(array('OK'));
    }
}

echo $json;
return;