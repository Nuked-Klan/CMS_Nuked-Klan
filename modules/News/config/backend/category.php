<?php

/* nkList configuration */

// Define the list of news category
$newsCatList = array(
    'classPrefix' => 'newsCat',
    'sqlQuery' => 'SELECT nid, titre FROM '. NEWS_CAT_TABLE,
    'defaultSortables' => array(
        'order'     => array('titre')
    ),
    'fields' => array(
        'titre'         => array('label' => _CAT)
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
    'callbackRowFunction' => array('functionName' => 'formatNewsCatRow')
);

?>