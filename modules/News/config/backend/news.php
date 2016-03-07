<?php

/* nkList configuration */

// Define the list of news
$newsList = array(
    'classPrefix' => 'news',
    'sqlQuery' => 'SELECT N.id, N.titre, N.auteur, N.auteur_id, N.date, NC.titre AS catName FROM '. NEWS_TABLE .' AS N LEFT JOIN '. NEWS_CAT_TABLE .' AS NC ON NC.nid = N.cat',
    'limit' => 30,
    'defaultSortables' => array(
        'order'     => array('N.date'),
        'dir'       => array('DESC')
    ),
    'sortables' => array(
        'date'      => array('N.date'),
        'title'     => array('N.title'),
        'auteur'    => array('N.auteur'),
        'titre'     => array('N.titre')
    ),
    'fields' => array(
        'title'     => array('label' => _TITLE),
        'catName'   => array('label' => _CAT),
        'date'      => array('label' => _DATE),
        'auteur'    => array('label' => __('AUTHOR'))
    ),
    'edit' => array(
        'op'                => 'editNews',
        'text'              => _EDITTHISNEWS
    ),
    'delete' => array(
        'op'                => 'deleteNews',
        'text'              => _DELTHISNEWS,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'titre'
    ),
    'emptytable' => _NONEWSINDB,
    'callbackRowFunction' => array('functionName' => 'formatNewsRow')
);

?>