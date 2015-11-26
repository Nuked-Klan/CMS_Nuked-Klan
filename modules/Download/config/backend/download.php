<?php

/* nkList configuration */

// Define the list of download
$linksList = array(
    'classPrefix' => 'download',
    'sqlQuery' => 'SELECT D.id, D.type, D.titre, D.url, D.date, DC.parentid, DC.titre AS catName FROM '. DOWNLOAD_TABLE .' AS D LEFT JOIN '. DOWNLOAD_CAT_TABLE .' AS DC ON DC.cid = D.type',
    'limit' => 30,
    'defaultSortables' => array(
        'order'     => array('D.id'),
        'dir'       => array('DESC')
    ),
    'sortables' => array(
        'titre'     => array('D.titre'),
        'date'      => array('D.id'),// DESC
        'catName'   => array('DC.titre', 'DC.parentid')
    ),
    'fields' => array(
        'titre'     => array('label' => _TITLE),
        'date'      => array('label' => _DATE),
        'catName'   => array('label' => _CAT)
    ),
    'edit' => array(
        'op'                => 'editDownload',
        'text'              => _EDITTHISFILE
    ),
    'delete' => array(
        'op'                => 'deleteDownload',
        'text'              => _DELTHISFILE,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'titre'
    ),
    'emptytable' => _NODOWNLOADINDB,
    'callbackRowFunction' => array('functionName' => 'formatDownloadRow')
);

?>