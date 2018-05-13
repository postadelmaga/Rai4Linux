<?php

class Maga_Video_Model_Rai extends Mage_Core_Model_Abstract
{
    const URL_BASE = "http://www.rai.it/dl/portale/html/palinsesti/replaytv/static/";

    public function debug()
    {
        $helper = Mage::helper('video/rai');
        $ch = $helper->getChannelList();
        $day = $helper->getDayRange();

        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
        echo "$ch[1],$day[1]<br>";

        return $this->updateDay($ch[1], $day[1], true);
    }

    public function updateAllStreams()
    {
        $msg = '';
        $helper = Mage::helper('video/rai');
        foreach ($helper->getChannelList() as $ch) {
            foreach ($helper->getDayRange() as $day) {
                $this->updateDay($ch, $day);
                $msg .= " ch:$ch - day:$day <br>";
            }
        }
        return $msg;
    }

    public function requestDay($ch_id, $date, $forceDownload = false)
    {
        $fileName = $date . '_' . $ch_id . ".json";
        $filePath = Mage::getBaseDir('media') . DS . 'video' . DS . 'rai' . DS . $fileName;

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

    protected function _getStreamContent($ch_id, $day, $iteration = 0)
    {
        try {
            //            $context = stream_context_create(array('http' => array('timeout' => 2500)));
            $programs = array();
            $helper = Mage::helper('video/rai');
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
                    'video_urls' => $helper->prepareVideoUrls($info),
                    'str' => $info['urlrisorsasottotitoli'],
                );
            }

        } catch (Exception $e) {
            if ($iteration < 20) {
                return $this->_getStreamContent($ch_id, $day, $iteration + 1);
            } else {
                $msg = "FAIL - [$ch_id - $day]--  {$e->getMessage()} -- Line:  {$e->getLine()}";
                Mage::log($msg, null, 'rai.log');
                return false;
            }
        }
        return $programs;
    }

    protected function _doRequest($ch_id, $day)
    {
        $helper = Mage::helper('video/rai');
        $channels = $helper->getChannelList();
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
            Mage::log("CURL_ERR:$err", null, 'rai.log');
        }
        $header['errmsg'] = $errmsg;
        if ($errmsg) {
            Mage::log("CURL_ERR:$errmsg", null, 'rai.log');
        }
        $header['content'] = $content;
        return $content;
    }

    public function cleanOldStreamSource()
    {
        $allowed = array();
        $helper = Mage::helper('video/rai');
        foreach ($helper->getChannelList() as $ch) {
            foreach ($helper->getDayRange() as $day) {
                $fname = $ch . '-' . $day . '.json';
                $allowed[] = $fname;
            }
        }

        if ($handle = opendir(Mage::getRoot())) {
            /* This is the correct way to loop over the directory. */
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $key = array_search($entry, $allowed);
                    if ($key !== false) {
                        unset($allowed[$key]);
                    } else {
                        unlink(Mage::getRoot() . $entry);
                    }
                }
            }
        }
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
