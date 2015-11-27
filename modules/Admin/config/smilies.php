<?php

/* nkList configuration */

// Define the list of smilies
$smiliesList = array(
    'classPrefix' => 'smilies',
    'sqlQuery' => 'SELECT id, code, url, name FROM '. SMILIES_TABLE,
    'defaultSortables' => array(
        'order'     => array('id')
    ),
    'fields' => array(
        'smiley'    => array('label' => _SMILEY, 'type' => 'image'),
        'name'      => array('label' => _NAME),
        'code'      => array('label' => _CODE)
    ),
    'edit' => array(
        'op'                => 'edit',
        'text'              => _SMILEYEDIT
    ),
    'delete' => array(
        'op'                => 'delete',
        'text'              => _SMILEYDEL,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'nom'
    ),
    'emptytable' => _NOSMILIESINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatSmileyRow'
    )
);

?>