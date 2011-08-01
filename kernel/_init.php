<?php

if(!defined('__DIR__')) {
    define('__DIR__', dirname(__FILE__));
}

define('KERNEL_DIR', __DIR__.'/');
define('BASE_DIR', realpath(__DIR__.'/../').'/');
define('PAGES_DIR', realpath(__DIR__.'/../').'/pages/');


define('BASE_FOLDER', '/Korri2/');
define('STATIC_FOLDER', BASE_FOLDER.'/static/');
define('CSS_FOLDER', BASE_FOLDER.'/static/');
define('IMAGE_FOLDER', BASE_FOLDER.'/static/images/');
define('TWIG_CACHE_DIR', BASE_FOLDER.'/static/cache/');

function my_autoload($class) {
    @include(KERNEL_DIR . $class . '.php');
}

session_start();
require(KERNEL_DIR.'Twig/Autoloader.php');
Twig_Autoloader::register();

spl_autoload_register('my_autoload');

/**
 * @global Database $db
 */
//$db = new Database('localhost', 'login', 'pass', 'database');

?>
