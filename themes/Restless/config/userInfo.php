<?php

$this->assign('pseudo' ,$GLOBALS['user'][2]);

$this->assign('avatar', 'themes/Restless/images/no_avatar.jpg');

$this->assign('nbMessages', 0);

$this->assign('messagesCss', null);

$dbsUser = 'SELECT avatar, (SELECT count(mid)
                              FROM '.USERBOX_TABLE.'
                              WHERE user_for="'.$GLOBALS['user'][0].'" AND status="") AS nbMessages
            FROM '.USER_TABLE.'
            WHERE id="'.$GLOBALS['user'][0].'" ';
$dbeUSer = mysql_query($dbsUser);

$dbrUser = mysql_fetch_assoc($dbeUSer);

if(!empty($dbrUser['avatar'])) {
    $this->assign('avatar', $dbrUser['avatar']);
}

$this->assign('nbMessages', $dbrUser['nbMessages']);

if($this->get('nbMessages') > 0){
    $this->assign('messagesCss', ' id="RL_newMessages" ');
}