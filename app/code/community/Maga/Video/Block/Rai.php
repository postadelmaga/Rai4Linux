<?php

class Maga_Video_Block_Rai extends Mage_Core_Block_Template
{
    public function getJsonConfig()
    {
        $model = Mage::getModel('video/rai');

        $helper = Mage::helper('video/rai');

        $config = array(
            'channels' => $helper->getChannelInfo(),
            'days' => $helper->getDayRange(),
            'ch_current' => '',
            'src_current' => '',
            'ajaxurl' => '?ajax=1',
        );

        return json_encode($config);
    }
}