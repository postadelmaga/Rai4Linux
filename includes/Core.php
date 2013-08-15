<?php

function __autoload($class_name)
{
    if (count(explode('_', $class_name)) > 1) {
        $arr = explode('_', $class_name);
        $n1 = './' . 'includes' . '/' . $arr[0] . '/' . $arr[1] . '.php';
    } else
        $n1 = './' . 'includes' . '/' . $class_name . '/' . $class_name . '.php';
    $n2 = './' . 'includes' . '/' . $class_name . '.php';
    $n3 = './' . $class_name . '.php';

    $test_names = array($n1, $n2, $n3);

    foreach ($test_names as $fileName) {

        if (file_exists($fileName)) {
            include $fileName;
            return;
        }
    }
}