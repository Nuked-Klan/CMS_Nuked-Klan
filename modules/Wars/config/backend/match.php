<?php

/* nkList configuration */

// Define the list of match
$matchList = array(
    'classPrefix' => 'match',
    'sqlQuery' => 'SELECT warid, team, adversaire, url_adv, etat, date_jour, date_mois, date_an FROM '. WARS_TABLE,
    'defaultSortables' => array(
        'order'     => array('etat', 'date_an', 'date_mois', 'date_jour'),
        'dir'       => array('ASC', 'DESC', 'DESC', 'DESC')
    ),
    'fields' => array(
        'date'      => array('label' => _DATE),
        'status'    => array('label' => _STATUS),
        'opponent'  => array('label' => _OPPONENT),
        'team'      => array('label' => _TEAM)
    ),
    'edit' => array(
        'op'                => 'editMatch',
        'text'              => _EDITTHISMATCH
    ),
    'delete' => array(
        'op'                => 'deleteMatch',
        'text'              => _DELTHISMATCH,
        'confirmTxt'        => _DELETEMATCH .' %s ! '. _CONFIRM,
        'confirmField'      => 'adversaire'
    ),
    'emptytable' => _NOMATCH,
    'callbackRowFunction' => array('functionName' => 'formatMatchRow')
);

?>