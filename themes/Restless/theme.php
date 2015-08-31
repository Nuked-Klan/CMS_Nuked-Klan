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

$tpl = new tpl();

$tpl->assign('cfg', new iniConfigTool('themes/Restless/config.ini'));

if($tpl->get('cfg')->get('general.createSql') != 1){
    $dbiMenu = "INSERT INTO `" . $db_prefix . "_block` VALUES
                            ('', 0, 0, '', 'Menu Restless', '|" . _NAVCONTENT . "||0||NEWLINE[News]|" . _NAVNEWS . "||0||NEWLINE[Archives]|" . _NAVARCHIV . "||0||NEWLINE[Sections]|" . _NAVART . "||0||NEWLINE[Calendar]|" . _NAVCALENDAR . "||0||NEWLINE[Stats]|" . _NAVSTATS . "||0||NEWLINE|" . _NAVCOMMUNITY . "||0||NEWLINE[Forum]|" . _NAVFORUM . "||0||NEWLINE[Guestbook]|" . _NAVGUESTBOOK . "||0||NEWLINE[Irc]|" . _NAVIRC . "||0||NEWLINE[Members]|" . _NAVMEMBERS . "||0||NEWLINE[Contact]|" . _NAVCONTACTUS . "||0||NEWLINE|" . _NAVMEDIAS . "||0||NEWLINE[Download]|" . _NAVDOWNLOAD . "||0||NEWLINE[Gallery]|" . _NAVGALLERY . "||0||NEWLINE[Links]|" . _NAVLINKS . "||0||NEWLINE|" . _NAVGAMES . "||0||NEWLINE[Team]|" . _NAVTEAM . "||0||NEWLINE[Defy]|" . _NAVDEFY . "||0||NEWLINE[Recruit]|" . _NAVRECRUIT . "||0||NEWLINE[Server]|" . _NAVSERVER . "||0||NEWLINE[Wars]|" . _NAVMATCHS . "||0||NEWLINE|" . _MEMBER . "||1||NEWLINE[User]|" . _NAVACCOUNT . "||1||NEWLINE[Admin]|" . _NAVADMIN . "||2||', 'menu', 0, 'Tous');";
    $dbeMenu = mysql_query($dbiMenu);

    $tpl->get('cfg')->set('general.createSql', 1);
    $tpl->get('cfg')->save();
}

// Include language file
require_once('themes/Restless/lang/'.$GLOBALS['language'].'.lang.php');

if($_REQUEST['file'] == $GLOBALS['nuked']['index_site'] && (empty($_REQUEST['page']) || $_REQUEST['page'] == 'index') && (empty($_REQUEST['op']) || $_REQUEST['op'] == 'index') ){
    define('HOMEPAGE', true);
}
else{
    define('HOMEPAGE', false);
}

if(in_array($_REQUEST['file'], explode(',', $tpl->get('cfg')->get('general.displayFullPage')))){
    define('FULLPAGE', true);
}
else{
    define('FULLPAGE', false);
}

if(in_array($_REQUEST['file'], explode(',', $tpl->get('cfg')->get('general.displaySlider')))){
    define('SLIDER', true);
}
else{
    define('SLIDER', false);
}

if(in_array($_REQUEST['file'], explode(',', $tpl->get('cfg')->get('general.displayArticle')))){
    define('SHOW_ARTICLE', true);
}
else{
    define('SHOW_ARTICLE', false);
}

function top(){
    global $tpl;

    $tpl->render('head');

    $tpl->render('bodyTop');

    $tpl->render('header');

    $tpl->render('navigation');

    if(HOMEPAGE || SLIDER){
        $tpl->render('blockUnikTop');
    }

    $tpl->render('globalContainerTop');

    if(SHOW_ARTICLE && $tpl->get('cfg')->get('blockArticle.fullPage') != 1){
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
    global $tpl;

    $tpl->render('blockCenter', $block);
}

function block_bas($block){
    global $tpl;

    $tpl->render('blockBottom', $block);
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