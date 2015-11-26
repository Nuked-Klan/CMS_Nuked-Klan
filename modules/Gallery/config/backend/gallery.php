<?php

/* nkList configuration */

// Define the list of gallery
$galleryList = array(
    'classPrefix' => 'gallery',
    'sqlQuery' => 'SELECT G.sid, G.titre, G.cat, G.url, G.date, GC.parentid, GC.titre AS catName FROM '. GALLERY_TABLE .' AS G LEFT JOIN '. SECTIONS_CAT_TABLE .' AS GC ON GC.cid = G.cat',
    'limit' => 30,
    'defaultSortables' => array(
        'order'     => array('G.sid'),
        'dir'       => array('DESC')
    ),
    'sortables' => array(
        'titre'     => array('G.titre'),
        'date'      => array('G.sid'),// DESC
        'catName'   => array('G.cat', 'GC.parentid')
    ),
    'fields' => array(
        'titre'     => array('label' => _TITLE),
        'date'      => array('label' => _DATE),
        'catName'   => array('label' => _CAT)
    ),
    'edit' => array(
        'op'                => 'editScreen',
        'text'              => _EDITTHISSCREEN
    ),
    'delete' => array(
        'op'                => 'deleteScreen',
        'text'              => _DELTHISSCREEN,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'titre'
    ),
    'emptytable' => _NOSCREENINDB,
    'callbackRowFunction' => array('functionName' => 'formatScreenRow')
);

?>