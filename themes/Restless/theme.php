<?php

/**
 * Restless
 * Design by Homax
 * Developped by Samoth93
 * Thanks GuigZ for participation
 * April 2013
 */

// Include template engine
require_once('librairies/templateEngine.php');
// Include config ini tool
require_once('librairies/iniConfigTool.php');

$cfg = new iniConfigTool('themes/Restless/config.ini');

$tpl = new tpl();

$tpl->assign('cfg', $cfg);

// Include language file
require_once('themes/Restless/lang/'.$GLOBALS['language'].'.lang.php');

if($_REQUEST['file'] == $GLOBALS['nuked']['index_site'] && (empty($_REQUEST['page']) || $_REQUEST['page'] == 'index') && (empty($_REQUEST['op']) || $_REQUEST['op'] == 'index') ){
    define('HOMEPAGE', true);
}
else{
    define('HOMEPAGE', false);
}

$arrayBigModules = explode(',', $cfg->get('general.bigModules'));

if(!in_array($_REQUEST['file'], $arrayBigModules)){
    define('FULLPAGE', false);
}
else{
    define('FULLPAGE', true);
}

function top(){
    global $tpl;

    $tpl->render('head');

    $tpl->render('bodyTop');

    $tpl->render('header');

    $tpl->render('navigation');

    if(HOMEPAGE){
        $tpl->render('blockUnikTop');
    }

    $tpl->render('globalContainerTop');

    if(HOMEPAGE){
        $tpl->render('blockUnikCenter');
    }

    $tpl->render('contentTop');
}

function footer(){
    global $tpl;

    $tpl->render('contentBottom');

    if(HOMEPAGE){
        $tpl->render('blockGallery');
        $tpl->render('blockUnikBottom');
    }

    if(!FULLPAGE){
        $tpl->render('blockUnikRight');
    }
    else {
        $tpl->render('bigpageBottom');
    }

    $tpl->render('globalContainerBottom');

    $tpl->render('footer');

    $tpl->render('bodyBottom');

}

function block_gauche($block){
    global $tpl;

    $tpl->render('blockRight', $block);
}

function block_droite($block){
    global $tpl;

    $tpl->render('blockRight', $block);
}

function block_centre($block){

}

function block_bas($block){

}

function news($data){
    global $tpl;

    $tpl->render('news', $data);
}

function opentable(){
    echo '<div style="padding:10px;">';
}

function closetable(){
    echo '</div>';
}