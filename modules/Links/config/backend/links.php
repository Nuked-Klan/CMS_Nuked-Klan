<?php

/* nkList configuration */

// Define the list of links
$linksList = array(
    'classPrefix' => 'links',
    'sqlQuery' => 'SELECT L.id, L.titre, L.cat, L.url, L.date, LC.titre, LC.parentid FROM '. LINKS_TABLE .' AS L LEFT JOIN '. LINKS_CAT_TABLE .' AS LC ON LC.cid = L.cat',
    'limit' => 30,
    'defaultSortables' => array(
        'order'     => array('L.id'),
        'dir'       => array('DESC')
    ),
    'sortables' => array(
        'titre'     => array('L.titre'),
        'date'      => array('L.id'),// DESC
        'catName'   => array('LC.titre', 'LC.parentid')
    ),
    'fields' => array(
        'titre'     => array('label' => _TITLE),
        'date'      => array('label' => _DATE),
        'catName'   => array('label' => _CAT)
    ),
    'edit' => array(
        'op'                => 'editLinks',
        'text'              => _EDITTHISLINK
    ),
    'delete' => array(
        'op'                => 'deleteLinks',
        'text'              => _DELTHISLINK,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'titre'
    ),
    'emptytable' => _NOLINKINDB,
    'callbackRowFunction' => array('functionName' => 'formatLinksRow')
);

?>