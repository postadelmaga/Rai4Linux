<?php

function coreErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (strpos($errstr, 'DateTimeZone::__construct') !== false) {
        // there's no way to distinguish between caught system exceptions and warnings
        return false;
    }

    $errno = $errno & error_reporting();
    if ($errno == 0) {
        return false;
    }
    if (!defined('E_STRICT')) {
        define('E_STRICT', 2048);
    }
    if (!defined('E_RECOVERABLE_ERROR')) {
        define('E_RECOVERABLE_ERROR', 4096);
    }
    if (!defined('E_DEPRECATED')) {
        define('E_DEPRECATED', 8192);
    }

    // PEAR specific message handling
    if (stripos($errfile . $errstr, 'pear') !== false) {
        // ignore strict and deprecated notices
        if (($errno == E_STRICT) || ($errno == E_DEPRECATED)) {
            return true;
        }
        // ignore attempts to read system files when open_basedir is set
        if ($errno == E_WARNING && stripos($errstr, 'open_basedir') !== false) {
            return true;
        }
    }

    $errorMessage = '';

    switch ($errno) {
        case E_ERROR:
            $errorMessage .= "Error";
            break;
        case E_WARNING:
            $errorMessage .= "Warning";
            break;
        case E_PARSE:
            $errorMessage .= "Parse Error";
            break;
        case E_NOTICE:
            $errorMessage .= "Notice";
            break;
        case E_CORE_ERROR:
            $errorMessage .= "Core Error";
            break;
        case E_CORE_WARNING:
            $errorMessage .= "Core Warning";
            break;
        case E_COMPILE_ERROR:
            $errorMessage .= "Compile Error";
            break;
        case E_COMPILE_WARNING:
            $errorMessage .= "Compile Warning";
            break;
        case E_USER_ERROR:
            $errorMessage .= "User Error";
            break;
        case E_USER_WARNING:
            $errorMessage .= "User Warning";
            break;
        case E_USER_NOTICE:
            $errorMessage .= "User Notice";
            break;
        case E_STRICT:
            $errorMessage .= "Strict Notice";
            break;
        case E_RECOVERABLE_ERROR:
            $errorMessage .= "Recoverable Error";
            break;
        case E_DEPRECATED:
            $errorMessage .= "Deprecated functionality";
            break;
        default:
            $errorMessage .= "Unknown error ($errno)";
            break;
    }

    $errorMessage .= ": {$errstr}  in {$errfile} on line {$errline}";
    Stream::log($errorMessage);
}

class Stream
{
    const FILE_BASE = "./streamSource/";
    const URL_BASE = "http://www.rai.it/dl/portale/html/palinsesti/replaytv/static/";
    const LOG_DIR = "./log/";
    const DEFAULT_ERROR_HANDLER = 'coreErrorHandler';

    public function __construct()
    {
        date_default_timezone_set('Europe/Rome');

        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        set_error_handler(self::DEFAULT_ERROR_HANDLER, E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        $this->cleanOldStreamSource();
    }

    public function getChannelList()
    {
        $ch = array('RaiUno', 'RaiDue', 'RaiTre', 'RaiCinque');
        return $ch;
    }

    public function getDaysRange()
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

    protected function _getStreamContent($ch, $date, $iteration = 0)
    {
        $content = "";
        $ex = false;

        $url = self::URL_BASE . $ch . '_' . str_replace('-', '_', $date);
        try {
            $context = stream_context_create(array('http' => array('timeout' => 2500)));

//            if ($iteration % 2 == 0) {
//                $m = 1;
////                Stream::log("I:$iteration,M:1--$ch-$date");
//                $json = file_get_contents($url, false, $context);
//
//            } elseif ($iteration % 2 == 1) {
            $m = 2;
//                Stream::log("I:$iteration,M:2--$ch-$date");
            $json = $this->get_file_contents_2($url);
//            }
            $content = $this->_extractContent($date, $json);

        } catch (Exception $e) {
            if ($iteration < 20) {
                return $this->_getStreamContent($ch, $date, $iteration + 1);
            } else {
                Stream::log($url."FAIL-I:$iteration|M:$m -- $ch-$date --".$e->getMessage().'-- Line: '.$e->getLine().$json);
                return false;
            }
            $ex = true;
        }
//        if ($iteration > 15 && !$ex)
//            Stream::log("SUCCESS-I:$iteration|M:$m -- $ch-$date");
        return $content;
    }

    public function get_file_contents_2($url)
    {
        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_ENCODING => "", // handle all encodings
            CURLOPT_USERAGENT => "spider", // who am i
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
            CURLOPT_TIMEOUT => 120, // timeout on response
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

    static function log($msg)
    {
        $filename = self::LOG_DIR . 'log.txt';

        $day = date("Y-m-d H:i:s") . ': ';
        $current = $day . $msg . PHP_EOL;

        file_put_contents($filename, $current, FILE_APPEND);
    }
}
