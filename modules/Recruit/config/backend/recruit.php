<?php

/* nkList configuration */

// Define the list of recruit
$recruitList = array(
    'classPrefix' => 'recruit',
    'sqlQuery' => 'SELECT R.id, R.pseudo, R.prenom, R.mail, R.date, G.name AS gameName FROM '. RECRUIT_TABLE .' AS R LEFT JOIN '. GAMES_TABLE .' AS SC ON G.id = R.game',
    'defaultSortables' => array(
        'order'     => array('R.id'),
        'dir'       => array('DESC')
    ),
    'fields' => array(
        'pseudo'    => array('label' => _NICK),
        'prenom'    => array('label' => _FIRSTNAME),
        'gameName'  => array('label' => _GAME),
        'mail'      => array('label' => _MAIL),
        'date'      => array('label' => _DATE)
    ),
    'emptytable' => _NORECRUIT,
    'callbackRowFunction' => array('functionName' => 'formatRecruitRow')
);

?>