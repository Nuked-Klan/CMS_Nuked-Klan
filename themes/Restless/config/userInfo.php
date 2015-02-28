<?php

$this->pseudo = $GLOBALS['user'][2];

$this->avatar = 'themes/Restless/images/no_avatar.jpg';

$this->nbMessages = 0;

$this->messagesCss = null;

$dbsUser = 'SELECT avatar, (SELECT count(mid)
                              FROM '.USERBOX_TABLE.'
                              WHERE user_for="'.$GLOBALS['user'][0].'" AND status="") AS nbMessages
            FROM '.USER_TABLE.'
            WHERE id="'.$GLOBALS['user'][0].'" ';
$dbeUSer = mysql_query($dbsUser);

$dbrUser = mysql_fetch_assoc($dbeUSer);

if(!empty($dbrUser['avatar'])) {
    $this->avatar = $dbrUser['avatar'];
}

$this->nbMessages = $dbrUser['nbMessages'];

if($this->nbMessages > 0){
    $this->messagesCss = ' id="RL_newMessages" ';
}