<?php

class Maga_Video_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function ajaxAction()
    {
        $day = $this->getRequest()->getParams('day');
        $channel = $this->getRequest()->getParams('channel');
        $result = Mage::getModel('video/rai')->requestDay($day, $channel);

        $this->getResponse()->setBody($result);
    }

    public function chartAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}