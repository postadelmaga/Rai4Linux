<?php
class Vue extends App
{
    public function getJsonConfig()
    {
        $config = array(
            'channels' => $this->getChannelInfo(),
            'days' => $this->getDayRange(),
            'ch_current' => '',
            'src_current' => '',
        );

        return json_encode($config);
    }

    public function getChannelInfo()
    {
        $ch = array(
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
        return $ch;
    }
}