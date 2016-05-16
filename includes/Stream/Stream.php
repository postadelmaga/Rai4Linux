<?php

class Stream
{
    const LOG_DIR = "log";
    const FILE_BASE = "./data/";
    const URL_BASE = "http://www.rai.it/dl/portale/html/palinsesti/replaytv/static/";
    const DEFAULT_ERROR_HANDLER = 'coreErrorHandler';

    public function __construct()
    {
        date_default_timezone_set('Europe/Rome');

        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        set_error_handler(self::DEFAULT_ERROR_HANDLER, E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

        if (!file_exists(self::FILE_BASE)) {
            mkdir(self::FILE_BASE, 0777, true);
        }

        if (!file_exists(self::LOG_DIR)) {
            mkdir(self::LOG_DIR, 0777, true);
        }

        // tmp disabled
//        $this->cleanOldStreamSource();
    }

    public function getJsonConfig()
    {
        $config = array(
            'ajaxUrl' => 'ajax.php',
            'channelList' => array_values($this->getChannelList()),
            'dayRange' => $this->getDayRange(),
            'qualityUrlType' => $this->getQualityType(),
            'debug' => isset($_GET['debug']) && $_GET['debug'] == 1
        );
        return json_encode($config);
    }

    public function getChannelList()
    {
        $ch = array(1 => 'RaiUno', 2 => 'RaiDue', 3 => 'RaiTre', 31 => 'RaiCinque');
        return $ch;
    }

    public function getQualityType()
    {
        $types = array(
            'h264_400',
            'h264_600',
            'h264_800',
            'h264_1200',
            'h264_1500',
            'h264_1800',
        );
        return $types;
    }

    public function getDayRange()
    {
        $range = array();

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
    public function cleanOldStreamSource()
    {
        $allowed = array();
        foreach ($this->getChannelList() as $ch) {
            foreach ($this->getDayRange() as $day) {
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
            foreach ($this->getDayRange() as $day) {
                $this->updateDay($ch, $day);
                $msg .= " ch:$ch - day:$day <br>";
            }
        }
        return $msg;
    }

    public function updateDay($ch, $date, $forceDownload = false)
    {
        $fileName = $ch . "-" . $date . ".json";
        $filePath = self::FILE_BASE . $fileName;

        if (!file_exists($filePath) || $forceDownload) {

            $content = $this->_getStreamContent($ch, $date);
            if ($content != "") {
                $content = json_encode($content);
                file_put_contents($filePath, $content);
                return $content;
            }
            return false;
        }
        return file_get_contents($filePath);
    }

    protected function _getStreamContent($ch, $day, $iteration = 0)
    {
        $url = $this->_getUrlByChannelDay($ch, $day);
        $data_clean = array();
        try {
//            $context = stream_context_create(array('http' => array('timeout' => 2500)));
            $json = $this->downloadFile($url);
            $data = json_decode($json, TRUE);
            $key = array_search($ch, $this->getChannelList());

            foreach ($data[$key][$day] as $time => $info) {

                $data_clean[$info['i']] = array(
                    'time' => $time,
                    'program_id' => $info['t'],
                    'title' => $info['t'],
                    'description' => $info['d'],
                    'video_urls' => $this->_getVideoUrls($info),
                    'str' => $info['urlrisorsasottotitoli']
                );
            }

        } catch (Exception $e) {
            if ($iteration < 20) {
                return $this->_getStreamContent($ch, $day, $iteration + 1);
            } else {
                Stream::log($url . "FAIL-I:$iteration| -- $ch-$day --" . $e->getMessage() . '-- Line: ' . $e->getLine() . $json);
                return false;
            }
        }
        return $data_clean;
    }

    protected function _getVideoUrls($info)
    {
        $tmvideoUrls = array();

        foreach ($this->getQualityType() as $type) {
            if (isset($info[$type]) && $info[$type] != '' && !in_array($info[$type], $videoUrls)) {
                $videoUrls[] = $info[$type];
            }
        }
        if (count($videoUrls) == 0 && isset($info['h264']) && $info['h264'] != '') {
            $videoUrls[] = $info['h264'];
        }

        if (count($videoUrls) > 1) {
            $videoUrls = array($videoUrls[0], $videoUrls[count($videoUrls) - 1]);
        }
        foreach ($videoUrls as $k => $url) {
            if ($direct_video = $this->_getVideoUrl($url)) {
                $videoUrls[$k] = $this->_getVideoUrl($url);
            } else {
                unset($videoUrls[$k]);
            }
        }
        return $videoUrls ? $videoUrls : array();
    }

    public function downloadFile($url)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_ENCODING => "", // handle all encodings
            CURLOPT_USERAGENT => "spider", // who am i
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 200, // timeout on connect
            CURLOPT_TIMEOUT => 200, // timeout on response
            CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
        );

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $header['errno'] = $err;
        if ($err) {
            Stream::log("CURL_ERR:$err");
        }
        $header['errmsg'] = $errmsg;
        if ($errmsg) {
            Stream::log("CURL_ERR:$errmsg");
        }
        $header['content'] = $content;
        return $content;
    }

    protected function _getUrlByChannelDay($ch, $day)
    {
        $url = self::URL_BASE . $ch . '_' . str_replace('-', '_', $day);
        return $url;
    }


    protected function _getVideoUrl($url)
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

        return ($redirect_url != '' && $redirect_url != "00 OK") ? $redirect_url : false;
    }

//    protected function _extractContent($date, $json)
//    {
//        $dayList = array();
//
//        $jsonIterator = new RecursiveIteratorIterator(
//            new RecursiveArrayIterator(json_decode($json, TRUE)),
//            RecursiveIteratorIterator::SELF_FIRST);
//
//        $is = false;
//
//        foreach ($jsonIterator as $key => $val) {
//            if ($key == $date || $is) {
//                if (is_array($val) && $is) {
//                    $dayList[$key] = $val;
//                }
//                $is = true;
//            }
//        }
//        return json_encode($dayList);
//    }

    public function debug()
    {
        $ch = $this->getChannelList();
        $day = $this->getDayRange();
        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        echo "$ch[1],$day[1]<br>";

        return $this->updateDay($ch[1], $day[1], true);
    }

    static function log($msg)
    {
        $filename = self::LOG_DIR . DIRECTORY_SEPARATOR . date("Y-m-d") . '.log';
        $day = date("Y-m-d H:i:s") . ': ';
        $current = $day . $msg . PHP_EOL;

        file_put_contents($filename, $current, FILE_APPEND);
    }
}
