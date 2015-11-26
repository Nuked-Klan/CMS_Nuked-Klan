<?php

/* nkList configuration */

// Define the list of download category
$downladCatList = array(
    'classPrefix' => 'downladCat',
    'sqlQuery' => 'SELECT cid, titre, parentid, position FROM '. DOWNLOAD_CAT_TABLE,
    'defaultSortables' => array(
        'order'     => array('parentid', 'position')
    ),
    'fields' => array(
        'titre'         => array('label' => _CAT),
        'parentCat'     => array('label' => _CATPARENT),
        'position'      => array('label' => _POSITION)
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
    'emptytable' => _NONE .'&nbsp;'. _CAT .'&nbsp;'. _INDATABASE,// or _NONECATINDATABASE ?
    'callbackRowFunction' => array('functionName' => 'formatDownloadCatRow')
);

?>