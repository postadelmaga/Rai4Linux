<?php

class Maga_Video_Block_Player extends Mage_Core_Block_Template
{
    public function getJsonConfig()
    {
//        $model = Mage::getModel('video/rai');
        /** @var Maga_Video_Helper_Rai $helper */
        $helper = Mage::helper('video/rai');

        $config = array(
            'channels' => $helper->getChannelCollection() ,
            'dayrange' => $helper->getDayRange(),
            'current_id' => '',
            'src_current' => '',
            'ajaxurl' => Mage::getUrl('video/index/ajax'),
        );

        return json_encode($config);
    }
}