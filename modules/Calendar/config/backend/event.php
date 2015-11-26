<?php

/* nkList configuration */

// Define the list of event
$eventList = array(
    'classPrefix' => 'event',
    'sqlQuery' => 'SELECT id, titre, auteur, date_jour, date_mois, date_an, heure FROM '. CALENDAR_TABLE,
    'defaultSortables' => array(
        'order'     => array('date_an', 'date_mois', 'date_jour'),
        'dir'       => array('DESC', 'DESC', 'DESC'),
    ),
    'fields' => array(
        'date'      => array('label' => _DATE),
        'titre'     => array('label' => _TITLE),
        'auteur'    => array('label' => _AUTEUR)
    ),
    'edit' => array(
        'op'                => 'editEvent',
        'text'              => _EDITTHISEVENT
    ),
    'delete' => array(
        'label'             => _DEL,
        'op'                => 'deleteEvent',
        'text'              => _DELTHISEVENT,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'titre'
    ),
    'emptytable' => _NOEVENT,
    'callbackRowFunction' => array('functionName' => 'formatEventRow')
);

?>