<?php

class Maga_Video_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function chartAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}