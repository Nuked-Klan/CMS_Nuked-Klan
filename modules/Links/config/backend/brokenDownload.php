<?php

/* nkList configuration */

// Define the list of broken download
$brokenDownloadList = array(
    'classPrefix' => 'brokenDownload',
    'sqlQuery' => 'SELECT id, titre, url, broke FROM '. DOWNLOAD_TABLE .' WHERE broke > 0',
    'defaultSortables' => array(
        'order'     => array('broke', 'cat'),
        'dir'       => array('DESC', 'ASC')
    ),
    'fields' => array(
        'number'    => array('label' => '#'),
        'titre'     => array('label' => _TITLE),
        'broke'     => array('label' => 'X'),
        'erase'     => array('label' => _ERASE)
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
    'callbackRowFunction' => array('functionName' => 'formatBrokenDownloadRow')
);

?>