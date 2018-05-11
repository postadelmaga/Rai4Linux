<?php

class Core_App extends Varien_Object
{
    const LOG_DIR = "log";
    const URL_BASE = "http://www.rai.it/dl/portale/html/palinsesti/replaytv/static/";
    const DEFAULT_ERROR_HANDLER = 'coreErrorHandler';

    public function run($code = null)
    {
        try {
            $this->getBlockHtml('page');

        } catch (Exception $e) {
            die($e->getMessage());
        }

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