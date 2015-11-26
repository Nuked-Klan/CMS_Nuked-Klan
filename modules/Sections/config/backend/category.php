<?php

/* nkList configuration */

// Define the list of article category
$articleCatList = array(
    'classPrefix' => 'articleCat',
    'sqlQuery' => 'SELECT secid, secname, parentid, position FROM '. SECTIONS_CAT_TABLE,
    'defaultSortables' => array(
        'order'     => array('parentid', 'position')
    ),
    'fields' => array(
        'secname'       => array('label' => _CAT),
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
        'confirmField'      => 'secname'
    ),
    'emptytable' => _NONE .'&nbsp;'. _CAT .'&nbsp;'. _INDATABASE,// or _NONECATINDATABASE ?
    'callbackRowFunction' => array('functionName' => 'formatArticleCatRow')
);

?>