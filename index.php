<?php

require('kernel/_init.php');


$tpl = new Views();

$consts = array('BASE_DIR', 'KERNEL_DIR', 'PAGES_DIR', 'BASE_FOLDER', 'STATIC_FOLDER', 'IMAGE_FOLDER', 'CSS_FOLDER');

foreach($consts as $const) {
    $tpl->$const = constant($const);
}
$tpl->renderPage();