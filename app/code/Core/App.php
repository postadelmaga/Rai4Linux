<?php

class Core_App extends Varien_Object
{
    const DEFAULT_ERROR_HANDLER = 'coreErrorHandler';

    public function run($code = null)
    {
        try {
            if ($code == 'ajax') {
                $ajax = new Video_Ajax();
                $json = $ajax->getResponseJson();
                echo $json;
            } else {
                $this->getBlockHtml('page');
            }

        } catch (Exception $e) {
            die($e->getMessage());
        }
        return;
    }

    public function getBlockHtml($id)
    {
        $template = Vue::getRoot() . '/templates/' . $id . '.phtml';
        if (file_exists($template)) {
            include $template;
        } else {
            die('not found');
        }
    }

}