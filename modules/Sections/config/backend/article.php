<?php

/* nkList configuration */

// Define the list of article
$articleList = array(
    'classPrefix' => 'article',
    'sqlQuery' => 'SELECT S.artid, S.title, S.autor, S.autor_id, S.secid, S.date, SC.parentid, SC.secname FROM '. SECTIONS_TABLE .' AS S LEFT JOIN '. SECTIONS_CAT_TABLE .' AS SC ON SC.secid = S.secid',
    'limit' => 30,
    'defaultSortables' => array(
        'order'     => array('S.artid'),
        'dir'       => array('DESC')
    ),
    'sortables' => array(
        'date'      => array('date'),
        'title'     => array('title'),
        'author'    => array('author'),
        'secname'   => array('secname')
    ),
    'fields' => array(
        'title'     => array('label' => _TITLE),
        'secname'   => array('label' => _CAT),
        'date'      => array('label' => _DATE),
        'author'    => array('label' => _AUTHOR)
    ),
    'edit' => array(
        'op'                => 'editArticle',
        'text'              => _EDITTHISART
    ),
    'delete' => array(
        'op'                => 'deleteArticle',
        'text'              => _DELTHISART,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'title'
    ),
    'emptytable' => _NOARTINDB,
    'callbackRowFunction' => array('functionName' => 'formatArticleRow')
);

?>