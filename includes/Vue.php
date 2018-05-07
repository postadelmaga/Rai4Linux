<?php
class Vue extends App
{
    public function getJsonConfig()
    {
        $config = array(
            'channels' => $this->getChannelInfo(),
            'days' => $this->getDayRange(),
            'current' => 0,
            'current_src' => 0,
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
            ),
            array(
                'id' => 2,
                'title' => 'RaiDue',
                'class' => 'w3-red',
            ),
            array(
                'id' => 3,
                'title' => 'RaiTre',
                'class' => 'w3-green',
            ),
            array(
                'id' => 31,
                'title' => 'RaiCinque',
                'class' => 'w3-orange',
            ),
        );
        return $ch;
    }
}