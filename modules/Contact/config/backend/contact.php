<?php

/* nkList configuration */

// Define the list of contact
$contactList = array(
    'classPrefix' => 'contact',
    'sqlQuery' => 'SELECT id, titre, nom, email, date FROM '. CONTACT_TABLE,
    'defaultSortables' => array(
        'order'     => array('id')
    ),
    'fields' => array(
        'number'    => array('label' => '#'),
        'titre'     => array('label' => _TITLE),
        'name'      => array('label' => _NAME),
        'date'      => array('label' => _DATE),
        'read'      => array('label' => _READMESS)
    ),
    'delete' => array(
        'label'             => _DEL,// TODO : A AJOUTER A NKLIST
        'op'                => 'deleteMessage',
        'text'              => _DELTHISMESS,
        'confirmTxt'        => _DELETEMESSAGEFROM .' %s ! '. _CONFIRM,
        'confirmField'      => 'name'
    ),
    'emptytable' => _NOMESSINDB,
    'callbackRowFunction' => array('functionName' => 'formatContactRow')
);

?>