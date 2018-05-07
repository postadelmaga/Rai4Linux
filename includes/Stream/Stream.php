<?php

class Stream extends App
{
       public function getJsonConfig()
    {
        $config = array(
            'ajaxUrl' => 'ajax.php',
            'channels' => $this->getChannelInfo(),
            'dayRange' => $this->getDayRange(),
            'qualityUrlType' => $this->getQualityType(),
            'debug' => isset($_GET['debug']) && $_GET['debug'] == 1
        );
        return json_encode($config);
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
                    'str' => $info['urlrisorsasottotitoli'],
                    'image' => $info['image'],
                    'image-big' => $info['image-big']
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
}
