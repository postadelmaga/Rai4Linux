<?php

class Video_Ajax extends Varien_Object
{
    public function __construct(array $data = array())
    {
        parent::__construct($_POST);
    }

    public function getResponseJson()
    {
        $result = $this->_getRaiChannel();

        $data = array(
            'success' => 1,
            'data' => $result
        );

        return json_encode($data);
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

        if ($ch && $day) {
            $data = $rai->getDayJson($ch, $day, $update);
        }

        return $data;
    }
}
