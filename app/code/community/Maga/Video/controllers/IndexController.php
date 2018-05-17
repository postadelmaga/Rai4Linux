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
        $day = $this->getRequest()->getParam('day');
        $channel = $this->getRequest()->getParam('channel');
        $result = Mage::getModel('video/rai')->requestDay($day, $channel);

        $this->getResponse()->setBody($result);
    }

    public function chartAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}