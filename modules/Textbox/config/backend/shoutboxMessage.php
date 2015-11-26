<?php

/* nkList configuration */

// Define the list of shoutbox message
$shoutboxMessageList = array(
    'classPrefix' => 'shoutboxMessage',
    'limit' => 30,
    'sqlQuery' => 'SELECT id, date, auteur, ip FROM '. TEXTBOX_TABLE,
    'defaultSortables' => array(
        'order'     => array('id'),
        'dir'       => array('DESC')
    ),
    'fields' => array(
        'date'      => array('label' => _DATE),
        'auteur'    => array('label' => _NICKNAME),
        'ip'        => array('label' => _IP)
    ),
    'edit' => array(
        'op'                => 'editMessage',
        'text'              => _EDITTHISMESS
    ),
    'delete' => array(
        'op'                => 'deleteMessage',
        'text'              => _DELTHISMESS,
        'confirmTxt'        => _DELETETEXT .' %s ! '. _CONFIRM,
        'confirmField'      => 'auteur'
    ),
    'emptytable' => _NOMESS,
    'callbackRowFunction' => array('functionName' => 'formatShoutboxMessageRow')
);

?>