<?php

/* nkList configuration */

// Define the list of page
$pageList = array(
    'classPrefix' => 'page',
    'sqlQuery' => 'SELECT id, titre, url, type FROM '. PAGE_TABLE,
    'defaultSortables' => array(
        'order'     => array('titre')
    ),
    'fields' => array(
        'titre'     => array('label' => _PAGENAME),
        'pagename'  => array('label' => _PAGEFILE),
        'type'      => array('label' => _PAGETYPE)
    ),
    'edit' => array(
        'op'                => 'editPage',
        'text'              => _EDITTHISPAGE
    ),
    'delete' => array(
        'op'                => 'deletePage',
        'text'              => _DELTHISPAGE,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'titre'
    ),
    'emptytable' => _NOPAGE,
    'callbackRowFunction' => array('functionName' => 'formatPageRow')
);

?>