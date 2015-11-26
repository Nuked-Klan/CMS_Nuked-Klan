<?php

/* nkList configuration */

// Define the list of staff category
$staffCatList = array(
    'classPrefix' => 'staffCat',
    'sqlQuery' => 'SELECT id, nom FROM '. $nuked['prefix'] .'_staff_cat',
    'fields' => array(
        'nom'       => array('label' => _CAT)
    ),
    'edit' => array(
        'op'                => 'editCat',
        'text'              => _EDITTHISCAT
    ),
    'delete' => array(
        'op'                => 'deleteCat',
        'text'              => _DELTHISCAT,
        'confirmTxt'        => _DELETE_CONFIRM .' %s ! '. _CONFIRM,
        'confirmField'      => 'nom'
    ),
    'emptytable' => _NOCATINDB,
    'callbackRowFunction' => array(
        'functionName'      => 'formatStaffCatRow'
    )
);

?>