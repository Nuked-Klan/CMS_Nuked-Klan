<?php

/* nkList configuration */

// Define the list of staff rank
$staffRankList = array(
    'classPrefix' => 'staffRank',
    'sqlQuery' => 'SELECT id, nom FROM '. $nuked['prefix'] .'_staff_rang',
    'fields' => array(
        'nom'       => array('label' => 'Rang')
    ),
    'edit' => array(
        'op'                => 'editRank',
        //'text'              => _EDITTHISRANK
    ),
    'delete' => array(
        'op'                => 'deleteRank',
        //'text'              => _DELTHISRANK,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'nom'
    ),
    //'emptytable' => _NORANKINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatStaffRankRow'
    )
);

?>