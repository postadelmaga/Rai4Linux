<?php

class Maga_Video_Model_Resource_Rai_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('rai');
    }
}
