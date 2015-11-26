<?php

/* nkList configuration */

// Define the list of broken links
$brokenLinksList = array(
    'classPrefix' => 'brokenLinks',
    'sqlQuery' => 'SELECT id, titre, url, broke FROM '. LINKS_TABLE .' WHERE broke > 0',
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
    'callbackRowFunction' => array('functionName' => 'formatBrokenLinksRow')
);

?>