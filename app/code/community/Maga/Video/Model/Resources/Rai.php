<?php

class Maga_Video_Model_Resource_Rai extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('video/rai', 'video_id');
    }
}
