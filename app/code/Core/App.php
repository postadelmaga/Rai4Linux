<?php

class Core_App extends Varien_Object
{
    const LOG_DIR = "log";
    const URL_BASE = "http://www.rai.it/dl/portale/html/palinsesti/replaytv/static/";
    const DEFAULT_ERROR_HANDLER = 'coreErrorHandler';

    public function run($code = null)
    {
        try {
            if ($code == 'ajax') {
                $ajax = new Video_Ajax();
                $json = $ajax->getResponse();
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