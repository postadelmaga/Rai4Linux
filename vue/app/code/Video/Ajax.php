<?php

class Video_Ajax extends Varien_Object
{
    public function __construct(array $data = array())
    {
        parent::__construct($_POST);
    }

    public function getResponse()
    {
        $rai = new Video_Rai();

        if ($this->getData('sd')) {
            $url = $rai->getVideoUrl($this->getData('sd'));
            $data['sd'] = $url;

            if ($this->getData('hq')) {
                $hd = $rai->getVideoUrl($this->getData('hq'));
                $data['hd'] = $hd;
            }
            $json = json_encode($data);
        }

        $ch = $this->getData('ch');
        $day = $this->getData('day');

        if ($this->getData('up')) {
            if ($ch && $day) {
                $json = $rai->updateDay($ch, $day, true);

            } else {
                $rai->updateAllStreams();
                $json = json_encode(array('Update All End'));
            }
        } elseif ($ch !== null && $day !== null) {
            $json = $rai->updateDay($ch, $day);
        } elseif ($ch !== null && !$day) {
            $json = json_encode($rai->getChannel($ch));
        }

        return $json;
    }

}
