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
        $range = array();

        date_default_timezone_set('Europe/Rome');

        for ($i = 1; $i <= 7; $i++) {
            $prev = date("Y-m-d", mktime(2, 0, 0, date("m"), date("d") - $i, date("Y")));

            $date = new DateTime($prev);
            $prevDay = $date->format('Y-m-d');

            $range[] = $prevDay;
        }
        return $range;
    }

    /*
     * Remove old streamSource
     */
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

    public function updateAllStreams()
    {
        foreach ($this->getChannelList() as $ch) {
            foreach ($this->getDaysRange() as $day) {
                $this->updateDay($ch, $day);
            }
        }
        return "OK";
    }

    public function updateDay($ch, $date, $forceDownload = false)
    {
        $fileName = "";
        $content = "";

        $fileName = $ch . "-" . $date . ".json";
        $filePath = self::FILE_BASE . $fileName;

        if (!file_exists($filePath) || $forceDownload) {

            $content = $this->_getStreamContent($ch, $date);
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
            $json = file_get_contents($url);
            $content = $this->_extractContent($date, $json);

        } catch (Exception $e) {
            return false;
        }
        return $content;
    }

    protected function _extractContent($date, $json)
    {
        $dayList = array();

        $jsonIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator(json_decode($json, TRUE)),
            RecursiveIteratorIterator::SELF_FIRST);

        $is = false;

        foreach ($jsonIterator as $key => $val) {
            if ($key == $date || $is) {
                if (is_array($val) && $is) {
                    $dayList[$key] = $val;
                }
                $is = true;
            }
        }
        return json_encode($dayList);
    }
}
