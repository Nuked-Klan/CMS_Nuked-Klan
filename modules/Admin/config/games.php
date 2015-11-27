<?php

/* nkList configuration */

// Define the list of games
$gamesList = array(
    'classPrefix' => 'games',
    'sqlQuery' => 'SELECT id, name FROM '. GAMES_TABLE,
    'defaultSortables' => array(
        'order'     => array('name')
    ),
    'fields' => array(
        'name'      => array('label' => _NAME)
    ),
    'edit' => array(
        'op'                => 'edit',
        'text'              => _GAMEEDIT
    ),
    'delete' => array(
        'op'                => 'delete',
        'text'              => _GAMEDEL,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'name'
    ),
    'emptytable' => _NOGAMEINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatGameRow'
    )
);

?>