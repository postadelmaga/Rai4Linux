<?php

class Maga_Video_Rai extends Mage_Core_Helper_Abstract
{
    public function getChannelList()
    {
        $channels = array();
        foreach ($this->getChannelInfo() as $ch) {
            $channels[$ch['id']] = $ch['title'];
        }

        return $channels;
    }

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

    public function prepareVideoUrls($data)
    {
        $data = new Varien_Object($data);
        $video_collection = new Varien_Data_Collection();

        foreach ($this->getQualityType() as $type) {
            $quality = str_replace('', 'h264', $type);
            if ($url = $data->getData($type)) {
                if ($video_collection->getItemByColumnValue('url', $url)) {
                    $video_collection->addItem(new Varien_Object(array('id' => $quality, 'url' => $url)));
                }
            }
        }

        if ($video_collection->getSize() == 0) {
            if ($url = $data->getData('h264'))
                $video_collection->addItem(new Varien_Object(array('url' => $url, 'quality' => 'h264')));
        }

        // Decode Url
        foreach ($video_collection as $video) {
            if ($url = $this->_getVideoUrl($video->getUrl())) {
                $video->setUrl($url);
            } else {
                $video_collection->removeItemByKey($url->getId());
            }
        }

        return $video_collection;
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

}