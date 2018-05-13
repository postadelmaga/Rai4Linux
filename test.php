<?php
//
//include_once('./includes/Core.php');
//
//echo "today:" . date("Y-d-m", mktime(0, 0, 0, date("m"), date("d"), date("Y"))) . "<br>";
//$stream = new Stream();
////echo $stream->updateAllStreams();
//echo $stream->debug();

require_once('./app/Mage.php');
//$store = '1';
$store = 'admin';
$app = Mage::app($store);

umask(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// skip SSL redirect
Mage::register('skip_ssl_check', true);
$path = BP . DS . 'scripts' . DS . 'boi' . PS . get_include_path();
set_include_path($path . PS . Mage::registry('original_include_path'));

$model= Mage::getModel('video/rai');
var_dump($model);