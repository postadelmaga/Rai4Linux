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
}
