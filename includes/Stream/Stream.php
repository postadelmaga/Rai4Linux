<?php
class Stream
{
    const FILE_BASE = "./streamSource/";
    const URL_BASE = "http://www.rai.tv/dl/portale/html/palinsesti/replaytv/static/";
    const LOG_DIR = "./log/";

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
        $msg = '';
        foreach ($this->getChannelList() as $ch) {
            foreach ($this->getDaysRange() as $day) {
                $this->updateDay($ch, $day);
                $msg .= " ch:$ch - day:$day <br>";
            }
        }
        return $msg;
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
            $context = stream_context_create(array('http' => array('timeout' => 2500)));

//            $json = file_get_contents($url, false, $context);
            $json = $this->new_get_file_contents($url);
            $content = $this->_extractContent($date, $json);

        } catch (Exception $e) {
            $this->_log($e);
            return false;
        }
        return $content;
    }

    protected function new_get_file_contents($url)
    {
        // Initializing curl
        $ch = curl_init( $url );

// Configuring curl options
        $options = array(
            CURLOPT_RETURNTRANSFER => 1,
//            CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
            CURLOPT_CONNECTTIMEOUT => 0,
        );

// Setting curl options
        curl_setopt_array( $ch, $options );

// Getting results
        $file_contents =  curl_exec($ch); // Getting jSON result string

        curl_close($ch);
        return $file_contents;
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

    public function debug()
    {
        $ch = $this->getChannelList();
        $day = $this->getDaysRange();
        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        echo "$ch[1],$day[1]<br>";

        return $this->updateDay($ch[1], $day[1], true);
    }

    protected function _log($msg)
    {
        $filename = self::LOG_DIR . 'log.txt';

        date_default_timezone_set('Europe/Rome');
        $day = date("Y-m-d", mktime(2, 0, 0, date("m"), date("d"), date("Y")));

        $current = $day . PHP_EOL;
        $current .= $msg . PHP_EOL;

        file_put_contents($filename, $current, FILE_APPEND);
    }
}
