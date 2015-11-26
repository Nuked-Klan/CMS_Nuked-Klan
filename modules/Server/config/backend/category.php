<?php

/* nkList configuration */

// Define the list of server category
$serverCatList = array(
    'classPrefix' => 'serverCat',
    'sqlQuery' => 'SELECT cid, titre FROM '. SERVER_CAT_TABLE,
    'defaultSortables' => array(
        'order'     => array('titre')
    ),
    'fields' => array(
        'titre'       => array('label' => _CAT)
    ),
    'edit' => array(
        'op'                => 'editCat',
        'text'              => _EDITTHISCAT
    ),
    'delete' => array(
        'op'                => 'deleteCat',
        'text'              => _DELTHISCAT,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'titre'
    ),
    'emptytable' => _NONECATINDATABASE,
    'callbackRowFunction' => array('functionName' => 'formatServerCatRow')
);

?>