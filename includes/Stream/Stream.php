<?php
class Stream
{
    const FILE_BASE = "./streamSource/";
    const URL_BASE = "http://www.rai.tv/dl/portale/html/palinsesti/replaytv/static/";

    public function __construct()
    {

        $this->cleanStreamSourceRep();
    }

    public function getChannelList()
    {
        $ch = array('RaiUno', 'RaiDue', 'RaiTre', 'RaiCinque');
        return $ch;
    }

    public function getDaysRange()
    {
        date_default_timezone_set('Europe/Rome');

        $range = array();
        for ($i = 1; $i <= 7; $i++) {
            $prev = date("Y-d-m", mktime(2, 0, 0, date("d") - $i, date("m"), date("Y")));

            $date = new DateTime($prev);
            $prevDay = $date->format('Y-m-d');

            $range[] = $prevDay;
        }
        return $range;
    }

    public function cleanStreamSourceRep()
    {
        $allowed = array();
        foreach ($this->getChannelList() as $ch) {
            foreach ($this->getDaysRange() as $day) {
                $fname = $ch . '-' . $day . '.json';
                $allowed[] = $fname;
            }
        }

        if ($handle = opendir(self::FILE_BASE)) {
            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $key = array_search($entry, $allowed);
                    if ($key !== false) {
                        unset($allowed[$key]);
                    } else {
                        unlink(self::FILE_BASE . $entry);
                    }
                }
            }
        }
    }

    public function getChannel($ch)
    {
        $fileName = $ch . "-";
        $jDays = array();

        foreach ($this->getDaysRange() as $day) {
            $jDays[] = updateDay($ch, $day);
        }
        return $jDays;
    }

//    public function getDay($ch, $day, $update = 0)
//    {
//        $currentFile = self::FILE_BASE . $ch . "-" . $day . ".json";
//
//        if (file_exists($currentFile)) {
//            $jsonDay = $this->extractContent(file_get_contents($currentFile));
//            return $jsonDay;
//        } elseif ($update) {
//            $jsonDay = $this->extractContent($this->updateDay($ch, $day,true));
//            return $jsonDay;
//        }
//
//        return false;
//    }

    public function updateAllStreams()
    {
        foreach ($this->getChannelList() as $ch) {
            foreach ($this->getDaysRange() as $day) {
                $this->updateDay($ch, $day);
            }
        }
        return "OK";
    }

    public function updateDay($ch, $date, $reload = false)
    {
        $fileName = "";
        $content = "";

        $fileName = $ch . "-" . $date . ".json";
        $filePath = self::FILE_BASE . $fileName;

        if (!file_exists($filePath)) { //|| $reload) {

            $content = $this->extractContent($ch, $date);
            if ($content) {
                file_put_contents($filePath, $content);
                return $content;
            }
            return false;
        }
        return file_get_contents($filePath);
    }

    protected function _getStreamContent($ch, $date)
    {
        $content = "";

        $url = self::URL_BASE . $ch . '_' . str_replace('-', '_', $date);
        try {
            $content = file_get_contents($url);

            return $content;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function extractContent($ch, $date)
    {
        $json = $this->_getStreamContent($ch, $date);
        $obj = json_decode($json);
//      $content = $obj->{1};

        // estraele il primo elemento numerico
        // contenente la lista oraria del giorno
        foreach ($obj as $key => $value) {
            $key = (int)$key;
            if ($key > 0) {
                return json_encode($value);
//                foreach ($value as $key => $item) {
//                    $item->date = $key;
//                    $days[$key] = $item;
//                }
            }
        }

        return false;
    }
}
