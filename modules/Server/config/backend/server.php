<?php

/* nkList configuration */

// Define the list of server
$serverList = array(
    'classPrefix' => 'server',
    'sqlQuery' => 'SELECT A.sid, A.ip, A.port, A.game, B.titre FROM '. SERVER_TABLE .' AS A LEFT JOIN '. SERVER_CAT_TABLE .' AS B ON B.cid = A.cat',
    'defaultSortables' => array(
        'order'     => array('A.cat', 'A.ip')
    ),
    'fields' => array(
        'ip'        => array('label' => _SERVIP),
        'game'      => array('label' => _SERVERGAME),
        'titre'     => array('label' => _CAT)
    ),
    'edit' => array(
        'op'                => 'editServer',
        'text'              => _EDITTHISSERV
    ),
    'delete' => array(
        'op'                => 'deleteServer',
        'text'              => _DELTHISSERV,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'ip'
    ),
    'emptytable' => _NOSERV,
    'callbackRowFunction' => array('functionName' => 'formatServerRow')
);

?>