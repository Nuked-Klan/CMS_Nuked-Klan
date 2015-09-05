<?php

if (version_compare(PHP_VERSION, '5.1.0', '<')) {
    echo '<div style="margin:20px auto;width:100%;text-align:center;">
                <p><b>Veuillez activer PHP5 sur votre hebergement!</b></p>
                <p><b>Please enable PHP5 on your hosting!</b></p>
            </div>';
    exit;
}

if (strpos($_SERVER['HTTP_HOST'], 'free.fr') !== false && ! is_dir($_SERVER['DOCUMENT_ROOT'] .'/sessions'))
    mkdir($_SERVER['DOCUMENT_ROOT'].'/sessions', 0700);

include 'class_install.php';

$install = new install();

?>