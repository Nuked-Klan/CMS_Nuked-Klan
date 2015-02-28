<?php

/**
 * Restless
 * Design by Homax
 * Developped by Samoth93
 * Thanks GuigZ for participation
 * April 2013
 */

// Include template generator
require_once('librairy.php');

// Include language file
require_once('themes/Restless/lang/'.$GLOBALS['language'].'.lang.php');

if($_REQUEST['file'] == $GLOBALS['nuked']['index_site'] && (empty($_REQUEST['page']) || $_REQUEST['page'] == 'index') && (empty($_REQUEST['op']) || $_REQUEST['op'] == 'index') ){
    define('HOMEPAGE', true);
}
else{
    define('HOMEPAGE', false);
}

$arrayBigModules = array('Forum');

if(!in_array($_REQUEST['file'], $GLOBALS['arrayBigModules'])){
    define('FULLPAGE', false);
}
else{
    define('FULLPAGE', true);
}

function top(){

    new viewTpl('head');

    new viewTpl('bodyTop');

    new viewTpl('header');

    new viewTpl('navigation');

    if(HOMEPAGE){
        new viewTpl('blockUnikTop');
    }

    new viewTpl('globalContainerTop');

    if(HOMEPAGE){
        new viewTpl('blockUnikCenter');
    }

    new viewTpl('contentTop');
}

function footer(){

    new viewTpl('contentBottom');

    if(HOMEPAGE){
        new viewTpl('gallery');
        new viewTpl('blockUnikBottom');
    }

    if(!FULLPAGE){
        new viewTpl('blockUnikRight');
    }
    else {
        new viewTpl('bigpageBottom');
    }

    new viewTpl('globalContainerBottom');

    new viewTpl('footer');

    new viewTpl('bodyBottom');

}

function block_gauche($block){
    new viewTpl('blockRight', $block);
}

function block_droite($block){
    new viewTpl('blockRight', $block);
}

function block_centre($block){

}

function block_bas($block){

}

function news($data){
   new viewTpl('news', $data);
}

function opentable(){
    echo '<div style="padding:10px;">';
}

function closetable(){
    echo '</div>';
}