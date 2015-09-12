<?php

ini_set('default_charset', 'ISO8859-1');
//error_reporting(E_ALL & ~E_DEPRECATED);

if (strpos($_SERVER['HTTP_HOST'], 'free.fr') !== false && ! is_dir($_SERVER['DOCUMENT_ROOT'] .'/sessions'))
    mkdir($_SERVER['DOCUMENT_ROOT'].'/sessions', 0700);

include 'install.class.php';

$install = new install();

$install->run();

?>