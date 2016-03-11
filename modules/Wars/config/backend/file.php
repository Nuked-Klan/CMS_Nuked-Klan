<?php

/* nkList configuration */

// Define the list of match file
$matchFileList = array(
    'classPrefix' => 'matchFile',
    'sqlQuery' => 'SELECT id, type, url FROM '. WARS_FILES_TABLE .' WHERE module = \'Wars\' AND im_id = %s',// Replace by nkDB_quote($im_id)
    'fields' => array(
        'type'      => array('label' => _TYPE)
    ),
    'edit' => array(
        'op'                => 'editFile',
        'text'              => _EDITFILE
    ),
    'delete' => array(
        'op'                => 'deleteFile',
        'text'              => _DELETEFILE,
        'confirmTxt'        => _DEL .' %s ! '. _CONFIRM,
        'confirmField'      => 'type'
    ),
    //'emptytable' => _NOMATCHFILE,
    'callbackRowFunction' => array('functionName' => 'formatMatchFileRow')
);

?>