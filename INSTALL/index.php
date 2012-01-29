<?php
    if (version_compare(PHP_VERSION, '5.0.0', '<')){
        echo '<div style="margin:20px auto;width:100%;text-align:center;">
                    <p><b>Veuillez activer PHP5 sur votre hebergement!</b></p>
                    <p><b>Please enable PHP5 on your hosting!</b></p>
                </div>';
        exit();
    }
    else{
        if(preg_match('#free\.fr#', $_SERVER['HTTP_HOST'])){
            if(!file_exists($_SERVER['DOCUMENT_ROOT'].'/sessions')){
                mkdir($_SERVER['DOCUMENT_ROOT'].'/sessions', 0700);
            }
        }
        include('class_install.php');
    }
?>