<?php

function autoloader($className) {
    $file = __DIR__ . '/../classes/' . $className . '.php';
    include_once($file);
}

spl_autoload_register('autoloader');