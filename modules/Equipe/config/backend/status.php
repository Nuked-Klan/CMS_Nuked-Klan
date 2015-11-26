<?php

/* nkList configuration */

// Define the list of staff status
$staffStatusList = array(
    'classPrefix' => 'staffStatus',
    'sqlQuery' => 'SELECT id, nom FROM '. $nuked['prefix'] .'_staff_status',
    'fields' => array(
        'nom'       => array('label' => 'Status')
    ),
    'edit' => array(
        'op'                => 'editStatus',
        //'text'              => _EDITTHISSTATUS
    ),
    'delete' => array(
        'op'                => 'deleteStatus',
        //'text'              => _DELTHISSTATUS,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'nom'
    ),
    //'emptytable' => _NOSTATUSINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatStaffStatusRow'
    )
);

?>