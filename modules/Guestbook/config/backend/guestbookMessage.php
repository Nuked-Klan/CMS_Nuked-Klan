<?php

/* nkList configuration */

// Define the list of guestbook message
$guestbookMessageList = array(
    'classPrefix' => 'guestbookMessage',
    'sqlQuery' => 'SELECT id, date, name, host FROM '. GUESTBOOK_TABLE,
    'limit' => 30,
    'defaultSortables' => array(
        'order'     => array('id'),
        'dir'       => array('DESC')
    ),
    'fields' => array(
        'date'          => array('label' => _DATE),
        'name'          => array('label' => _AUTHOR),
        'host'          => array('label' => _IP)
    ),
    'edit' => array(
        'op'                => 'editMessage',
        'text'              => _EDITTHISPOST
    ),
    'delete' => array(
        'op'                => 'deleteMessage',
        'text'              => _DELTHISPOST,
        'confirmTxt'        => _SIGNDELETE .' %s ! '. _CONFIRM,
        'confirmField'      => 'name'
    ),
    'emptytable' => _NOSIGN,
    'callbackRowFunction' => array('functionName' => 'formatGuestbookMessageRow')
);

?>