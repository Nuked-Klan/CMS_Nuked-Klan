<?php

/* nkList configuration */

// Define the list of irc awards
$awardsList = array(
    'classPrefix' => 'awards',
    'sqlQuery' => 'SELECT id, date, text FROM '. IRC_AWARDS_TABLE,
    'defaultSortables' => array(
        'order'     => array('id'),
        'dir'       => array('DESC')
    ),
    'fields' => array(
        'date'          => array('label' => _DATE),
        'text'          => array('label' => _TEXT)
    ),
    'edit' => array(
        'op'                => 'editAward',
        'text'              => _EDITTHISAWARD
    ),
    'delete' => array(
        'op'                => 'deleteAward',
        'text'              => _DELTHISAWARD,
        'confirmTxt'        => _AWARDSDELETE .' %s ! '. _CONFIRM,
        'confirmField'      => 'id'
    ),
    'emptytable' => _NOAWARD,
    'callbackRowFunction' => array('functionName' => 'formatAwardRow')
);

?>