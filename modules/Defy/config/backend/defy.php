<?php

/* nkList configuration */

// Define the list of defy
$defyList = array(
    'classPrefix' => 'defy',
    'sqlQuery' => 'SELECT D.id, D.pseudo, D.send, D.mail, D.clan, G.name AS gameName FROM '. DEFY_TABLE .' AS D LEFT JOIN '. GAMES_TABLE .' AS G ON G.id = D.game',
    'defaultSortables' => array(
        'order'     => array('D.id'),
        'dir'       => array('DESC')
    ),
    'fields' => array(
        'pseudo'    => array('label' => _NICK),
        'clan'      => array('label' => _CLAN),
        'gameName'  => array('label' => _GAME),
        'mail'      => array('label' => _MAIL),
        'send'      => array('label' => _DATE)
    ),
    'emptytable' => _NODEFY,
    'callbackRowFunction' => array('functionName' => 'formatDefyRow')
);

?>