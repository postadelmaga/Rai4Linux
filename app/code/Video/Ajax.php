<?php

class Video_Ajax extends Varien_Object
{
    public function __construct(array $data = array())
    {
        parent::__construct($_POST);
    }

    public function getResponse()
    {
        if ($this->getData('sd')) {
            $data = $this->_getRaiVideoUrl();
        } else {
            $data = $this->_getRaiChannel();
        }

        return json_encode($data);
    }

    protected function _getRaiVideoUrl()
    {
        $rai = new Video_Rai();

        $url = $rai->getVideoUrl($this->getData('sd'));
        $data['sd'] = $url;

        if ($this->getData('hq')) {
            $hd = $rai->getVideoUrl($this->getData('hq'));
            $data['hd'] = $hd;
        }

        return $data;
    }

    protected function _getRaiChannel()
    {
        $rai = new Video_Rai();

        $data = array();
        $ch = $this->getData('ch');
        $day = $this->getData('day');
        $update = $this->getData('up');

        if ((int)$update === 2) {
            $rai->updateAllStreams();
            return array('Update All End');
        }

        if ($ch) {
            $data = json_decode($rai->getDayJson($ch, $day, $update));
        }

        if ($ch !== null && !$day) {
            $data = $rai->getChannel($ch);
        }

        return $data;
    }
}
