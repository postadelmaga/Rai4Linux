<?php

class Video_Rai extends Varien_Object
{
    const URL_BASE = "http://www.rai.it/dl/portale/html/palinsesti/replaytv/static/";

    public function getChannelInfo()
    {
        $channels = array(
            array(
                'id' => 1,
                'title' => 'RaiUno',
                'lass' => 'w3-blu',
                'days' => array(),
            ),
            array(
                'id' => 2,
                'title' => 'RaiDue',
                'class' => 'w3-red',
                'days' => array()
            ),
            array(
                'id' => 3,
                'title' => 'RaiTre',
                'class' => 'w3-green',
                'days' => array()
            ),
            array(
                'id' => 31,
                'title' => 'RaiCinque',
                'class' => 'w3-orange',
                'days' => array()
            ),
        );
        return $channels;
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
        return array_reverse($range);
    }


    public function getJsonConfig()
    {
        $config = array(
            'channels' => $this->getChannelInfo(),
            'days' => $this->getDayRange(),
            'ch_current' => '',
            'src_current' => '',
            'ajaxurl' => '?ajax=1',
        );

        return json_encode($config);
    }


    public function getChannelList()
    {
        $channels = array();
        foreach ($this->getChannelInfo() as $ch) {
            $channels[$ch['id']] = $ch['title'];
        }

        return $channels;
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

        if ($handle = opendir(Vue::getRoot())) {
            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $key = array_search($entry, $allowed);
                    if ($key !== false) {
                        unset($allowed[$key]);
                    } else {
                        unlink(Vue::getRoot() . $entry);
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

    public function requestDay($ch_id, $date, $forceDownload = false)
    {
        $fileName = $date . '_' . $ch_id . ".json";
        $filePath = MAGENTO_ROOT . '/data/' . $fileName;

        if (!file_exists($filePath) || $forceDownload) {
            try {
                if ($content = $this->_getStreamContent($ch_id, $date)) {
                    $content = json_encode($content);
                    file_put_contents($filePath, $content);
                    return $content;
                }
                return false;

            } catch (Exception $e) {
                return false;
            }
        }

        return file_get_contents($filePath);
    }

    protected function _prepareVideoUrls($data)
    {
        $videoUrls = array();
        $data = new Varien_Object($data);

        foreach ($this->getQualityType() as $type) {
            $key = str_replace('', 'h264', $type);

            if ($url = $data->getData($type)) {
                if (array_search($url, $videoUrls)) {
                    $videoUrls[$key] = $url;
                }
            }
        }
        if (count($videoUrls) == 0) {
            if ($url = $data->getData('h264'))
                $videoUrls['only'] = $url;
        }

        // Decode Url
        foreach ($videoUrls as $k => $url) {
            if ($direct_video = $this->_getVideoUrl($url)) {
                $videoUrls[$k] = $direct_video;
            } else {
                unset($videoUrls[$k]);
            }
        }

        return $videoUrls ? $videoUrls : array();
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

    protected function _getStreamContent($ch_id, $day, $iteration = 0)
    {
        try {
            //            $context = stream_context_create(array('http' => array('timeout' => 2500)));
            $programs = array();
            $json = $this->_doRequest($ch_id, $day);
            $data = json_decode($json, TRUE);

            $data_day = $data[$ch_id][$day];
            foreach ($data_day as $time => $info) {
                $programs[] = array(
                    'program_id' => $info['i'],
                    'title' => $info['t'],
                    'time' => $time,
                    'description' => $info['d'],
                    'image' => $info['image'],
                    'image_big' => $info['image-big'],
                    'video_urls' => $this->_prepareVideoUrls($info),
                    'str' => $info['urlrisorsasottotitoli'],
                );
            }

        } catch (Exception $e) {
            if ($iteration < 20) {
                return $this->_getStreamContent($ch_id, $day, $iteration + 1);
            } else {
                Stream::log("FAIL - [$ch_id - $day]--" . $e->getMessage() . '-- Line: ' . $e->getLine());
                return false;
            }
        }
        return $programs;
    }

    protected function _doRequest($ch_id, $day)
    {
        $channels = $this->getChannelList();
        $chanel_name = $channels[$ch_id];
        $url = self::URL_BASE . $chanel_name . '_' . str_replace('-', '_', $day);

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

}
